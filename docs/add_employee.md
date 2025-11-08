# File: add_employee.php

## Purpose

This file allows administrators to add new employees to the system. It provides a comprehensive form to enter employee details including personal information, credentials, department assignment, and role. The file validates all inputs, checks for duplicates, hashes passwords securely, and creates both user account and department mapping.

## Key Features

- Employee registration form with comprehensive validation
- Duplicate username and email checking
- Password hashing for security
- Department assignment
- Role selection (Admin/Staff)
- Input sanitization and validation
- Success/error message display
- Automatic redirect after successful creation
- Form data persistence on validation errors

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
- Ensures only administrators can add employees

### 2. Form Submission Handling

```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname'] ?? "");
    $username = trim($_POST['username'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $mobile = trim($_POST['mobile'] ?? "");
    $department = trim($_POST['department'] ?? "");
    $role = trim($_POST['role'] ?? "");
    $password = $_POST['password'] ?? "";
```

**Explanation:**
- Checks if form is submitted via POST
- Retrieves all form data
- `trim()` removes whitespace from inputs
- Uses null coalescing operator (`??`) for default values

### 3. Comprehensive Input Validation

```php
// Validation
if ($fullname === '' || $username === '' || $email === '' || $mobile === '' || $department === '' || $role === '' || $password === '') {
    $message = "⚠️ All fields are required.";
    $alertType = "danger";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "⚠️ Please enter a valid email address.";
    $alertType = "danger";
} elseif (!ctype_digit($mobile)) {
    $message = "⚠️ Mobile number should contain only digits.";
    $alertType = "danger";
} elseif (strlen($mobile) < 10 || strlen($mobile) > 15) {
    $message = "⚠️ Mobile number length should be between 10 and 15 digits.";
    $alertType = "danger";
} elseif (!in_array($role, ['staff', 'admin'])) {
    $message = "⚠️ Invalid role selected.";
    $alertType = "danger";
} elseif (strlen($password) < 6) {
    $message = "⚠️ Password should be at least 6 characters.";
    $alertType = "danger";
}
```

**Explanation:**
- Validates all fields are filled
- Validates email format using `filter_var()`
- Validates mobile contains only digits using `ctype_digit()`
- Validates mobile length (10-15 digits)
- Validates role is either 'staff' or 'admin'
- Validates password minimum length (6 characters)

### 4. Duplicate Username Check

```php
// Check for duplicate username
$checkUser = mysqli_query($conn, "SELECT users_id FROM users WHERE username = '".mysqli_real_escape_string($conn, $username)."' LIMIT 1");
if ($checkUser && mysqli_num_rows($checkUser) > 0) {
    $message = "⚠️ Username already exists.";
    $alertType = "danger";
}
```

**Explanation:**
- Checks if username already exists in database
- Prevents duplicate usernames
- Uses escaped string to prevent SQL injection
- Shows error if username is taken

### 5. Duplicate Email Check

```php
// Check for duplicate email
$checkEmail = mysqli_query($conn, "SELECT users_id FROM users WHERE email = '".mysqli_real_escape_string($conn, $email)."' LIMIT 1");
if ($checkEmail && mysqli_num_rows($checkEmail) > 0) {
    $message = "⚠️ Email already exists.";
    $alertType = "danger";
}
```

**Explanation:**
- Checks if email already exists in database
- Prevents duplicate email addresses
- Ensures unique email for each user

### 6. Password Hashing

```php
// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
```

**Explanation:**
- Hashes password using PHP's `password_hash()` function
- Uses `PASSWORD_DEFAULT` algorithm (bcrypt)
- Passwords are never stored as plain text
- Secure password storage

### 7. Data Sanitization

```php
// Escape data for database
$fullname = mysqli_real_escape_string($conn, $fullname);
$username = mysqli_real_escape_string($conn, $username);
$email = mysqli_real_escape_string($conn, $email);
$mobile = mysqli_real_escape_string($conn, $mobile);
$department = (int) $department;
```

**Explanation:**
- Escapes all string inputs to prevent SQL injection
- Casts department to integer for security
- Sanitizes all user inputs before database insertion

