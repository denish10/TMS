# File: manage_task.php (Admin)

## Purpose

This file allows administrators to view and manage all tasks assigned to employees in the system. It displays a comprehensive table showing task details including task title, assigned employee, priority, and status. The file provides actions to view, edit, and delete tasks, along with a search functionality to filter tasks quickly.

## Key Features

- Display all tasks in a table format
- Show task details: title, employee, priority, status
- Search functionality to filter tasks
- Action buttons: View, Edit, Delete
- Priority badges with color coding (High = red, Medium = yellow, Low = green)
- Status badges with color coding (multiple statuses)
- Confirmation dialog before deletion
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

include HEADER_PATH;
include SIDEBAR_PATH;
```

**Explanation:**
- Starts PHP session to access user data
- Includes configuration file for constants
- Validates user is logged in AND has admin role
- Redirects to login page if not admin
- Includes admin header and sidebar
- Ensures only administrators can manage tasks

### 2. Database Query with JOIN

```php
$sno = 1;
$query = "
    SELECT 
        ta.record_id, 
        ta.status,
        tm.task_title, 
        tm.priority,
        u.fullname
    FROM task_assign ta
    JOIN task_manage tm ON ta.task_id = tm.task_id
    JOIN users u ON ta.users_id = u.users_id
    ORDER BY ta.task_id DESC
";
```

**Explanation:**
- Initializes serial number counter
- Joins three tables:
  - `task_assign` (ta) - Contains task assignments and status
  - `task_manage` (tm) - Contains task details (title, priority)
  - `users` (u) - Contains employee names
- JOIN connects tables using foreign keys:
  - `ta.task_id = tm.task_id` (links assignment to task)
  - `ta.users_id = u.users_id` (links assignment to user)
- Orders by task_id DESC (newest tasks first)
- Selects: record_id, status, task_title, priority, fullname

### 3. Search Input Field

```php
<div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by title, employee, priority, or status...">
</div>
```

**Explanation:**
- Creates search input field
- Uses Bootstrap styling
- Placeholder guides user on searchable fields
- JavaScript will filter table rows
- Real-time search functionality

### 4. Table Structure

```php
<table class="table table-bordered table-striped table-hover" id="task_table">
    <thead class="table-dark">
      <tr>
        <th>S.No</th>
        <th>Task Title</th>
        <th>Employee</th>
        <th>Priority</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
```

**Explanation:**
- Creates Bootstrap table with styling
- Six columns: Serial Number, Task Title, Employee, Priority, Status, Action
- Dark header for better visibility
- Responsive table design

### 5. Displaying Tasks with Priority Badges

```php
<td>
    <?php
    if ($row['priority'] == 'High') {
        echo '<span class="badge bg-danger">High</span>';
    } elseif ($row['priority'] == 'Medium') {
        echo '<span class="badge bg-warning text-dark">Medium</span>';
    } elseif ($row['priority'] == 'Low') {
        echo '<span class="badge bg-success">Low</span>';
    } else {
        echo '-';
    }
    ?>
</td>
```

**Explanation:**
- Displays priority as color-coded badges
- **High** = Red badge (bg-danger) - urgent tasks
- **Medium** = Yellow badge (bg-warning) - normal tasks
- **Low** = Green badge (bg-success) - low priority tasks
- Shows dash (-) if priority not set
- Visual representation for quick identification

### 6. Displaying Status Badges

```php
<td>
    <?php
    if ($row['status'] == 'Not Started') {
        echo '<span class="badge bg-secondary">Not Started</span>';
    } elseif ($row['status'] == 'In Progress') {
        echo '<span class="badge bg-primary">In Progress</span>';
    } elseif ($row['status'] == 'Completed') {
        echo '<span class="badge bg-success">Completed</span>';
    } elseif ($row['status'] == 'On Hold') {
        echo '<span class="badge bg-warning text-dark">On Hold</span>';
    } elseif ($row['status'] == 'Cancelled') {
        echo '<span class="badge bg-danger">Cancelled</span>';
    } elseif ($row['status'] == 'Pending') {
        echo '<span class="badge bg-info text-dark">Pending</span>';
    } else {
        echo '-';
    }
    ?>
