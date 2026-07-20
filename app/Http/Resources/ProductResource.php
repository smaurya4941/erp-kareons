<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_code' => $this->product_code,
            'name' => $this->name,
            'category' => $this->category,
            'strength' => $this->strength,
            'pack_size' => $this->pack_size,
            'image_url' => $this->image ? url('storage/' . $this->image) : null,
            'description' => $this->description,
            'status' => (bool)$this->status,
            'brand' => $this->brand,
            'manufacturer' => $this->manufacturer,
            'hsn_code' => $this->hsn_code,
            'mrp' => $this->mrp,
            'gst' => $this->gst,
            'barcode' => $this->barcode,
            'expiry_required' => (bool)$this->expiry_required,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
