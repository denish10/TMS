# File: add_department.php

## Purpose

This file allows administrators to add new departments to the system. It provides a simple form to enter department name, validates the input, and stores it in the database. Departments are used to organize employees and enable department-based task assignment.

## Key Features

- Simple department creation form
- Input validation (department name required)
- Success/error message display
- Automatic redirect after successful creation
- Session and role-based access control
- Clean and minimal interface

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
- Ensures only administrators can add departments

### 2. Form Submission Handling

```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department_name = trim($_POST['department_name']);
```

**Explanation:**
- Checks if form is submitted via POST
- Retrieves department name from form
- `trim()` removes whitespace from input

### 3. Input Validation

```php
if (!empty($department_name)) {
    // Insert department
} else {
    $message = "⚠️ Department name is required!";
}
```

**Explanation:**
- Validates that department name is not empty
- Shows error message if validation fails
- Simple validation (could be enhanced with length checks, special character validation)

### 4. Database Insertion

```php
$sql = "INSERT INTO department (department_name) VALUES ('$department_name')";
if (mysqli_query($conn, $sql)) {
    $message = "✅ Department added successfully! Redirecting...";
    $redirect = true;
} else {
    $message = "❌ Error: " . mysqli_error($conn);
}
```

**Explanation:**
- Inserts new department into `department` table
- Stores only department name (ID is auto-generated)
- Shows success message on successful insertion
- Shows error message if insertion fails
- Sets redirect flag for automatic redirect

### 5. Automatic Redirect

```php
<?php if ($redirect): ?>
    <meta http-equiv="refresh" content="2;url=manage_department.php">
<?php endif; ?>
```

**Explanation:**
- Automatically redirects to manage departments page after 2 seconds
- Only redirects on successful creation
- Provides smooth user experience

### 6. Add Department Form

```php
<form action="" method="POST">
    <div class="mb-3">
        <label for="department_name" class="form-label fw-bold">Department Name</label>
        <input type="text" class="form-control" id="department_name" name="department_name" placeholder="Enter department name" required>
    </div>
    <div class="d-grid gap-2 col-4 mx-auto">
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="manage_department.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>
```

**Explanation:**
- Creates simple form with single input field
- Department name input with placeholder
- `required` attribute ensures field is filled
- Submit and Cancel buttons
- Centered button layout

## Output / Result

**When the file runs:**

1. **Add Department Form:**
   - Shows centered card with "Add Department" heading
   - Single input field: Department Name
   - Submit and Cancel buttons

2. **After Successful Creation:**
   - Displays success message: "✅ Department added successfully! Redirecting..."
   - Automatically redirects to `manage_department.php` after 2 seconds
   - Department is stored in `department` table

3. **After Failed Creation:**
   - Shows error messages:
     - "⚠️ Department name is required!" (if field is empty)
     - "❌ Error: [error message]" (if database error occurs)

4. **User Experience:**
   - Simple, clean interface
   - Clear validation feedback
   - Smooth redirect after success

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies admin adding department
- `$_SESSION['role']` - Validates admin access

### Validation:
- **Required Field:** Department name must be filled
- **Simple Validation:** Only checks for empty field
- **Could Be Enhanced:** Add length validation, duplicate checking, special character validation

### Database Logic:
- Inserts into `department` table
- Stores: department_name (department_id is auto-generated)
- Simple single-table insertion

### Alerts:
- Success messages shown with ✅ emoji
- Error messages shown with ❌ emoji
- Warning messages shown with ⚠️ emoji

### Security Features:
- **SQL Injection Prevention:** Uses direct variable (could be improved with prepared statements)
- **Role-Based Access:** Only admins can add departments
- **Session Validation:** Ensures authenticated access
- **Input Validation:** Validates department name is not empty

### For Presentation/Viva:
- **Explain:** Admin can add new departments to organize employees
- **Highlight:** Simple form with single field
- **Mention:** Departments are used for task assignment
- **Show:** How departments are created and stored
- **Demonstrate:** Form validation and error handling

### Department Usage:
- Departments are used to:
  - Organize employees
  - Filter employees when assigning tasks
  - Group employees for reporting
  - Manage organizational structure

### Improvements Needed:
- Add duplicate department name checking
- Add department name length validation
- Add special character validation
- Use prepared statements for better security
- Add activity logging

