<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Reconcile sample_assignments.distributed_quantity with the authoritative
 * doctor_visit_samples records.
 *
 * A previous bug recorded sample distributions by deducting from
 * assigned_quantity while never incrementing distributed_quantity. As a result
 * the "Distributed" column always showed 0 and "Total Assigned" was silently
 * eroded. This migration rebuilds both figures from the source of truth so that:
 *
 *   distributed_quantity = SUM(doctor_visit_samples.quantity) per MR + product
 *   assigned_quantity    = remaining_stock + distributed_quantity
 *
 * "remaining_stock" (assigned_quantity - distributed_quantity as currently
 * stored) is preserved, so no MR gains or loses real available stock. The
 * migration is idempotent — re-running it produces the same result.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Total quantity distributed per (user, product), joined via the visit owner.
        $distributed = DB::table('doctor_visit_samples as dvs')
            ->join('doctor_visits as dv', 'dv.id', '=', 'dvs.doctor_visit_id')
            ->select('dv.user_id', 'dvs.product_id', DB::raw('SUM(dvs.quantity) as total'))
            ->groupBy('dv.user_id', 'dvs.product_id')
            ->get();

        DB::transaction(function () use ($distributed) {
            foreach ($distributed as $row) {
                $assignment = DB::table('sample_assignments')
                    ->where('user_id', $row->user_id)
                    ->where('product_id', $row->product_id)
                    ->first();

                if (!$assignment) {
                    continue;
                }

                $totalDistributed = (int) $row->total;
                // Current remaining is authoritative and must be preserved.
                $remaining = (int) $assignment->assigned_quantity - (int) $assignment->distributed_quantity;

                DB::table('sample_assignments')
                    ->where('id', $assignment->id)
                    ->update([
                        'distributed_quantity' => $totalDistributed,
                        'assigned_quantity' => $remaining + $totalDistributed,
                        'updated_at' => now(),
                    ]);
            }
        });
    }

    public function down(): void
    {
        // Data-reconciliation migration; no automatic rollback.
    }
};
