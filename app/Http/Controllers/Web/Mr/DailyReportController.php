<?php

namespace App\Http\Controllers\Web\Mr;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyReport\SubmitDailyReportRequest;
use App\Models\DailyReport;
use App\Services\DailyReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    protected $dailyReportService;

    public function __construct(DailyReportService $dailyReportService)
    {
        $this->dailyReportService = $dailyReportService;
    }

    public function index(Request $request)
    {
        $reports = DailyReport::where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->paginate(15);
            
        return view('mr.reports.index', compact('reports'));
    }

    public function createOrEdit()
    {
        $date = Carbon::today()->format('Y-m-d');
        
        try {
            // This generates the draft and grabs all stats
            $report = $this->dailyReportService->generateDraftReport(auth()->id(), $date);
            
            // If already submitted, redirect to a read-only view or show error
            if ($report->status !== 'Draft') {
                return redirect()->route('mr.reports.index')
                    ->with('error', 'Today\'s report has already been submitted and cannot be edited.');
            }

            return view('mr.reports.form', compact('report'));
        } catch (\Exception $e) {
            return redirect()->route('mr.reports.index')->with('error', $e->getMessage());
        }
    }

    public function store(SubmitDailyReportRequest $request)
    {
        $date = Carbon::today()->format('Y-m-d');
        $report = DailyReport::where('user_id', auth()->id())->where('date', $date)->firstOrFail();

        if ($report->status !== 'Draft') {
            return redirect()->route('mr.reports.index')
                ->with('error', 'Today\'s report has already been submitted.');
        }

        $report->update([
            'today_summary' => $request->today_summary,
            'problems_faced' => $request->problems_faced,
            'tomorrow_plan' => $request->tomorrow_plan,
            'status' => 'Submitted',
        ]);

        return redirect()->route('mr.reports.index')
            ->with('success', 'Your daily report has been successfully submitted. Great job today!');
    }
}
