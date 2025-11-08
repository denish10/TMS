# File: delete_department.php

## Purpose

This file allows administrators to delete departments from the system. It includes safety checks to prevent deletion of departments that have employees assigned to them, ensuring data integrity. The file provides appropriate error handling and user feedback.

## Key Features

- Department deletion with safety checks
- Prevents deletion if employees are assigned
- Error handling and user feedback
- Success/error message display
- Automatic redirect after deletion
- Session and role-based access control
- Data integrity protection

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
- Ensures only administrators can delete departments

### 2. Get Department ID

```php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
```

**Explanation:**
- Gets department ID from URL parameter
- Casts to integer for security
- Used to identify which department to delete

### 3. Safety Check - Employee Assignment

```php
if ($id > 0) {
    // Check if any employees are assigned to this department
    $check = "SELECT COUNT(*) AS cnt FROM employee_department WHERE department_id = $id";
    $result = mysqli_query($conn, $check);
    $row = mysqli_fetch_assoc($result);

    if ($row['cnt'] > 0) {
        $message = 'Cannot delete department. Employees are still assigned to it.';
        $alertType = 'warning';
    }
```

**Explanation:**
- Checks if any employees are assigned to the department
- Queries `employee_department` table for count
- Prevents deletion if employees exist
- Shows warning message if deletion is blocked
- Protects data integrity

### 4. Department Deletion

```php
// Delete department if no employees assigned
if ($message === '') {
    $query = "DELETE FROM department WHERE department_id = $id";
    if (mysqli_query($conn, $query)) {
        $message = 'Department deleted successfully! Redirecting...';
        $alertType = 'success';
        $redirectUrl = 'manage_department.php?msg=deleted';
    } else {
        $message = 'Error deleting department: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8');
        $alertType = 'danger';
    }
}
```

**Explanation:**
- Only deletes if no employees assigned (message is empty)
- Deletes department from `department` table
- Shows success message on successful deletion
- Auto-redirects to manage departments page
- Shows error message if deletion fails
- Uses `htmlspecialchars()` for XSS prevention

### 5. Invalid ID Handling

```php
} else {
    $message = 'Invalid department ID.';
    $alertType = 'danger';
}
```

**Explanation:**
- Handles case where department ID is invalid or missing
- Shows error message
- Prevents invalid deletion attempts

### 6. Success/Error Display

```php
<div class="container" style="max-width: 700px; margin-top: 80px;">
  <div class="card p-4">
    <?php if (!empty($message)) { ?>
      <div class="alert alert-<?php echo $alertType; ?>" role="alert">
        <?php echo $message; ?>
      </div>
      <meta http-equiv="refresh" content="2;url=<?php echo $redirectUrl; ?>">
      <div class="mt-3">
        <a href="<?php echo $redirectUrl; ?>" class="btn btn-secondary">Back to Manage Departments</a>
      </div>
    <?php } ?>
  </div>
</div>
```

**Explanation:**
- Displays success or error message
- Shows appropriate alert color (success/warning/danger)
- Auto-redirects after 2 seconds
- Provides manual back button
- Clean, user-friendly interface

## Output / Result

**When the file runs:**

1. **Deletion Process:**
   - Checks if employees are assigned to department
   - If employees exist: Shows warning and prevents deletion
   - If no employees: Proceeds with deletion
   - Deletes department from database

2. **After Successful Deletion:**
   - Displays success message: "Department deleted successfully! Redirecting..."
   - Automatically redirects to `manage_department.php` after 2 seconds
   - Department is removed from database

3. **After Failed Deletion (Employees Assigned):**
   - Displays warning message: "Cannot delete department. Employees are still assigned to it."
   - Prevents deletion to maintain data integrity
   - User must reassign employees before deletion

4. **After Failed Deletion (Database Error):**
   - Displays error message with details
   - User can try again

5. **User Experience:**
   - Clear feedback on deletion status
   - Safety checks prevent data loss
   - Automatic redirect
   - Manual back button available

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies admin
- `$_SESSION['role']` - Validates admin access

### Database Logic:
- Checks `employee_department` table for assigned employees
- Deletes from `department` table if safe
- Maintains referential integrity

### Validation:
- **Department ID Validation:** Must be provided and valid
- **Employee Assignment Check:** Prevents deletion if employees assigned
- **Safety First:** Data integrity over convenience

### Security Features:
- **Role-Based Access:** Only admins can delete
- **Session Validation:** Ensures authenticated access
- **Safety Checks:** Prevents data loss
- **XSS Prevention:** Uses `htmlspecialchars()` for error messages

### Alerts:
- Success messages shown in green alert
- Warning messages shown in yellow alert
- Error messages shown in red alert
- Clear feedback to user

### For Presentation/Viva:
- **Explain:** Admin can delete departments from system
- **Highlight:** Safety checks prevent deletion if employees assigned
- **Mention:** Data integrity protection
- **Show:** How safety checks work
- **Demonstrate:** Error handling and user feedback
- **Discuss:** Importance of maintaining referential integrity

### Safety Mechanism:
- **Prevention:** Checks before deletion
- **Protection:** Prevents orphaned employee records
- **User-Friendly:** Clear error messages
- **Data Integrity:** Maintains database consistency

### Deletion Workflow:
1. Admin clicks delete on department
2. System checks for assigned employees
3. If employees exist: Show warning, prevent deletion
4. If no employees: Proceed with deletion
5. Delete department from database
6. Redirect to manage departments page

### Important Notes:
- **Irreversible:** Deletion is permanent (no undo)
- **Safe:** Only deletes if no employees assigned
- **Protected:** Data integrity is maintained
- **Clear:** User gets clear feedback

### Why Safety Check is Important:
- Prevents orphaned employee records
- Maintains referential integrity
- Prevents data inconsistencies
- Protects against accidental data loss
- Ensures database remains in valid state

### Alternative Approaches:
- Could use CASCADE DELETE (not recommended - loses employee data)
- Could reassign employees automatically (complex)
- Current approach is safest: require manual reassignment

