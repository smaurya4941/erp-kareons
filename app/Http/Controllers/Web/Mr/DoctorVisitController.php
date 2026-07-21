<?php

namespace App\Http\Controllers\Web\Mr;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorVisit\StoreDoctorVisitRequest;
use App\Models\DoctorVisit;
use App\Services\DoctorVisitService;
use Illuminate\Http\Request;

class DoctorVisitController extends Controller
{
    protected DoctorVisitService $visitService;

    public function __construct(DoctorVisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Display MR's visit history
     */
    public function index(Request $request)
    {
        $visits = DoctorVisit::with(['discussedProducts', 'distributedSamples', 'order'])
                             ->where('user_id', auth()->id())
                             ->orderBy('date', 'desc')
                             ->orderBy('time', 'desc')
                             ->paginate(15);
                             
        $attendance = \App\Models\Attendance::where('user_id', auth()->id())
                        ->whereDate('date', \Carbon\Carbon::today())
                        ->first();
                                 
        return view('mr.visits.index', compact('visits', 'attendance'));
    }

    /**
     * Show the Doctor Visit Wizard
     */
    public function create()
    {
        $attendance = \App\Models\Attendance::where('user_id', auth()->id())
                        ->whereDate('date', \Carbon\Carbon::today())
                        ->first();
                        
        if (!$attendance) {
            return redirect()->route('mr.dashboard')->with('error', 'You must check in for attendance before creating a doctor visit.');
        }
        
        if ($attendance->check_out_time !== null) {
            return redirect()->route('mr.dashboard')->with('error', 'You have already checked out today. You cannot create new doctor visits.');
        }

        $products = \App\Models\Product::where('status', true)->get();
        
        $assignedSamples = \App\Models\SampleAssignment::with('product')
                                ->where('user_id', auth()->id())
                                ->whereRaw('assigned_quantity - distributed_quantity > 0')
                                ->get()
                                ->map(function($assignment) {
                                    return [
                                        'product_id' => $assignment->product_id,
                                        'product_name' => $assignment->product->name,
                                        'remaining_quantity' => $assignment->remaining_quantity,
                                    ];
                                });

        return view('mr.visits.create', compact('products', 'assignedSamples'));
    }

    /**
     * Handle submission of the wizard via Web
     */
    public function store(StoreDoctorVisitRequest $request)
    {
        try {
            $this->visitService->createVisit(auth()->id(), $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Doctor visit recorded successfully.',
                'redirect' => route('mr.visits.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
