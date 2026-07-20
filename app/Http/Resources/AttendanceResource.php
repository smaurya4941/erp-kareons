<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'date' => $this->date->toDateString(),
            'status' => $this->status,
            'is_late' => $this->is_late,
            'check_in' => [
                'time' => $this->check_in_time ? $this->check_in_time->format('Y-m-d H:i:s') : null,
                'selfie_url' => $this->check_in_selfie ? url('storage/' . $this->check_in_selfie) : null,
                'lat' => $this->check_in_lat,
                'lng' => $this->check_in_lng,
                'accuracy' => $this->check_in_accuracy,
                'address' => $this->check_in_address,
            ],
            'check_out' => [
                'time' => $this->check_out_time ? $this->check_out_time->format('Y-m-d H:i:s') : null,
                'selfie_url' => $this->check_out_selfie ? url('storage/' . $this->check_out_selfie) : null,
                'lat' => $this->check_out_lat,
                'lng' => $this->check_out_lng,
                'accuracy' => $this->check_out_accuracy,
                'address' => $this->check_out_address,
            ],
            'working_minutes' => $this->working_minutes,
            'working_hours_formatted' => $this->formatted_working_hours,
        ];
    }
}
