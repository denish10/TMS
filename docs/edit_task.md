# File: edit_task.php

## Purpose

This file allows administrators to edit existing tasks assigned to employees. It provides a form to update task details including description, start date, end date, priority, and status. The file fetches current task data, validates updates, and saves changes to the database. It also includes logic to detect if no changes were made.

## Key Features

- Fetch and display current task details
- Update task description, dates, priority, and status
- Validation of all input fields
- Detection of no changes (prevents unnecessary updates)
- Read-only fields for task title and assigned employee
- Status dropdown with all available statuses
- Priority radio buttons
- Success/error message display
- Automatic redirect after successful update

## Code Breakdown

### 1. Session and Access Control

```php
session_start();

require_once __DIR__ . '/../../dbsetting/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}
```

**Explanation:**
- Validates user is logged in AND has admin role
- Redirects to login if not authorized
- Ensures only administrators can edit tasks

### 2. Get Task ID from URL

```php
$record_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$redirect_url = $_GET['redirect'] ?? 'manage_task.php';
```

**Explanation:**
- Gets task record ID from URL parameter
- Casts to integer for security
- Gets redirect URL (defaults to manage_task.php)
- Used to identify which task to edit

### 3. Fetch Current Task Details

```php
if ($record_id > 0) {
    $query = "
        SELECT 
            ta.record_id, 
            ta.status,
            tm.task_title, 
            tm.task_description, 
            tm.start_date, 
            tm.end_date, 
            tm.priority,
            u.fullname
        FROM task_assign ta
        JOIN task_manage tm ON ta.task_id = tm.task_id
        JOIN users u ON ta.users_id = u.users_id
        WHERE ta.record_id = $record_id
        LIMIT 1
    ";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $task = mysqli_fetch_assoc($result);
        // Extract task data into variables
    }
}
```

**Explanation:**
- Joins three tables to get complete task information
- Fetches task details, employee name, and assignment info
- Stores data in variables for form population
- Returns error if task not found

### 4. Form Submission Handling

```php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $record_id    = isset($_POST['record_id']) ? (int) $_POST['record_id'] : 0;
    $description  = trim($_POST['description'] ?? '');
    $start_date   = trim($_POST['start_date'] ?? '');
    $end_date     = trim($_POST['end_date'] ?? '');
    $priority     = trim($_POST['task_priority'] ?? '');
    $status       = trim($_POST['status'] ?? '');
```

**Explanation:**
- Checks if form is submitted via POST
- Retrieves all form data
- Trims whitespace from inputs
- Prepares data for validation and update

### 5. Input Validation

```php
$errors = [];
if ($description === '') {
    $errors[] = 'Description is required.';
}
$dateRegex = '/^\d{4}-\d{2}-\d{2}$/';
if (!preg_match($dateRegex, $start_date) || !preg_match($dateRegex, $end_date)) {
    $errors[] = 'Please provide valid dates (YYYY-MM-DD).';
} elseif (strtotime($start_date) > strtotime($end_date)) {
    $errors[] = 'Start date cannot be after end date.';
}
$allowedPriorities = ['High','Medium','Low'];
if (!in_array($priority, $allowedPriorities, true)) {
    $errors[] = 'Please choose a valid priority.';
}
$allowedStatuses = ['Not Started','In Progress','Completed','On Hold','Cancelled','Pending'];
if (!in_array($status, $allowedStatuses, true)) {
    $errors[] = 'Please choose a valid status.';
}
```

**Explanation:**
- Validates description is not empty
- Validates date format (YYYY-MM-DD)
- Validates start date is before end date
- Validates priority is one of allowed values
- Validates status is one of allowed values
- Collects all errors in array

### 6. No Changes Detection

```php
// Fetch current values to detect no-op edits
$currentSql = "
    SELECT tm.task_description, tm.start_date, tm.end_date, tm.priority, ta.status
    FROM task_assign ta
    JOIN task_manage tm ON ta.task_id = tm.task_id
    WHERE ta.record_id = $record_id
    LIMIT 1
";
$currentRes = mysqli_query($conn, $currentSql);
if ($currentRes && ($current = mysqli_fetch_assoc($currentRes))) {
    if (
        $current['task_description'] === $description &&
        $current['start_date'] === $start_date &&
        $current['end_date'] === $end_date &&
        $current['priority'] === $priority &&
        $current['status'] === $status
    ) {
        $infoMessage = 'No changes detected.';
    }
```

