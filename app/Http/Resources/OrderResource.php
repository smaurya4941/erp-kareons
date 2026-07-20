<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor_name' => $this->doctor_name,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'total_amount' => $this->total_amount,
            'created_at' => $this->created_at?->toIso8601String(),

            'mr' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'employee_code' => $this->user->employee_code,
            ]),

            'visit' => $this->whenLoaded('visit', fn () => [
                'id' => $this->visit->id,
                'doctor_name' => $this->visit->doctor_name,
                'date' => $this->visit->date?->toDateString(),
            ]),

            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? null,
                    'quantity' => $item->quantity,
                ]);
            }),

            'status_history' => $this->whenLoaded('statusHistories', function () {
                return $this->statusHistories->map(fn ($h) => [
                    'status' => $h->status,
                    'changed_by' => $h->changedBy->name ?? null,
                    'changed_at' => $h->created_at?->toIso8601String(),
                ]);
            }),
        ];
    }
}
