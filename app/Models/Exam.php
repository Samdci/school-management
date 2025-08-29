<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Exam extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'term_id',
    ];

    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
