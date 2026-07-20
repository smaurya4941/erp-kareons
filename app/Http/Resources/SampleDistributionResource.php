<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SampleDistributionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor_visit_id' => $this->doctor_visit_id,
            'product_id' => $this->product_id,
            'product_name' => $this->product->name ?? null,
            'quantity' => $this->quantity,
            'distributed_at' => $this->created_at?->toIso8601String(),
            'visit' => $this->whenLoaded('visit', fn () => [
                'id' => $this->visit->id,
                'doctor_name' => $this->visit->doctor_name,
                'date' => $this->visit->date?->toDateString(),
            ]),
        ];
    }
}
