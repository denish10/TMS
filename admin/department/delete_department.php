<?php
session_start();

require_once __DIR__ . '/../../dbsetting/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

include HEADER_PATH;
include SIDEBAR_PATH;

// Defaults for alert UI
$message = '';
$alertType = 'info';
$redirectUrl = 'manage_department.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Check if any employees are assigned to this department
    $check = "SELECT COUNT(*) AS cnt FROM employee_department WHERE department_id = $id";
    $result = mysqli_query($conn, $check);
    $row = mysqli_fetch_assoc($result);

    if ($row['cnt'] > 0) {
        $message = 'Cannot delete department. Employees are still assigned to it.';
        $alertType = 'warning';
    }

    // Delete department if no employees assigned
    if ($message === '') {
        $query = "DELETE FROM department WHERE department_id = $id";
        if (mysqli_query($conn, $query)) {
            $message = 'Department deleted successfully! Redirecting...';
            $alertType = 'success';
            $redirectUrl = 'manage_department.php?msg=deleted';
        } else {
            $message = 'Error deleting department: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8');
            $alertType = 'danger';
        }
    }
} else {
    $message = 'Invalid department ID.';
    $alertType = 'danger';
}
?>

<div class="container" style="max-width: 700px; margin-top: 80px;">
  <div class="card p-4">
    <?php if (!empty($message)) { ?>
      <div class="alert alert-<?php echo $alertType; ?>" role="alert">
        <?php echo $message; ?>
      </div>
      <meta http-equiv="refresh" content="2;url=<?php echo $redirectUrl; ?>">
      <div class="mt-3">
        <a href="<?php echo $redirectUrl; ?>" class="btn btn-secondary">Back to Manage Departments</a>
      </div>
    <?php } ?>
  </div>
</div>

<?php include FOOTER_PATH; ?>
