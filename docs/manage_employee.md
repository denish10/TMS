# File: manage_employee.php

## Purpose

This file allows administrators to view and manage all employees in the system. It displays a comprehensive table showing employee details including ID, name, username, email, mobile, department, and role. The file provides action buttons to view, edit, reset password, and delete employees, along with a search functionality to filter employees quickly.

## Key Features

- Display all employees in a table format
- Show employee details: ID, name, username, email, mobile, department, role
- Search functionality to filter employees
- Action buttons: View, Edit, Reset Password, Delete
- Department information display
- Role display (Admin/Staff)
- Confirmation dialog before deletion
- Session and role-based access control
- Real-time search filtering

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
- Ensures only administrators can manage employees

### 2. Database Query with JOIN

```php
$query = "
    SELECT u.users_id, u.fullname, u.username, u.email, u.mobile,
           d.department_name, u.role
    FROM users u
    LEFT JOIN employee_department ed ON u.users_id = ed.users_id
    LEFT JOIN department d ON ed.department_id = d.department_id
    ORDER BY u.users_id ASC
";
```

**Explanation:**
- Joins three tables to get complete employee information
- `users` (u) - Employee account information
- `employee_department` (ed) - Links employees to departments
- `department` (d) - Department names
- LEFT JOIN ensures employees without departments are still shown
- Orders by user ID (ascending)

### 3. Search Input Field

```php
<div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by name, username, email, or department...">
</div>
```

**Explanation:**
- Creates search input field
- Uses Bootstrap styling
- Placeholder guides user on searchable fields
- JavaScript will filter table rows
- Real-time search functionality

### 4. Employee Table Display

```php
<table class="table table-bordered table-striped table-hover" id="employee_table">
    <thead class="table-dark">
      <tr>
        <th>S.No</th>
        <th>Employee ID</th>
        <th>Full Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Department</th>
        <th>Role</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      while ($row = mysqli_fetch_assoc($query_run)) {
      ?>
        <tr>
          <td><?php echo $sno++; ?></td>
          <td><?php echo $row['users_id']; ?></td>
          <td><?php echo $row['fullname']; ?></td>
          <td><?php echo $row['username']; ?></td>
          <td><?php echo $row['email']; ?></td>
          <td><?php echo $row['mobile']; ?></td>
          <td><?php echo $row['department_name'] ?? 'N/A'; ?></td>
          <td><?php echo ucfirst($row['role']); ?></td>
          <td>
            <!-- Action buttons -->
          </td>
        </tr>
      <?php } ?>
    </tbody>
</table>
```

**Explanation:**
- Creates Bootstrap table with styling
- Displays all employee information in columns
- Shows department name or 'N/A' if no department assigned
- Capitalizes role (Admin/Staff) using `ucfirst()`
- Loops through all employees from query result

### 5. Action Buttons

```php
<div class="d-flex">
    <a href="view_employee.php?id=<?php echo $row['users_id']; ?>" 
       class="btn btn-sm btn-primary me-2 text-white text-decoration-none">View</a>
    <a href="edit_employee.php?id=<?php echo $row['users_id']; ?>" 
       class="btn btn-sm btn-warning me-2 text-white text-decoration-none">Edit</a>
    <a href="reset_password.php?id=<?php echo $row['users_id']; ?>" 
       class="btn btn-sm btn-info mb-1 me-2">Reset</a>
    <a href="delete_employee.php?id=<?php echo $row['users_id']; ?>" 
       class="btn btn-sm btn-danger text-white text-decoration-none"
       onclick="return confirm('Are you sure you want to delete <?php echo addslashes($row['username']); ?>?');">Delete</a>
</div>
```

**Explanation:**
- Creates four action buttons:
  - **View** - Opens employee profile page (blue/primary button)
  - **Edit** - Opens employee edit page (yellow/warning button)
  - **Reset** - Resets employee password (light blue/info button)
  - **Delete** - Deletes employee (red/danger button)
- Uses `users_id` to identify specific employee
- `onclick` shows confirmation dialog before deletion
- `addslashes()` prevents JavaScript errors in confirmation message

### 6. Add Employee Button

```php
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manage Employees</h3>
    <a href="add_employee.php" class="btn btn-sm btn-success text-white">+ Add Employee</a>
</div>
```

**Explanation:**
- Header with page title
- "Add Employee" button to create new employees
- Green/success button for positive action

### 7. JavaScript Search Functionality

```php
<script>
  document.getElementById("searchInput").addEventListener("keyup", function() {
    var value = this.value.toLowerCase();
    var rows = document.querySelectorAll("#employee_table tbody tr");

    rows.forEach(function(row) {
      var text = row.textContent.toLowerCase();
      if (text.includes(value)) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });
</script>
```

**Explanation:**
- Adds event listener to search input field
- Triggers on `keyup` event (as user types)
- Converts search value to lowercase for case-insensitive search
- Gets all table rows from tbody
- Loops through each row
- Shows row if match found, hides if no match
- Real-time filtering without page refresh

## Output / Result

**When the file runs:**

1. **Employee Management Page:**
   - Header showing "Manage Employees" with "Add Employee" button
   - Search input field at the top
   - Table displaying all employees with columns:
     - S.No (Serial Number)
     - Employee ID
     - Full Name
     - Username
     - Email
     - Mobile
     - Department (or N/A)
     - Role (Admin/Staff)
     - Actions (View, Edit, Reset, Delete buttons)

2. **Search Functionality:**
   - User can type in search box
   - Table filters in real-time as user types
   - Searches across all columns (name, username, email, department)
   - Case-insensitive search
   - Rows that don't match are hidden

3. **Action Buttons:**
   - **View** - Opens employee profile page
   - **Edit** - Opens employee edit form
   - **Reset** - Opens password reset page
   - **Delete** - Deletes employee (with confirmation)

4. **Empty State:**
   - If no employees exist, shows "No employees found." message
   - Message spans all columns and is centered

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies logged-in admin
- `$_SESSION['role']` - Validates admin access

### Database Logic:
- Uses LEFT JOIN to combine data from three tables
- `users` - Employee account information
- `employee_department` - Employee-department relationships
- `department` - Department names
- Orders by user ID (ascending)

### Validation:
- Admin role validation
- Session validation
- SQL injection prevention (user_id used in URLs)

### Security Features:
- **Role-Based Access:** Only admins can access
- **Session Validation:** Ensures authenticated access
- **Confirmation Dialog:** Prevents accidental deletion
- **XSS Prevention:** Uses htmlspecialchars() for output (implicit in echo)

### Alerts:
- No explicit alert messages in this file
- Confirmation dialog for deletion
- Empty state message when no data exists

### For Presentation/Viva:
- **Explain:** Admin can view and manage all employees
- **Highlight:** JOIN operation combining multiple tables
- **Mention:** Search functionality for quick filtering
- **Show:** Action buttons and their functionality
- **Demonstrate:** How employees are displayed and managed
- **Discuss:** Employee-department relationships

### Table Features:
- Bootstrap styling for professional appearance
- Striped rows for better readability
- Hover effect on rows
- Bordered table for clear structure
- Responsive design

### JavaScript Features:
- Real-time search filtering
- Case-insensitive search
- Searches across all table columns
- No page refresh required
- Smooth user experience

