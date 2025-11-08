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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch task details
$query = "
    SELECT 
        ta.record_id, 
        ta.task_id, 
        ta.status,
        tm.task_title, 
        tm.task_description, 
        tm.created_date, 
        tm.start_date, 
        tm.end_date, 
        tm.priority,
        u.users_id,
        u.fullname
    FROM task_assign ta
    JOIN task_manage tm ON ta.task_id = tm.task_id
    JOIN users u ON ta.users_id = u.users_id
    WHERE ta.record_id = $id
    LIMIT 1
";

$result = mysqli_query($conn, $query);
$task = mysqli_fetch_assoc($result);
?>

<div class="container mt-4">
  <h3 class="text-center mb-4">Task Details</h3>

  <?php if ($task) { ?>
    <div class="card shadow p-4" style="margin-left: 204px;">
      <p><strong>Task ID:</strong> <?php echo (int)$task['task_id']; ?></p>
      <h5 class="mb-3">Task Title: <?php echo htmlspecialchars($task['task_title']); ?></h5>

      <p><strong>Assigned To:</strong> <?php echo htmlspecialchars($task['fullname']); ?></p>
      <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($task['task_description'])); ?></p>

      <p><strong>Priority:</strong>
        <?php
          if ($task['priority'] == 'High') {
            echo '<span class="badge bg-danger">High</span>';
          } elseif ($task['priority'] == 'Medium') {
            echo '<span class="badge bg-warning text-dark">Medium</span>';
          } elseif ($task['priority'] == 'Low') {
            echo '<span class="badge bg-success">Low</span>';
          } else {
            echo '-';
          }
        ?>
      </p>

      <p><strong>Status:</strong>
        <?php
          if ($task['status'] == 'Not Started') {
            echo '<span class="badge bg-secondary">Not Started</span>';
          } elseif ($task['status'] == 'In Progress') {
            echo '<span class="badge bg-primary">In Progress</span>';
          } elseif ($task['status'] == 'Completed') {
            echo '<span class="badge bg-success">Completed</span>';
          } elseif ($task['status'] == 'On Hold') {
            echo '<span class="badge bg-warning text-dark">On Hold</span>';
          } elseif ($task['status'] == 'Cancelled') {
            echo '<span class="badge bg-danger">Cancelled</span>';
          } elseif ($task['status'] == 'Pending') {
            echo '<span class="badge bg-info text-dark">Pending</span>';
          } else {
            echo '-';
          }
        ?>
      </p>

      <p><strong>Start Date:</strong> <?php echo htmlspecialchars($task['start_date']); ?></p>
      <p><strong>End Date:</strong> <?php echo htmlspecialchars($task['end_date']); ?></p>
      <p><strong>Created At:</strong> <?php echo htmlspecialchars($task['created_date']); ?></p>

      <div class="mt-3">
        <a href="manage_task.php" class="btn btn-secondary">Back</a>
        <a href="edit_task.php?id=<?php echo $task['record_id']; ?>" class="btn btn-primary">Edit</a>
      </div>
    </div>
  <?php } else { ?>
    <div class="alert alert-danger text-center">⚠️ Task not found.</div>
    <div class="text-center">
      <a href="manage_task.php" class="btn btn-secondary">Back</a>
    </div>
  <?php } ?>
</div>

<?php
include FOOTER_PATH;
?>
