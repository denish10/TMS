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
$redirectUrl = 'leave_application.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $query = "DELETE FROM leave_apply WHERE leave_id = $id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $message = 'Leave application deleted successfully! Redirecting...';
        $alertType = 'success';
    } else {
        $message = 'Error deleting leave application: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8');
        $alertType = 'danger';
    }
} else {
    $message = 'Invalid leave application ID.';
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
        <a href="<?php echo $redirectUrl; ?>" class="btn btn-secondary">Back to Leave Applications</a>
      </div>
    <?php } ?>
  </div>
  
</div>

<?php include FOOTER_PATH; ?>
