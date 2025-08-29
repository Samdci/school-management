<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Teacher extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'user_id',
        'gender',
        'phonenumber',
        'student_class_id',
        'homecounty',
    ];

    /**
     * Get the user that owns the teacher.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * The courses that belong to the teacher.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'teacher_courses')
            ->using(TeacherCourse::class)
            ->withTimestamps();
    }

    /**
     * Get the class that the teacher belongs to.
     */
    public function studentClass()
    {
        return $this->belongsTo(StudentClasses::class, 'student_class_id');
    }

}