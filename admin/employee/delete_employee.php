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
$redirectUrl = 'manage_employee.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Use a transaction to safely remove dependent rows then the user
    mysqli_begin_transaction($conn);

    try {
        // Step 1: Delete tasks from task_manage where users_id matches the deleted employee
        // (This handles tasks that were created/owned by this employee)
        mysqli_query($conn, "DELETE FROM task_manage WHERE users_id = $id");

        // Step 2: Remove dependent records referencing users_id
        mysqli_query($conn, "DELETE FROM leave_apply WHERE users_id = $id");
        mysqli_query($conn, "DELETE FROM task_assign WHERE users_id = $id");
        mysqli_query($conn, "DELETE FROM employee_department WHERE users_id = $id");
        
        // Step 3: Delete orphaned tasks (tasks that have no assignments left in task_assign)
        // First, get all task_ids from task_assign to see which tasks still have assignments
        $assigned_tasks_query = "SELECT DISTINCT task_id FROM task_assign";
        $assigned_tasks_result = mysqli_query($conn, $assigned_tasks_query);
        $assigned_task_ids = [];
        if ($assigned_tasks_result && mysqli_num_rows($assigned_tasks_result) > 0) {
            while ($row = mysqli_fetch_assoc($assigned_tasks_result)) {
                $assigned_task_ids[] = (int)$row['task_id'];
            }
        }
        
        // If there are no assigned tasks left, delete all tasks from task_manage
        // Otherwise, delete only tasks that are not in the assigned list
        if (empty($assigned_task_ids)) {
            // No tasks are assigned to anyone, delete all tasks
            mysqli_query($conn, "DELETE FROM task_manage");
        } else {
            // Delete tasks that are not assigned to any employee
            $assigned_ids_str = implode(',', $assigned_task_ids);
            mysqli_query($conn, "DELETE FROM task_manage WHERE task_id NOT IN ($assigned_ids_str)");
        }

        // Step 4: Finally remove the user
        $result = mysqli_query($conn, "DELETE FROM users WHERE users_id = $id");

        if (!$result) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_commit($conn);
        $message = 'Employee deleted successfully! Redirecting...';
        $alertType = 'success';
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = 'Error deleting employee: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        $alertType = 'danger';
    }
} else {
    $message = 'Invalid employee ID.';
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
        <a href="<?php echo $redirectUrl; ?>" class="btn btn-secondary">Back to Manage Employees</a>
      </div>
    <?php } ?>
  </div>
  
</div>

<?php include FOOTER_PATH; ?>