</td>
```

**Explanation:**
- Displays status as color-coded badges
- **Not Started** = Gray badge (bg-secondary)
- **In Progress** = Blue badge (bg-primary)
- **Completed** = Green badge (bg-success)
- **On Hold** = Yellow badge (bg-warning)
- **Cancelled** = Red badge (bg-danger)
- **Pending** = Light blue badge (bg-info)
- Visual status indicators for quick recognition

### 7. Action Buttons

```php
<td>
    <div class="d-flex">
        <a href="view_task.php?id=<?php echo $row['record_id']; ?>" class="btn btn-sm btn-info me-2 text-white">View</a>
        <a href="edit_task.php?id=<?php echo $row['record_id']; ?>" class="btn btn-sm btn-primary me-2 text-white">Edit</a>
        <a href="delete_task.php?id=<?php echo $row['record_id']; ?>" 
           onclick="return confirm('Are you sure you want to delete this task?');"
           class="btn btn-sm btn-danger text-white">Delete</a>
    </div>
</td>
```

**Explanation:**
- Creates three action buttons:
  - **View** - Opens task details page (blue/info button)
  - **Edit** - Opens task edit page (blue/primary button)
  - **Delete** - Deletes task with confirmation (red/danger button)
- Uses `record_id` to identify specific task
- `onclick` shows confirmation dialog before deletion
- Bootstrap button styling with spacing (me-2)

### 8. Empty State Handling

```php
} else {
    echo '<tr><td colspan="6" class="text-center text-danger">No tasks found.</td></tr>';
}
```

**Explanation:**
- Shows message if no tasks exist
- Spans all 6 columns
- Centered text with red color for visibility
- Provides user feedback

### 9. JavaScript Search Functionality

```php
<script>
  document.getElementById("searchInput").addEventListener("keyup", function() {
    var value = this.value.toLowerCase();
    var rows = document.querySelectorAll("#task_table tbody tr");

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
- Adds event listener for search input
- Converts search to lowercase (case-insensitive)
- Gets all table rows
- Filters rows based on search text
- Shows matching rows, hides others
- Real-time filtering without page refresh

## Output / Result

**When the file runs:**

1. **Task Management Page:**
   - Header showing "Manage Tasks"
   - Search input field
   - Table displaying all tasks with columns:
     - S.No (Serial Number)
     - Task Title
     - Employee (assigned to)
     - Priority (color-coded badge)
     - Status (color-coded badge)
     - Action (View, Edit, Delete buttons)

2. **Priority Badges:**
   - **High** - Red badge
   - **Medium** - Yellow badge
   - **Low** - Green badge

3. **Status Badges:**
   - **Not Started** - Gray
   - **In Progress** - Blue
   - **Completed** - Green
   - **On Hold** - Yellow
   - **Cancelled** - Red
   - **Pending** - Light Blue

4. **Action Buttons:**
   - **View** - Opens task details
   - **Edit** - Opens task edit form
   - **Delete** - Deletes task (with confirmation)

5. **Search Functionality:**
   - Filters tasks as user types
   - Searches across all columns
   - Case-insensitive search
   - Real-time filtering

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies logged-in user
- `$_SESSION['role']` - Validates admin access

### Database Logic:
- Uses JOIN to combine data from three tables
- `task_assign` - Assignment and status
- `task_manage` - Task details
- `users` - Employee names
- Orders by task_id (newest first)

### Validation:
- Admin role validation
- Session validation
- SQL injection prevention (record_id used in URLs)

### Security Features:
- **Role-Based Access:** Only admins can access
- **Session Validation:** Ensures authenticated access
- **Confirmation Dialog:** Prevents accidental deletion
- **XSS Prevention:** Uses htmlspecialchars() for output

### Alerts:
- No explicit alert messages in this file
- Status and priority badges serve as indicators
- Confirmation dialog for deletion

### For Presentation/Viva:
- **Explain:** Admin can view and manage all tasks
- **Highlight:** JOIN operation combining multiple tables
- **Mention:** Color-coded badges for quick recognition
- **Show:** Search functionality and action buttons
- **Demonstrate:** How tasks are displayed and managed
- **Discuss:** Task lifecycle (creation to completion)

### Task Workflow:
1. Admin creates task and assigns to employee
2. Task appears in this page with 'Not Started' status
3. Employee works on task (status changes to 'In Progress')
4. Task is completed (status changes to 'Completed')
5. Admin can view, edit, or delete tasks anytime

### JavaScript Features:
- Real-time search filtering
- Case-insensitive search
- No page refresh required
- Smooth user experience

### Table Features:
- Bootstrap styling
- Striped rows
- Hover effects
- Responsive design
- Clear action buttons

