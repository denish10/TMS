# File: user/edit_profile.php

## Purpose

This file allows employees (staff users) to edit their own profile information. Users can update their full name, username, email, and mobile number. The file includes comprehensive validation to ensure data integrity and prevents duplicate usernames or emails. After successful update, the user's session is updated and they are redirected to the dashboard.

## Key Features

- Edit personal profile information
- Update full name, username, email, and mobile
- Comprehensive input validation
- Duplicate username/email prevention
- Session update after profile change
- Success/error message alerts
- Automatic redirect after update
- Responsive form design

## Code Breakdown

### 1. Session and Access Control

```php
session_start();

require_once __DIR__ . '/../dbsetting/config.php';

// Check if user is logged in
if (!isset($_SESSION['users_id'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}
```

**Explanation:**
- Starts session to access user information
- Includes database configuration
- Checks if user is logged in (any role)
- Redirects to login page if not authenticated
- Allows both admin and staff to edit their profile

### 2. Fetch Current User Data

```php
$user_id = $_SESSION['users_id'];

$query = "SELECT * FROM users WHERE users_id = $user_id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$fullname = $data['fullname'] ?? '';
$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$mobile = $data['mobile'] ?? '';
```

**Explanation:**
- Gets user ID from session
- Fetches current user data from database
- Retrieves fullname, username, email, and mobile
- Sets default values if fields are empty
- Populates form fields with existing data

### 3. Form Submission Handling

```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    // ... validation and update logic
}
```

**Explanation:**
- Checks if form was submitted (POST method)
- Gets form values and trims whitespace
- Uses null coalescing operator for safety
- Processes form data only on submission

### 4. Input Validation

```php
if ($fullname === '' || $username === '' || $email === '' || $mobile === '') {
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
}
```

**Explanation:**
- Validates all fields are not empty
- Validates email format using filter_var()
- Validates mobile contains only digits
- Validates mobile length (10-15 digits)
- Sets error messages for validation failures
- Uses appropriate alert types (danger for errors)

### 5. Duplicate Username Check

```php
$checkUser = mysqli_query($conn, "SELECT users_id FROM users WHERE username = '".mysqli_real_escape_string($conn, $username)."' AND users_id != $user_id LIMIT 1");
if ($checkUser && mysqli_num_rows($checkUser) > 0) {
    $message = "⚠️ Username already exists.";
    $alertType = "danger";
}
```

**Explanation:**
- Checks if username already exists
- Excludes current user from check (users_id != $user_id)
- Prevents duplicate usernames
- Escapes input to prevent SQL injection
- Shows error if username is taken

### 6. Duplicate Email Check

```php
$checkEmail = mysqli_query($conn, "SELECT users_id FROM users WHERE email = '".mysqli_real_escape_string($conn, $email)."' AND users_id != $user_id LIMIT 1");
if ($checkEmail && mysqli_num_rows($checkEmail) > 0) {
    $message = "⚠️ Email already exists.";
    $alertType = "danger";
}
```

**Explanation:**
- Checks if email already exists
- Excludes current user from check
- Prevents duplicate emails
- Escapes input for security
- Shows error if email is taken

### 7. No Changes Detection

```php
if ($fullname === $data['fullname'] && $username === $data['username'] && 
    $email === $data['email'] && $mobile === $data['mobile']) {
    $message = "ℹ️ No changes detected.";
    $alertType = "info";
}
```

**Explanation:**
- Compares new values with existing data
- Detects if no changes were made
- Shows info message if no changes
- Prevents unnecessary database updates
- Improves user experience

### 8. Update Database

```php
$fullname = mysqli_real_escape_string($conn, $fullname);
$username = mysqli_real_escape_string($conn, $username);
$email = mysqli_real_escape_string($conn, $email);
$mobile = mysqli_real_escape_string($conn, $mobile);

$update = "UPDATE users SET fullname='$fullname', username='$username', email='$email', mobile='$mobile' WHERE users_id=$user_id";

if (mysqli_query($conn, $update)) {
    $_SESSION['name'] = $fullname;
    $message = "✅ Profile updated successfully! Redirecting...";
    $alertType = "success";
    $redirect = true;
} else {
    $message = "❌ Error: " . mysqli_error($conn);
    $alertType = "danger";
}
```

