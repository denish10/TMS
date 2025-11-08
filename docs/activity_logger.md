# File: admin/common/activity_logger.php & user/common/activity_logger.php

## Purpose

This file provides functions for logging system activities and user actions in the Task Management System. It records all important events such as task creation, employee management, leave approvals, login/logout, and other system activities. The logged data helps in auditing, tracking user behavior, and monitoring system usage.

**Note:** This file exists in two locations:
- `admin/common/activity_logger.php` - Used by admin files
- `user/common/activity_logger.php` - Used by user files

Both files contain the same functionality and are located in their respective `common/` folders for easy access.

## Key Features

- Activity logging function (`logActivity()`)
- Recent activities retrieval (`getRecentActivities()`)
- Activity icon mapping (`getActivityIcon()`)
- Time ago formatting (`timeAgo()`)
- Support for related table and ID tracking
- User identification and name storage
- Timestamp tracking for all activities

## Code Breakdown

### 1. File Includes and Configuration

```php
require_once __DIR__ . '/../../dbsetting/config.php';
```

**Explanation:**
- Includes configuration file from dbsetting folder for database connection
- Ensures database connection is available
- Provides access to `$conn` global variable
- Path goes up two levels (from admin/common/ or user/common/) to project root, then into dbsetting/

### 2. Main Activity Logging Function

```php
function logActivity($activity_type, $description, $user_id = null, $related_table = null, $related_id = null) {
    global $conn;
    
    // Get user ID from session if not provided
    if ($user_id === null) {
        $user_id = isset($_SESSION['users_id']) ? intval($_SESSION['users_id']) : null;
    }
    
    // Get user name for display
    $user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'System';
```

**Explanation:**
- Main function to log activities
- Parameters:
  - `$activity_type` - Type of activity (e.g., 'task_created', 'login')
  - `$description` - Description of the activity
  - `$user_id` - User ID (optional, defaults to session user)
  - `$related_table` - Related table name (optional)
  - `$related_id` - Related record ID (optional)
- Gets user ID from session if not provided
- Gets user name from session (defaults to 'System')

### 3. Data Sanitization for Logging

```php
// Escape inputs
$activity_type = mysqli_real_escape_string($conn, $activity_type);
$description = mysqli_real_escape_string($conn, $description);
$user_id = $user_id ? intval($user_id) : 'NULL';
$related_table = $related_table ? "'" . mysqli_real_escape_string($conn, $related_table) . "'" : 'NULL';
$related_id = $related_id ? intval($related_id) : 'NULL';
```

**Explanation:**
- Escapes all string inputs to prevent SQL injection
- Converts user_id and related_id to integers or 'NULL'
- Handles optional parameters (NULL if not provided)
- Sanitizes related_table name

### 4. Database Insertion

```php
// Insert into activity_logs table
$query = "INSERT INTO activity_logs 
          (activity_type, description, user_id, user_name, related_table, related_id, created_at) 
          VALUES 
          ('$activity_type', '$description', $user_id, '$user_name', $related_table, $related_id, NOW())";

return mysqli_query($conn, $query);
```

**Explanation:**
- Inserts activity record into `activity_logs` table
- Stores: activity_type, description, user_id, user_name, related_table, related_id, created_at
- Uses `NOW()` for current timestamp
- Returns true on success, false on failure

### 5. Get Recent Activities Function

```php
function getRecentActivities($limit = 10) {
    global $conn;
    
    $limit = intval($limit);
    $query = "SELECT * FROM activity_logs 
              ORDER BY created_at DESC 
              LIMIT $limit";
    
    $result = mysqli_query($conn, $query);
    $activities = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $activities[] = $row;
        }
    }
    
    return $activities;
}
```

**Explanation:**
- Retrieves recent activities from database
- Parameter: `$limit` - Number of activities to retrieve (default: 10)
- Orders by `created_at DESC` (newest first)
- Limits results to specified number
- Returns array of activity records
- Returns empty array if no activities found

### 6. Activity Icon Mapping Function

