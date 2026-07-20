<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'employee_code' => $this->employee_code,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'photo_url' => $this->photo ? url('storage/' . $this->photo) : null,
            'address' => $this->address,
            'joining_date' => $this->joining_date,
            'status' => (bool)$this->status,
            'role' => $this->roles->first()?->name ?? 'MR',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
