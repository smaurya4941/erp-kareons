<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'today_summary',
        'problems_faced',
        'tomorrow_plan',
        'status',
        'stats_snapshot',
    ];

    protected $casts = [
        'date' => 'date',
        'stats_snapshot' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
