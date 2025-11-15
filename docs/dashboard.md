# File: dashboard.php (Admin & User)

## Purpose

This documentation covers both **admin/dashboard.php** and **user/dashboard.php** files. These files display comprehensive dashboard interfaces showing statistics, recent activities, and quick access links. The admin dashboard shows system-wide statistics, while the user dashboard shows personal task and leave statistics.

## Key Features

### Admin Dashboard:
- Employee statistics (total, active employees)
- Department statistics
- Task statistics (total, completed, pending, in progress, on hold, cancelled)
- Leave statistics (total, approved, pending, rejected)
- Activity logs statistics
- Top performing employees
- Recent tasks list
- Recent pending leaves
- Recent activity logs
- Task status distribution chart
- Leave status distribution chart
- Quick access links

### User Dashboard:
- Personal task statistics (total, completed, pending, in progress, on hold)
- Personal leave statistics (total, approved, pending, rejected)
- Recent tasks assigned to user
- Recent leave applications
- Task completion rate
- Task status distribution chart
- Leave status distribution chart
- Quick access links

## Code Breakdown

### 1. Session Validation and Access Control

```php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}
```

**Explanation:**
- Starts PHP session to access user data
- Checks if user is logged in (session variable exists)
- For admin dashboard: verifies user role is 'admin'
- For user dashboard: only checks if user is logged in
- Redirects to login page if access denied
- `exit` stops script execution after redirect

### 2. File Includes

```php
require_once __DIR__ . '/../dbsetting/config.php';
include(HEADER_PATH);
include(SIDEBAR_PATH);
```

**Explanation:**
- `dbsetting/config.php` - Loads configuration constants (BASE_URL, paths) and database connection
- `HEADER_PATH` - Constant defined in config.php, points to admin/common/header.php or user/common/user_header.php
- `SIDEBAR_PATH` - Constant defined in config.php, points to admin/common/sidebar.php or user/common/user_sidebar.php

### 3. Employee Statistics Query (Admin Only)

```php
$total_employees_query = "SELECT COUNT(*) as total FROM users WHERE role = 'staff'";
$total_employees_result = mysqli_query($conn, $total_employees_query);
$total_employees = mysqli_fetch_assoc($total_employees_result)['total'] ?? 0;

$active_employees_query = "SELECT COUNT(DISTINCT users_id) as total FROM users WHERE role = 'staff' AND last_login IS NOT NULL AND last_login != '0000-00-00 00:00:00'";
$active_employees_result = mysqli_query($conn, $active_employees_query);
$active_employees = mysqli_fetch_assoc($active_employees_result)['total'] ?? 0;
```

**Explanation:**
- Counts total employees (users with role = 'staff')
- Counts active employees (those who have logged in at least once)
- Uses `COUNT(*)` to get total number of records
- Uses `COUNT(DISTINCT users_id)` to count unique users
- `?? 0` provides default value if query fails

### 5. Task Statistics Query

```php
$total_tasks_query = "SELECT COUNT(*) as total FROM task_assign";
$total_tasks_result = mysqli_query($conn, $total_tasks_query);
$total_tasks = mysqli_fetch_assoc($total_tasks_result)['total'] ?? 0;

$completed_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE status = 'Completed'";
$completed_tasks_result = mysqli_query($conn, $completed_tasks_query);
$completed_tasks = mysqli_fetch_assoc($completed_tasks_result)['total'] ?? 0;

$pending_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE status IN ('Not Started', 'In Progress', 'Pending')";
$pending_tasks_result = mysqli_query($conn, $pending_tasks_query);
$pending_tasks = mysqli_fetch_assoc($pending_tasks_result)['total'] ?? 0;
```

**Explanation:**
- Counts total tasks from `task_assign` table
- Counts completed tasks (status = 'Completed')
- Counts pending tasks (multiple statuses: Not Started, In Progress, Pending)
- Uses `IN` clause to match multiple status values
- For user dashboard: adds `WHERE users_id = $user_id` filter

