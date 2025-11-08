# File: admin/activity_logs.php

## Purpose

This file provides a comprehensive interface for administrators to view, search, and filter all system activity logs. It displays all recorded activities in the system, including user actions, task operations, leave applications, and other system events. The page includes advanced filtering options, search functionality, and the ability to export logs to CSV format.

## Key Features

- View all activity logs in a table format
- Filter logs by activity type
- Filter logs by user
- Filter logs by date range
- Search across all log fields (ID, type, description, user, date, time)
- Export logs to CSV format
- Real-time search with debounce
- Activity icons and color coding
- Time ago formatting
- Pagination (shows latest 100 logs)
- Table existence check

## Code Breakdown

### 1. Session and Access Control

```php
session_start();

require_once __DIR__ . '/../dbsetting/config.php';
require_once __DIR__ . '/common/activity_logger.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}
```

**Explanation:**
- Starts session to access user information
- Includes database configuration and activity logger functions
- Checks if user is logged in and has admin role
- Redirects to login page if not authorized
- Ensures only administrators can access activity logs

### 2. Filter Parameters

```php
$filter_type = isset($_GET['type']) ? mysqli_real_escape_string($conn, $_GET['type']) : '';
$filter_user = isset($_GET['user']) ? intval($_GET['user']) : 0;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($conn, $_GET['date_from']) : '';
```

**Explanation:**
- Gets filter parameters from URL query string
- Escapes string inputs to prevent SQL injection
- Converts user ID to integer for safety
- Sets default values if parameters are not provided
- Allows filtering by activity type, user, search term, and date

### 3. Table Existence Check

```php
$table_exists = false;
$check_query = "SHOW TABLES LIKE 'activity_logs'";
$check_result = mysqli_query($conn, $check_query);
if ($check_result && mysqli_num_rows($check_result) > 0) {
    $table_exists = true;
}
```

**Explanation:**
- Checks if activity_logs table exists in database
- Prevents errors if table hasn't been created yet
- Shows appropriate message if table doesn't exist
- Allows graceful handling of missing table

### 4. Building Query with Filters

```php
$query = "SELECT * FROM activity_logs WHERE 1=1";

if (!empty($filter_type)) {
    $query .= " AND activity_type = '$filter_type'";
}

if ($filter_user > 0) {
    $query .= " AND user_id = $filter_user";
}

if (!empty($date_from)) {
    $query .= " AND DATE(created_at) >= '$date_from'";
}

if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $query .= " AND (
        log_id LIKE '%$search_escaped%' OR
        activity_type LIKE '%$search_escaped%' OR
        description LIKE '%$search_escaped%' OR
        user_name LIKE '%$search_escaped%' OR
        ...
    )";
}

$query .= " ORDER BY created_at DESC LIMIT 100";
```

**Explanation:**
- Starts with base query selecting all logs
- Adds WHERE conditions based on filters
- Uses LIKE for search across multiple fields
- Orders by creation date (newest first)
- Limits to 100 most recent logs
- Builds dynamic query based on user filters

### 5. Getting Filter Options

```php
$types_query = "SELECT DISTINCT activity_type FROM activity_logs ORDER BY activity_type";
$types_result = $table_exists ? mysqli_query($conn, $types_query) : false;

$users_query = "SELECT DISTINCT user_id, user_name FROM activity_logs WHERE user_id IS NOT NULL ORDER BY user_name";
$users_result = $table_exists ? mysqli_query($conn, $users_query) : false;
```

**Explanation:**
- Gets unique activity types for filter dropdown
- Gets unique users for user filter dropdown
- Only executes if table exists
- Provides options for filtering

### 6. Real-time Search with JavaScript

```javascript
let searchTimeout;
function performSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        if (searchInput.value.length >= 2 || searchInput.value.length === 0) {
            document.getElementById('filterForm').submit();
        }
    }, 500);
}

searchInput.addEventListener('input', performSearch);
```

**Explanation:**
- Implements debounced search (waits 500ms after typing stops)
- Auto-submits form when search term is 2+ characters or empty
- Prevents excessive form submissions
- Provides responsive search experience

### 7. CSV Export Functionality

```javascript
function exportTable() {
    const table = document.getElementById('activityTable');
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');
        for (let j = 0; j < cols.length; j++) {
            let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        csv.push(row.join(','));
    }
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.setAttribute('href', URL.createObjectURL(blob));
    link.setAttribute('download', 'activity_logs_' + new Date().toISOString().split('T')[0] + '.csv');
    link.click();
}
```

**Explanation:**
- Extracts table data into CSV format
- Handles special characters and newlines
- Creates downloadable CSV file
- Names file with current date
- Triggers download automatically

## Output / Result

**When the page loads:**

1. **Filter Section:**
   - Search input field
   - Activity type dropdown (populated with available types)
   - User dropdown (populated with users who have activities)
   - Date range input
   - Apply and Reset buttons

2. **Activity Logs Table:**
   - Shows columns: ID, Type, Description, User, Related, Date & Time
   - Displays up to 100 most recent logs
   - Shows activity icons and color-coded badges
   - Displays time ago format (e.g., "2 hours ago")
   - Shows related table and ID if available

3. **Features:**
   - Real-time search as user types
   - Filter options update table immediately
   - Export button to download CSV
   - Result count badge
   - Empty state message if no logs found

4. **If Table Doesn't Exist:**
   - Shows warning message
   - Provides link to setup script
   - Explains that table needs to be created

## Additional Notes

### Security Considerations:
- **SQL Injection Prevention:** All inputs are escaped using `mysqli_real_escape_string()`
- **Access Control:** Only administrators can access this page
- **Input Validation:** User ID converted to integer, dates validated
- **XSS Prevention:** Output encoded with `htmlspecialchars()`

### Performance Considerations:
- **Limit to 100 logs:** Prevents loading too many records
- **Indexed Queries:** Uses indexed columns for filtering
- **Debounced Search:** Reduces server load from frequent requests

### Filter Options:
- **Activity Type:** Filter by specific activity types (login, task_created, etc.)
- **User:** Filter by specific user who performed the activity
- **Date Range:** Filter logs from a specific date onwards
- **Search:** Search across all fields simultaneously

### Export Functionality:
- **CSV Format:** Standard comma-separated values format
- **File Naming:** Includes current date in filename
- **Browser Download:** Triggers browser download automatically
- **Data Format:** Preserves table structure and formatting

### Activity Icons:
- Different icons for different activity types
- Color-coded badges for visual distinction
- Icons help quickly identify activity types

### Time Display:
- Shows formatted date (e.g., "Jan 15, 2024")
- Shows formatted time (e.g., "02:30 PM")
- Shows relative time (e.g., "2 hours ago")
- Provides multiple time representations

### Table Structure:
- Responsive table design
- Hover effects for better UX
- Sortable by date (newest first)
- Pagination through LIMIT clause

### Error Handling:
- Checks table existence before querying
- Handles missing table gracefully
- Shows appropriate error messages
- Prevents fatal errors

## Usage Example

1. **Viewing All Logs:**
   - Navigate to Activity Logs page
   - View all recent activities in table

2. **Filtering by Type:**
   - Select activity type from dropdown
   - Table updates to show only that type

3. **Searching:**
   - Type search term in search box
   - Results update automatically after 500ms

4. **Exporting:**
   - Click Export button
   - CSV file downloads automatically

5. **Filtering by User:**
   - Select user from dropdown
   - Table shows only that user's activities

6. **Date Range Filter:**
   - Select date from date picker
   - Table shows logs from that date onwards