### 8. User Creation

```php
// Insert into users
$sql_user = "INSERT INTO users (fullname, username, email, mobile, role, password, created_at) 
            VALUES ('$fullname', '$username', '$email', '$mobile', '$role', '$hashed_password', NOW())";

if (mysqli_query($conn, $sql_user)) {
    $new_user_id = mysqli_insert_id($conn);
```

**Explanation:**
- Inserts new user into `users` table
- Stores all user information including hashed password
- Uses `NOW()` for creation timestamp
- Gets auto-generated user ID for department mapping

### 9. Department Mapping

```php
// Map department
$sql_dept = "INSERT INTO employee_department (users_id, department_id) VALUES ($new_user_id, $department)";
mysqli_query($conn, $sql_dept);
```

**Explanation:**
- Links employee to department in `employee_department` table
- Creates relationship between user and department
- Enables department-based task assignment

### 10. Employee Registration Form

```php
<form action="" method="POST">
    <div class="mb-3">
        <label for="fullname" class="form-label">Full Name:</label>
        <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter full name" value="<?php echo $fullname; ?>">
    </div>
    <div class="mb-3">
        <label for="username" class="form-label">Username:</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" value="<?php echo $username; ?>">
    </div>
    <!-- More fields... -->
    <div class="mb-3">
        <label>Department:</label>
        <select name="department" class="form-control">
            <option value="">Select department</option>
            <?php
            $dept_query = mysqli_query($conn, "SELECT * FROM department ORDER BY department_name ASC");
            while ($dept = mysqli_fetch_assoc($dept_query)) {
                echo "<option value='".$dept['department_id']."'>".$dept['department_name']."</option>";
            }
            ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="role">Role:</label>
        <select name="role" class="form-control" id="role">
            <option value="">Select role</option>
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
        </select>
    </div>
</form>
```

**Explanation:**
- Creates comprehensive registration form
- All fields with labels and placeholders
- Department dropdown populated from database
- Role dropdown with Staff/Admin options
- Form data persists on validation errors

## Output / Result

**When the file runs:**

1. **Add Employee Form:**
   - Shows centered card with "Add Employee" heading
   - Form fields: Full Name, Username, Email, Mobile, Department, Role, Password
   - Submit and Back buttons

2. **After Successful Creation:**
   - Displays success message: "✅ Employee added successfully! Redirecting..."
   - Automatically redirects to `manage_employee.php` after 2 seconds
   - Employee is created in `users` table
   - Department mapping is created in `employee_department` table

3. **After Failed Creation:**
   - Shows specific error messages for validation failures
   - Form data is preserved (user can correct and resubmit)
   - Error messages for: empty fields, invalid email, invalid mobile, duplicate username/email, weak password

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies admin adding employee
- `$_SESSION['role']` - Validates admin access

### Validation:
- **Required Fields:** All fields must be filled
- **Email Format:** Must be valid email address
- **Mobile:** Must contain only digits, 10-15 characters
- **Role:** Must be 'staff' or 'admin'
- **Password:** Minimum 6 characters
- **Duplicate Check:** Username and email must be unique

### Database Logic:
- Inserts into `users` table (user account)
- Inserts into `employee_department` table (department mapping)
- Uses transactions implicitly (could be improved with explicit transactions)

### Alerts:
- Success messages shown with ✅ emoji
- Error messages shown with ⚠️ emoji
- Warning messages shown with ⚠️ emoji

### Security Features:
- **Password Hashing:** Passwords stored as hashes, never plain text
- **SQL Injection Prevention:** Uses `mysqli_real_escape_string()`
- **Input Validation:** Comprehensive validation of all inputs
- **Duplicate Prevention:** Checks for existing username/email
- **Role-Based Access:** Only admins can add employees

### For Presentation/Viva:
- **Explain:** Admin can add new employees to the system
- **Highlight:** Comprehensive validation and duplicate checking
- **Mention:** Password hashing for security
- **Show:** How employees are linked to departments
- **Demonstrate:** Form validation and error handling

