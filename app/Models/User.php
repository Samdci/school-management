<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phonenumber',
        'gender',
        'guardian_fullname',
        'guardian_relationship',
        'guardian_phonenumber',
        'home_county',
        'kcpe_marks',
        'cert_copy',
        'student_class_id',
        'class_name',
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
}
