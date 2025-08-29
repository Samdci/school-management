<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Auditable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Auditable;

    /** 
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'username',
        'email_verified_at',
        'is_active',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'must_change_password'
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'must_change_password' => 'boolean',
    ];

    /**
     * Get the student class that owns the user.
     */
    public function studentClass()
    {
        return $this->belongsTo(StudentClasses::class, 'student_class_id');
    }

    /**
     * Get the class name attribute, ensuring it's always a string.
     */
    public function getClassNameAttribute($value)
    {
        // If the value is a JSON object, extract the class_name
        if (is_string($value) && (strpos($value, '{') === 0 || strpos($value, '[') === 0)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded) && isset($decoded['class_name'])) {
                return $decoded['class_name'];
            }
        }
        return $value;
    }
    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function parent()
    {
        return $this->hasOne(ParentModel::class, 'user_id');
    }
} 
