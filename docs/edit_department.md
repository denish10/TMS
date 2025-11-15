# File: edit_department.php

## Purpose

This file allows administrators to edit existing department names in the system. It provides a form to update department information, validates the input, detects if no changes were made, and saves changes to the database. The file fetches current department data and pre-populates the form.

## Key Features

- Fetch and display current department name
- Update department name
- No changes detection (prevents unnecessary updates)
- Input validation (department name required)
- Success/error message display
- Automatic redirect after successful update
- Confirmation dialog before submission
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
- Ensures only administrators can edit departments

### 2. Get Department ID

```php
$id = $_GET['id'] ?? 0;
```

**Explanation:**
- Gets department ID from URL parameter
- Uses null coalescing operator for default value
- Used to identify which department to edit

### 3. Fetch Current Department Data

```php
$query = "SELECT * FROM department WHERE department_id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
$department_name = $data['department_name'] ?? '';
```

**Explanation:**
- Queries `department` table for specific department
- Fetches department data by ID
- Extracts department name for form population
- Uses null coalescing operator for safety

### 4. Form Submission Handling

```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_name = trim($_POST['department_name']);
```

**Explanation:**
- Checks if form is submitted via POST
- Retrieves department name from form
- `trim()` removes whitespace from input

### 5. Input Validation

```php
if (!empty($department_name)) {
    // Update department
} else {
    $message = "⚠️ Department name cannot be empty.";
}
```

**Explanation:**
- Validates that department name is not empty
- Shows error message if validation fails
- Simple validation (could be enhanced)

### 6. No Changes Detection

```php
// No changes detection
if ($department_name === ($data['department_name'] ?? '')) {
    $message = "ℹ️ No changes detected.";
} else {
    // Update department
}
```

**Explanation:**
- Compares new department name with current name
- Shows info message if no changes detected
- Prevents unnecessary database updates
- Saves database resources

### 7. Database Update

```php
$updateQuery = "UPDATE department SET department_name = '$department_name' WHERE department_id = $id";
if (mysqli_query($conn, $updateQuery)) {
    $message = "✅ Department updated successfully. Redirecting...";
    echo '<meta http-equiv="refresh" content="2;url=manage_department.php">';
} else {
    $message = "❌ Error updating department: " . mysqli_error($conn);
}
```

**Explanation:**
- Updates department name in `department` table
- Uses WHERE clause to update specific department
- Shows success message on successful update
- Auto-redirects to manage departments page
- Shows error message if update fails

### 8. Edit Department Form

```php
<form method="POST" id="updateDeptForm" onsubmit="return confirmUpdate();">
    <div class="mb-3">
        <label for="department_name" class="form-label fw-bold">Department Name</label>
        <input type="text" name="department_name" id="department_name" class="form-control" value="<?php echo $department_name; ?>" required>
    </div>
    <div class="d-grid gap-2 col-4 mx-auto">
        <button type="submit" class="btn btn-warning">Update</button>
        <a href="manage_department.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>
```

**Explanation:**
- Creates edit form with department name field
- Pre-populates field with current department name
- `required` attribute ensures field is filled
- Update and Cancel buttons
- Confirmation dialog before submission

### 9. JavaScript Confirmation

```php
<script>
function confirmUpdate() {
    let deptName = document.getElementById("department_name").value;
    return confirm("Are you sure you want to update this department to '" + deptName + "'?");
}
</script>
```

**Explanation:**
- JavaScript function for confirmation dialog
- Gets department name from form
- Shows confirmation with new department name
- Returns true/false to allow/prevent submission
- Prevents accidental updates

## Output / Result

**When the file runs:**

1. **Edit Department Form:**
   - Header: "Edit Department"
   - Form field pre-populated with current department name
   - Update and Cancel buttons

2. **After Successful Update:**
   - Shows success message: "✅ Department updated successfully. Redirecting..."
   - Automatically redirects to `manage_department.php` after 2 seconds
   - Department name updated in database

3. **After Failed Update:**
   - Shows error message if database error occurs
   - Form data preserved for correction

4. **No Changes Detected:**
   - Shows info message: "ℹ️ No changes detected."
   - Prevents unnecessary database update
   - User can make changes and resubmit

5. **User Experience:**
   - Confirmation dialog before submission
   - Clear validation feedback
   - Smooth redirect after success

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies admin
- `$_SESSION['role']` - Validates admin access

### Database Logic:
- Updates `department` table
- Simple single-table update
- Uses WHERE clause to update specific department

### Validation:
- **Required Field:** Department name must be filled
- **No Changes Detection:** Prevents unnecessary updates
- **Simple Validation:** Only checks for empty field
- **Could Be Enhanced:** Add duplicate checking, length validation

### Security Features:
- **SQL Injection Prevention:** Uses direct variable (could be improved with prepared statements)
- **Role-Based Access:** Only admins can edit
- **Session Validation:** Ensures authenticated access
- **Confirmation Dialog:** Prevents accidental updates
- **Input Validation:** Validates department name is not empty

### Alerts:
- Success messages shown with ✅ emoji
- Error messages shown with ❌ emoji
- Warning messages shown with ⚠️ emoji
- Info messages shown with ℹ️ emoji

### For Presentation/Viva:
- **Explain:** Admin can edit department names
- **Highlight:** No changes detection prevents unnecessary updates
- **Mention:** Simple update operation
- **Show:** Form validation and error handling
- **Demonstrate:** How department updates work
- **Discuss:** Confirmation dialog for safety

### Improvements Needed:
- Add duplicate department name checking
- Add department name length validation
- Add special character validation
- Use prepared statements for better security
- Add validation for department name format

### Department Update Impact:
- Updating department name affects:
  - Employee-department relationships (display only)
  - Task assignment filtering (if department name is used)
  - Reports and statistics
- Department ID remains unchanged (maintains relationships)

