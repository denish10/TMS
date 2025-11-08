<?php
session_start();
require_once __DIR__ . '/../../dbsetting/config.php'; 
include USER_HEADER_PATH;
include USER_SIDEBAR_PATH;


// Make sure user is logged in
if (!isset($_SESSION['users_id'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$user_id = (int) $_SESSION['users_id'];
?>

<div class="user_view_leave_status">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">Your Leave Applications</h3>
  </div>

  <div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by subject, message, dates, or status...">
  </div>

  <table class="table table-bordered table-striped table-hover" id="leave_table">
    <thead class="table-dark">
      <tr>
        <th>S.NO</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $sno = 1;
      $query = "SELECT subject, message, start_date, end_date, status FROM leave_apply WHERE users_id = $user_id ORDER BY created_date DESC";
      $result = mysqli_query($conn, $query);

      if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $subject = $row['subject'];
          $message = $row['message'];
          $start_date = $row['start_date'];
          $end_date = $row['end_date'];
          $status = $row['status'];
    ?>
    <tr>
      <td><?php echo $sno++; ?></td>
      <td><?php echo htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'); ?></td>
      <td><?php echo nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')); ?></td>
      <td><?php echo htmlspecialchars($start_date, ENT_QUOTES, 'UTF-8'); ?></td>
      <td><?php echo htmlspecialchars($end_date, ENT_QUOTES, 'UTF-8'); ?></td>
      <td>
        <?php if ($status == 'Approved') { ?>
          <span class="badge bg-success">Approved</span>
        <?php } elseif ($status == 'Rejected') { ?>
          <span class="badge bg-danger">Rejected</span>
        <?php } else { ?>
          <span class="badge bg-warning text-dark">Pending</span>
        <?php } ?>
      </td>
    </tr>
    <?php 
        }
      } else { ?>
    <tr>
      <td colspan="6" class="text-center">No leave applications found.</td>
    </tr>
    <?php } ?>
    </tbody>
  </table>
</div>

<?php include USER_FOOTER_PATH ?>

<script>
  document.getElementById("searchInput").addEventListener("keyup", function() {
    var value = this.value.toLowerCase();
    var rows = document.querySelectorAll("#leave_table tbody tr");

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