### 6. Task Completion Rate Calculation

```php
$task_completion_rate = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100, 1) : 0;
```

**Explanation:**
- Calculates percentage of completed tasks
- Formula: (completed_tasks / total_tasks) * 100
- Rounds to 1 decimal place using `round()`
- Prevents division by zero with conditional check
- Returns 0 if no tasks exist

### 7. Leave Statistics Query

```php
$total_leaves_query = "SELECT COUNT(*) as total FROM leave_apply";
$total_leaves_result = mysqli_query($conn, $total_leaves_query);
$total_leaves = mysqli_fetch_assoc($total_leaves_result)['total'] ?? 0;

$approved_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE status = 'Approved'";
$approved_leaves_result = mysqli_query($conn, $approved_leaves_query);
$approved_leaves = mysqli_fetch_assoc($approved_leaves_result)['total'] ?? 0;

$pending_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE status = 'Pending'";
$pending_leaves_result = mysqli_query($conn, $pending_leaves_query);
$pending_leaves = mysqli_fetch_assoc($pending_leaves_result)['total'] ?? 0;
```

**Explanation:**
- Counts total leave applications
- Counts approved leaves (status = 'Approved')
- Counts pending leaves (status = 'Pending')
- Counts rejected leaves (status = 'Rejected')
- For user dashboard: filters by `users_id = $user_id`

### 8. Top Performing Employees Query (Admin Only)

```php
$top_employees_query = "
    SELECT u.users_id, u.fullname, COUNT(ta.record_id) as total_tasks, 
           SUM(CASE WHEN ta.status = 'Completed' THEN 1 ELSE 0 END) as completed
    FROM users u
    LEFT JOIN task_assign ta ON u.users_id = ta.users_id
    WHERE u.role = 'staff'
    GROUP BY u.users_id, u.fullname
    HAVING total_tasks > 0
    ORDER BY completed DESC, total_tasks DESC
    LIMIT 5
";
```

**Explanation:**
- Joins `users` and `task_assign` tables
- Counts total tasks per employee
- Counts completed tasks using `SUM(CASE...)`
- Groups by user to aggregate data
- Filters employees with at least one task
- Orders by completed tasks (descending), then total tasks
- Limits to top 5 employees

### 9. Recent Tasks Query

```php
$recent_tasks_query = "
    SELECT ta.*, tm.task_title, tm.priority, u.fullname
    FROM task_assign ta
    JOIN task_manage tm ON ta.task_id = tm.task_id
    JOIN users u ON ta.users_id = u.users_id
    ORDER BY ta.record_id DESC
    LIMIT 5
";
```

**Explanation:**
- Joins three tables: `task_assign`, `task_manage`, `users`
- Gets task details, title, priority, and assignee name
- Orders by record_id (newest first)
- Limits to 5 most recent tasks
- For user dashboard: adds `WHERE ta.users_id = $user_id`

### 10. Statistics Display Cards

```php
<div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="card-body text-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-2 opacity-75">Total Employees</h6>
                <h2 class="mb-0"><?php echo $total_employees; ?></h2>
                <small class="opacity-75">
                    <i class="fas fa-user-check me-1"></i> <?php echo $active_employees; ?> Active
                </small>
            </div>
            <div>
                <i class="fas fa-users fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
</div>
```

**Explanation:**
- Creates Bootstrap card component
- Displays statistics in attractive card format
- Uses gradient background for visual appeal
- Shows icon, title, main number, and subtitle
- Responsive design with Bootstrap classes

### 11. Progress Bar Charts

