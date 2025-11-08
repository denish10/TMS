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

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    $query = "
        SELECT u.*, d.department_name
        FROM users u
        LEFT JOIN employee_department ed ON u.users_id = ed.users_id
        LEFT JOIN department d ON ed.department_id = d.department_id
        WHERE u.users_id = $user_id
        LIMIT 1
    ";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $employee = mysqli_fetch_assoc($result);
    } else {
        echo "<div class='alert alert-danger text-center'>Employee not found.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger text-center'>Invalid request.</div>";
    exit;
}
?>

<style>
/* Table Styling */
.employee-table {
  background-color: transparent;
}
.employee-table th,
.employee-table td {
  background-color: transparent !important; 
  color: #fff;
  padding: 10px;
  vertical-align: middle;
}

/* Container */
.employee_view {
    background: linear-gradient(to right, #141e30, #243b55);
    width: 59vw;
    margin-left: 370px;
    margin-top: 104px;
    height: 80vh;
}

/* Profile Edit Icon */
.profile-pic-container {
  position: relative;
  display: inline-block;
}
.edit-pic-btn {
  position: absolute;
  bottom: 0;
  right: 10px;
  background: #0d6efd;
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 35px;
  height: 35px;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.edit-pic-btn:hover {
  background: #0b5ed7;
}
</style>

<!-- Employee Details -->
<div class="container-fluid py-5 card employee_view">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <!-- Header -->
      <h4 class="text-center mb-4 text-primary fw-bold">Employee Details</h4>

      <div class="row mb-4">
        <!-- Profile Section -->
        <div class="col-md-4 text-center">
          <div class="profile-pic-container">
            <img src="../../assets/uploads/<?php echo $employee['profile_photo'] ?: 'default.png'; ?>" 
                 class="img-thumbnail rounded-circle shadow-sm"
                 style="width: 150px; height: 150px; object-fit: cover;" alt="Profile Photo">
            <!-- Edit Photo Button -->
            <a href="edit_profile_picture.php?id=<?php echo $employee['users_id']; ?>" class="edit-pic-btn">
              ✎
            </a>
          </div>
          <h5 class="mt-3"><?php echo $employee['fullname']; ?></h5>
          <span class="badge bg-secondary"><?php echo ucfirst($employee['role']); ?></span>
        </div>

        <!-- Table Section -->
        <div class="col-md-8">
          <table class="table table-bordered employee-table">
            <tr><th class="w-25">Employee ID:</th><td><?php echo $employee['users_id']; ?></td></tr>
            <tr><th>Username:</th><td><?php echo $employee['username']; ?></td></tr>
            <tr><th>Email:</th><td><?php echo $employee['email']; ?></td></tr>
            <tr><th>Mobile:</th><td><?php echo $employee['mobile']; ?></td></tr>
            <tr><th>Department:</th><td><?php echo $employee['department_name'] ?? 'N/A'; ?></td></tr>
            <tr><th>Created At:</th><td><?php echo $employee['created_at']; ?></td></tr>
            <tr><th>Last Login:</th>
                <td>
                    <?php 
                    if (!empty($employee['last_login']) && $employee['last_login'] != '0000-00-00 00:00:00') {
                        echo date('M j, Y h:i A', strtotime($employee['last_login']));
                    } else {
                        echo '<span class="text-muted">Never</span>';
                    }
                    ?>
                </td>
            </tr>
          </table>
        </div>
      </div>

      <!-- Buttons -->
      <div class="text-center">
        <a href="manage_employee.php" class="btn btn-secondary px-4">Back</a>
        <a href="edit_employee.php?id=<?php echo $employee['users_id']; ?>" class="btn btn-primary px-4">Edit</a>
      </div>
    </div>
  </div>
</div>

<?php include FOOTER_PATH; ?>