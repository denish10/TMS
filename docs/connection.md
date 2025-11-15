# File: dbsetting/connection.php

## Purpose

This file establishes the database connection for the entire Task Management System. It is a critical component that enables all PHP files to communicate with the MySQL database. The file creates a connection object that is used throughout the application for executing database queries. The file is located in the `dbsetting/` folder along with the configuration file.

## Key Features

- MySQL database connection using mysqli extension
- Connection error handling
- Centralized database configuration
- Reusable connection object
- Simple and clean implementation

## Code Breakdown

### 1. Database Configuration Variables

```php
$host = "localhost";
$username = "root";
$password = "";
$database = "db_task_management_system";
```

**Explanation:**
- `$host` - Database server address (localhost means database is on same server)
- `$username` - MySQL username (default is 'root' in XAMPP)
- `$password` - MySQL password (empty by default in XAMPP)
- `$database` - Name of the database to connect to
- These values are specific to XAMPP localhost setup

### 2. Creating Database Connection

```php
$conn = new mysqli($host, $username, $password, $database);
```

**Explanation:**
- Creates a new mysqli connection object
- Uses object-oriented style (OOP)
- Attempts to connect to MySQL server with provided credentials
- Selects the specified database automatically
- Connection object stored in `$conn` variable (used globally)

### 3. Connection Error Handling

```php
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

**Explanation:**
- Checks if connection was successful
- `$conn->connect_error` contains error message if connection fails
- `die()` function stops script execution and displays error message
- Prevents application from running with invalid database connection
- Shows user-friendly error message for debugging

## Output / Result

**When the file is included:**

1. **Successful Connection:**
   - Creates `$conn` object that can be used for database queries
   - No output is displayed (silent success)
   - All subsequent PHP files can use `$conn` for database operations

2. **Failed Connection:**
   - Displays error message: "Connection failed: [error details]"
   - Stops script execution
   - Prevents further code execution

3. **Usage in Other Files:**
   - After including this file, `$conn` variable is available
   - Can execute queries like: `mysqli_query($conn, "SELECT * FROM users")`
   - Connection remains active until script ends or explicitly closed

## Additional Notes

### Database Configuration:
- **Host:** localhost (can be changed to remote server IP)
- **Username:** root (default XAMPP username)
- **Password:** empty (should be set in production)
- **Database:** db_task_management_system (main database name)

### Security Considerations:
- **Production Setup:** Password should never be empty in production
- **Credentials:** Should be stored in environment variables for security
- **Error Messages:** In production, error messages should be logged, not displayed to users

### Connection Object Properties:
- `$conn->connect_error` - Contains error message if connection fails
- `$conn->connect_errno` - Contains error number if connection fails
- `$conn->host_info` - Information about the connection

### mysqli vs mysql:
- Uses **mysqli** (MySQL Improved Extension)
- More secure than old `mysql_` functions
- Supports prepared statements (better security)
- Object-oriented and procedural styles both supported

### Connection Lifecycle:
1. File is included via `dbsetting/config.php` which automatically includes connection.php
2. Connection is established when config.php is loaded
3. Connection remains active during script execution
4. Connection closes automatically when script ends
5. Can be manually closed with: `$conn->close()`

### File Location:
- **Path:** `dbsetting/connection.php`
- **Included by:** `dbsetting/config.php` automatically
- **Usage:** All PHP files require `dbsetting/config.php` which includes this file
- **Separation:** Database credentials are kept separate from application logic

### Best Practices:
- Should use **prepared statements** for queries (prevents SQL injection)
- Should use **try-catch** blocks for better error handling
- Should store credentials in **config file** (not hardcoded)
- Should use **environment variables** in production
- Should implement **connection pooling** for better performance

### Database Tables Used:
Once connected, the system uses these tables:
- `users` - Stores user accounts (admin and staff)
- `task_manage` - Stores task information
- `task_assign` - Stores task assignments to users
- `leave_apply` - Stores leave applications
- `department` - Stores department information
- `employee_department` - Links employees to departments

