<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Admin extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'user_id',
        'phonenumber',
        'gender',
    ];

    /**
     * Get the user that owns the admin.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}