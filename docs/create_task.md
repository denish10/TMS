# File: create_task.php

## Purpose

This file allows administrators to create new tasks and assign them to employees. It provides a form where admins can select a department, choose employees from that department, enter task details (title, description, priority, dates), and assign the task to one or multiple employees. The file handles task creation, employee assignment, and activity logging.

## Key Features

- Department selection dropdown
- Dynamic employee selection based on department (AJAX)
- Task creation form with validation
- Multiple employee assignment
- Priority selection (High, Medium, Low)
- Date range selection (start date and end date)
- Activity logging for task creation
- Form validation and error handling
- Automatic redirect after successful creation

## Code Breakdown

### 1. Session and Access Control

```php
session_start();

require_once __DIR__ . '/../../dbsetting/config.php';
require_once __DIR__ . '/../common/activity_logger.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}
```

**Explanation:**
- Starts PHP session for user authentication
- Includes configuration and activity logger files
- Validates user is logged in AND has admin role
- Redirects to login if not authorized
- Ensures only administrators can create tasks

### 2. Form Submission Handling

```php
if (isset($_POST['task_assign'])) {
    $department_id = isset($_POST['department_id']) ? (int) $_POST['department_id'] : 0;
    $users_ids = isset($_POST['users_id']) ? $_POST['users_id'] : [];
    $task_title = trim($_POST['task_title'] ?? '');
    $task_description = trim($_POST['task_description'] ?? '');
    $task_priority = trim($_POST['task_priority'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
```

**Explanation:**
- Checks if form is submitted with 'task_assign' button
- Retrieves department ID (cast to integer)
- Gets array of selected user IDs (multiple employees can be selected)
- Retrieves task details: title, description, priority, dates
- `trim()` removes whitespace from inputs
- `?? []` provides empty array if no users selected

### 3. Comprehensive Input Validation

```php
// Validation
if ($department_id <= 0) {
    $message = "⚠️ Please select a valid department.";
    $alertType = "danger";
} elseif (empty($users_ids)) {
    $message = "⚠️ Please select at least one employee.";
    $alertType = "danger";
} elseif (empty($task_title)) {
    $message = "⚠️ Task title is required.";
    $alertType = "danger";
} elseif (strlen($task_title) > 255) {
    $message = "⚠️ Task title must be less than 255 characters.";
    $alertType = "danger";
} elseif (empty($task_description)) {
    $message = "⚠️ Task description is required.";
    $alertType = "danger";
} elseif (!in_array($task_priority, ['High', 'Medium', 'Low'])) {
    $message = "⚠️ Please select a valid priority.";
    $alertType = "danger";
} elseif (empty($start_date) || empty($end_date)) {
    $message = "⚠️ Please provide both start and end dates.";
    $alertType = "danger";
} elseif (strtotime($start_date) > strtotime($end_date)) {
    $message = "⚠️ Start date cannot be after end date.";
    $alertType = "danger";
}
```

**Explanation:**
- Validates department is selected (must be > 0)
- Validates at least one employee is selected
- Validates task title is not empty
- Validates task title length (max 255 characters)
- Validates task description is not empty
- Validates priority is one of: High, Medium, Low
- Validates both dates are provided
- Validates start date is before or equal to end date
- Sets appropriate error messages for each validation failure

### 4. Data Sanitization

```php
// Escape data for database
$task_title = mysqli_real_escape_string($conn, $task_title);
$task_description = mysqli_real_escape_string($conn, $task_description);
$start_date = mysqli_real_escape_string($conn, $start_date);
$end_date = mysqli_real_escape_string($conn, $end_date);
$task_priority = mysqli_real_escape_string($conn, $task_priority);
```

**Explanation:**
- Escapes special characters to prevent SQL injection
- Sanitizes all user inputs before database insertion
- Uses `mysqli_real_escape_string()` for security

### 5. Task Creation in Database

