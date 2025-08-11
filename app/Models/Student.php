<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'gender',
        'student_class_id',
        'guardian_fullname',
        'guardian_relationship',
        'guardian_phonenumber',
        'guardian_email',
        'home_county',
        'kcpe_marks',
        'cert_number',
    ];

    /**
     * Get the class that the student belongs to.
     */
    public function studentClass()
    {
        return $this->belongsTo(StudentClasses::class, 'student_class_id');
    }
    /**
     * The parents that belong to the student.
     */
    public function parents()
    {
        return $this->belongsToMany(ParentModel::class, 'parent_student', 'student_id', 'parent_id');
    }
}