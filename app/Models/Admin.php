<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

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