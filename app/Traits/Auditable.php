<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Auditable Trait
 * 
 * This trait can be added to any model that should be audited.
 * It will automatically log all model events (created, updated, deleted, etc.)
 * to the audit_logs table.
 */
trait Auditable
{
    /**
     * Boot the Auditable trait for the model.
     */
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logAuditEvent('created');
        });

        static::updated(function ($model) {
            $model->logAuditEvent('updated');
        });

        static::deleted(function ($model) {
            $model->logAuditEvent('deleted');
        });

        // Only register the restored event if the model uses SoftDeletes
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->logAuditEvent('restored');
            });
        }

        static::retrieved(function ($model) {
            // For tracking view events if needed
            // $model->logAuditEvent('viewed');
        });
    }

    /**
     * Log an audit event for the model.
     *
     * @param string $event
     * @param array $oldValues
     * @param array $newValues
     * @param string|null $description
     * @param string|null $tags
     * @return \App\Models\AuditLog
     */
    public function logAuditEvent(
        string $event,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
        ?string $tags = null
    ) {
        // Don't log if the model has the $withoutAuditing property set to true
        if (property_exists($this, 'withoutAuditing') && $this->withoutAuditing === true) {
            return null;
        }

        // Get the authenticated user
        $user = Auth::user();
        
        // Prepare the data to log
        $data = [
            'event' => $event,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'url' => Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_id' => $user ? $user->id : null,
            'user_type' => $user ? get_class($user) : null,
            'user_agent' => Request::userAgent(),
            'old_values' => $oldValues ?? ($event === 'updated' || $event === 'deleted' ? $this->getOriginal() : null),
            'new_values' => $newValues ?? ($event === 'created' || $event === 'updated' ? $this->getDirty() : null),
            'description' => $description ?? $this->getAuditDescription($event),
            'tags' => $tags,
        ];

        // Create the audit log
        return AuditLog::create($data);
    }

    /**
     * Get the description for the audit event.
     *
     * @param string $event
     * @return string
     */
    protected function getAuditDescription(string $event): string
    {
        $modelName = class_basename($this);
        $key = $this->getKey();
        
        switch ($event) {
            case 'created':
                return "Created {$modelName} #{$key}";
            case 'updated':
                return "Updated {$modelName} #{$key}";
            case 'deleted':
                return "Deleted {$modelName} #{$key}";
            case 'restored':
                return "Restored {$modelName} #{$key}";
            case 'viewed':
                return "Viewed {$modelName} #{$key}";
            default:
                return "{$event} {$modelName} #{$key}";
        }
    }

    /**
     * Get all audit logs for the model.
     */
    public function auditLogs()
    {
        return $this->morphMany(\App\Models\AuditLog::class, 'auditable')->latest();
    }

    /**
     * Get the latest audit log for the model.
     */
    public function latestAuditLog()
    {
        return $this->morphOne(\App\Models\AuditLog::class, 'auditable')->latestOfMany();
    }

    /**
     * Get the user who last modified the model.
     */
    public function lastModifiedBy()
    {
        return $this->latestAuditLog ? $this->latestAuditLog->user : null;
    }
}
