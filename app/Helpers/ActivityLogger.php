<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity manually or via the LogsActivity trait.
     */
    public static function log(
        string $module,
        string $action,
        string $description,
        Model $subject = null,
        array $properties = null,
        string $status = 'Success',
        string $severity = 'Information'
    ) {
        try {
            ActivityLog::create([
                'user_id' => auth()->id() ?? null,
                'module' => $module,
                'action' => $action,
                'description' => $description,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject ? $subject->getKey() : null,
                'properties' => $properties,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'status' => $status,
                'severity' => $severity,
            ]);
        } catch (\Exception $e) {
            // Rule 7: Log creation should never interrupt the user's workflow.
            \Illuminate\Support\Facades\Log::error('Failed to write activity log: ' . $e->getMessage());
        }
    }
}
