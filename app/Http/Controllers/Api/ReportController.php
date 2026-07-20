<?php

namespace App\Http\Controllers\Api;

use App\Models\Attendance;
use App\Models\DoctorVisit;
use App\Models\DoctorVisitSample;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends BaseApiController
{
    /**
     * Generate specific report data based on type.
     */
    public function generate(Request $request, string $type): JsonResponse
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $range = [$startDate . ' 00:00:00', $endDate . ' 23:59:59'];

        switch ($type) {
            case 'attendance':
                $data = Attendance::with('user')
                    ->whereBetween('check_in_time', $range)
                    ->get();
                break;

            case 'visits':
                $data = DoctorVisit::with(['user', 'discussedProducts.product', 'distributedSamples.product'])
                    ->whereBetween('created_at', $range)
                    ->get();
                break;

            case 'orders':
                $data = Order::with(['user', 'visit', 'items.product'])
                    ->whereBetween('created_at', $range)
                    ->get();
                break;

            case 'samples':
                $data = DoctorVisitSample::with(['product', 'visit.user'])
                    ->whereBetween('created_at', $range)
                    ->get();
                break;

            default:
                return $this->errorResponse('Invalid report type requested.', 400);
        }

        return $this->successResponse([
            'report_type' => $type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_records' => $data->count(),
            'data' => $data,
        ], ucfirst($type) . ' report generated successfully');
    }
}