**Explanation:**
- Escapes all inputs before database update
- Updates user record in database
- Updates session name if update successful
- Shows success message
- Sets redirect flag for automatic redirect
- Shows error message if update fails

### 9. Session Update

```php
$_SESSION['name'] = $fullname;
```

**Explanation:**
- Updates session variable with new name
- Ensures session reflects current data
- Maintains consistency between session and database
- Updates display name immediately

### 10. Automatic Redirect

```php
<?php if ($redirect): ?>
    <meta http-equiv="refresh" content="2;url=dashboard.php">
<?php endif; ?>
```

**Explanation:**
- Redirects to dashboard after 2 seconds
- Only redirects on successful update
- Gives user time to see success message
- Improves user experience

## Output / Result

**When the page loads:**

1. **Edit Profile Form:**
   - Full Name input (pre-filled with current value)
   - Username input (pre-filled with current value)
   - Email input (pre-filled with current value)
   - Mobile No input (pre-filled with current value)
   - Update Profile button
   - Back to Dashboard button

2. **Alert Messages:**
   - Success: Green alert for successful update
   - Error: Red alert for validation errors
   - Info: Blue alert for no changes

3. **After Successful Update:**
   - Success message displayed
   - Automatic redirect to dashboard after 2 seconds
   - Session updated with new name

4. **Form Validation:**
   - Real-time validation feedback
   - Clear error messages
   - Prevents invalid data submission

## Additional Notes

### Security Considerations:
- **SQL Injection Prevention:** All inputs escaped with `mysqli_real_escape_string()`
- **XSS Prevention:** Output encoded with `htmlspecialchars()`
- **Input Validation:** Comprehensive validation before database update
- **Session Security:** Only logged-in users can access
- **User Isolation:** Users can only edit their own profile

### Validation Rules:
- **Full Name:** Required, cannot be empty
- **Username:** Required, must be unique (excluding current user)
- **Email:** Required, must be valid email format, must be unique (excluding current user)
- **Mobile:** Required, must contain only digits, length between 10-15 digits

### User Experience:
- **Pre-filled Fields:** Form shows current values
- **Clear Messages:** Descriptive error and success messages
- **Automatic Redirect:** Redirects after successful update
- **No Changes Detection:** Informs user if no changes made
- **Responsive Design:** Works on all screen sizes

### Session Management:
- **Session Update:** Updates session name after profile change
- **Consistency:** Maintains consistency between session and database
- **Immediate Effect:** Changes reflect immediately in session

### Error Handling:
- **Database Errors:** Shows database error messages
- **Validation Errors:** Shows specific validation error messages
- **Duplicate Detection:** Prevents duplicate username/email
- **Empty Fields:** Validates all required fields

### Data Integrity:
- **Unique Constraints:** Enforces unique username and email
- **Data Validation:** Ensures data meets requirements
- **Escaping:** Prevents SQL injection
- **Trimming:** Removes leading/trailing whitespace

## Usage Example

1. **Editing Profile:**
   - Navigate to Edit Profile page
   - Form shows current profile information
   - Modify desired fields
   - Click Update Profile button

2. **Validation:**
   - System validates all inputs
   - Shows error messages if validation fails
   - Prevents submission of invalid data

3. **Duplicate Check:**
   - System checks for duplicate username
   - System checks for duplicate email
   - Shows error if duplicates found

4. **Successful Update:**
   - Profile updated in database
   - Session updated with new name
   - Success message displayed
   - Automatic redirect to dashboard

5. **No Changes:**
   - System detects if no changes made
   - Shows info message
   - No database update performed

## Related Files

- **user/dashboard.php:** Dashboard where user is redirected after update
- **dbsetting/config.php:** Database configuration
- **user/common/user_header.php:** User header navigation
- **user/common/user_sidebar.php:** User sidebar navigation
- **user/common/user_footer.php:** User footer

## Notes for Developers

- Users can only edit their own profile (enforced by session user_id)
- Password update is handled separately (not in this file)
- Profile picture update is handled separately
- Department and role cannot be changed by user (admin only)

