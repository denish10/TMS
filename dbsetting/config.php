<?php

// Set timezone to Nepal (UTC+5:45)
date_default_timezone_set('Asia/Kathmandu');

define('BASE_URL', 'http://localhost/TMS');

// Get the base path (parent directory of dbsetting)
define('BASE_PATH', dirname(__DIR__));

// Database connection path
define('CONNECTION_PATH', __DIR__ . '/connection.php');

// Admin common files path
define('HEADER_PATH', BASE_PATH . '/admin/common/header.php');
define('SIDEBAR_PATH', BASE_PATH . '/admin/common/sidebar.php');
define('FOOTER_PATH', BASE_PATH . '/admin/common/footer.php');

// User common files path
define('USER_HEADER_PATH', BASE_PATH . '/user/common/user_header.php');
define('USER_SIDEBAR_PATH', BASE_PATH . '/user/common/user_sidebar.php');
define('USER_FOOTER_PATH', BASE_PATH . '/user/common/user_footer.php');

// Alert path (if it exists)
define('ALERT_PATH', BASE_PATH . '/common/alert.php');

// Include database connection
if (file_exists(CONNECTION_PATH)) {
    require_once CONNECTION_PATH;
} else {
    die("Database connection file not found: " . CONNECTION_PATH);
}

