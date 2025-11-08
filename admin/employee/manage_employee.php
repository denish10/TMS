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

<div class="manage_employee">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manage Employees</h3>
    <a href="add_employee.php" class="btn btn-sm btn-success text-white">+ Add Employee</a>
  </div>

  <div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by name, username, email, or department...">
  </div>

  <table class="table table-bordered table-striped table-hover" id="employee_table">
    <thead class="table-dark">
      <tr>
        <th>S.No</th>
        <th>Employee ID</th>
        <th>Full Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Department</th>
        <th>Role</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sno = 1;
      
      $query = "
        SELECT u.users_id, u.fullname, u.username, u.email, u.mobile,
               d.department_name, u.role
        FROM users u
        LEFT JOIN employee_department ed ON u.users_id = ed.users_id
        LEFT JOIN department d ON ed.department_id = d.department_id
        ORDER BY u.users_id ASC
      ";
      $query_run = mysqli_query($conn, $query);

      if ($query_run && mysqli_num_rows($query_run) > 0) {
          while ($row = mysqli_fetch_assoc($query_run)) {
      ?>
            <tr>
              <td><?php echo $sno++; ?></td>
              <td><?php echo $row['users_id']; ?></td>
              <td><?php echo $row['fullname']; ?></td>
              <td><?php echo $row['username']; ?></td>
              <td><?php echo $row['email']; ?></td>
              <td><?php echo $row['mobile']; ?></td>
              <td><?php echo $row['department_name'] ?? 'N/A'; ?></td>
              <td><?php echo ucfirst($row['role']); ?></td>
              <td>
  <div class="d-flex">
    <a href="view_employee.php?id=<?php echo $row['users_id']; ?>" 
       class="btn btn-sm btn-primary me-2 text-white text-decoration-none">
       View
    </a>
    <a href="edit_employee.php?id=<?php echo $row['users_id']; ?>" 
       class="btn btn-sm btn-warning me-2 text-white text-decoration-none">
       Edit
    </a>
    <a href="reset_password.php?id=<?php echo $row['users_id']; ?>" class="btn btn-sm btn-info mb-1 me-2">Reset</a>
    <a href="delete_employee.php?id=<?php echo $row['users_id']; ?>" 
       class="btn btn-sm btn-danger text-white text-decoration-none"
       onclick="return confirm('Are you sure you want to delete <?php echo addslashes($row['username']); ?>?');">
       Delete
    </a>
  </div>
</td>

            </tr>
      <?php
          }
      } else {
          echo '<tr><td colspan="10" class="text-center text-danger">No employees found.</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>


<?php include FOOTER_PATH; ?>