```php
// Insert task
$query_task = "INSERT INTO task_manage 
               (task_title, task_description, created_date, start_date, end_date, priority) 
               VALUES 
               ('$task_title', '$task_description', NOW(), '$start_date', '$end_date', '$task_priority')";

if (mysqli_query($conn, $query_task)) {
    $task_id = mysqli_insert_id($conn);
```

**Explanation:**
- Inserts task into `task_manage` table
- Stores: title, description, created_date, start_date, end_date, priority
- Uses `NOW()` for current timestamp
- `mysqli_insert_id()` gets the auto-generated task ID
- This ID is used to assign task to employees

### 6. Task Assignment to Employees

```php
// Assign task to employees
$assigned_users = [];
foreach ($users_ids as $user_id) {
    $user_id = (int) $user_id;
    if ($user_id > 0) {
        $query_assign = "INSERT INTO task_assign (task_id, users_id, status) VALUES ($task_id, $user_id, 'Not Started')";
        mysqli_query($conn, $query_assign);
        
        // Get user name for logging
        $user_query = "SELECT fullname FROM users WHERE users_id = $user_id LIMIT 1";
        $user_result = mysqli_query($conn, $user_query);
        if ($user_row = mysqli_fetch_assoc($user_result)) {
            $assigned_users[] = $user_row['fullname'];
        }
    }
}
```

**Explanation:**
- Loops through selected user IDs
- For each employee, creates assignment in `task_assign` table
- Sets initial status as 'Not Started'
- Retrieves employee names for activity logging
- Stores names in array for log message

### 7. Activity Logging

```php
// Log task creation
$users_list = !empty($assigned_users) ? implode(', ', $assigned_users) : 'employees';
logActivity('task_created', "Created task: '$task_title' and assigned to $users_list", null, 'task_manage', $task_id);
```

**Explanation:**
- Logs task creation event
- Creates descriptive message with task title and assigned employees
- Stores in activity_logs table
- Links to task_manage table with task_id

### 8. Department Selection Dropdown

```php
<select name="department_id" id="department_id" class="form-select">
    <option value="">-- Select Department --</option>
    <?php
    $dept = mysqli_query($conn, "SELECT department_id, department_name FROM department");
    while ($row = mysqli_fetch_assoc($dept)) {
        echo "<option value='{$row['department_id']}'>{$row['department_name']}</option>";
    }
    ?>
</select>
```

**Explanation:**
- Creates dropdown with all departments
- Queries `department` table for department list
- Populates dropdown with department options
- Default option prompts user to select

### 9. Dynamic Employee Selection (AJAX)

```php
<div class="dropdown w-100" data-bs-auto-close="outside">
    <button class="form-select text-start d-flex justify-content-between align-items-center"
            type="button" id="employeeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <span id="employeeDropdownText">Choose Employees</span>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu w-100" aria-labelledby="employeeDropdown"
        id="employee_list" style="max-height: 250px; overflow-y: auto;">
        <li><p class="dropdown-item text-muted mb-0">Select a department first...</p></li>
    </ul>
</div>
```

**Explanation:**
- Creates Bootstrap dropdown for employee selection
- Initially shows "Select a department first..." message
- Dropdown updates dynamically when department is selected
- Supports multiple employee selection with checkboxes
- Max height with scroll for many employees

### 10. JavaScript for Dynamic Employee Loading

```php
document.getElementById('department_id').addEventListener('change', function() {
    var deptId = this.value;
    var xhr = new XMLHttpRequest();

    if (!deptId) {
        document.getElementById('employee_list').innerHTML =
            '<li><p class="dropdown-item text-muted mb-0">Select a department first...</p></li>';
        updateEmployeeDropdownText();
        return;
    }
    xhr.open("GET", "get_employees.php?department_id=" + deptId, true);
    xhr.onload = function () {
        if (this.status == 200) {
            document.getElementById('employee_list').innerHTML = this.responseText;
            initEmployeeDropdown();
        } else {
            document.getElementById('employee_list').innerHTML =
                '<li><p class="dropdown-item text-danger mb-0">Could not load employees</p></li>';
        }
    };
    xhr.send();
});
```

