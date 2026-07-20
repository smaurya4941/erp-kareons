<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SettingController extends BaseApiController
{
    /**
     * Get company settings.
     */
    public function company(): JsonResponse
    {
        $settings = Cache::rememberForever('settings.company', function () {
            return Setting::where('group', 'company')->pluck('value', 'key')->toArray();
        });

        return $this->successResponse($settings, 'Company settings retrieved successfully');
    }

    /**
     * Update company settings.
     */
    public function updateCompany(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string',
            'company_email' => 'required|email',
            'company_phone' => 'required|string',
            'company_address' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => 'company',
                    'type' => 'string',
                    'updated_by' => auth()->user()->name
                ]
            );
        }

        Cache::forget('settings.company');
        
        \App\Helpers\ActivityLogger::log(
            module: 'Settings',
            action: 'Updated Settings via API',
            description: 'Admin updated Company settings via API.',
            severity: 'Important'
        );

        return $this->successResponse($validated, 'Company settings updated successfully');
    }
}
