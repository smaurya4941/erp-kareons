<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorVisitSample extends Model
{
    protected $fillable = [
        'doctor_visit_id',
        'product_id',
        'quantity',
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
