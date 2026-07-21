<?php

namespace App\Services;

use App\Models\Attendance;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AttendanceService extends BaseService
{
    // E.g., if checking in after 10:00 AM, mark as late
    public const LATE_THRESHOLD = '10:00:00';

    protected DailyReportService $dailyReportService;

    public function __construct(DailyReportService $dailyReportService)
    {
        $this->dailyReportService = $dailyReportService;
    }

    /**
     * Handle MR Check In
     *
     * @param int $userId
     * @param array $data
     * @return Attendance
     * @throws Exception
     */
    public function checkIn(int $userId, array $data): Attendance
    {
        $today = Carbon::today();
        
        // Rule 1: MR can Check In only once per day
        $existing = Attendance::where('user_id', $userId)
                              ->whereDate('date', $today)
                              ->first();

        if ($existing) {
            throw new Exception("You have already checked in today.");
        }

        // Handle Selfie
        if (!isset($data['selfie']) || !($data['selfie'] instanceof UploadedFile)) {
            throw new Exception("Selfie is required for Check In.");
        }
        
        $selfiePath = $data['selfie']->store('attendances/checkin', 'public');

        // Check if Late
        $now = Carbon::now();
        $thresholdTime = Carbon::parse($today->toDateString() . ' ' . self::LATE_THRESHOLD);
        $isLate = $now->greaterThan($thresholdTime);

        $attendance = Attendance::create([
            'user_id' => $userId,
            'date' => $today->toDateString(),
            'check_in_time' => $now,
            'check_in_selfie' => $selfiePath,
            'check_in_lat' => $data['lat'] ?? null,
            'check_in_lng' => $data['lng'] ?? null,
            'check_in_accuracy' => $data['accuracy'] ?? null,
            'check_in_address' => $data['address'] ?? null,
            'check_in_device_info' => $data['device_info'] ?? null,
            'status' => 'Incomplete',
            'is_late' => $isLate,
        ]);

        return $attendance;
    }

    /**
     * Handle MR Check Out
     *
     * @param int $userId
     * @param array $data
     * @return Attendance
     * @throws Exception
     */
    public function checkOut(int $userId, array $data): Attendance
    {
        $today = Carbon::today();
        
        // Rule 2 & 4: MR cannot Check Out without Check In
        $attendance = Attendance::where('user_id', $userId)
                                ->whereDate('date', $today)
                                ->first();

        if (!$attendance) {
            throw new Exception("You must check in first before checking out.");
        }

        if ($attendance->check_out_time) {
            throw new Exception("You have already checked out today.");
        }

        // Handle Selfie
        if (!isset($data['selfie']) || !($data['selfie'] instanceof UploadedFile)) {
            throw new Exception("Selfie is required for Check Out.");
        }
        
        $selfiePath = $data['selfie']->store('attendances/checkout', 'public');
        
        $now = Carbon::now();

        // Calculate Working Hours (in minutes)
        $workingMinutes = $attendance->check_in_time->diffInMinutes($now);

        // Persist the check-out and auto-compile the daily report atomically so
        // the MR never has to prepare the report by hand.
        DB::transaction(function () use ($attendance, $now, $selfiePath, $data, $workingMinutes, $userId) {
            $attendance->update([
                'check_out_time' => $now,
                'check_out_selfie' => $selfiePath,
                'check_out_lat' => $data['lat'] ?? null,
                'check_out_lng' => $data['lng'] ?? null,
                'check_out_accuracy' => $data['accuracy'] ?? null,
                'check_out_address' => $data['address'] ?? null,
                'check_out_device_info' => $data['device_info'] ?? null,
                'working_minutes' => $workingMinutes,
                'status' => 'Present', // Successfully completed full day
            ]);

            $this->dailyReportService->generateOnCheckout($userId, $attendance->date->toDateString());
        });

        return $attendance;
    }
}
