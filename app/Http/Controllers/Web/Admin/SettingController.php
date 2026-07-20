<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Show the settings dashboard.
     */
    public function index()
    {
        // Load all settings grouped by their category
        $settings = Setting::all()->groupBy('group');
        
        // Helper to get a setting object if it exists
        $getSetting = function($key, $group, $type = 'string', $default = '') use ($settings) {
            if (isset($settings[$group])) {
                $setting = $settings[$group]->firstWhere('key', $key);
                if ($setting) return $setting->value;
            }
            return $default;
        };

        return view('admin.settings.index', compact('getSetting'));
    }

    /**
     * Store or update settings.
     */
    public function store(Request $request)
    {
        $group = $request->input('setting_group', 'general');
        $inputs = $request->except(['_token', '_method', 'setting_group', 'company_logo', 'favicon']);

        // Handle File Uploads for Company Group
        if ($group === 'company') {
            if ($request->hasFile('company_logo')) {
                $path = $request->file('company_logo')->store('settings', 'public');
                $inputs['company_logo'] = $path;
            }
            if ($request->hasFile('favicon')) {
                $path = $request->file('favicon')->store('settings', 'public');
                $inputs['favicon'] = $path;
            }
        }

        // Handle Checkboxes (they aren't sent if unchecked)
        if ($group === 'system') {
            $inputs['maintenance_mode'] = $request->has('maintenance_mode') ? 'true' : 'false';
            $inputs['email_notifications'] = $request->has('email_notifications') ? 'true' : 'false';
        }

        foreach ($inputs as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $group,
                    'type' => $this->determineType($value, $key),
                    'updated_by' => auth()->user()->name
                ]
            );
        }

        \App\Helpers\ActivityLogger::log(
            module: 'Settings',
            action: 'Updated Settings',
            description: 'Admin updated ' . ucfirst($group) . ' settings.',
            severity: 'Important'
        );

        return back()->with('success', ucfirst($group) . ' settings updated successfully.');
    }

    /**
     * Basic type determination for casting.
     */
    private function determineType($value, $key)
    {
        if ($value === 'true' || $value === 'false') return 'boolean';
        if (is_numeric($value) && strpos($key, 'timeout') !== false) return 'integer';
        return 'string';
    }
}
