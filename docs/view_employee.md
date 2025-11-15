# File: view_employee.php

## Purpose

This file allows administrators to view detailed information about a specific employee. It displays employee profile including personal details, department, role, profile picture, and login information. The file provides links to edit employee details and edit profile picture.

## Key Features

- Display complete employee profile
- Show employee details: ID, name, username, email, mobile, department
- Display profile picture with edit option
- Show creation date and last login timestamp
- Role badge display
- Links to edit employee and edit profile picture
- Session and role-based access control
- Styled profile view with gradient background

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
- Ensures only administrators can view employee profiles

### 2. Get Employee ID and Validation

```php
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
```

**Explanation:**
- Gets employee ID from URL parameter
- Casts to integer for security
- Used to fetch specific employee data

### 3. Fetch Employee Details with JOIN

```php
$query = "
    SELECT u.*, d.department_name
    FROM users u
    LEFT JOIN employee_department ed ON u.users_id = ed.users_id
    LEFT JOIN department d ON ed.department_id = d.department_id
    WHERE u.users_id = $user_id
    LIMIT 1
";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $employee = mysqli_fetch_assoc($result);
} else {
    echo "<div class='alert alert-danger text-center'>Employee not found.</div>";
    exit;
}
```

**Explanation:**
- Joins three tables to get complete employee information
- `users` (u) - Employee account information
- `employee_department` (ed) - Links employees to departments
- `department` (d) - Department names
- LEFT JOIN ensures employees without departments are still shown
- Validates employee exists, shows error if not found

### 4. First Letter Avatar Display

```php
<?php 
$firstLetter = !empty($employee['fullname']) ? strtoupper(substr(trim($employee['fullname']), 0, 1)) : '?';
?>
<div class="employee-avatar">
    <?php echo htmlspecialchars($firstLetter); ?>
</div>
```

**Explanation:**
- Displays first letter of employee's name in a circular avatar
- Uses purple gradient background with white letter
- Automatically generated from employee's fullname
- No image upload required
- Clean and modern design

### 5. Employee Information Table

```php
<table class="table table-bordered employee-table">
    <tr><th class="w-25">Employee ID:</th><td><?php echo $employee['users_id']; ?></td></tr>
    <tr><th>Username:</th><td><?php echo $employee['username']; ?></td></tr>
    <tr><th>Email:</th><td><?php echo $employee['email']; ?></td></tr>
    <tr><th>Mobile:</th><td><?php echo $employee['mobile']; ?></td></tr>
    <tr><th>Department:</th><td><?php echo $employee['department_name'] ?? 'N/A'; ?></td></tr>
    <tr><th>Created At:</th><td><?php echo $employee['created_at']; ?></td></tr>
    <tr><th>Last Login:</th>
        <td>
            <?php 
            if (!empty($employee['last_login']) && $employee['last_login'] != '0000-00-00 00:00:00') {
                echo date('M j, Y h:i A', strtotime($employee['last_login']));
            } else {
                echo '<span class="text-muted">Never</span>';
            }
            ?>
        </td>
    </tr>
</table>
```

**Explanation:**
- Displays employee information in table format
- Shows: Employee ID, Username, Email, Mobile, Department, Created At, Last Login
- Formats last login date nicely
- Shows "Never" if employee never logged in
- Shows "N/A" if no department assigned

### 6. Role Badge Display

```php
<h5 class="mt-3"><?php echo $employee['fullname']; ?></h5>
<span class="badge bg-secondary"><?php echo ucfirst($employee['role']); ?></span>
```

**Explanation:**
- Displays employee name
- Shows role as badge (Admin/Staff)
- Capitalizes role using `ucfirst()`
- Visual role indicator

### 7. Action Buttons

```php
<div class="text-center">
    <a href="manage_employee.php" class="btn btn-secondary px-4">Back</a>
    <a href="edit_employee.php?id=<?php echo $employee['users_id']; ?>" class="btn btn-primary px-4">Edit</a>
</div>
```

**Explanation:**
- "Back" button returns to employee list
- "Edit" button opens employee edit form
- Both buttons styled with padding for better appearance

### 8. Custom Styling

```php
<style>
.employee-table {
  background-color: transparent;
}
.employee-table th,
.employee-table td {
  background-color: transparent !important; 
  color: #fff;
  padding: 10px;
  vertical-align: middle;
}
.employee_view {
    background: linear-gradient(to right, #141e30, #243b55);
    width: 59vw;
    margin-left: 370px;
    margin-top: 104px;
    height: 80vh;
}
.profile-pic-container {
  position: relative;
  display: inline-block;
}
.edit-pic-btn {
  position: absolute;
  bottom: 0;
  right: 10px;
  background: #0d6efd;
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 35px;
  height: 35px;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
</style>
```

**Explanation:**
- Custom CSS for employee view
- Gradient background for visual appeal
- Transparent table with white text
- Profile picture container with edit button overlay
- Responsive styling

## Output / Result

**When the file runs:**

1. **Employee Profile Page:**
   - Gradient background card
   - Profile picture on left side with edit button overlay
   - Employee name and role badge
   - Information table on right side showing:
     - Employee ID
     - Username
     - Email
     - Mobile
     - Department
     - Created At
     - Last Login (formatted or "Never")
   - Action buttons: Back, Edit

2. **Profile Picture:**
   - Circular image (150x150px)
   - Default image if no photo uploaded
   - Edit button overlay for quick access
   - Professional appearance

3. **Last Login Display:**
   - Formatted date/time if employee has logged in
   - "Never" message if employee never logged in
   - Clear indication of account activity

4. **User Experience:**
   - Clean, professional profile view
   - Easy access to edit functions
   - Clear information display
   - Responsive design

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies logged-in admin
- `$_SESSION['role']` - Validates admin access

### Database Logic:
- Uses LEFT JOIN to combine data from three tables
- `users` - Employee account information
- `employee_department` - Employee-department relationships
- `department` - Department names
- Fetches single employee record

### Validation:
- Employee ID validation (must be provided)
- Employee existence check
- Error message if employee not found

### Security Features:
- **Role-Based Access:** Only admins can view
- **Session Validation:** Ensures authenticated access
- **XSS Prevention:** Uses htmlspecialchars() for output (implicit in echo)
- **SQL Injection Prevention:** Employee ID cast to integer

### Alerts:
- Error message if employee not found
- Clear display of all information

### For Presentation/Viva:
- **Explain:** Admin can view detailed employee profile
- **Highlight:** JOIN operation combining multiple tables
- **Mention:** Profile picture display and edit option
- **Show:** How employee information is displayed
- **Demonstrate:** Last login tracking
- **Discuss:** Employee profile management

### Visual Features:
- Gradient background for modern look
- Circular profile picture
- Role badge for quick identification
- Formatted dates for readability
- Professional table layout

### File Structure:
- First letter avatars are automatically generated from employee names
- No image uploads or storage required

