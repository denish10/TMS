# File: delete_employee.php

## Purpose

This file allows administrators to delete employees from the system. It handles cascading deletion by removing all related records (leave applications, task assignments, department mappings) before deleting the employee account. The file uses database transactions to ensure data integrity and provides appropriate error handling.

## Key Features

- Employee deletion with cascading deletes
- Transaction-based deletion for data integrity
- Removes related records (leaves, tasks, department mappings)
- Error handling and rollback on failure
- Success/error message display
- Automatic redirect after deletion
- Session and role-based access control

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
- Ensures only administrators can delete employees

### 2. Get Employee ID

```php
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
```

**Explanation:**
- Gets employee ID from URL parameter
- Casts to integer for security
- Used to identify which employee to delete

### 3. Database Transaction Start

```php
// Use a transaction to safely remove dependent rows then the user
mysqli_begin_transaction($conn);
```

**Explanation:**
- Starts database transaction
- Ensures all deletions succeed or none do (atomicity)
- Prevents partial deletions if error occurs
- Maintains data integrity

### 4. Cascading Deletion - Leave Applications

```php
// Remove dependent records referencing users_id
mysqli_query($conn, "DELETE FROM leave_apply WHERE users_id = $id");
```

**Explanation:**
- Deletes all leave applications by this employee
- Prevents orphaned records in `leave_apply` table
- Maintains referential integrity

### 5. Cascading Deletion - Task Assignments

```php
mysqli_query($conn, "DELETE FROM task_assign WHERE users_id = $id");
```

**Explanation:**
- Deletes all task assignments for this employee
- Prevents orphaned records in `task_assign` table
- Removes employee from all assigned tasks

### 6. Cascading Deletion - Department Mapping

```php
mysqli_query($conn, "DELETE FROM employee_department WHERE users_id = $id");
```

**Explanation:**
- Deletes department mapping for this employee
- Removes employee-department relationship
- Prevents orphaned records in `employee_department` table

### 7. Delete Employee Account

```php
// Finally remove the user
$result = mysqli_query($conn, "DELETE FROM users WHERE users_id = $id");

if (!$result) {
    throw new Exception(mysqli_error($conn));
}
```

**Explanation:**
- Deletes employee from `users` table
- This is the final step after removing all dependencies
- Throws exception if deletion fails
- Ensures user is only deleted if all related records are removed

### 8. Transaction Commit

```php
mysqli_commit($conn);
$message = 'Employee deleted successfully! Redirecting...';
$alertType = 'success';
```

**Explanation:**
- Commits transaction if all deletions succeed
- Makes all changes permanent
- Sets success message
- All deletions are now final

### 9. Transaction Rollback on Error

```php
} catch (Exception $e) {
    mysqli_rollback($conn);
    $message = 'Error deleting employee: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    $alertType = 'danger';
}
```

**Explanation:**
- Catches any exceptions during deletion
- Rolls back transaction (undoes all changes)
- Prevents partial deletions
- Displays error message
- Maintains data integrity

### 10. Success/Error Display

```php
<div class="container" style="max-width: 700px; margin-top: 80px;">
  <div class="card p-4">
    <?php if (!empty($message)) { ?>
      <div class="alert alert-<?php echo $alertType; ?>" role="alert">
        <?php echo $message; ?>
      </div>
      <meta http-equiv="refresh" content="2;url=<?php echo $redirectUrl; ?>">
      <div class="mt-3">
        <a href="<?php echo $redirectUrl; ?>" class="btn btn-secondary">Back to Manage Employees</a>
      </div>
    <?php } ?>
  </div>
</div>
```

**Explanation:**
- Displays success or error message
- Shows appropriate alert color (success/danger)
- Auto-redirects after 2 seconds
- Provides manual back button

## Output / Result

**When the file runs:**

1. **Deletion Process:**
   - Starts database transaction
   - Deletes all leave applications by employee
   - Deletes all task assignments for employee
   - Deletes department mapping
   - Deletes employee account
   - Commits transaction if successful

2. **After Successful Deletion:**
   - Displays success message: "Employee deleted successfully! Redirecting..."
   - Automatically redirects to `manage_employee.php` after 2 seconds
   - All related records are removed
   - Employee account is deleted

3. **After Failed Deletion:**
   - Displays error message with details
   - Transaction is rolled back (no changes made)
   - All data remains intact
   - User can try again

4. **User Experience:**
   - Clean deletion process
   - Clear success/error messages
   - Automatic redirect
   - Manual back button available

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies admin
- `$_SESSION['role']` - Validates admin access

### Database Logic:
- Uses transactions for data integrity
- Cascading deletion removes all related records
- Deletes from: `leave_apply`, `task_assign`, `employee_department`, `users`
- Ensures no orphaned records remain

### Validation:
- Employee ID validation (must be provided)
- Transaction ensures all-or-nothing deletion
- Error handling prevents partial deletions

### Security Features:
- **Role-Based Access:** Only admins can delete
- **Session Validation:** Ensures authenticated access
- **Transaction Safety:** Prevents partial deletions
- **Error Handling:** Catches and handles errors gracefully

### Alerts:
- Success messages shown in green alert
- Error messages shown in red alert
- Clear feedback to user

### For Presentation/Viva:
- **Explain:** Admin can delete employees from system
- **Highlight:** Transaction-based deletion for data integrity
- **Mention:** Cascading deletion removes all related records
- **Show:** How transactions ensure data consistency
- **Demonstrate:** Error handling and rollback mechanism
- **Discuss:** Importance of maintaining referential integrity

### Transaction Benefits:
- **Atomicity:** All deletions succeed or none do
- **Consistency:** Database remains in valid state
- **Isolation:** Other operations don't see partial changes
- **Durability:** Changes persist after commit

### Deletion Order:
1. Leave applications (most dependent)
2. Task assignments (dependent)
3. Department mapping (relationship)
4. User account (main record)

This order ensures foreign key constraints are satisfied.

### Important Notes:
- **Irreversible:** Deletion is permanent (no undo)
- **Cascading:** All related data is removed
- **Safe:** Transaction ensures no partial deletions
- **Complete:** All employee data is removed from system

