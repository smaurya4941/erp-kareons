<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'product_code',
        'name',
        'category',
        'strength',
        'pack_size',
        'image',
        'description',
        'status',
        'brand',
        'manufacturer',
        'hsn_code',
        'mrp',
        'gst',
        'barcode',
        'expiry_required',
    ];

    protected $casts = [
        'status' => 'boolean',
        'expiry_required' => 'boolean',
        'mrp' => 'decimal:2',
        'gst' => 'decimal:2',
    ];
}
