<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date?->toDateString(),
            'status' => $this->status,
            'today_summary' => $this->today_summary,
            'problems_faced' => $this->problems_faced,
            'tomorrow_plan' => $this->tomorrow_plan,
            'stats' => $this->stats_snapshot,
            'created_at' => $this->created_at?->toIso8601String(),

            'mr' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'employee_code' => $this->user->employee_code,
            ]),
        ];
    }
}
