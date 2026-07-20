<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SampleTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'product_code' => $this->product->product_code,
            ],
            'type' => $this->type,
            'quantity' => $this->quantity, // positive or negative
            'reason' => $this->reason,
            'performed_by' => $this->performer ? $this->performer->name : 'System',
            'created_at' => $this->created_at,
        ];
    }
}
