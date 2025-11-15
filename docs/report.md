# File: admin/report.php

## Purpose

This file generates comprehensive performance and leave reports for administrators. It provides detailed statistics about employee performance, task completion rates, and leave applications. The reports can be filtered by employee, month, and year, and can be printed for documentation purposes.

## Key Features

- Overall statistics dashboard (employees, tasks, leaves)
- Employee performance report with task completion metrics
- Employee leave report with approval statistics
- Filter by employee, month, and year
- Print functionality for reports
- Color-coded performance indicators
- Completion rate calculations
- Visual progress bars
- Responsive card-based layout

## Code Breakdown

### 1. Session and Access Control

```php
session_start();

require_once __DIR__ . '/../dbsetting/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}
```

**Explanation:**
- Starts session for user authentication
- Includes database configuration
- Verifies user is logged in and has admin role
- Redirects unauthorized users to login page
- Ensures only administrators can view reports

### 2. Filter Parameters

```php
$filter_employee = isset($_GET['employee']) ? intval($_GET['employee']) : 0;
$filter_month = isset($_GET['month']) ? mysqli_real_escape_string($conn, $_GET['month']) : date('Y-m');
$filter_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
```

**Explanation:**
- Gets filter parameters from URL query string
- Employee filter: 0 means all employees
- Month filter: Defaults to current month (YYYY-MM format)
- Year filter: Defaults to current year
- Validates and escapes inputs for security

### 3. Overall Statistics

```php
$total_employees_query = mysqli_query($conn, "SELECT users_id FROM users WHERE role = 'staff'");
$total_employees = $total_employees_query ? mysqli_num_rows($total_employees_query) : 0;

$total_tasks_query = "SELECT COUNT(*) as total FROM task_assign";
$completed_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE status = 'Completed'";
$pending_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE status IN ('Not Started', 'In Progress', 'Pending')";

$completion_rate = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100, 2) : 0;
```

**Explanation:**
- Counts total employees (staff role only)
- Counts total tasks from task_assign table
- Counts completed tasks
- Counts pending tasks (Not Started, In Progress, Pending)
- Calculates completion rate as percentage
- Handles division by zero

### 4. Leave Statistics

```php
$total_leaves_query = "SELECT COUNT(*) as total FROM leave_apply";
$approved_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE status = 'Approved'";
$pending_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE status = 'Pending'";
$rejected_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE status = 'Rejected'";
```

**Explanation:**
- Counts total leave applications
- Counts approved leaves
- Counts pending leaves
- Counts rejected leaves
- Provides comprehensive leave statistics

### 5. Employee Performance Query

```php
$employee_performance_query = "
    SELECT 
        u.users_id,
        u.fullname,
        u.email,
        COUNT(ta.record_id) as total_tasks,
        SUM(CASE WHEN ta.status = 'Completed' THEN 1 ELSE 0 END) as completed_tasks,
        SUM(CASE WHEN ta.status IN ('Not Started', 'In Progress', 'Pending') THEN 1 ELSE 0 END) as pending_tasks,
        SUM(CASE WHEN ta.status = 'On Hold' THEN 1 ELSE 0 END) as on_hold_tasks,
        SUM(CASE WHEN ta.status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_tasks
    FROM users u
    LEFT JOIN task_assign ta ON u.users_id = ta.users_id
    WHERE u.role = 'staff'
    GROUP BY u.users_id, u.fullname, u.email
    ORDER BY completed_tasks DESC, total_tasks DESC
";
```

**Explanation:**
- Joins users table with task_assign table
- Counts total tasks per employee
- Uses CASE statements to count tasks by status
- Groups by employee to get individual statistics
- Orders by completion rate (completed tasks first)
- Uses LEFT JOIN to include employees with no tasks

### 6. Employee Leave Query

```php
$employee_leave_query = "
    SELECT 
        u.users_id,
        u.fullname,
        COUNT(la.leave_id) as total_leaves,
        SUM(CASE WHEN la.status = 'Approved' THEN 1 ELSE 0 END) as approved_leaves,
        SUM(CASE WHEN la.status = 'Pending' THEN 1 ELSE 0 END) as pending_leaves,
        SUM(CASE WHEN la.status = 'Rejected' THEN 1 ELSE 0 END) as rejected_leaves
    FROM users u
    LEFT JOIN leave_apply la ON u.users_id = la.users_id
    WHERE u.role = 'staff'
    GROUP BY u.users_id, u.fullname
    ORDER BY total_leaves DESC
";
```

**Explanation:**
- Joins users table with leave_apply table
- Counts total leaves per employee
- Uses CASE statements to count leaves by status
- Groups by employee for individual statistics
- Orders by total leaves (descending)
- Includes employees with no leaves

### 7. Filter Application