**Explanation:**
- Fetches current task values from database
- Compares new values with current values
- Shows info message if no changes detected
- Prevents unnecessary database updates

### 7. Database Update

```php
// Escape values for DB
$descEsc   = mysqli_real_escape_string($conn, $description);
$startEsc  = mysqli_real_escape_string($conn, $start_date);
$endEsc    = mysqli_real_escape_string($conn, $end_date);
$prioEsc   = mysqli_real_escape_string($conn, $priority);
$statusEsc = mysqli_real_escape_string($conn, $status);

// Update task_manage
$update1 = "
    UPDATE task_manage 
    SET task_description = '$descEsc',
        start_date = '$startEsc',
        end_date = '$endEsc',
        priority = '$prioEsc'
    WHERE task_id = (SELECT task_id FROM task_assign WHERE record_id = $record_id)
";

// Update task_assign
$update2 = "UPDATE task_assign SET status = '$statusEsc' WHERE record_id = $record_id";

if (mysqli_query($conn, $update1) && mysqli_query($conn, $update2)) {
    $successMessage = 'Task updated successfully. Redirecting...';
    $redirectAfter = 'view_task.php?id=' . $record_id;
}
```

**Explanation:**
- Escapes all values to prevent SQL injection
- Updates task_manage table (description, dates, priority)
- Updates task_assign table (status)
- Uses subquery to get task_id from record_id
- Shows success message and redirects on success

### 8. Edit Form Display

```php
<form method="post" action="">
    <div class="mb-3">
        <label class="form-label fw-bold">Task Title</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($task_title); ?>" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Assigned To</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($employee_name); ?>" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Description</label>
        <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
    </div>
```

**Explanation:**
- Displays task title (read-only)
- Displays assigned employee (read-only)
- Editable description textarea
- Pre-populated with current values
- Uses htmlspecialchars() for security

### 9. Status Dropdown

```php
<div class="mb-3">
    <label class="form-label fw-bold">Status</label>
    <select class="form-select" name="status" required>
        <option value="">Select Status</option>
        <option value="Not Started" <?php echo ($status == 'Not Started') ? 'selected' : ''; ?>>Not Started</option>
        <option value="In Progress" <?php echo ($status == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
        <option value="Completed" <?php echo ($status == 'Completed') ? 'selected' : ''; ?>>Completed</option>
        <option value="On Hold" <?php echo ($status == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
        <option value="Cancelled" <?php echo ($status == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
        <option value="Pending" <?php echo ($status == 'Pending') ? 'selected' : ''; ?>>Pending</option>
    </select>
</div>
```

**Explanation:**
- Dropdown with all available statuses
- Pre-selects current status
- Required field
- Easy status change

## Output / Result

**When the file runs:**

1. **Edit Task Form:**
   - Header: "✏️ Edit Task"
   - Task Title (read-only)
   - Assigned To (read-only)
   - Description (editable)
   - Start Date (editable)
   - End Date (editable)
   - Priority (radio buttons, editable)
   - Status (dropdown, editable)
   - Update and Back buttons

2. **After Successful Update:**
   - Shows success message: "Task updated successfully. Redirecting..."
   - Redirects to view_task.php after 2 seconds
   - Task data updated in database

3. **After Failed Update:**
   - Shows error messages for validation failures
   - Displays specific error for each validation issue
   - Form data preserved for correction

4. **No Changes Detected:**
   - Shows info message: "No changes detected."
   - Prevents unnecessary database update
   - User can make changes and resubmit

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies admin
- `$_SESSION['role']` - Validates admin access

### Database Logic:
- Updates two tables: task_manage and task_assign
- Uses JOIN to fetch current task data
- Uses subquery to update task_manage
- Updates status in task_assign table

### Validation:
- Description required
- Date format validation
- Date logic validation (start before end)
- Priority validation (High, Medium, Low)
- Status validation (6 allowed statuses)

### Security Features:
- SQL injection prevention
- XSS prevention
- Role-based access control
- Input validation

### For Presentation/Viva:
- **Explain:** Admin can edit task details
- **Highlight:** Updates both task_manage and task_assign tables
- **Mention:** No changes detection prevents unnecessary updates
- **Show:** Form validation and error handling
- **Demonstrate:** How task updates work

