<?php

namespace App\Traits;

use App\Helpers\ActivityLogger;

trait LogsActivity
{
    /**
     * Boot the trait to listen for model events.
     */
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $properties = [
                'attributes' => $model->getAttributes()
            ];
            
            ActivityLogger::log(
                module: class_basename($model),
                action: 'Created',
                description: 'Created ' . class_basename($model) . ' #' . $model->getKey(),
                subject: $model,
                properties: $properties
            );
        });

        static::updated(function ($model) {
            // Get changed attributes
            $dirty = $model->getDirty();
            $original = array_intersect_key($model->getOriginal(), $dirty);
            
            // Don't log if no meaningful changes
            if (empty($dirty)) return;

            $properties = [
                'old' => $original,
                'new' => $dirty
            ];

            ActivityLogger::log(
                module: class_basename($model),
                action: 'Updated',
                description: 'Updated ' . class_basename($model) . ' #' . $model->getKey(),
                subject: $model,
                properties: $properties
            );
        });

        static::deleted(function ($model) {
            $properties = [
                'attributes' => $model->getAttributes()
            ];

            ActivityLogger::log(
                module: class_basename($model),
                action: 'Deleted',
                description: 'Deleted ' . class_basename($model) . ' #' . $model->getKey(),
                subject: $model,
                properties: $properties,
                severity: 'Warning'
            );
        });
    }
}
