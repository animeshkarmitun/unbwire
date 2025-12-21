<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivityLogService
{
    /**
     * Log an activity
     */
    public function log(
        Model $model,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $changes = null
    ): ActivityLog {
        $user = Auth::guard('admin')->user() ?? Auth::user();
        
        $description = $this->generateDescription($model, $action, $oldValues, $newValues);
        
        // Clean sensitive data
        $oldValues = $this->cleanSensitiveData($oldValues);
        $newValues = $this->cleanSensitiveData($newValues);
        $changes = $this->cleanSensitiveData($changes);
        
        $userType = $user instanceof \App\Models\Admin ? 'admin' : 'user';
        
        // Determine created_by, updated_by, approve_by based on action
        $createdBy = null;
        $createdByType = null;
        $updatedBy = null;
        $updatedByType = null;
        $approveBy = null;
        $approveByType = null;
        
        if ($action === 'created') {
            $createdBy = $user?->id;
            $createdByType = $userType;
        } elseif ($action === 'updated') {
            $updatedBy = $user?->id;
            $updatedByType = $userType;
            
            // Check if model has created_by field and use it
            if (isset($model->created_by)) {
                $createdBy = $model->created_by;
                $createdByType = $model->created_by_type ?? 'admin';
            } elseif (isset($oldValues['created_by'])) {
                $createdBy = $oldValues['created_by'];
                $createdByType = $oldValues['created_by_type'] ?? 'admin';
            }
            
            // Check if this update includes approval
            if (isset($changes['is_approved']) && $changes['is_approved'] == 1) {
                $approveBy = $user?->id;
                $approveByType = $userType;
            } elseif (isset($newValues['is_approved']) && $newValues['is_approved'] == 1) {
                $approveBy = $user?->id;
                $approveByType = $userType;
            }
        } elseif ($action === 'approved' || (isset($newValues['is_approved']) && $newValues['is_approved'] == 1)) {
            $approveBy = $user?->id;
            $approveByType = $userType;
        }
        
        return ActivityLog::create([
            'user_id' => $user?->id,
            'user_type' => $userType,
            'created_by' => $createdBy,
            'created_by_type' => $createdByType,
            'updated_by' => $updatedBy,
            'updated_by_type' => $updatedByType,
            'approve_by' => $approveBy,
            'approve_by_type' => $approveByType,
            'model_type' => get_class($model),
            'model_id' => $model->id ?? null,
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);
    }

    /**
     * Get logs for a specific model
     */
    public function getLogsForModel(string $modelType, ?int $modelId = null)
    {
        $query = ActivityLog::where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get activity for a specific user
     */
    public function getUserActivity(int $userId, string $userType = 'admin', int $limit = 50)
    {
        return ActivityLog::forUser($userId, $userType)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity(int $limit = 50)
    {
        return ActivityLog::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all deleted items
     */
    public function getDeletedItems(?string $modelType = null, int $limit = 100)
    {
        $query = ActivityLog::where('action', 'deleted')
            ->whereNotNull('old_values')
            ->orderBy('created_at', 'desc');
        
        if ($modelType) {
            $query->where('model_type', $modelType);
        }
        
        return $query->limit($limit)->get();
    }

    /**
     * Restore a deleted model from activity log
     */
    public function restoreDeletedModel(int $activityLogId): bool
    {
        try {
            DB::beginTransaction();
            
            $log = ActivityLog::findOrFail($activityLogId);
            
            if (!$log->canBeRestored()) {
                throw new \Exception('This log cannot be restored');
            }
            
            $modelClass = $log->model_type;
            $oldValues = $log->old_values;
            
            if (!class_exists($modelClass)) {
                throw new \Exception("Model class {$modelClass} does not exist");
            }
            
            // Check if model already exists (might have been restored already)
            $existingModel = $modelClass::find($log->model_id);
            
            if ($existingModel) {
                // Model exists, update it with old values
                $existingModel->fill($oldValues);
                $existingModel->save();
            } else {
                // Model doesn't exist, create it
                $model = new $modelClass();
                $model->fill($oldValues);
                
                // Handle timestamps
                if (isset($oldValues['created_at'])) {
                    $model->created_at = $oldValues['created_at'];
                }
                if (isset($oldValues['updated_at'])) {
                    $model->updated_at = $oldValues['updated_at'];
                }
                
                $model->save();
            }
            
            // Log the restoration
            if (isset($model)) {
                $model->logActivity('restored', null, $model->getAttributes());
            } elseif ($existingModel) {
                $existingModel->logActivity('restored', null, $existingModel->getAttributes());
            }
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to restore model from activity log', [
                'log_id' => $activityLogId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Generate human-readable description
     */
    protected function generateDescription(
        Model $model,
        string $action,
        ?array $oldValues,
        ?array $newValues
    ): string {
        $modelName = class_basename(get_class($model));
        $user = Auth::guard('admin')->user() ?? Auth::user();
        
        // Determine user type and name
        $isAdmin = $user instanceof \App\Models\Admin;
        
        if ($isAdmin && method_exists($user, 'getRoleNames')) {
            $roleName = $user->getRoleNames()->first();
            $userName = $roleName ?: $user->name;
            $userTypeLabel = '[Admin]';
        } else {
            $userName = $user ? $user->name : 'System';
            $userTypeLabel = $user ? '[User]' : '[System]';
        }
        
        $description = match($action) {
            'created' => "{$userTypeLabel} {$userName} created {$modelName}",
            'updated' => "{$userTypeLabel} {$userName} updated {$modelName}",
            'deleted' => "{$userTypeLabel} {$userName} deleted {$modelName}",
            'restored' => "{$userTypeLabel} {$userName} restored {$modelName}",
            'viewed' => "{$userTypeLabel} {$userName} viewed {$modelName}",
            'exported' => "{$userTypeLabel} {$userName} exported {$modelName}",
            'commented' => "{$userTypeLabel} {$userName} commented on {$modelName}",
            default => "{$userTypeLabel} {$userName} performed {$action} on {$modelName}",
        };
        
        // Add identifier if available
        if (isset($model->id)) {
            $identifier = $this->getModelIdentifier($model);
            if ($identifier) {
                $description .= " ({$identifier})";
            }
        }
        
        return $description;
    }

    /**
     * Get a human-readable identifier for the model
     */
    protected function getModelIdentifier(Model $model): ?string
    {
        // Try common identifier fields
        $identifierFields = ['title', 'name', 'email', 'slug', 'id'];
        
        foreach ($identifierFields as $field) {
            if (isset($model->$field)) {
                $value = $model->$field;
                if (is_string($value) && strlen($value) <= 100) {
                    return $value;
                }
            }
        }
        
        return "ID: {$model->id}";
    }

    /**
     * Remove sensitive data from arrays
     */
    protected function cleanSensitiveData(?array $data): ?array
    {
        if (!$data) {
            return null;
        }
        
        $sensitiveFields = ['password', 'password_confirmation', 'remember_token', 'api_token', 'secret'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***HIDDEN***';
            }
        }
        
        return $data;
    }

    /**
     * Get activity statistics
     */
    public function getStatistics(int $days = 30, ?string $userType = null): array
    {
        $startDate = now()->subDays($days);
        $query = ActivityLog::where('created_at', '>=', $startDate);
        
        if ($userType) {
            $query->where('user_type', $userType);
        }
        
        return [
            'total_activities' => (clone $query)->count(),
            'created' => (clone $query)->where('action', 'created')->count(),
            'updated' => (clone $query)->where('action', 'updated')->count(),
            'deleted' => (clone $query)->where('action', 'deleted')->count(),
            'viewed' => (clone $query)->where('action', 'viewed')->count(),
            'exported' => (clone $query)->where('action', 'exported')->count(),
            'commented' => (clone $query)->where('action', 'commented')->count(),
            'by_model' => (clone $query)
                ->select('model_type', DB::raw('COUNT(*) as count'))
                ->groupBy('model_type')
                ->orderBy('count', 'desc')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [class_basename($item->model_type) => $item->count];
                }),
        ];
    }
    
    /**
     * Get top viewed news with date filters
     */
    public function getTopViewedNews(?string $period = 'today', int $limit = 10): array
    {
        $query = ActivityLog::where('action', 'viewed')
            ->where('model_type', \App\Models\News::class);
        
        // Apply date filter
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
            default:
                // All time
                break;
        }
        
        return $query->select('model_id', DB::raw('COUNT(*) as view_count'))
            ->whereNotNull('model_id')
            ->groupBy('model_id')
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $news = \App\Models\News::find($item->model_id);
                return [
                    'id' => $item->model_id,
                    'title' => $news ? $news->title : 'Deleted News',
                    'slug' => $news ? $news->slug : null,
                    'views' => $item->view_count,
                ];
            })
            ->toArray();
    }
    
    /**
     * Get top exported news with date filters
     */
    public function getTopExportedNews(?string $period = 'today', int $limit = 10): array
    {
        $query = ActivityLog::where('action', 'exported')
            ->where('model_type', \App\Models\News::class);
        
        // Apply date filter
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
            default:
                // All time
                break;
        }
        
        return $query->select('model_id', DB::raw('COUNT(*) as export_count'))
            ->whereNotNull('model_id')
            ->groupBy('model_id')
            ->orderBy('export_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $news = \App\Models\News::find($item->model_id);
                return [
                    'id' => $item->model_id,
                    'title' => $news ? $news->title : 'Deleted News',
                    'slug' => $news ? $news->slug : null,
                    'exports' => $item->export_count,
                ];
            })
            ->toArray();
    }
}

