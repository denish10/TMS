<?php
session_start();
require_once __DIR__ . '/../../dbsetting/config.php';

// Check if user is logged in
if (!isset($_SESSION['users_id'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

include USER_HEADER_PATH;
include USER_SIDEBAR_PATH;

$user_id = $_SESSION['users_id'];
?>

<div class="user_task_update">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">Your Tasks</h3>
  </div>

  <div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by title, description, dates, or status...">
  </div>

  <table class="table table-bordered table-striped table-hover" id="task_table">
    <thead class="table-dark">
      <tr>
        <th>S.No</th>
        <th>Task ID</th>
        <th>Task Title</th>
        <th>Description</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $sno = 1;
      $query = "
        SELECT ta.task_id, ta.status, tm.task_title, tm.task_description, tm.start_date, tm.end_date
        FROM task_assign ta
        JOIN task_manage tm ON ta.task_id = tm.task_id
        WHERE ta.users_id = $user_id
      ";
      $query_run = mysqli_query($conn, $query);

      if (mysqli_num_rows($query_run) > 0) {
        while ($row = mysqli_fetch_assoc($query_run)) {
    ?>
    <tr>
      <td><?php echo $sno++; ?></td>
      <td><?php echo $row['task_id']; ?></td>
      <td><?php echo $row['task_title']; ?></td>
      <td><?php 
        $desc = htmlspecialchars($row['task_description']);
        echo (strlen($desc) > 120) ? substr($desc, 0, 120) . '...' : $desc; 
      ?></td>
      <td><?php echo $row['start_date']; ?></td>
      <td><?php echo $row['end_date']; ?></td>
      <td>
        <?php if ($row['status'] == 'Not Started') { ?>
          <span class="badge bg-secondary">Not Started</span>
        <?php } elseif ($row['status'] == 'In Progress') { ?>
          <span class="badge bg-primary">In Progress</span>
        <?php } elseif ($row['status'] == 'Completed') { ?>
          <span class="badge bg-success">Completed</span>
        <?php } elseif ($row['status'] == 'On Hold') { ?>
          <span class="badge bg-warning text-dark">On Hold</span>
        <?php } elseif ($row['status'] == 'Cancelled') { ?>
          <span class="badge bg-danger">Cancelled</span>
        <?php } ?>
      </td>
      <td>
        <div class="d-flex">
          <a href="task_view.php?id=<?php echo $row['task_id']; ?>" 
             class="btn btn-sm btn-primary me-2 text-white text-decoration-none">View</a>
          <a href="task_status.php?id=<?php echo $row['task_id']; ?>" 
             class="btn btn-sm btn-warning me-2 text-white text-decoration-none">Update</a>
        </div>
      </td>
    </tr>
    <?php
        }
      } else {
        echo '<tr><td colspan="8" class="text-center">No tasks assigned yet.</td></tr>';
      }
    ?>
    </tbody>
  </table>
</div>

<?php include(USER_FOOTER_PATH); ?>

