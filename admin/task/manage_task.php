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
?>

<div class="manage_task">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manage Tasks</h3>
  </div>

  <div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by title, employee, priority, or status...">
  </div>

  <table class="table table-bordered table-striped table-hover" id="task_table">
    <thead class="table-dark">
      <tr>
        <th>S.No</th>
        <th>Task Title</th>
        <th>Employee</th>
        <th>Priority</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sno = 1;
      $query = "
        SELECT 
          ta.record_id, 
          ta.status,
          tm.task_title, 
          tm.priority,
          u.fullname
        FROM task_assign ta
        JOIN task_manage tm ON ta.task_id = tm.task_id
        JOIN users u ON ta.users_id = u.users_id
        ORDER BY ta.task_id DESC
      ";

      $result = mysqli_query($conn, $query);

      if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          ?>
          <tr>
            <td><?php echo $sno++; ?></td>
            <td><?php echo htmlspecialchars($row['task_title']); ?></td>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td>
              <?php
              if ($row['priority'] == 'High') {
                echo '<span class="badge bg-danger">High</span>';
              } elseif ($row['priority'] == 'Medium') {
                echo '<span class="badge bg-warning text-dark">Medium</span>';
              } elseif ($row['priority'] == 'Low') {
                echo '<span class="badge bg-success">Low</span>';
              } else {
                echo '-';
              }
              ?>
            </td>
            <td>
              <?php
              if ($row['status'] == 'Not Started') {
                echo '<span class="badge bg-secondary">Not Started</span>';
              } elseif ($row['status'] == 'In Progress') {
                echo '<span class="badge bg-primary">In Progress</span>';
              } elseif ($row['status'] == 'Completed') {
                echo '<span class="badge bg-success">Completed</span>';
              } elseif ($row['status'] == 'On Hold') {
                echo '<span class="badge bg-warning text-dark">On Hold</span>';
              } elseif ($row['status'] == 'Cancelled') {
                echo '<span class="badge bg-danger">Cancelled</span>';
              } elseif ($row['status'] == 'Pending') {
                echo '<span class="badge bg-info text-dark">Pending</span>';
              } else {
                echo '-';
              }
              ?>
            </td>
            <td>
              <div class="d-flex">
                <a href="view_task.php?id=<?php echo $row['record_id']; ?>" class="btn btn-sm btn-info me-2 text-white">View</a>
                <a href="edit_task.php?id=<?php echo $row['record_id']; ?>" class="btn btn-sm btn-primary me-2 text-white">Edit</a>
                <a href="delete_task.php?id=<?php echo $row['record_id']; ?>" 
                   onclick="return confirm('Are you sure you want to delete this task?');"
                   class="btn btn-sm btn-danger text-white">Delete</a>
              </div>
            </td>
          </tr>
          <?php
        }
      } else {
        echo '<tr><td colspan="6" class="text-center text-danger">No tasks found.</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>

<?php
include FOOTER_PATH;
?>

