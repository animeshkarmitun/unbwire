# Analytics Permissions Setup

## ✅ Permissions Added

The following analytics permissions have been added to the system:

1. **`analytics index`** - Access to the main analytics dashboard
2. **`analytics view`** - View analytics data (real-time, date-wise, country-wise, organic, repeaters)
3. **`analytics export`** - Export analytics data

## 🔐 Permission Structure

### Permission Names
- `analytics index` - Main dashboard access
- `analytics view` - View all analytics reports
- `analytics export` - Export analytics data

### Guard
All permissions use the `admin` guard, consistent with other admin permissions.

## 📋 Implementation Details

### 1. Database Seeder Updated
The `DatabaseSeeder` has been updated to include analytics permissions when seeding the database.

**File:** `database/seeders/DatabaseSeeder.php`

### 2. Command Created
A new artisan command has been created to add analytics permissions:

```bash
php artisan analytics:add-permissions
```

**File:** `app/Console/Commands/AddAnalyticsPermissions.php`

This command:
- Creates the three analytics permissions
- Assigns them to the Super Admin role
- Clears permission cache

### 3. Controller Middleware
The `AnalyticsController` has been updated with permission middleware:

**File:** `app/Http/Controllers/Admin/AnalyticsController.php`

```php
$this->middleware(['permission:analytics index,admin'])->only(['index']);
$this->middleware(['permission:analytics view,admin'])->only(['realTime', 'dateWise', 'countryWise', 'organic', 'repeaters']);
$this->middleware(['permission:analytics export,admin'])->only(['export']);
```

### 4. Routes Protected
All analytics routes are protected by the `admin` middleware group, which ensures:
- User must be authenticated as admin
- User must have the appropriate permission

## 🎯 Usage

### Assigning Permissions to Roles

You can assign these permissions to roles through the admin panel:
1. Go to **Admin → Role Management**
2. Edit a role
3. Select the analytics permissions you want to grant
4. Save

### Checking Permissions in Code

```php
// Check if user has permission
if (auth()->guard('admin')->user()->can('analytics index')) {
    // User can access analytics dashboard
}

// Or use middleware
Route::middleware(['permission:analytics index,admin'])->group(function() {
    // Protected routes
});
```

## 🔄 Adding Permissions to Existing Roles

If you need to add analytics permissions to existing roles:

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$role = Role::where('name', 'Your Role Name')->first();
$permissions = Permission::whereIn('name', [
    'analytics index',
    'analytics view',
    'analytics export'
])->get();

$role->givePermissionTo($permissions);
```

Or use the admin panel to assign permissions to roles.

## 📊 Permission Breakdown

| Permission | Routes | Description |
|------------|--------|-------------|
| `analytics index` | `/admin/analytics` | Main dashboard access |
| `analytics view` | `/admin/analytics/real-time`<br>`/admin/analytics/date-wise`<br>`/admin/analytics/country-wise`<br>`/admin/analytics/organic`<br>`/admin/analytics/repeaters` | View all analytics reports |
| `analytics export` | `/admin/analytics/export` | Export analytics data |

## ✅ Verification

To verify permissions were added correctly:

```bash
php artisan tinker
```

```php
use Spatie\Permission\Models\Permission;

Permission::where('name', 'like', 'analytics%')->get();
```

This should return the three analytics permissions.

## 🚀 Next Steps

1. **Assign Permissions**: Assign analytics permissions to appropriate roles through the admin panel
2. **Test Access**: Test that users with/without permissions can/cannot access analytics
3. **Customize**: Adjust permissions as needed for your organization's requirements

## 📝 Notes

- All permissions are automatically assigned to the "Super Admin" role
- Permission cache is cleared when permissions are added
- The middleware uses Spatie Permission package syntax: `permission:permission_name,guard_name`


