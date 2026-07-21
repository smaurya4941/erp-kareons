<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\LogsActivity;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_code',
        'name',
        'email',
        'password',
        'photo',
        'profile_photo_path',
        'mobile',
        'phone',
        'gender',
        'dob',
        'address',
        'joining_date',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'joining_date' => 'date',
            'dob' => 'date',
        ];
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function doctorVisits()
    {
        return $this->hasMany(DoctorVisit::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }

    public function sampleAssignments()
    {
        return $this->hasMany(SampleAssignment::class);
    }

    /**
     * Check if the user is currently on duty (clocked in today without clocking out).
     */
    public function isOnDuty(): bool
    {
        return $this->attendances()
            ->whereDate('date', now()->toDateString())
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->exists();
    }
}
