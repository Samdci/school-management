<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClasses extends Model
{
    use HasFactory;

    protected $table = 'student_classes';

    protected $fillable = [
        'class_name',
        'category',
    ];
}