```php
<div class="progress" style="height: 30px;">
    <?php 
    $total = $completed_tasks + $pending_tasks + $in_progress_tasks + $on_hold_tasks;
    $completed_pct = $total > 0 ? ($completed_tasks / $total) * 100 : 0;
    $pending_pct = $total > 0 ? ($pending_tasks / $total) * 100 : 0;
    $progress_pct = $total > 0 ? ($in_progress_tasks / $total) * 100 : 0;
    $hold_pct = $total > 0 ? ($on_hold_tasks / $total) * 100 : 0;
    ?>
    <div class="progress-bar bg-success" style="width: <?php echo $completed_pct; ?>%"></div>
    <div class="progress-bar bg-warning" style="width: <?php echo $pending_pct; ?>%"></div>
    <div class="progress-bar bg-info" style="width: <?php echo $progress_pct; ?>%"></div>
    <div class="progress-bar bg-secondary" style="width: <?php echo $hold_pct; ?>%"></div>
</div>
```

**Explanation:**
- Creates visual progress bar showing task distribution
- Calculates percentage for each status
- Uses Bootstrap progress bar component
- Multiple colored segments (success, warning, info, secondary)
- Visual representation of task status distribution

### 12. Auto-Refresh Script

```php
<script>
// Auto-refresh dashboard every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>
```

**Explanation:**
- Automatically refreshes dashboard every 5 minutes
- 300000 milliseconds = 5 minutes
- Keeps statistics up-to-date
- Uses JavaScript `setInterval()` function

## Output / Result

**When the dashboard loads:**

1. **Admin Dashboard Display:**
   - Header showing "Admin Dashboard" with welcome message and current date/time
   - Four main statistic cards:
     - Total Employees (with active count)
     - Task Completion Rate (percentage)
     - Pending Leaves (with total count)
     - Departments (total count)
   - Secondary statistics row showing:
     - Completed Tasks
     - Pending Tasks
     - In Progress Tasks
     - Approved Leaves
     - Rejected Leaves
     - Today's Activities
   - Top Performers table (showing employee rankings)
   - Recent Tasks list
   - Pending Leaves list
   - Recent Activity log
   - Quick Links section
   - Task Status Distribution chart
   - Leave Status Distribution chart

2. **User Dashboard Display:**
   - Header showing "Employee Dashboard" with welcome message
   - Four main statistic cards:
     - Task Completion Rate
     - Total Tasks
     - Pending Leaves
     - Approved Leaves
   - Secondary statistics row
   - Recent Tasks list (assigned to user)
   - Task Status Distribution chart
   - Recent Leave Applications
   - Leave Status Distribution chart
   - Quick Links section

3. **Visual Features:**
   - Colorful gradient cards
   - Icons for each statistic
   - Progress bars for visual representation
   - Badges for status indicators
   - Responsive design (works on mobile and desktop)
   - Hover effects on cards

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Used to identify logged-in user
- `$_SESSION['name']` - Displayed in welcome message
- `$_SESSION['role']` - Determines dashboard access (admin vs user)

### Database Logic:
- Multiple SQL queries to fetch statistics
- JOIN operations to combine data from multiple tables
- Aggregate functions (COUNT, SUM) for calculations
- Filtering by user_id for user-specific data

### Validation:
- Session validation ensures only logged-in users access dashboard
- Role-based access control for admin dashboard
- Error handling for database queries (using ?? operator)

### Alerts:
- Success messages for completed actions
- Error messages if queries fail
- Info messages for system notifications

### For Presentation/Viva:
- **Explain:** Dashboard provides overview of system statistics
- **Highlight:** Real-time data from database
- **Mention:** Role-based dashboards (admin sees all, user sees personal)
- **Show:** How statistics are calculated and displayed
- **Demonstrate:** Visual charts and progress bars
- **Discuss:** Auto-refresh feature for keeping data updated

### Performance Considerations:
- Multiple queries can be optimized with single query using GROUP BY
- Consider caching statistics for better performance
- Limit recent data queries to prevent slow loading

### Security Features:
- Session validation prevents unauthorized access
- Role-based access control
- SQL injection prevention (though prepared statements recommended)

