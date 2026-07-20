<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Sample\StoreSampleAssignmentRequest;
use App\Http\Requests\Sample\AdjustSampleRequest;
use App\Http\Resources\SampleAssignmentResource;
use App\Http\Resources\SampleTransactionResource;
use App\Models\SampleAssignment;
use App\Models\SampleTransaction;
use App\Models\User;
use App\Services\SampleAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SampleAssignmentController extends BaseApiController
{
    protected SampleAssignmentService $sampleService;

    public function __construct(SampleAssignmentService $sampleService)
    {
        $this->sampleService = $sampleService;
    }

    /**
     * Get summary of assignments for all MRs.
     */
    public function index(Request $request): JsonResponse
    {
        // Simple logic for API: get MRs and their total assigned/distributed stats
        $users = User::role('MR')->withCount([
            'sampleAssignments as total_assigned' => function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw('SUM(assigned_quantity)'));
            },
            'sampleAssignments as total_distributed' => function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw('SUM(distributed_quantity)'));
            }
        ])->paginate($request->get('per_page', 15));

        return $this->successResponse($users, 'Sample assignment summaries retrieved');
    }

    /**
     * Get specific MR's assigned samples and transaction history.
     */
    public function show(User $user): JsonResponse
    {
        if (!$user->hasRole('MR')) {
            return $this->errorResponse('User is not an MR', 400);
        }

        $assignments = SampleAssignment::with('product')
            ->where('user_id', $user->id)
            ->get();

        $transactions = SampleTransaction::with(['product', 'performer'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(50)
            ->get();

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'employee_code' => $user->employee_code,
            ],
            'assignments' => SampleAssignmentResource::collection($assignments),
            'transactions' => SampleTransactionResource::collection($transactions),
        ], 'MR samples retrieved');
    }

    /**
     * Assign new samples or increase existing ones for an MR.
     */
    public function store(StoreSampleAssignmentRequest $request): JsonResponse
    {
        try {
            $this->sampleService->assignSamples(
                $request->user_id,
                $request->products,
                $request->user()->id
            );
            return $this->successResponse([], 'Samples assigned successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Adjust (reduce/return) samples.
     */
    public function adjust(AdjustSampleRequest $request): JsonResponse
    {
        try {
            $assignment = $this->sampleService->adjustSamples(
                $request->user_id,
                $request->product_id,
                $request->action_type,
                $request->quantity,
                $request->reason,
                $request->user()->id
            );
            
            return $this->successResponse(new SampleAssignmentResource($assignment), 'Samples adjusted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
