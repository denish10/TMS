# File: apply_leave.php

## Purpose

This file allows employees (staff users) to submit leave applications to the system. It provides a form where users can enter leave details including subject, message, start date, and end date. The file validates the input, stores the leave application in the database with 'Pending' status, and redirects the user to view their leave status.

## Key Features

- Leave application form with validation
- Date validation (start date cannot be after end date)
- Input sanitization and escaping
- Automatic status assignment (Pending)
- Success/error message display
- Session-based user identification
- Automatic redirect after submission
- Confirmation dialog before submission

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
```

**Explanation:**
- Starts PHP session to access user data
- Includes configuration file for constants
- Includes user header and sidebar (different from admin)
- Checks if user is logged in (session variable exists)
- Redirects to login page if not logged in

### 2. Form Submission Handling

```php
if (isset($_POST['submit_leave'])) {
    $users_id   = (int) $_SESSION['users_id'];
    $subject    = trim($_POST['subject'] ?? '');
    $user_msg   = trim($_POST['message'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date   = trim($_POST['end_date'] ?? '');
    $created_at = date('Y-m-d H:i:s');
    $status     = 'Pending';
```

**Explanation:**
- Checks if form is submitted with 'submit_leave' button
- Gets user ID from session (cast to integer for security)
- Retrieves form data: subject, message, start_date, end_date
- `trim()` removes whitespace from input
- Sets current timestamp for created_at
- Sets default status as 'Pending' (needs admin approval)

### 3. Input Validation

```php
// Simple validations
if ($subject === '' || $user_msg === '' || $start_date === '' || $end_date === '') {
    $message = '❌ All fields are required.';
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
    $message = '⚠️ Please select valid dates.';
} elseif (strtotime($start_date) > strtotime($end_date)) {
    $message = '⚠️ Start date cannot be after end date.';
}
```

**Explanation:**
- Validates that all fields are filled (not empty)
- Uses regex pattern to validate date format (YYYY-MM-DD)
- Checks if start date is before or equal to end date
- `strtotime()` converts date string to timestamp for comparison
- Sets appropriate error messages for validation failures

### 4. Data Sanitization

```php
// Basic escaping to keep things stable
$subjectEsc = mysqli_real_escape_string($conn, $subject);
$messageEsc = mysqli_real_escape_string($conn, $user_msg);
$startEsc   = mysqli_real_escape_string($conn, $start_date);
$endEsc     = mysqli_real_escape_string($conn, $end_date);
$createdEsc = mysqli_real_escape_string($conn, $created_at);
$statusEsc  = mysqli_real_escape_string($conn, $status);
```

**Explanation:**
- Escapes special characters to prevent SQL injection
- `mysqli_real_escape_string()` sanitizes input for database
- Escapes all user inputs before inserting into database

### 5. Database Insertion

```php
$sql = "INSERT INTO leave_apply (users_id, subject, message, start_date, end_date, created_date, status)
        VALUES ($users_id, '$subjectEsc', '$messageEsc', '$startEsc', '$endEsc', '$createdEsc', '$statusEsc')";

if (mysqli_query($conn, $sql)) {
    $message = '✅ Leave applied successfully. Redirecting...';
    echo '<meta http-equiv="refresh" content="2;url=view_leave_status.php">';
} else {
    $message = '❌ Error applying leave: ' . mysqli_error($conn);
}
```

**Explanation:**
- Constructs SQL INSERT query to add leave application
- Inserts into `leave_apply` table with all required fields
- Uses escaped values to prevent SQL injection
- On success: shows success message and redirects after 2 seconds
- On failure: displays database error message

### 6. Leave Application Form

```php
<form method="POST" onsubmit="return confirm('Are you sure you want to submit this leave application?');">
    <div class="mb-3">
        <label class="form-label fw-bold">Subject</label>
        <input type="text" class="form-control" name="subject" placeholder="Enter Subject" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Message</label>
        <textarea class="form-control" rows="5" name="message" placeholder="Enter Message" required></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Start Date</label>
        <input type="date" name="start_date" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">End Date</label>
        <input type="date" name="end_date" class="form-control" required>
    </div>
    <button type="submit" name="submit_leave" class="btn btn-primary" value="1">Submit</button>
</form>
```

**Explanation:**
- Creates HTML form with POST method
- `onsubmit` shows confirmation dialog before submission
- Four input fields: subject (text), message (textarea), start_date (date), end_date (date)
- `required` attribute ensures fields are filled
- Submit button triggers form processing

## Output / Result

**When the file runs:**

1. **Leave Application Form:**
   - Shows centered card with "Apply Leave" heading
   - Form with four fields: Subject, Message, Start Date, End Date
   - Submit and Cancel buttons

2. **After Successful Submission:**
   - Displays success message: "✅ Leave applied successfully. Redirecting..."
   - Automatically redirects to `view_leave_status.php` after 2 seconds
   - Leave application is stored in database with 'Pending' status

3. **After Failed Submission:**
   - Shows error messages for validation failures
   - Form data is preserved (user can correct and resubmit)

## Additional Notes

### Session Usage:
- `$_SESSION['users_id']` - Identifies the user applying for leave
- Automatically retrieved from session (no need for user to enter)

### Validation:
- **Required Fields:** All fields must be filled
- **Date Format:** Must be in YYYY-MM-DD format
- **Date Logic:** Start date must be before or equal to end date
- **Input Sanitization:** All inputs are escaped before database insertion

### Database Logic:
- Inserts into `leave_apply` table
- Stores: users_id, subject, message, start_date, end_date, created_date, status
- Status is always set to 'Pending' (requires admin approval)

### Alerts:
- Success messages shown with ✅ emoji
- Error messages shown with ❌ emoji
- Warning messages shown with ⚠️ emoji

### Security Features:
- **SQL Injection Prevention:** Uses `mysqli_real_escape_string()`
- **XSS Prevention:** Uses `htmlspecialchars()` for output
- **Session Validation:** Ensures only logged-in users can apply
- **Input Validation:** Validates all inputs before processing

