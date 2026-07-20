<?php

namespace App\Http\Requests\DailyReport;

use Illuminate\Foundation\Http\FormRequest;

class SubmitDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('MR');
    }

    public function rules(): array
    {
        return [
            // Rule 4: Today's Summary is mandatory
            'today_summary' => ['required', 'string', 'max:5000'],
            // Problems faced is optional
            'problems_faced' => ['nullable', 'string', 'max:5000'],
            // Rule 5: Tomorrow's Plan is mandatory
            'tomorrow_plan' => ['required', 'string', 'max:5000'],
        ];
    }
}
