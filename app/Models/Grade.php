<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Grade extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'exam_id',
        'course_id',
        'marks',
        'grade',
        'remarks',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
