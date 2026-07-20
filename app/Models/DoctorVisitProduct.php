<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorVisitProduct extends Model
{
    protected $fillable = [
        'doctor_visit_id',
        'product_id',
        'interest_level',
        'remarks',
    ];

    public function visit()
    {
        return $this->belongsTo(DoctorVisit::class, 'doctor_visit_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
