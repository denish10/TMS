<?php
session_start();

require_once __DIR__ . '/../../dbsetting/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$status = 'info';
$msg = '';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $query = "UPDATE leave_apply SET status='Rejected' WHERE leave_id = $id";
    $query_run = mysqli_query($conn, $query);
    if ($query_run) {
        $status = 'success';
        $msg = 'Leave rejected successfully!';
    } else {
        $status = 'danger';
        $msg = 'Failed to reject leave: ' . mysqli_error($conn);
    }
} else {
    $status = 'danger';
    $msg = 'Invalid leave ID.';
}

header('Location: leave_application.php?status=' . urlencode($status) . '&msg=' . urlencode($msg));
exit;
?>