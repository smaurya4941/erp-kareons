<?php

namespace App\Http\Controllers\Web\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DailyReport;
use App\Models\DoctorVisit;
use App\Models\DoctorVisitSample;
use App\Models\Order;

class ReportHubController extends Controller
{
    public function index()
    {
        // Simple counts for the hub cards
        $stats = [
            'attendance_records' => Attendance::count(),
            'doctor_visits' => DoctorVisit::count(),
            'orders' => Order::count(),
            'samples_distributed' => DoctorVisitSample::sum('quantity'),
            'daily_reports' => DailyReport::count(),
        ];

        return view('admin.reports.hub', compact('stats'));
    }
}
