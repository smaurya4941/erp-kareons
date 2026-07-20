<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDiscussionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor_visit_id' => $this->doctor_visit_id,
            'product_id' => $this->product_id,
            'product_name' => $this->product->name ?? null,
            'interest_level' => $this->interest_level,
            'remarks' => $this->remarks,
        ];
    }
}
