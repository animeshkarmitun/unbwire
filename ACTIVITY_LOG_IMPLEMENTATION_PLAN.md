# User Activity Log & Restore System Implementation Plan

## Overview
Comprehensive activity logging system that tracks all user actions (create, update, delete) with the ability to restore deleted items from logs.

## Architecture

### 1. Database Schema

#### `activity_logs` Table
- `id` (bigint, primary)
- `user_id` (foreign key, nullable) - Admin/User who performed action
- `user_type` (string) - 'admin' or 'user'
- `model_type` (string) - Full model class name (e.g., App\Models\News)
- `model_id` (bigint, nullable) - ID of the affected model
- `action` (enum: 'created', 'updated', 'deleted', 'restored', 'viewed')
- `description` (text) - Human-readable description
- `old_values` (json, nullable) - Previous values before update/delete
- `new_values` (json, nullable) - New values after create/update
- `changes` (json, nullable) - Only changed fields (for updates)
- `ip_address` (string, nullable)
- `user_agent` (text, nullable)
- `url` (string, nullable) - Request URL
- `method` (string, nullable) - HTTP method (GET, POST, etc.)
- `created_at`, `updated_at`

#### `activity_log_restores` Table (Optional - Track restore operations)
- `id` (bigint, primary)
- `activity_log_id` (foreign key)
- `restored_by` (foreign key, nullable)
- `restored_at` (timestamp)
- `restore_status` (enum: 'success', 'failed', 'partial')
- `restore_notes` (text, nullable)
- `created_at`, `updated_at`

### 2. Models

- `App\Models\ActivityLog`
- `App\Models\ActivityLogRestore` (optional)

### 3. Traits

**Loggable Trait**
- To be added to models that need activity logging
- Provides methods: `logActivity()`, `getActivityLogs()`, `restoreFromLog()`

### 4. Service Classes

**ActivityLogService**
- `log($model, $action, $oldValues = null, $newValues = null)`
- `getLogsForModel($modelType, $modelId)`
- `getUserActivity($userId, $userType)`
- `getRecentActivity($limit = 50)`
- `restoreDeletedModel($activityLogId)`
- `getDeletedItems($modelType = null)`

### 5. Event Listeners/Observers

**Model Observers** (for automatic logging)
- `Created` - Log creation
- `Updated` - Log updates with changes
- `Deleted` - Log deletion with full model data
- `Restored` - Log restoration

### 6. Middleware

**LogActivityMiddleware** (Optional)
- Logs page views and general activity
- Tracks navigation patterns

### 7. Controllers

**ActivityLogController** (Admin)
- `index()` - List all activity logs
- `show($id)` - View single log details
- `userActivity($userId)` - User-specific activity
- `modelActivity($modelType, $modelId)` - Model-specific activity
- `deletedItems()` - List all deleted items
- `restore($id)` - Restore deleted item
- `export()` - Export logs

### 8. Features

#### Logging Features
- Automatic logging on model events
- Manual logging support
- Batch operation logging
- Relationship changes tracking
- File upload/download tracking
- Login/logout tracking

#### Viewing Features
- Filterable log list (by user, model, action, date)
- Detailed log viewer
- Comparison view (old vs new values)
- Timeline view
- Search functionality

#### Restore Features
- Restore deleted items
- Preview before restore
- Partial restore (selected fields)
- Restore with relationships
- Restore history tracking
- Conflict resolution

### 9. Admin Interface

**Views:**
- `admin/activity-log/index.blade.php` - Main log list
- `admin/activity-log/show.blade.php` - Log details
- `admin/activity-log/deleted.blade.php` - Deleted items
- `admin/activity-log/restore.blade.php` - Restore interface

### 10. Permissions

- `activity log index` - View logs
- `activity log view` - View log details
- `activity log restore` - Restore deleted items
- `activity log export` - Export logs
- `activity log delete` - Delete logs (optional)

### 11. Performance Considerations

- Indexing on frequently queried columns
- Archiving old logs
- Caching recent activity
- Queue jobs for heavy operations
- Pagination for large datasets

### 12. Security

- IP address logging
- User agent tracking
- Request validation
- Permission checks
- Audit trail integrity

## Implementation Steps

1. ✅ Create database migrations
2. ✅ Create models with relationships
3. ✅ Create Loggable trait
4. ✅ Create ActivityLogService
5. ✅ Create model observers
6. ✅ Create ActivityLogController
7. ✅ Create admin views
8. ✅ Add routes
9. ✅ Add permissions
10. ✅ Add to sidebar
11. ✅ Test restore functionality
12. ✅ Add export feature

## Dependencies

- Laravel Events/Observers
- JSON columns for storing data
- Soft deletes (for restore capability)
- Queue system (optional, for async logging)


