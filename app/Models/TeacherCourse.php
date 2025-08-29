<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Auditable;

class TeacherCourse extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'teacher_id',
        'course_id',
        'is_primary'  // For head of department designation
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];
    
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
    
    
    /**
     * Scope a query to only include department heads.
     */
    public function scopeDepartmentHeads($query)
    {
        return $query->where('is_primary', true);
    }

}
