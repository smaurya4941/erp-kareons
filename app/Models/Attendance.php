<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Attendance extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'date',
        'check_in_time',
        'check_in_selfie',
        'check_in_lat',
        'check_in_lng',
        'check_in_accuracy',
        'check_in_address',
        'check_in_device_info',
        'check_out_time',
        'check_out_selfie',
        'check_out_lat',
        'check_out_lng',
        'check_out_accuracy',
        'check_out_address',
        'check_out_device_info',
        'working_minutes',
        'status',
        'is_late',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'check_in_device_info' => 'array',
        'check_out_device_info' => 'array',
        'is_late' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get a human readable working hours string (e.g. "9h 30m")
     */
    public function getFormattedWorkingHoursAttribute(): string
    {
        if (!$this->working_minutes) {
            return '-';
        }
        
        $hours = floor($this->working_minutes / 60);
        $minutes = $this->working_minutes % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }
}
