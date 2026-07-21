<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'today_summary',
        'problems_faced',
        'tomorrow_plan',
        'status',
        'stats_snapshot',
    ];

    protected $casts = [
        'date' => 'date',
        'stats_snapshot' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Resolve attendance figures (working hours, check-in/out) for display.
     *
     * Older reports froze stale/zero attendance values into their snapshot, so
     * we always prefer the authoritative Attendance record and fall back to the
     * snapshot only when no attendance row exists.
     *
     * @return array{working_hours: string, check_in: ?string, check_out: ?string}
     */
    public function resolvedAttendance(): array
    {
        $snap = $this->stats_snapshot['attendance'] ?? [];

        $attendance = Attendance::where('user_id', $this->user_id)
            ->where('date', $this->date->toDateString())
            ->first();

        $minutes = $attendance->working_minutes ?? $snap['working_minutes'] ?? null;

        return [
            'working_hours' => $minutes !== null
                ? intdiv((int) $minutes, 60) . 'h ' . ((int) $minutes % 60) . 'm'
                : ($snap['working_hours'] ?? 'N/A'),
            'check_in' => $attendance?->check_in_time?->format('h:i A') ?? ($snap['check_in'] ?? null),
            'check_out' => $attendance?->check_out_time?->format('h:i A') ?? ($snap['check_out'] ?? null),
        ];
    }
}
