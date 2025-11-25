<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActivityLogController extends Controller
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
        
        // Apply permission middleware
        $this->middleware(['permission:activity log index,admin'])->only(['index', 'show', 'settings', 'updateSettings']);
        $this->middleware(['permission:activity log restore,admin'])->only(['restore', 'deletedItems']);
        $this->middleware(['permission:activity log export,admin'])->only(['export']);
    }

    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::query();
        
        // Filter by user type (admin or user) - this is the key distinction
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        } else {
            // Default to showing admin activities only
            $query->where('user_type', 'admin');
        }
        
        // Filters
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
            if ($request->filled('user_type')) {
                $query->where('user_type', $request->user_type);
            }
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%");
            });
        }
        
        // Get logs first
        $logsQuery = $query->orderBy('created_at', 'desc');
        $totalLogs = $logsQuery->count();
        
        // Get paginated logs
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 50;
        $logs = $logsQuery->skip(($currentPage - 1) * $perPage)->take($perPage)->get();
        
        // Separate admin and user IDs for efficient loading
        $adminIds = $logs->where('user_type', 'admin')->pluck('user_id')->filter()->unique();
        $userIds = $logs->where('user_type', 'user')->pluck('user_id')->filter()->unique();
        
        // Load admins with roles
        $admins = collect();
        if ($adminIds->isNotEmpty()) {
            $admins = \App\Models\Admin::with('roles')->whereIn('id', $adminIds)->get()->keyBy('id');
        }
        
        // Load users
        $users = collect();
        if ($userIds->isNotEmpty()) {
            $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
        }
        
        // Set the correct user relationship for each log
        foreach ($logs as $log) {
            if ($log->user_type === 'admin' && $log->user_id) {
                $log->setRelation('user', $admins->get($log->user_id));
            } elseif ($log->user_type === 'user' && $log->user_id) {
                $log->setRelation('user', $users->get($log->user_id));
            }
            
            // Load createdBy, updatedBy, approvedBy relationships
            if ($log->created_by && $log->created_by_type === 'admin') {
                $log->setRelation('createdBy', $admins->get($log->created_by));
            } elseif ($log->created_by && $log->created_by_type === 'user') {
                $log->setRelation('createdBy', $users->get($log->created_by));
            }
            
            if ($log->updated_by && $log->updated_by_type === 'admin') {
                $log->setRelation('updatedBy', $admins->get($log->updated_by));
            } elseif ($log->updated_by && $log->updated_by_type === 'user') {
                $log->setRelation('updatedBy', $users->get($log->updated_by));
            }
            
            if ($log->approve_by && $log->approve_by_type === 'admin') {
                $log->setRelation('approvedBy', $admins->get($log->approve_by));
            } elseif ($log->approve_by && $log->approve_by_type === 'user') {
                $log->setRelation('approvedBy', $users->get($log->approve_by));
            }
        }
        
        // Convert to paginator
        $logs = new \Illuminate\Pagination\LengthAwarePaginator($logs, $totalLogs, $perPage, $currentPage, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
        
        // Get statistics for both admin and user activities
        $userType = $request->input('user_type', 'admin');
        $statistics = $this->activityLogService->getStatistics(30, $userType);
        $adminStats = $this->activityLogService->getStatistics(30, 'admin');
        $userStats = $this->activityLogService->getStatistics(30, 'user');
        
        // Get available model types for filter
        $modelTypes = ActivityLog::select('model_type')
            ->distinct()
            ->orderBy('model_type')
            ->pluck('model_type')
            ->map(function($type) {
                return class_basename($type);
            })
            ->unique()
            ->values();
        
        return view('admin.activity-log.index', compact('logs', 'statistics', 'modelTypes', 'adminStats', 'userStats'));
    }

    /**
     * Display the specified activity log
     */
    public function show($id)
    {
        $log = ActivityLog::with(['user', 'createdBy', 'updatedBy', 'approvedBy'])->findOrFail($id);
        
        // Manually load roles for admin users
        if ($log->user && $log->user_type === 'admin' && method_exists($log->user, 'getRoleNames')) {
            $log->user->load('roles');
        }
        if ($log->createdBy && $log->created_by_type === 'admin' && method_exists($log->createdBy, 'getRoleNames')) {
            $log->createdBy->load('roles');
        }
        if ($log->updatedBy && $log->updated_by_type === 'admin' && method_exists($log->updatedBy, 'getRoleNames')) {
            $log->updatedBy->load('roles');
        }
        if ($log->approvedBy && $log->approve_by_type === 'admin' && method_exists($log->approvedBy, 'getRoleNames')) {
            $log->approvedBy->load('roles');
        }
        
        return view('admin.activity-log.show', compact('log'));
    }

    /**
     * Show deleted items that can be restored
     */
    public function deleted(Request $request)
    {
        $modelType = $request->input('model_type');
        
        $deletedItems = $this->activityLogService->getDeletedItems($modelType);
        
        // Get available model types for filter
        $modelTypes = ActivityLog::where('action', 'deleted')
            ->select('model_type')
            ->distinct()
            ->orderBy('model_type')
            ->pluck('model_type')
            ->map(function($type) {
                return class_basename($type);
            })
            ->unique()
            ->values();
        
        return view('admin.activity-log.deleted', compact('deletedItems', 'modelTypes', 'modelType'));
    }

    /**
     * Restore a deleted item
     */
    public function restore($id)
    {
        try {
            $this->activityLogService->restoreDeletedModel($id);
            
            return redirect()->back()
                ->with('success', 'Item restored successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to restore item: ' . $e->getMessage());
        }
    }

    /**
     * Get activity for a specific model
     */
    public function modelActivity(Request $request, string $modelType, int $modelId)
    {
        $logs = $this->activityLogService->getLogsForModel($modelType, $modelId);
        
        return view('admin.activity-log.model-activity', compact('logs', 'modelType', 'modelId'));
    }

    /**
     * Get activity for a specific user
     */
    public function userActivity(Request $request, int $userId, string $userType = 'admin')
    {
        $logs = $this->activityLogService->getUserActivity($userId, $userType);
        
        return view('admin.activity-log.user-activity', compact('logs', 'userId', 'userType'));
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        // TODO: Implement CSV/Excel export
        return response()->json(['message' => 'Export feature coming soon']);
    }

    /**
     * Display activity log settings page
     */
    public function settings()
    {
        $settings = \App\Models\Setting::whereIn('key', [
            'activity_log_whois_enabled',
            'activity_log_track_admin',
            'activity_log_track_frontend'
        ])->pluck('value', 'key')->toArray();

        return view('admin.activity-log.settings', compact('settings'));
    }

    /**
     * Update activity log settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'activity_log_whois_enabled' => ['nullable', 'boolean'],
            'activity_log_track_admin' => ['nullable', 'boolean'],
            'activity_log_track_frontend' => ['nullable', 'boolean'],
        ]);

        \App\Models\Setting::updateOrCreate(
            ['key' => 'activity_log_whois_enabled'],
            ['value' => $request->has('activity_log_whois_enabled') ? '1' : '0']
        );

        \App\Models\Setting::updateOrCreate(
            ['key' => 'activity_log_track_admin'],
            ['value' => $request->has('activity_log_track_admin') ? '1' : '0']
        );

        \App\Models\Setting::updateOrCreate(
            ['key' => 'activity_log_track_frontend'],
            ['value' => $request->has('activity_log_track_frontend') ? '1' : '0']
        );

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.activity-log.settings');
    }
}