```php
if ($filter_employee > 0) {
    $employee_performance_query = "
        ...
        WHERE u.role = 'staff' AND u.users_id = $filter_employee
        ...
    ";
}
```

**Explanation:**
- Modifies query if employee filter is applied
- Adds WHERE condition for specific employee
- Allows viewing individual employee reports
- Maintains same query structure with filter

### 8. Completion Rate Calculation

```php
$total = (int)$emp['total_tasks'];
$completed = (int)$emp['completed_tasks'];
$rate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

// Color coding for completion rate
$rate_color = 'success';
if ($rate < 50) $rate_color = 'danger';
elseif ($rate < 75) $rate_color = 'warning';
```

**Explanation:**
- Calculates completion rate as percentage
- Handles division by zero
- Color codes based on rate:
  - Green (success): 75% and above
  - Yellow (warning): 50-74%
  - Red (danger): Below 50%
- Provides visual indicators for performance

### 9. Print Functionality

```php
<button onclick="window.print()" class="btn btn-primary">
    <i class="fas fa-print me-2"></i> Print Report
</button>
```

**Explanation:**
- Uses browser's print functionality
- Triggers print dialog when clicked
- Allows printing reports for documentation
- CSS media queries handle print layout

### 10. Print Styles

```css
@media print {
    .card, .btn, form {
        page-break-inside: avoid;
    }
    .btn {
        display: none;
    }
}
```

**Explanation:**
- Hides buttons when printing
- Prevents page breaks inside cards
- Ensures clean print output
- Maintains table structure in print

## Output / Result

**When the page loads:**

1. **Filter Section:**
   - Employee dropdown (All Employees or specific employee)
   - Month picker (defaults to current month)
   - Year input (defaults to current year)
   - Apply Filters button
   - Reset button

2. **Overall Statistics Cards:**
   - Total Employees card (blue)
   - Task Completion Rate card (green) with percentage
   - Pending Tasks card (yellow)
   - Total Leaves card (blue) with approved count

3. **Leave Statistics Section:**
   - Approved Leaves count
   - Pending Leaves count
   - Rejected Leaves count
   - Total Applications count

4. **Employee Performance Table:**
   - Columns: Employee, Email, Total Tasks, Completed, Pending, On Hold, Cancelled, Completion Rate
   - Color-coded completion rates
   - Progress bars for visual representation
   - Sorted by completion rate (highest first)

5. **Employee Leave Details Table:**
   - Columns: Employee, Total Leaves, Approved, Pending, Rejected, Approval Rate
   - Color-coded badges for each status
   - Progress bars for approval rates
   - Sorted by total leaves (highest first)

6. **Print Button:**
   - Allows printing the entire report
   - Hides buttons and forms in print view
   - Maintains table structure

## Additional Notes

### Security Considerations:
- **SQL Injection Prevention:** All inputs escaped and validated
- **Access Control:** Only administrators can access reports
- **Input Validation:** Employee ID and year converted to integers
- **XSS Prevention:** Output encoded with `htmlspecialchars()`

### Performance Considerations:
- **Efficient Queries:** Uses JOINs and aggregations
- **Indexed Columns:** Uses indexed columns for filtering
- **Grouped Data:** Reduces number of queries

### Filter Functionality:
- **Employee Filter:** View all employees or specific employee
- **Month Filter:** Filter by specific month (currently for display, can be extended)
- **Year Filter:** Filter by specific year (currently for display, can be extended)

### Color Coding:
- **Completion Rate:**
  - Green: 75% and above (excellent)
  - Yellow: 50-74% (good)
  - Red: Below 50% (needs improvement)
- **Task Status:**
  - Blue: Total tasks
  - Green: Completed
  - Yellow: Pending
  - Info: On Hold
  - Secondary: Cancelled

### Report Metrics:
- **Task Metrics:**
  - Total tasks assigned
  - Completed tasks
  - Pending tasks
  - On hold tasks
  - Cancelled tasks
  - Completion rate percentage

- **Leave Metrics:**
  - Total leave applications
  - Approved leaves
  - Pending leaves
  - Rejected leaves
  - Approval rate percentage

### Print Optimization:
- Hides interactive elements (buttons, forms)
- Maintains table structure
- Prevents page breaks in cards
- Clean, professional print output

## Usage Example

1. **Viewing All Employees:**
   - Navigate to Reports page
   - View overall statistics and all employee reports

2. **Filtering by Employee:**
   - Select specific employee from dropdown
   - Click Apply Filters
   - View that employee's performance and leave data

3. **Printing Report:**
   - Click Print Report button
   - Browser print dialog appears
   - Print or save as PDF

4. **Analyzing Performance:**
   - Review completion rates (color-coded)
   - Check progress bars for visual representation
   - Compare employee performance

5. **Reviewing Leave Statistics:**
   - View leave approval rates
   - Check pending leave applications
   - Analyze leave patterns