**Explanation:**
- Adds event listener to department dropdown
- When department changes, makes AJAX request to `get_employees.php`
- Passes department_id as parameter
- Updates employee dropdown with response
- Handles errors if request fails
- No page refresh required (AJAX)

### 11. Priority Selection

```php
<div class="d-flex">
    <div class="form-check me-3">
        <input class="form-check-input" type="radio" name="task_priority" value="High">
        <label class="form-check-label text-danger fw-bold">High</label>
    </div>
    <div class="form-check me-3">
        <input class="form-check-input" type="radio" name="task_priority" value="Medium" checked>
        <label class="form-check-label text-warning fw-bold">Medium</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="task_priority" value="Low">
        <label class="form-check-label text-success fw-bold">Low</label>
    </div>
</div>
```

**Explanation:**
- Creates radio button group for priority selection
- Three options: High (red), Medium (yellow, default), Low (green)
- Color-coded labels for visual distinction
- Medium is selected by default

## Output / Result

**When the file runs:**

1. **Task Creation Form:**
   - Header: "📋 Create A New Task"
   - Department dropdown (loads from database)
   - Employee dropdown (updates based on department selection)
   - Task Title input field
   - Task Description textarea
   - Priority radio buttons (High, Medium, Low)
   - Start Date and End Date date pickers
   - "Assign Task" button

2. **After Successful Creation:**
   - Shows success message: "✅ Task assigned successfully! Redirecting..."
   - Automatically redirects to `manage_task.php` after 2 seconds
   - Task is created in `task_manage` table
   - Task is assigned to selected employees in `task_assign` table
   - Activity is logged in `activity_logs` table

3. **After Failed Creation:**
   - Shows error messages for validation failures
   - Form data is preserved (user can correct and resubmit)
   - Specific error messages for each validation issue

4. **User Experience:**
   - Dynamic employee loading (no page refresh)
   - Multiple employee selection
   - Visual priority indicators
   - Clear validation messages
   - Smooth redirect after success

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies admin creating task
- `$_SESSION['role']` - Validates admin access

### Database Logic:
- Inserts into `task_manage` table (task details)
- Inserts into `task_assign` table (task assignments)
- Uses JOIN to get employee names from `users` table
- Links tasks to employees via `users_id` foreign key

### Validation:
- Department selection required
- At least one employee must be selected
- Task title required (max 255 characters)
- Task description required
- Priority must be High, Medium, or Low
- Dates required and must be valid
- Start date must be before end date

### Security Features:
- **SQL Injection Prevention:** Uses `mysqli_real_escape_string()`
- **Role-Based Access:** Only admins can create tasks
- **Input Validation:** Validates all inputs before processing
- **XSS Prevention:** Uses `htmlspecialchars()` for output

### Alerts:
- Success messages shown with ✅ emoji
- Error messages shown with ⚠️ emoji
- Validation errors displayed clearly

### For Presentation/Viva:
- **Explain:** Admin can create tasks and assign to employees
- **Highlight:** Dynamic employee loading based on department
- **Mention:** Multiple employee assignment capability
- **Show:** How tasks are created and assigned
- **Demonstrate:** AJAX functionality for employee selection
- **Discuss:** Activity logging for task creation

### Workflow:
1. Admin selects department
2. Employees from that department load dynamically
3. Admin selects one or more employees
4. Admin enters task details (title, description, priority, dates)
5. Task is created in database
6. Task is assigned to selected employees
7. Activity is logged
8. Admin is redirected to manage tasks page

### AJAX Functionality:
- Loads employees without page refresh
- Makes request to `get_employees.php`
- Updates dropdown dynamically
- Handles errors gracefully

### Database Tables Used:
- `department` - Department list
- `employee_department` - Employee-department relationships
- `users` - Employee information
- `task_manage` - Task details
- `task_assign` - Task assignments
- `activity_logs` - Activity tracking

