<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\DoctorVisit;
use App\Models\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class DoctorVisitService extends BaseService
{
    protected SampleAssignmentService $sampleAssignmentService;

    public function __construct(SampleAssignmentService $sampleAssignmentService)
    {
        $this->sampleAssignmentService = $sampleAssignmentService;
    }

    /**
     * Create a complete doctor visit record including products, samples, and orders.
     *
     * @param int $userId
     * @param array $data
     * @return DoctorVisit
     * @throws Exception
     */
    public function createVisit(int $userId, array $data): DoctorVisit
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Rule 1: Attendance must be checked in before creating a visit
        $attendance = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
        if (!$attendance) {
            throw new Exception("You must check in for attendance before creating a doctor visit.");
        }

        return DB::transaction(function () use ($userId, $data, $today, $now) {
            
            // 1. Create Visit Record
            $visit = DoctorVisit::create([
                'user_id' => $userId,
                'date' => $today,
                'time' => $now->format('H:i:s'),
                'lat' => $data['lat'] ?? null,
                'lng' => $data['lng'] ?? null,
                'accuracy' => $data['accuracy'] ?? null,
                'address' => $data['address'] ?? null,
                
                'doctor_name' => $data['doctor_name'],
                'clinic_name' => $data['clinic_name'] ?? null,
                'specialization' => $data['specialization'],
                'phone' => $data['phone'] ?? null,
                'area' => $data['area'] ?? null,
                'doctor_address' => $data['doctor_address'] ?? null,
                
                'discussion_summary' => $data['discussion_summary'],
                'doctor_response' => $data['doctor_response'],
                'competitor_medicines' => $data['competitor_medicines'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'status' => 'Completed',
            ]);

            // 2. Attach Discussed Products
            if (!empty($data['products'])) {
                foreach ($data['products'] as $product) {
                    $visit->discussedProducts()->create([
                        'product_id' => $product['product_id'],
                        'interest_level' => $product['interest_level'] ?? null,
                        'remarks' => $product['remarks'] ?? null,
                    ]);
                }
            }

            // 3. Process Sample Distribution
            if (!empty($data['samples'])) {
                foreach ($data['samples'] as $sample) {
                    // Record in the visit
                    $visit->distributedSamples()->create([
                        'product_id' => $sample['product_id'],
                        'quantity' => $sample['quantity'],
                    ]);

                    // Rule 4: Reduce MR's assigned stock
                    $this->sampleAssignmentService->adjustSamples(
                        $userId,
                        $sample['product_id'],
                        'distributed',
                        $sample['quantity'],
                        "Distributed during visit to {$data['doctor_name']}",
                        $userId // performed by MR
                    );
                }
            }

            // 4. Record Orders
            if (!empty($data['orders'])) {
                // For V1, creating a single Order record linked to the visit
                $order = Order::create([
                    'user_id' => $userId,
                    'doctor_visit_id' => $visit->id,
                    'doctor_name' => $data['doctor_name'],
                    'status' => 'Pending',
                    'remarks' => $data['order_remarks'] ?? null,
                ]);

                // Record initial status history
                $order->statusHistories()->create([
                    'changed_by_user_id' => $userId,
                    'status' => 'Pending',
                ]);

                foreach ($data['orders'] as $orderItem) {
                    $order->items()->create([
                        'product_id' => $orderItem['product_id'],
                        'quantity' => $orderItem['quantity'],
                        // Prices left as 0 default per the Phase 5 spec (simplified order taking)
                    ]);
                }
            }

            return $visit;
        });
    }

    /**
     * Fetch a visit owned by the given MR or throw.
     *
     * @throws Exception
     */
    public function findOwnedVisit(int $userId, int $visitId): DoctorVisit
    {
        $visit = DoctorVisit::where('id', $visitId)->where('user_id', $userId)->first();

        if (!$visit) {
            throw new Exception('Doctor visit not found or you are not authorized to modify it.');
        }

        return $visit;
    }

    /**
     * Attach discussed products to an existing visit.
     *
     * @param array $products Array of ['product_id' => X, 'interest_level' => Y, 'remarks' => Z]
     * @throws Exception
     */
    public function addProductDiscussions(int $userId, int $visitId, array $products): DoctorVisit
    {
        $visit = $this->findOwnedVisit($userId, $visitId);

        return DB::transaction(function () use ($visit, $products) {
            foreach ($products as $product) {
                $visit->discussedProducts()->updateOrCreate(
                    ['product_id' => $product['product_id']],
                    [
                        'interest_level' => $product['interest_level'] ?? null,
                        'remarks' => $product['remarks'] ?? null,
                    ]
                );
            }

            return $visit->load('discussedProducts.product');
        });
    }

    /**
     * Distribute samples during an existing visit, enforcing remaining stock.
     *
     * @param array $samples Array of ['product_id' => X, 'quantity' => Y]
     * @throws Exception
     */
    public function distributeSamples(int $userId, int $visitId, array $samples): DoctorVisit
    {
        $visit = $this->findOwnedVisit($userId, $visitId);

        return DB::transaction(function () use ($visit, $samples, $userId) {
            foreach ($samples as $sample) {
                // Rule: reduce MR's assigned stock first (throws if insufficient).
                $this->sampleAssignmentService->adjustSamples(
                    $userId,
                    $sample['product_id'],
                    'distributed',
                    $sample['quantity'],
                    "Distributed during visit to {$visit->doctor_name}",
                    $userId
                );

                $visit->distributedSamples()->create([
                    'product_id' => $sample['product_id'],
                    'quantity' => $sample['quantity'],
                ]);
            }

            return $visit->load('distributedSamples.product');
        });
    }

    /**
     * Record an order taken during one of the MR's visits.
     *
     * @param array $items Array of ['product_id' => X, 'quantity' => Y]
     * @throws Exception
     */
    public function recordOrder(int $userId, array $items, int $visitId, ?string $remarks = null): Order
    {
        $visit = $this->findOwnedVisit($userId, $visitId);

        return DB::transaction(function () use ($userId, $items, $visit, $remarks) {
            $order = Order::create([
                'user_id' => $userId,
                'doctor_visit_id' => $visit->id,
                'doctor_name' => $visit->doctor_name,
                'status' => 'Pending',
                'remarks' => $remarks,
            ]);

            $order->statusHistories()->create([
                'changed_by_user_id' => $userId,
                'status' => 'Pending',
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return $order->load('items.product');
        });
    }
}
