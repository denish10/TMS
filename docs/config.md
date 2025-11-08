# File: dbsetting/config.php

## Purpose

This file contains configuration settings and constants for the entire Task Management System. It sets up timezone, defines base URLs and file paths, and includes the database connection file. This centralized configuration makes it easy to manage settings and ensures consistency across the application. The file is located in the `dbsetting/` folder, which contains all database-related configuration files.

## Key Features

- Timezone configuration
- Base URL definition
- File path constants
- Database connection inclusion
- Centralized configuration management
- Error handling for missing files

## Code Breakdown

### 1. Timezone Configuration

```php
// Set timezone to Nepal (UTC+5:45)
date_default_timezone_set('Asia/Kathmandu');
```

**Explanation:**
- Sets PHP timezone to Asia/Kathmandu (Nepal timezone)
- UTC+5:45 offset (Nepal Standard Time)
- Ensures all date/time functions use correct timezone
- Important for accurate timestamps in database

### 2. Base URL Definition

```php
define('BASE_URL', 'http://localhost/TMS');
```

**Explanation:**
- Defines base URL of the application
- Used for redirects and link generation
- Currently set to localhost (development environment)
- Should be changed to production URL in live server

### 3. Base Path Definition

```php
define('BASE_PATH', dirname(__DIR__));
```

**Explanation:**
- Defines base directory path of the application (parent directory of dbsetting)
- `dirname(__DIR__)` gets the parent directory of the current directory (dbsetting)
- Used to construct absolute file paths relative to project root
- Ensures paths work regardless of current working directory
- Since config.php is in `dbsetting/`, BASE_PATH points to the project root (TMS/)

### 4. File Path Constants

```php
define('CONNECTION_PATH', __DIR__ . '/connection.php');
define('HEADER_PATH', BASE_PATH . '/admin/common/header.php');
define('SIDEBAR_PATH', BASE_PATH . '/admin/common/sidebar.php');
define('FOOTER_PATH', BASE_PATH . '/admin/common/footer.php');

define('USER_HEADER_PATH', BASE_PATH . '/user/common/user_header.php');
define('USER_SIDEBAR_PATH', BASE_PATH . '/user/common/user_sidebar.php');
define('USER_FOOTER_PATH', BASE_PATH . '/user/common/user_footer.php');
```

**Explanation:**
- `CONNECTION_PATH`: Points to connection.php in the same dbsetting directory
- Admin interface files: Located in `admin/common/` folder
- User interface files: Located in `user/common/` folder with `user_` prefix
- All paths relative to BASE_PATH (project root)
- Makes it easy to include files throughout the application
- Separates admin and user interface components

### 5. Database Connection Inclusion

```php
if (file_exists(CONNECTION_PATH)) {
    require_once CONNECTION_PATH;
} else {
    die("Database connection file not found: " . CONNECTION_PATH);
}
```

**Explanation:**
- Checks if connection file exists before including
- Uses `file_exists()` to verify file presence
- Includes connection file if it exists
- Stops script execution with error message if file not found
- Prevents fatal errors from missing files

## Output / Result

**When the file is included:**

1. **Configuration Setup:**
   - Timezone is set to Asia/Kathmandu
   - All constants are defined
   - Database connection is established
   - No visible output (configuration only)

2. **Constants Available:**
   - `BASE_URL` - Application base URL
   - `BASE_PATH` - Application base directory
   - `CONNECTION_PATH` - Database connection file path
   - `HEADER_PATH`, `SIDEBAR_PATH`, `FOOTER_PATH` - Admin interface files
   - `USER_HEADER_PATH`, `USER_SIDEBAR_PATH`, `USER_FOOTER_PATH` - User interface files

3. **Database Connection:**
   - `$conn` variable is available globally
   - Can be used for database queries
   - Connection established successfully

## Additional Notes

### Configuration Management:
- **Centralized:** All settings in one file
- **Easy to Update:** Change values in one place
- **Consistent:** Same settings used throughout application
- **Maintainable:** Easy to modify and update

### Timezone Settings:
- **Current:** Asia/Kathmandu (UTC+5:45)
- **Changeable:** Can be updated to any timezone
- **Important:** Affects all date/time functions
- **Database:** Timestamps stored in database timezone

### URL Configuration:
- **Development:** http://localhost/TMS
- **Production:** Should be changed to production URL
- **Usage:** Used for redirects and absolute links
- **Update:** Change BASE_URL for different environments

### File Paths:
- **Location:** File is located in `dbsetting/config.php`
- **Absolute Paths:** Uses BASE_PATH (project root) for consistency
- **Database Files:** Connection and config files are in `dbsetting/` folder
- **Separate UI:** Admin files in `admin/common/`, User files in `user/common/`
- **Flexible:** Easy to reorganize file structure
- **Structure:** Clear separation between database config and application code

### Security Considerations:
- **No Sensitive Data:** Does not store passwords or API keys
- **Database Credentials:** Stored in connection.php (separate file)
- **Error Messages:** Should be minimal in production
- **File Existence:** Checks before including files

