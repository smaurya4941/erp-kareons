<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'assigned_quantity',
        'distributed_quantity',
    ];

    /**
     * Get the remaining quantity (calculated dynamically).
     */
    public function getRemainingQuantityAttribute(): int
    {
        return $this->assigned_quantity - $this->distributed_quantity;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
