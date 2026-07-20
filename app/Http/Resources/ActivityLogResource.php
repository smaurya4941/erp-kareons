<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'module' => $this->module,
            'action' => $this->action,
            'description' => $this->description,
            'status' => $this->status,
            'severity' => $this->severity,
            'properties' => $this->properties,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'created_at' => $this->created_at?->toIso8601String(),

            'user' => $this->whenLoaded('user', fn () => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'employee_code' => $this->user->employee_code,
            ] : null),

            'subject' => $this->whenLoaded('subject', fn () => $this->subject ? [
                'type' => class_basename($this->subject_type),
                'id' => $this->subject_id,
            ] : null),
        ];
    }
}
