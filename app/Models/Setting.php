<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'updated_by',
    ];

    /**
     * Get the properly casted value based on type.
     */
    public function getCastedValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $this->value;
            case 'json':
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    /**
     * Clear settings cache when a setting is updated/saved.
     */
    protected static function booted()
    {
        static::saved(function ($setting) {
            Cache::forget('app_settings');
        });
        
        static::deleted(function ($setting) {
            Cache::forget('app_settings');
        });
    }
}
