<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event',
        'auditable_type',
        'auditable_id',
        'url',
        'ip_address',
        'user_id',
        'user_type',
        'user_agent',
        'old_values',
        'new_values',
        'tags',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that triggered the audit event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'System',
            'email' => 'system@example.com',
        ]);
    }

    /**
     * Get the auditable model that the audit belongs to.
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include logs for a specific model.
     */
    public function scopeForModel($query, $model)
    {
        return $query->where('auditable_type', get_class($model))
                    ->where('auditable_id', $model->id);
    }

    /**
     * Scope a query to only include logs for a specific event.
     */
    public function scopeForEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope a query to only include logs for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the diff between old and new values.
     */
    public function getDiffAttribute(): array
    {
        if (empty($this->old_values) || empty($this->new_values)) {
            return [];
        }

        $diff = [];
        $keys = array_unique(array_merge(
            array_keys($this->old_values ?? []),
            array_keys($this->new_values ?? [])
        ));

        foreach ($keys as $key) {
            $old = $this->old_values[$key] ?? null;
            $new = $this->new_values[$key] ?? null;
            
            if ($old !== $new) {
                $diff[$key] = [
                    'old' => $old,
                    'new' => $new,
                ];
            }
        }

        return $diff;
    }
}
