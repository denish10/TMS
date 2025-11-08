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

<div class="manage_department">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manage Departments</h3>
    <a href="add_department.php" class="btn btn-sm btn-success text-white">+ Add Department</a>
  </div>

  <div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by department name...">
  </div>


<table class="table table-sm table-bordered table-striped table-hover text-center align-middle" id="department_table">
    <thead class="table-dark">
      <tr>
        <th>S.No</th>
        <th>Dept ID</th>
        <th>Department Name</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sno = 1;
      $query = "SELECT * FROM department ORDER BY department_id ASC";
      $query_run = mysqli_query($conn, $query);

      if ($query_run && mysqli_num_rows($query_run) > 0) {
          while ($row = mysqli_fetch_assoc($query_run)) {
      ?>
            <tr>
              <td><?php echo $sno++; ?></td>
              <td><?php echo $row['department_id']; ?></td>
              <td class="text-center"><?php echo $row['department_name']; ?></td>
              <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                  <a href="edit_department.php?id=<?php echo $row['department_id']; ?>" 
                     class="btn btn-sm btn-warning text-white text-decoration-none">Edit</a>
                  <a href="delete_department.php?id=<?php echo $row['department_id']; ?>" 
                     class="btn btn-sm btn-danger text-white text-decoration-none" 
                     onclick="return confirm('Are you sure you want to delete this department?');">Delete</a>
                </div>
              </td>
            </tr>
      <?php
          }
      } else {
          echo '<tr><td colspan="4" class="text-center text-danger">No departments found.</td></tr>';
      }
      ?>
    </tbody>
</table>

</div>



<?php
include FOOTER_PATH;
?>