```php
function getActivityIcon($activity_type) {
    $icons = [
        'task_created' => ['icon' => 'fa-tasks', 'color' => 'bg-info'],
        'task_updated' => ['icon' => 'fa-edit', 'color' => 'bg-primary'],
        'task_completed' => ['icon' => 'fa-check', 'color' => 'bg-success'],
        'task_deleted' => ['icon' => 'fa-trash', 'color' => 'bg-danger'],
        'employee_added' => ['icon' => 'fa-user-plus', 'color' => 'bg-success'],
        'employee_updated' => ['icon' => 'fa-user-edit', 'color' => 'bg-primary'],
        'employee_deleted' => ['icon' => 'fa-user-times', 'color' => 'bg-danger'],
        'department_added' => ['icon' => 'fa-building', 'color' => 'bg-info'],
        'department_updated' => ['icon' => 'fa-edit', 'color' => 'bg-primary'],
        'department_deleted' => ['icon' => 'fa-trash', 'color' => 'bg-danger'],
        'leave_applied' => ['icon' => 'fa-calendar-check', 'color' => 'bg-warning'],
        'leave_approved' => ['icon' => 'fa-check-circle', 'color' => 'bg-success'],
        'leave_rejected' => ['icon' => 'fa-times-circle', 'color' => 'bg-danger'],
        'login' => ['icon' => 'fa-sign-in-alt', 'color' => 'bg-primary'],
        'logout' => ['icon' => 'fa-sign-out-alt', 'color' => 'bg-secondary'],
        'profile_updated' => ['icon' => 'fa-user-edit', 'color' => 'bg-info'],
        'password_changed' => ['icon' => 'fa-key', 'color' => 'bg-warning'],
    ];
    
    return $icons[$activity_type] ?? ['icon' => 'fa-circle', 'color' => 'bg-secondary'];
}
```

**Explanation:**
- Maps activity types to Font Awesome icons and Bootstrap colors
- Returns array with 'icon' and 'color' keys
- Icons are Font Awesome classes (e.g., 'fa-tasks')
- Colors are Bootstrap background classes (e.g., 'bg-info')
- Returns default icon/color if activity type not found
- Visual representation for different activity types

### 7. Time Ago Formatting Function

```php
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}
```

**Explanation:**
- Converts database datetime to human-readable "time ago" format
- Calculates difference between current time and activity time
- Returns appropriate format based on time difference:
  - Less than 60 seconds: "just now"
  - Less than 1 hour: "X minute(s) ago"
  - Less than 1 day: "X hour(s) ago"
  - Less than 1 week: "X day(s) ago"
  - More than 1 week: Full date (e.g., "Jan 15, 2024")
- Makes timestamps more user-friendly

## Output / Result

**When functions are used:**

1. **Logging Activities:**
   - Activities are stored in `activity_logs` table
   - Each activity includes: type, description, user, timestamp
   - Returns true on success, false on failure
   - No visible output (silent operation)

2. **Retrieving Recent Activities:**
   - Returns array of activity records
   - Ordered by date (newest first)
   - Limited to specified number of records
   - Used in dashboard to show recent activities

3. **Getting Activity Icons:**
   - Returns icon and color for activity type
   - Used to display visual indicators in dashboard
   - Different icons for different activity types
   - Color-coded for quick recognition

4. **Formatting Time Ago:**
   - Converts timestamps to human-readable format
   - Shows "just now", "5 minutes ago", "2 hours ago", etc.
   - Makes activity timestamps more user-friendly

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Used to identify user performing activity
- `$_SESSION['name']` - Used to store user name in log

### Database Logic:
- Inserts into `activity_logs` table
- Stores comprehensive activity information
- Links activities to users and related records
- Tracks timestamps for all activities

### Activity Types Supported:
- **Task Activities:** task_created, task_updated, task_completed, task_deleted
- **Employee Activities:** employee_added, employee_updated, employee_deleted
- **Department Activities:** department_added, department_updated, department_deleted
- **Leave Activities:** leave_applied, leave_approved, leave_rejected
- **User Activities:** login, logout, profile_updated, password_changed

### Security Features:
- **SQL Injection Prevention:** Uses `mysqli_real_escape_string()`
- **Input Validation:** Validates and sanitizes all inputs
- **User Identification:** Tracks which user performed activity

### For Presentation/Viva:
- **Explain:** Activity logging tracks all system events
- **Highlight:** Comprehensive tracking of user actions
- **Mention:** Useful for auditing and monitoring
- **Show:** How activities are logged and displayed
- **Demonstrate:** Recent activities in dashboard
- **Discuss:** Security and compliance benefits

### Use Cases:
1. **Auditing:** Track who did what and when
2. **Debugging:** Identify issues by reviewing activities
3. **Monitoring:** Monitor system usage and user behavior
4. **Compliance:** Meet regulatory requirements for activity tracking
5. **Analytics:** Analyze system usage patterns

### Database Table Structure:
- `activity_logs` table contains:
  - `activity_id` (primary key)
  - `activity_type` (type of activity)
  - `description` (activity description)
  - `user_id` (user who performed activity)
  - `user_name` (user name for display)
  - `related_table` (related table name, optional)
  - `related_id` (related record ID, optional)
  - `created_at` (activity timestamp)

### Best Practices:
- Log all important system events
- Include descriptive messages
- Link activities to related records when possible
- Use consistent activity type names
- Store user information for accountability

### Integration:
- Used throughout the system to log activities
- Called after important operations (create, update, delete)
- Displayed in admin dashboard
- Can be exported for reporting
- Supports filtering and searching

