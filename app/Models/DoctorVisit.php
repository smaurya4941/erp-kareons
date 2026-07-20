<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class DoctorVisit extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'date',
        'time',
        'lat',
        'lng',
        'accuracy',
        'address',
        'doctor_name',
        'clinic_name',
        'specialization',
        'phone',
        'area',
        'doctor_address',
        'discussion_summary',
        'doctor_response',
        'competitor_medicines',
        'remarks',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function discussedProducts()
    {
        return $this->hasMany(DoctorVisitProduct::class, 'doctor_visit_id');
    }

    public function distributedSamples()
    {
        return $this->hasMany(DoctorVisitSample::class, 'doctor_visit_id');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'doctor_visit_id');
    }
}
