# Activity Log System Setup Guide

## ✅ Implementation Complete

A comprehensive activity logging system has been implemented that tracks all user actions (create, update, delete) with the ability to restore deleted items.

## 📋 Setup Steps

### 1. Run Migrations

```bash
php artisan migrate
```

This creates the `activity_logs` table.

### 2. Add Permissions

```bash
php artisan activity-log:add-permissions
```

This creates the following permissions:
- `activity log index` - View activity logs
- `activity log view` - View log details
- `activity log restore` - Restore deleted items
- `activity log export` - Export logs

### 3. Add Loggable Trait to Models

To enable activity logging on a model, add the `Loggable` trait:

```php
<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use Loggable;
    
    // ... rest of your model
}
```

**Example: Adding to News Model**

```php
// app/Models/News.php
use App\Traits\Loggable;

class News extends Model
{
    use HasFactory, Loggable;
    // ... existing code
}
```

**Example: Adding to Category Model**

```php
// app/Models/Category.php
use App\Traits\Loggable;

class Category extends Model
{
    use Loggable;
    // ... existing code
}
```

## 🎯 Features

### Automatic Logging

Once the trait is added, the following actions are automatically logged:

1. **Created** - When a new record is created
2. **Updated** - When a record is updated (tracks changes)
3. **Deleted** - When a record is deleted (saves full data for restore)
4. **Restored** - When a soft-deleted record is restored

### What Gets Logged

- User who performed the action
- Model type and ID
- Action type (created, updated, deleted, restored)
- Old values (before update/delete)
- New values (after create/update)
- Changes (only modified fields for updates)
- IP address
- User agent
- Request URL and method
- Timestamp

### Restore Functionality

- View all deleted items
- Restore deleted items with full data
- Track restore operations
- Handle conflicts (if item already exists)

## 📊 Usage

### Access Activity Logs

1. Navigate to **Admin → Activity Logs** in the sidebar
2. View all activities with filtering options
3. Click on any log to see detailed information

### View Deleted Items

1. Go to **Activity Logs → Deleted Items**
2. Filter by model type
3. Click **Restore** to restore any deleted item

### Filter Logs

You can filter activity logs by:
- Action type (created, updated, deleted, restored)
- Model type
- Date range
- User
- Search term

## 🔧 Manual Logging

You can also manually log activities:

```php
use App\Services\ActivityLogService;

$service = app(ActivityLogService::class);

$service->log(
    $model,
    'custom_action',
    $oldValues,
    $newValues,
    $changes
);
```

Or using the model method:

```php
$news = News::find(1);
$news->logActivity('viewed', null, $news->getAttributes());
```

## 📝 Example: Adding to Multiple Models

To add logging to multiple models at once, update each model:

```php
// app/Models/News.php
use App\Traits\Loggable;
class News extends Model { use Loggable; }

// app/Models/Category.php
use App\Traits\Loggable;
class Category extends Model { use Loggable; }

// app/Models/Admin.php
use App\Traits\Loggable;
class Admin extends Model { use Loggable; }
```

## 🔒 Security

- Sensitive fields (password, tokens) are automatically hidden
- IP addresses and user agents are logged
- All actions are tied to authenticated users
- Permission-based access control

## 🚀 Performance

- Indexed columns for fast queries
- Pagination for large datasets
- Efficient JSON storage for data
- Caching for statistics

## 📈 Statistics

The dashboard shows:
- Total activities (last 30 days)
- Created count
- Updated count
- Deleted count
- Activity by model type

## 🔄 Restore Process

1. Find deleted item in "Deleted Items" page
2. Click "View" to see full details
3. Click "Restore" to restore the item
4. System will:
   - Check if item already exists
   - Create or update the item with original data
   - Log the restoration
   - Preserve original timestamps if available

## ⚠️ Important Notes

1. **Soft Deletes**: For best restore functionality, use Laravel's soft deletes on models
2. **Relationships**: Restore may not restore relationships automatically
3. **File Uploads**: File paths are restored, but files must still exist
4. **Permissions**: Only users with `activity log restore` permission can restore items

## 🐛 Troubleshooting

**Logs not appearing?**
- Check if model uses `Loggable` trait
- Verify user is authenticated
- Check database for activity_logs table

**Restore not working?**
- Verify old_values exist in log
- Check model class still exists
- Ensure user has restore permission
- Check application logs for errors

## 📚 API Methods

### ActivityLogService Methods

```php
// Get logs for a model
$service->getLogsForModel(News::class, $newsId);

// Get user activity
$service->getUserActivity($userId, 'admin');

// Get recent activity
$service->getRecentActivity(50);

// Get deleted items
$service->getDeletedItems(News::class);

// Restore deleted item
$service->restoreDeletedModel($logId);

// Get statistics
$service->getStatistics(30);
```

### Model Methods (with Loggable trait)

```php
// Get all logs for this model
$news->activityLogs()->get();

// Get latest log
$news->latestActivity();

// Log custom activity
$news->logActivity('viewed', null, $news->getAttributes());

// Restore from log
$news->restoreFromLog($logId);
```

## 🎓 Learning Topics

### Core Concepts

1. **Activity Logging**
   - Event-driven logging
   - Model observers
   - Trait-based functionality

2. **Data Restoration**
   - Soft deletes
   - Data recovery
   - Conflict resolution

3. **Audit Trails**
   - Compliance requirements
   - Change tracking
   - User accountability

4. **Laravel Events & Observers**
   - Model events
   - Event listeners
   - Observer pattern

### Topics to Study

1. Laravel Model Events
   - created, updated, deleted events
   - Observer pattern
   - Event listeners

2. Database Design
   - JSON columns
   - Indexing strategies
   - Data archiving

3. Security & Compliance
   - Audit trails
   - Data retention
   - Privacy regulations (GDPR)

4. Performance Optimization
   - Query optimization
   - Caching strategies
   - Data archiving

The activity log system is now ready to use! Add the `Loggable` trait to any model you want to track.


