# File: view_leave_status.php

## Purpose

This file displays all leave applications submitted by the logged-in user. It shows a table with leave details including subject, message, start date, end date, and current status (Pending/Approved/Rejected). The file includes a search functionality to filter leave applications and provides a clear visual representation of leave status using color-coded badges.

## Key Features

- Display user's leave applications in a table
- Search functionality to filter leave applications
- Status badges with color coding (Approved = green, Rejected = red, Pending = yellow)
- Chronological ordering (newest first)
- Session-based user identification
- Responsive table design
- Real-time search filtering

## Code Breakdown

### 1. Session and Access Control

```php
session_start();
require_once __DIR__ . '/../../dbsetting/config.php'; 
include USER_HEADER_PATH;
include USER_SIDEBAR_PATH;

// Make sure user is logged in
if (!isset($_SESSION['users_id'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$user_id = (int) $_SESSION['users_id'];
```

**Explanation:**
- Starts PHP session to access user data
- Includes configuration and user interface files
- Validates user login status
- Redirects to login if not authenticated
- Gets user ID from session (cast to integer)
- Ensures users can only see their own leave applications

### 2. Database Query for Leave Applications

```php
$sno = 1;
$query = "SELECT subject, message, start_date, end_date, status FROM leave_apply WHERE users_id = $user_id ORDER BY created_date DESC";
$result = mysqli_query($conn, $query);
```

**Explanation:**
- Initializes serial number counter
- Queries `leave_apply` table for user's leave applications
- Filters by `users_id` to show only current user's applications
- Orders by `created_date DESC` (newest applications first)
- Selects: subject, message, start_date, end_date, status
- Executes query and stores result

### 3. Search Input Field

```php
<div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by subject, message, dates, or status...">
</div>
```

**Explanation:**
- Creates search input field
- Uses Bootstrap form-control class for styling
- Placeholder text guides user on what can be searched
- JavaScript will filter table rows based on input
- Real-time search (filters as user types)

### 4. Table Header

```php
<table class="table table-bordered table-striped table-hover" id="leave_table">
    <thead class="table-dark">
      <tr>
        <th>S.NO</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Status</th>
      </tr>
    </thead>
```

**Explanation:**
- Creates Bootstrap table with styling classes
- `table-bordered` - adds borders to table
- `table-striped` - alternates row colors
- `table-hover` - highlights row on hover
- Dark header for better visibility
- Six columns: Serial Number, Subject, Message, Start Date, End Date, Status

### 5. Displaying Leave Applications

```php
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $subject = $row['subject'];
        $message = $row['message'];
        $start_date = $row['start_date'];
        $end_date = $row['end_date'];
        $status = $row['status'];
?>
    <tr>
        <td><?php echo $sno++; ?></td>
        <td><?php echo htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')); ?></td>
        <td><?php echo htmlspecialchars($start_date, ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($end_date, ENT_QUOTES, 'UTF-8'); ?></td>
        <td>
            <?php if ($status == 'Approved') { ?>
                <span class="badge bg-success">Approved</span>
            <?php } elseif ($status == 'Rejected') { ?>
                <span class="badge bg-danger">Rejected</span>
            <?php } else { ?>
                <span class="badge bg-warning text-dark">Pending</span>
            <?php } ?>
        </td>
    </tr>
<?php 
    }
}
```

**Explanation:**
- Checks if query returned results
- Loops through each leave application using `while` loop
- Extracts data from result array
- Displays serial number (increments for each row)
- Uses `htmlspecialchars()` to prevent XSS attacks
- `nl2br()` converts newlines to HTML line breaks in message
- Status displayed as color-coded badges:
  - **Approved** = Green badge (bg-success)
  - **Rejected** = Red badge (bg-danger)
  - **Pending** = Yellow badge (bg-warning)

### 6. Empty State Handling

```php
} else { ?>
    <tr>
        <td colspan="6" class="text-center">No leave applications found.</td>
    </tr>
<?php } ?>
```

**Explanation:**
- Shows message if no leave applications exist
- `colspan="6"` spans all 6 columns
- Centered text for better visibility
- Provides user feedback when no data available

### 7. JavaScript Search Functionality

```php
<script>
  document.getElementById("searchInput").addEventListener("keyup", function() {
    var value = this.value.toLowerCase();
    var rows = document.querySelectorAll("#leave_table tbody tr");

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
- Checks if row text contains search value
- Shows row if match found, hides if no match
- Real-time filtering without page refresh

## Output / Result

**When the file runs:**

1. **Leave Status Page Display:**
   - Header showing "Your Leave Applications"
   - Search input field at the top
   - Table displaying leave applications with columns:
     - S.NO (Serial Number)
     - Subject
     - Message
     - Start Date
     - End Date
     - Status (with color-coded badges)

2. **Status Badges:**
   - **Approved** - Green badge (bg-success)
   - **Rejected** - Red badge (bg-danger)
   - **Pending** - Yellow badge with dark text (bg-warning text-dark)

3. **Search Functionality:**
   - User can type in search box
   - Table filters in real-time as user types
   - Searches across all columns (subject, message, dates, status)
   - Case-insensitive search
   - Rows that don't match are hidden

4. **Empty State:**
   - If no leave applications exist, shows "No leave applications found." message
   - Message spans all columns and is centered

5. **User Experience:**
   - Clean, organized table layout
   - Easy-to-read status indicators
   - Quick search functionality
   - Responsive design (works on mobile and desktop)
   - Ordered by date (newest first)

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies the logged-in user
- Used to filter leave applications (users only see their own)

### Database Logic:
- Queries `leave_apply` table
- Filters by `users_id` to show user-specific data
- Orders by `created_date DESC` (newest first)
- Retrieves: subject, message, start_date, end_date, status

### Validation:
- Session validation ensures only logged-in users can access
- User can only see their own leave applications
- SQL injection prevention (user_id is cast to integer)

### Security Features:
- **XSS Prevention:** Uses `htmlspecialchars()` for all output
- **SQL Injection Prevention:** User ID is cast to integer
- **Session Validation:** Ensures authenticated access only
- **Data Filtering:** Users can only see their own data

### Alerts:
- No explicit alert messages in this file
- Status badges serve as visual indicators
- Empty state message when no data exists

### For Presentation/Viva:
- **Explain:** This shows employees their leave application status
- **Highlight:** Real-time search functionality
- **Mention:** Color-coded status badges for quick recognition
- **Show:** How data is filtered by user ID
- **Demonstrate:** Search functionality filtering table rows
- **Discuss:** How status changes when admin approves/rejects

### Status Workflow:
1. User submits leave application (status = 'Pending')
2. Application appears in this page with 'Pending' status
3. Admin reviews and approves/rejects
4. Status updates to 'Approved' or 'Rejected'
5. User can see updated status in this page

### JavaScript Features:
- Real-time search (filters as user types)
- Case-insensitive search
- Searches across all table columns
- No page refresh required
- Smooth user experience

### Table Features:
- Bootstrap styling for professional appearance
- Striped rows for better readability
- Hover effect on rows
- Bordered table for clear structure
- Responsive design

