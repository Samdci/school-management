<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ParentModel extends Model
{
    use HasFactory, Auditable;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'phonenumber',
        'homecounty',
        'course_id'
    ];

    /**
     * Get the user that owns the parent.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    /**
     * The students that belong to the parent.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id');
    }
}