<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'phonenumber',
        'student_class_id',
        'homecounty',
        'course_id',
    ];

    /**
     * Get the user that owns the teacher.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class that the teacher belongs to.
     */
    public function studentClass()
    {
        return $this->belongsTo(StudentClasses::class, 'student_class_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}