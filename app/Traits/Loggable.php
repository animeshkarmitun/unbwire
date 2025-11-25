<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;

trait Loggable
{
    /**
     * Boot the trait and set up model event listeners
     */
    public static function bootLoggable()
    {
        static::creating(function ($model) {
            // Set created_by when creating
            if (in_array('created_by', $model->getFillable()) && !$model->created_by) {
                $user = Auth::guard('admin')->user() ?? Auth::user();
                if ($user) {
                    $model->created_by = $user->id;
                    $model->created_by_type = $user instanceof \App\Models\Admin ? 'admin' : 'user';
                }
            }
        });

        static::created(function ($model) {
            // Check if tracking is enabled for this user type
            $user = Auth::guard('admin')->user() ?? Auth::user();
            if (!$user) {
                return;
            }
            
            $userType = $user instanceof \App\Models\Admin ? 'admin' : 'user';
            $trackAdmin = \App\Models\Setting::where('key', 'activity_log_track_admin')->value('value') ?? '1';
            $trackFrontend = \App\Models\Setting::where('key', 'activity_log_track_frontend')->value('value') ?? '1';
            
            // Skip logging if disabled for this user type
            if ($userType === 'admin' && $trackAdmin !== '1') {
                return;
            }
            if ($userType === 'user' && $trackFrontend !== '1') {
                return;
            }
            
            $model->logActivity('created', null, $model->getAttributes());
        });

        static::updating(function ($model) {
            // Set updated_by when updating
            if (in_array('updated_by', $model->getFillable())) {
                $user = Auth::guard('admin')->user() ?? Auth::user();
                if ($user) {
                    $model->updated_by = $user->id;
                    $model->updated_by_type = $user instanceof \App\Models\Admin ? 'admin' : 'user';
                }
            }
            
            // Set approve_by when is_approved changes to 1
            if (in_array('approve_by', $model->getFillable()) && 
                $model->isDirty('is_approved') && 
                $model->is_approved == 1) {
                $user = Auth::guard('admin')->user() ?? Auth::user();
                if ($user) {
                    $model->approve_by = $user->id;
                    $model->approve_by_type = $user instanceof \App\Models\Admin ? 'admin' : 'user';
                }
            }
        });

        static::updated(function ($model) {
            // Check if tracking is enabled for this user type
            $user = Auth::guard('admin')->user() ?? Auth::user();
            if (!$user) {
                return;
            }
            
            $userType = $user instanceof \App\Models\Admin ? 'admin' : 'user';
            $trackAdmin = \App\Models\Setting::where('key', 'activity_log_track_admin')->value('value') ?? '1';
            $trackFrontend = \App\Models\Setting::where('key', 'activity_log_track_frontend')->value('value') ?? '1';
            
            // Skip logging if disabled for this user type
            if ($userType === 'admin' && $trackAdmin !== '1') {
                return;
            }
            if ($userType === 'user' && $trackFrontend !== '1') {
                return;
            }
            
            $oldValues = $model->getOriginal();
            $newValues = $model->getAttributes();
            $changes = $model->getChanges();
            
            $model->logActivity('updated', $oldValues, $newValues, $changes);
        });

        static::deleted(function ($model) {
            // Check if tracking is enabled for this user type
            $user = Auth::guard('admin')->user() ?? Auth::user();
            if (!$user) {
                return;
            }
            
            $userType = $user instanceof \App\Models\Admin ? 'admin' : 'user';
            $trackAdmin = \App\Models\Setting::where('key', 'activity_log_track_admin')->value('value') ?? '1';
            $trackFrontend = \App\Models\Setting::where('key', 'activity_log_track_frontend')->value('value') ?? '1';
            
            // Skip logging if disabled for this user type
            if ($userType === 'admin' && $trackAdmin !== '1') {
                return;
            }
            if ($userType === 'user' && $trackFrontend !== '1') {
                return;
            }
            
            // Get all attributes before deletion
            $attributes = $model->getAttributes();
            
            // If soft delete, get original attributes
            if (method_exists($model, 'getOriginal')) {
                $attributes = array_merge($model->getOriginal(), $attributes);
            }
            
            $model->logActivity('deleted', $attributes, null);
        });

        // Handle soft delete restore
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                // Check if tracking is enabled for this user type
                $user = Auth::guard('admin')->user() ?? Auth::user();
                if (!$user) {
                    return;
                }
                
                $userType = $user instanceof \App\Models\Admin ? 'admin' : 'user';
                $trackAdmin = \App\Models\Setting::where('key', 'activity_log_track_admin')->value('value') ?? '1';
                $trackFrontend = \App\Models\Setting::where('key', 'activity_log_track_frontend')->value('value') ?? '1';
                
                // Skip logging if disabled for this user type
                if ($userType === 'admin' && $trackAdmin !== '1') {
                    return;
                }
                if ($userType === 'user' && $trackFrontend !== '1') {
                    return;
                }
                
                $model->logActivity('restored', null, $model->getAttributes());
            });
        }
    }

    /**
     * Log an activity for this model
     */
    public function logActivity(
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $changes = null
    ): ActivityLog {
        $service = app(ActivityLogService::class);
        
        return $service->log(
            $this,
            $action,
            $oldValues,
            $newValues,
            $changes
        );
    }

    /**
     * Get all activity logs for this model
     */
    public function activityLogs()
    {
        return ActivityLog::where('model_type', static::class)
            ->where('model_id', $this->id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest activity log
     */
    public function latestActivity()
    {
        return $this->activityLogs()->first();
    }

    /**
     * Restore this model from a deleted activity log
     */
    public function restoreFromLog(int $activityLogId): bool
    {
        $service = app(ActivityLogService::class);
        return $service->restoreDeletedModel($activityLogId);
    }
}

