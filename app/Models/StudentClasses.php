<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class StudentClasses extends Model
{
    use HasFactory, Auditable;

    protected $table = 'student_classes';

    protected $fillable = [
        'class_name',
        'category',
    ];
}
