# File: manage_department.php

## Purpose

This file allows administrators to view and manage all departments in the system. It displays a table showing all departments with their IDs and names, and provides action buttons to edit or delete departments. The file includes search functionality to filter departments quickly.

## Key Features

- Display all departments in a table format
- Show department details: ID and Name
- Search functionality to filter departments
- Action buttons: Edit, Delete
- Confirmation dialog before deletion
- Session and role-based access control
- Real-time search filtering
- Add department button

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
- Ensures only administrators can manage departments

### 2. Database Query

```php
$query = "SELECT * FROM department ORDER BY department_id ASC";
$query_run = mysqli_query($conn, $query);
```

**Explanation:**
- Queries all departments from `department` table
- Orders by department_id (ascending)
- Simple query (no JOINs needed)

### 3. Search Input Field

```php
<div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by department name...">
</div>
```

**Explanation:**
- Creates search input field
- Uses Bootstrap styling
- Placeholder guides user on searchable field
- JavaScript will filter table rows
- Real-time search functionality

### 4. Department Table Display

```php
<table class="table table-sm table-bordered table-striped table-hover text-center align-middle" id="department_table">
    <thead class="table-dark">
      <tr>
        <th>S.No</th>
        <th>Dept ID</th>
        <th>Department Name</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sno = 1;
      while ($row = mysqli_fetch_assoc($query_run)) {
      ?>
        <tr>
          <td><?php echo $sno++; ?></td>
          <td><?php echo $row['department_id']; ?></td>
          <td class="text-center"><?php echo $row['department_name']; ?></td>
          <td class="text-center">
            <!-- Action buttons -->
          </td>
        </tr>
      <?php } ?>
    </tbody>
</table>
```

**Explanation:**
- Creates Bootstrap table with styling
- Displays: Serial Number, Department ID, Department Name, Actions
- Loops through all departments from query result
- Centered text alignment for better appearance

### 5. Action Buttons

```php
<div class="d-flex justify-content-center gap-2">
    <a href="edit_department.php?id=<?php echo $row['department_id']; ?>" 
       class="btn btn-sm btn-warning text-white text-decoration-none">Edit</a>
    <a href="delete_department.php?id=<?php echo $row['department_id']; ?>" 
       class="btn btn-sm btn-danger text-white text-decoration-none" 
       onclick="return confirm('Are you sure you want to delete this department?');">Delete</a>
</div>
```

**Explanation:**
- Creates two action buttons:
  - **Edit** - Opens department edit page (yellow/warning button)
  - **Delete** - Deletes department (red/danger button)
- Uses `department_id` to identify specific department
- `onclick` shows confirmation dialog before deletion
- Centered button layout with gap spacing

### 6. Add Department Button

```php
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manage Departments</h3>
    <a href="add_department.php" class="btn btn-sm btn-success text-white">+ Add Department</a>
</div>
```

**Explanation:**
- Header with page title
- "Add Department" button to create new departments
- Green/success button for positive action

### 7. JavaScript Search Functionality

```php
<script>
  document.getElementById("searchInput").addEventListener("keyup", function() {
    var value = this.value.toLowerCase();
    var rows = document.querySelectorAll("#department_table tbody tr");

    rows.forEach(function(row) {
      row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
    });
  });
</script>
```

**Explanation:**
- Adds event listener to search input field
- Triggers on `keyup` event (as user types)
- Converts search value to lowercase for case-insensitive search
- Gets all table rows from tbody
- Shows row if match found, hides if no match
- Real-time filtering without page refresh
- Compact code using ternary operator

## Output / Result

**When the file runs:**

1. **Department Management Page:**
   - Header showing "Manage Departments" with "Add Department" button
   - Search input field at the top
   - Table displaying all departments with columns:
     - S.No (Serial Number)
     - Dept ID (Department ID)
     - Department Name
     - Actions (Edit, Delete buttons)

2. **Search Functionality:**
   - User can type in search box
   - Table filters in real-time as user types
   - Searches department names
   - Case-insensitive search
   - Rows that don't match are hidden

3. **Action Buttons:**
   - **Edit** - Opens department edit form
   - **Delete** - Deletes department (with confirmation)

4. **Empty State:**
   - If no departments exist, shows "No departments found." message
   - Message spans all columns and is centered

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies logged-in admin
- `$_SESSION['role']` - Validates admin access

### Database Logic:
- Simple query from `department` table
- No JOINs needed (single table)
- Orders by department_id (ascending)

### Validation:
- Admin role validation
- Session validation
- SQL injection prevention (department_id used in URLs)

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
- **Explain:** Admin can view and manage all departments
- **Highlight:** Simple table display with search functionality
- **Mention:** Departments are used to organize employees
- **Show:** How departments are displayed and managed
- **Demonstrate:** Search functionality and action buttons
- **Discuss:** Department management workflow

### Table Features:
- Bootstrap styling for professional appearance
- Striped rows for better readability
- Hover effect on rows
- Bordered table for clear structure
- Centered text alignment
- Responsive design

### JavaScript Features:
- Real-time search filtering
- Case-insensitive search
- Searches department names
- No page refresh required
- Smooth user experience
- Compact code implementation

### Department Management Workflow:
1. Admin views all departments
2. Can search for specific department
3. Can edit department name
4. Can delete department (if no employees assigned)
5. Can add new departments

