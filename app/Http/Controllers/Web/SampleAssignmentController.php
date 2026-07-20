<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sample\StoreSampleAssignmentRequest;
use App\Http\Requests\Sample\AdjustSampleRequest;
use App\Models\SampleAssignment;
use App\Models\SampleTransaction;
use App\Models\User;
use App\Models\Product;
use App\Services\SampleAssignmentService;
use Illuminate\Http\Request;

class SampleAssignmentController extends Controller
{
    protected SampleAssignmentService $sampleService;

    public function __construct(SampleAssignmentService $sampleService)
    {
        $this->sampleService = $sampleService;
    }

    /**
     * Display a listing of MRs and their total assigned samples.
     */
    public function index(Request $request)
    {
        $query = User::role('MR')->withCount([
            'sampleAssignments as total_assigned' => function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw('SUM(assigned_quantity)'));
            },
            'sampleAssignments as total_distributed' => function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw('SUM(distributed_quantity)'));
            }
        ]);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($request->get('per_page', 10))->withQueryString();

        return view('admin.samples.index', compact('users'));
    }

    /**
     * Show the ledger and history for a specific MR.
     */
    public function show(User $user)
    {
        if (!$user->hasRole('MR')) {
            return redirect()->route('admin.samples.index')->with('error', 'User is not an MR.');
        }

        $assignments = SampleAssignment::with('product')
            ->where('user_id', $user->id)
            ->get();

        $transactions = SampleTransaction::with(['product', 'performer'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('admin.samples.show', compact('user', 'assignments', 'transactions'));
    }

    /**
     * Show the form for assigning new samples.
     */
    public function create(Request $request)
    {
        $mrs = User::role('MR')->where('status', true)->get();
        $products = Product::where('status', true)->get();
        
        $selectedMr = $request->get('user_id');

        return view('admin.samples.assign', compact('mrs', 'products', 'selectedMr'));
    }

    /**
     * Store new sample assignments.
     */
    public function store(StoreSampleAssignmentRequest $request)
    {
        try {
            $this->sampleService->assignSamples(
                $request->user_id,
                $request->products,
                auth()->id()
            );
            return redirect()->route('admin.samples.show', $request->user_id)
                             ->with('success', 'Samples assigned successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form to adjust (reduce/return) samples.
     */
    public function adjustForm(User $user, Product $product)
    {
        $assignment = SampleAssignment::where('user_id', $user->id)
                                      ->where('product_id', $product->id)
                                      ->firstOrFail();

        return view('admin.samples.adjust', compact('user', 'product', 'assignment'));
    }

    /**
     * Process sample adjustment.
     */
    public function adjust(AdjustSampleRequest $request)
    {
        try {
            $this->sampleService->adjustSamples(
                $request->user_id,
                $request->product_id,
                $request->action_type,
                $request->quantity,
                $request->reason,
                auth()->id()
            );
            return redirect()->route('admin.samples.show', $request->user_id)
                             ->with('success', 'Sample stock adjusted successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
