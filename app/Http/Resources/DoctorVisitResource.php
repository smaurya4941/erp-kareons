<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorVisitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'employee_code' => $this->user->employee_code,
            ],
            'date' => $this->date ? $this->date->toDateString() : null,
            'time' => $this->time,
            
            // Location
            'location' => [
                'lat' => $this->lat,
                'lng' => $this->lng,
                'accuracy' => $this->accuracy,
                'address' => $this->address,
            ],

            // Doctor Details
            'doctor' => [
                'name' => $this->doctor_name,
                'clinic_name' => $this->clinic_name,
                'specialization' => $this->specialization,
                'phone' => $this->phone,
                'area' => $this->area,
                'address' => $this->doctor_address,
            ],

            // Discussion
            'discussion' => [
                'summary' => $this->discussion_summary,
                'doctor_response' => $this->doctor_response,
                'competitor_medicines' => $this->competitor_medicines,
                'remarks' => $this->remarks,
            ],

            'status' => $this->status,

            // Relations
            'discussed_products' => $this->whenLoaded('discussedProducts', function () {
                return $this->discussedProducts->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? '',
                        'interest_level' => $item->interest_level,
                        'remarks' => $item->remarks,
                    ];
                });
            }),

            'samples_distributed' => $this->whenLoaded('distributedSamples', function () {
                return $this->distributedSamples->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? '',
                        'quantity' => $item->quantity,
                    ];
                });
            }),

            'order' => $this->whenLoaded('order', function () {
                return [
                    'id' => $this->order->id,
                    'status' => $this->order->status,
                    'items' => $this->order->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name ?? '',
                            'quantity' => $item->quantity,
                        ];
                    }),
                ];
            }),
        ];
    }
}
