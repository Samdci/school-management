<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Course extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'course_name',
        'course_code',
        'category',
        'description',
    ];

    /**
     * The teachers that belong to the course.
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_courses')
            ->using(TeacherCourse::class)
            ->withTimestamps();
    }
}
