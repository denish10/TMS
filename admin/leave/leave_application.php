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

<div class="leave_application">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>All Leave Applications</h3>
  </div>

  <?php
  $alertType = isset($_GET['status']) ? preg_replace('/[^a-z]/', '', $_GET['status']) : '';
  $alertMsg  = isset($_GET['msg']) ? $_GET['msg'] : '';
  if ($alertType && $alertMsg) {
  ?>
    <div class="alert alert-<?php echo htmlspecialchars($alertType, ENT_QUOTES, 'UTF-8'); ?>" role="alert">
      <?php echo htmlspecialchars($alertMsg, ENT_QUOTES, 'UTF-8'); ?>
    </div>
  <?php } ?>

  <div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by employee, subject, dates, or status...">
  </div>

  <table class="table table-bordered table-striped table-hover" id="task_table">
    <thead class="table-dark">
      <tr>
        <th>S.NO</th>
        <th>Employee Name</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Created At</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $query = "SELECT * FROM leave_apply";
      $query_run = mysqli_query($conn, $query);
      $sno = 1;

      while ($row = mysqli_fetch_assoc($query_run)) {
          $emp_id = $row['users_id'];
          
          $emp_query = "SELECT fullname FROM users WHERE users_id = $emp_id"; 
          $emp_result = mysqli_query($conn, $emp_query);
          $emp_row = mysqli_fetch_assoc($emp_result);
          $emp_name = $emp_row['fullname'] ?? 'Unknown';
      ?>
        <tr>
          <td><?= $sno++; ?></td>
          <td><?= $emp_name; ?></td>
          <td><?= $row['subject']; ?></td>
          <td><?= $row['message']; ?></td>
          <td><?= $row['start_date']; ?></td>
          <td><?= $row['end_date']; ?></td>
          <td><?= $row['created_date']; ?></td> 
          <td>
            <?php if ($row['status'] == 'Approved') { ?>
              <span class="badge bg-success">Approved</span>
            <?php } elseif ($row['status'] == 'Rejected') { ?>
              <span class="badge bg-danger">Rejected</span>
            <?php } else { ?>
              <span class="badge bg-warning text-dark">Pending</span>
            <?php } ?>
          </td>
          <td>
            <div class="d-flex">
              <a href="approve_leave.php?id=<?= $row['leave_id']; ?>" class="btn btn-success btn-sm me-2">Approve</a>
              <a href="reject_leave.php?id=<?= $row['leave_id']; ?>" class="btn btn-danger btn-sm me-2">Reject</a>
              <a href="delete_leave_application.php?id=<?= $row['leave_id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this leave application?');">Delete</a>
            </div>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<?php include FOOTER_PATH; ?>

<script>
  document.getElementById("searchInput").addEventListener("keyup", function() {
    var value = this.value.toLowerCase();
    var rows = document.querySelectorAll("#task_table tbody tr");

    rows.forEach(function(row) {
      var text = row.textContent.toLowerCase();
      if (text.includes(value)) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });
</script>
