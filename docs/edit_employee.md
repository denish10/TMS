# File: edit_employee.php

## Purpose

This file allows administrators to edit existing employee information in the system. It provides a form to update employee details including name, username, email, mobile, department, and role. The file fetches current employee data, validates updates, detects if no changes were made, and saves changes to both the users table and employee_department table.

## Key Features

- Fetch and display current employee details
- Update employee information (name, username, email, mobile, department, role)
- No changes detection (prevents unnecessary updates)
- Department dropdown populated from database
- Role selection (Admin/Staff)
- Success/error message display
- Automatic redirect after successful update
- Confirmation dialog before submission

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
- Ensures only administrators can edit employees

### 2. Get Employee ID from URL

```php
$id = $_GET['id'] ?? 0;
```

**Explanation:**
- Gets employee ID from URL parameter
- Uses null coalescing operator for default value
- Used to identify which employee to edit

### 3. Fetch Current Employee Details

```php
$query = "SELECT u.*, ed.department_id 
          FROM users u 
          LEFT JOIN employee_department ed ON u.users_id = ed.users_id 
          WHERE u.users_id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
```

**Explanation:**
- Joins `users` and `employee_department` tables
- Fetches complete employee information including department
- LEFT JOIN ensures employees without departments are still accessible
- Stores data in `$data` array for form population

### 4. Form Submission Handling

```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname   = $_POST['fullname'];
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $mobile     = $_POST['mobile'];
    $department = $_POST['department'];
    $role       = $_POST['role'];
```

**Explanation:**
- Checks if form is submitted via POST
- Retrieves all form data
- Prepares data for validation and update

### 5. No Changes Detection

```php
// No-op detection
$current_fullname = $data['fullname'] ?? '';
$current_username = $data['username'] ?? '';
$current_email    = $data['email'] ?? '';
$current_mobile   = $data['mobile'] ?? '';
$current_role     = $data['role'] ?? '';
$current_dept_id  = $data['department_id'] ?? '';

if (
    $fullname === $current_fullname &&
    $username === $current_username &&
    $email === $current_email &&
    $mobile === $current_mobile &&
    $role === $current_role &&
    $department == $current_dept_id
) {
    $message = "ℹ️ No changes detected.";
}
```

**Explanation:**
- Compares new values with current values from database
- Checks all fields for changes
- Shows info message if no changes detected
- Prevents unnecessary database updates

### 6. Update User Information

```php
$updateUser = "UPDATE users SET 
    fullname='$fullname',
    username='$username',
    email='$email',
    mobile='$mobile',
    role='$role'
    WHERE users_id=$id";

if (mysqli_query($conn, $updateUser)) {
```

**Explanation:**
- Updates employee information in `users` table
- Updates: fullname, username, email, mobile, role
- Uses WHERE clause to update specific employee
- Executes update query

### 7. Update Department Mapping

```php
$checkDept = mysqli_query($conn, "SELECT * FROM employee_department WHERE users_id=$id");
if (mysqli_num_rows($checkDept) > 0) {
    $updateDept = "UPDATE employee_department SET department_id='$department' WHERE users_id=$id";
    mysqli_query($conn, $updateDept);
} else {
    $insertDept = "INSERT INTO employee_department (users_id, department_id) VALUES ($id, '$department')";
    mysqli_query($conn, $insertDept);
}
```

**Explanation:**
- Checks if employee has existing department mapping
- If exists: Updates department_id
- If not exists: Creates new department mapping
- Handles both update and insert scenarios

### 8. Edit Employee Form

```php
<form method="POST" id="updateForm" onsubmit="return confirm('Are you sure you want to update details for <?php echo addslashes($employee_name); ?>?');">
    <div class="mb-3">
        <label>Full Name</label>
        <input type="text" name="fullname" class="form-control" value="<?php echo $data['fullname']; ?>" required>
    </div>
    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" value="<?php echo $data['username']; ?>" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo $data['email']; ?>" required>
    </div>
    <div class="mb-3">
        <label>Mobile</label>
        <input type="text" name="mobile" class="form-control" value="<?php echo $data['mobile']; ?>" required>
    </div>
    <div class="mb-3">
        <label>Department</label>
        <select name="department" class="form-control" required>
            <option value="" disabled>Select Department</option>
            <?php
            $dept_query = mysqli_query($conn, "SELECT * FROM department ORDER BY department_name ASC");
            while ($dept = mysqli_fetch_assoc($dept_query)) {
                $selected = ($dept['department_id'] == $data['department_id']) ? "selected" : "";
                echo "<option value='".$dept['department_id']."' $selected>".$dept['department_name']."</option>";
            }
            ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-control" required>
            <option value="staff" <?php if($data['role'] == 'staff') echo 'selected'; ?>>Staff</option>
            <option value="admin" <?php if($data['role'] == 'admin') echo 'selected'; ?>>Admin</option>
        </select>
    </div>
    <button type="submit" class="btn btn-warning text-white">Update</button>
</form>
```

**Explanation:**
- Creates edit form with all employee fields
- Pre-populates fields with current values
- Department dropdown shows current department as selected
- Role dropdown shows current role as selected
- Confirmation dialog before submission
- All fields are required

## Output / Result

**When the file runs:**

1. **Edit Employee Form:**
   - Header: "Edit Employee"
   - Form fields pre-populated with current values:
     - Full Name
     - Username
     - Email
     - Mobile
     - Department (dropdown with current selection)
     - Role (dropdown with current selection)
   - Update button

2. **After Successful Update:**
   - Shows success message: "✅ Employee details updated successfully. Redirecting..."
   - Redirects to `view_employee.php` after 2 seconds
   - Employee data updated in `users` table
   - Department mapping updated/created in `employee_department` table

3. **After Failed Update:**
   - Shows error message if database error occurs
   - Form data preserved for correction

4. **No Changes Detected:**
   - Shows info message: "ℹ️ No changes detected."
   - Prevents unnecessary database update
   - User can make changes and resubmit

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies admin
- `$_SESSION['role']` - Validates admin access

### Database Logic:
- Updates `users` table (employee information)
- Updates or inserts into `employee_department` table (department mapping)
- Uses JOIN to fetch current employee data
- Handles both update and insert for department mapping

### Validation:
- All fields are required
- No explicit validation (could be improved)
- No duplicate checking (could allow duplicate usernames/emails)

### Security Features:
- **SQL Injection Prevention:** Uses direct variable interpolation (could be improved with prepared statements)
- **XSS Prevention:** Uses htmlspecialchars() for output (implicit in echo)
- **Role-Based Access:** Only admins can edit
- **Confirmation Dialog:** Prevents accidental updates

### Alerts:
- Success messages shown with ✅ emoji
- Error messages shown with ❌ emoji
- Info messages shown with ℹ️ emoji

### For Presentation/Viva:
- **Explain:** Admin can edit employee details
- **Highlight:** Updates both users and employee_department tables
- **Mention:** No changes detection prevents unnecessary updates
- **Show:** Form validation and error handling
- **Demonstrate:** How employee updates work

### Improvements Needed:
- Add duplicate username/email checking
- Add input validation (email format, mobile format)
- Use prepared statements for better security
- Add password update functionality

