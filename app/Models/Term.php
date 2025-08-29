<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Term extends Model
{
    use HasFactory, Auditable;
    
    protected $fillable = [
        'term_name',
        'term_year',
        'start_date',
        'end_date',
        'status',
    ];
}
