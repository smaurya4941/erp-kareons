<?php

namespace App\Services;

use App\Models\SampleAssignment;
use App\Models\SampleTransaction;
use App\Models\Product;
use App\Notifications\SamplesAssignedNotification;
use Illuminate\Support\Facades\DB;
use Exception;

class SampleAssignmentService extends BaseService
{
    public function __construct(protected NotificationService $notificationService)
    {
    }

    /**
     * Assign or increase multiple products for an MR.
     *
     * @param int $userId
     * @param array $products Array of ['product_id' => X, 'quantity' => Y]
     * @param int $adminId
     * @return void
     * @throws Exception
     */
    public function assignSamples(int $userId, array $products, int $adminId): void
    {
        DB::transaction(function () use ($userId, $products, $adminId) {
            foreach ($products as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];

                // Verify product is active
                $product = Product::findOrFail($productId);
                if (!$product->status) {
                    throw new Exception("Product {$product->name} is inactive and cannot be assigned.");
                }

                // Check if assignment exists
                $assignment = SampleAssignment::where('user_id', $userId)
                                              ->where('product_id', $productId)
                                              ->lockForUpdate() // Prevent race conditions
                                              ->first();

                if ($assignment) {
                    // Increase existing assignment
                    $assignment->assigned_quantity += $quantity;
                    $assignment->save();
                    
                    $type = 'increased';
                } else {
                    // Create new assignment
                    SampleAssignment::create([
                        'user_id' => $userId,
                        'product_id' => $productId,
                        'assigned_quantity' => $quantity,
                        'distributed_quantity' => 0,
                    ]);
                    
                    $type = 'assigned';
                }

                // Log the transaction
                SampleTransaction::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'type' => $type,
                    'quantity' => $quantity,
                    'reason' => 'New Assignment',
                    'performed_by' => $adminId,
                ]);
            }
        });

        // Notify the MR of their new samples (after commit; non-blocking).
        $this->notificationService->notifyUser(
            $userId,
            new SamplesAssignedNotification(count($products), (int) array_sum(array_column($products, 'quantity')))
        );
    }

    /**
     * Reduce, return, adjust, or distribute samples for an MR.
     *
     * Distribution ('distributed') records stock handed to doctors during visits:
     * it increments distributed_quantity while leaving assigned_quantity intact,
     * so the "Total Assigned" figure is preserved and "Distributed" reflects usage.
     *
     * Stock removals ('reduce', 'return', 'adjustment') deduct from assigned_quantity,
     * as those permanently take stock back out of the MR's possession.
     *
     * In both cases Remaining = assigned_quantity - distributed_quantity decreases.
     *
     * @param int $userId
     * @param int $productId
     * @param string $actionType ('distributed', 'reduce', 'return', 'adjustment')
     * @param int $quantity (Must be positive integer)
     * @param string $reason
     * @param int $adminId
     * @return SampleAssignment
     * @throws Exception
     */
    public function adjustSamples(int $userId, int $productId, string $actionType, int $quantity, string $reason, int $adminId): SampleAssignment
    {
        return DB::transaction(function () use ($userId, $productId, $actionType, $quantity, $reason, $adminId) {
            $assignment = SampleAssignment::where('user_id', $userId)
                                          ->where('product_id', $productId)
                                          ->lockForUpdate()
                                          ->first();

            if (!$assignment) {
                throw new Exception("No sample assignment found for this product.");
            }

            // Validation: Ensure remaining quantity won't become negative
            if ($assignment->remaining_quantity < $quantity) {
                throw new Exception("Cannot process {$quantity}. Only {$assignment->remaining_quantity} samples are available.");
            }

            if ($actionType === 'distributed') {
                // Track the units handed out; assigned stock stays untouched.
                $assignment->distributed_quantity += $quantity;
            } else {
                // Stock is taken back out of the MR's allocation entirely.
                $assignment->assigned_quantity -= $quantity;
            }
            $assignment->save();

            // Log the transaction (negative quantity = stock leaving the MR).
            SampleTransaction::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'type' => $actionType,
                'quantity' => -$quantity,
                'reason' => $reason,
                'performed_by' => $adminId,
            ]);

            return $assignment;
        });
    }

    /**
     * Get MR's assigned samples.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMrSamples(int $userId)
    {
        return SampleAssignment::with('product')->where('user_id', $userId)->get();
    }
}
