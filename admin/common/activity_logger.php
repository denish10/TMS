<?php
/**
 * Activity Logger for Admin Dashboard
 * Records all admin activities and system events
 */

require_once __DIR__ . '/../../dbsetting/config.php';

/**
 * Log an activity to the database
 * 
 * @param string $activity_type Type of activity (e.g., 'task_created', 'employee_added', 'login', etc.)
 * @param string $description Description of the activity
 * @param int|null $user_id User ID who performed the action (defaults to session user)
 * @param string|null $related_table Table related to the activity (optional)
 * @param int|null $related_id ID in the related table (optional)
 * @return bool True on success, False on failure
 */
function logActivity($activity_type, $description, $user_id = null, $related_table = null, $related_id = null) {
    global $conn;
    
    // Get user ID from session if not provided
    if ($user_id === null) {
        $user_id = isset($_SESSION['users_id']) ? intval($_SESSION['users_id']) : null;
    }
    
    // Get user name for display
    $user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'System';
    
    // Escape inputs
    $activity_type = mysqli_real_escape_string($conn, $activity_type);
    $description = mysqli_real_escape_string($conn, $description);
    $user_id = $user_id ? intval($user_id) : 'NULL';
    $related_table = $related_table ? "'" . mysqli_real_escape_string($conn, $related_table) . "'" : 'NULL';
    $related_id = $related_id ? intval($related_id) : 'NULL';
    
    // Insert into activity_logs table
    $query = "INSERT INTO activity_logs 
              (activity_type, description, user_id, user_name, related_table, related_id, created_at) 
              VALUES 
              ('$activity_type', '$description', $user_id, '$user_name', $related_table, $related_id, NOW())";
    
    return mysqli_query($conn, $query);
}

/**
 * Get recent activities for dashboard
 * 
 * @param int $limit Number of activities to retrieve
 * @return array Array of activity records
 */
function getRecentActivities($limit = 10) {
    global $conn;
    
    $limit = intval($limit);
    $query = "SELECT * FROM activity_logs 
              ORDER BY created_at DESC 
              LIMIT $limit";
    
    $result = mysqli_query($conn, $query);
    $activities = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $activities[] = $row;
        }
    }
    
    return $activities;
}

/**
 * Get activity icon class based on activity type
 * 
 * @param string $activity_type Type of activity
 * @return string Bootstrap icon class and color
 */
function getActivityIcon($activity_type) {
    $icons = [
        'task_created' => ['icon' => 'fa-tasks', 'color' => 'bg-info'],
        'task_updated' => ['icon' => 'fa-edit', 'color' => 'bg-primary'],
        'task_completed' => ['icon' => 'fa-check', 'color' => 'bg-success'],
        'task_deleted' => ['icon' => 'fa-trash', 'color' => 'bg-danger'],
        'employee_added' => ['icon' => 'fa-user-plus', 'color' => 'bg-success'],
        'employee_updated' => ['icon' => 'fa-user-edit', 'color' => 'bg-primary'],
        'employee_deleted' => ['icon' => 'fa-user-times', 'color' => 'bg-danger'],
        'department_added' => ['icon' => 'fa-building', 'color' => 'bg-info'],
        'department_updated' => ['icon' => 'fa-edit', 'color' => 'bg-primary'],
        'department_deleted' => ['icon' => 'fa-trash', 'color' => 'bg-danger'],
        'leave_applied' => ['icon' => 'fa-calendar-check', 'color' => 'bg-warning'],
        'leave_approved' => ['icon' => 'fa-check-circle', 'color' => 'bg-success'],
        'leave_rejected' => ['icon' => 'fa-times-circle', 'color' => 'bg-danger'],
        'login' => ['icon' => 'fa-sign-in-alt', 'color' => 'bg-primary'],
        'logout' => ['icon' => 'fa-sign-out-alt', 'color' => 'bg-secondary'],
        'profile_updated' => ['icon' => 'fa-user-edit', 'color' => 'bg-info'],
        'password_changed' => ['icon' => 'fa-key', 'color' => 'bg-warning'],
    ];
    
    return $icons[$activity_type] ?? ['icon' => 'fa-circle', 'color' => 'bg-secondary'];
}

/**
 * Format time ago string
 * 
 * @param string $datetime Database datetime string
 * @return string Human readable time ago
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}


