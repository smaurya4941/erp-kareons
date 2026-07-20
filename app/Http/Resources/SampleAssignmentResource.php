<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SampleAssignmentResource extends JsonResource
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
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'product_code' => $this->product->product_code,
            ],
            'assigned_quantity' => $this->assigned_quantity,
            'distributed_quantity' => $this->distributed_quantity,
            'remaining_quantity' => $this->remaining_quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
