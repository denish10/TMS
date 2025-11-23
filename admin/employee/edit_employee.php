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

$id = $_GET['id'] ?? 0;
$message = "";

$query = "SELECT u.*, ed.department_id 
          FROM users u 
          LEFT JOIN employee_department ed ON u.users_id = ed.users_id 
          WHERE u.users_id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$employee_name = $data['fullname'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname   = $_POST['fullname'];
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $mobile     = $_POST['mobile'];
    $department = $_POST['department'];
    $role       = $_POST['role'];

    // No-op detection
    $current_fullname = $data['fullname'] ?? '';
    $current_username = $data['username'] ?? '';
    $current_email    = $data['email'] ?? '';
    $current_mobile   = $data['mobile'] ?? '';
    $current_role     = $data['role'] ?? '';
    $current_dept_id  = $data['department_id'] ?? '';

    if (
        $fullname === $current_fullname &&
        $username === $current_username &&
        $email === $current_email &&
        $mobile === $current_mobile &&
        $role === $current_role &&
        $department == $current_dept_id
    ) {
        $message = "ℹ️ No changes detected.";
    } else {
        $updateUser = "UPDATE users SET 
        fullname='$fullname',
        username='$username',
        email='$email',
        mobile='$mobile',
        role='$role'
        WHERE users_id=$id";

        if (mysqli_query($conn, $updateUser)) {
        $checkDept = mysqli_query($conn, "SELECT * FROM employee_department WHERE users_id=$id");
        if (mysqli_num_rows($checkDept) > 0) {
            $updateDept = "UPDATE employee_department SET department_id='$department' WHERE users_id=$id";
            mysqli_query($conn, $updateDept);
        } else {
            $insertDept = "INSERT INTO employee_department (users_id, department_id) VALUES ($id, '$department')";
            mysqli_query($conn, $insertDept);
        }

        $message = "✅ Employee details updated successfully. Redirecting...";
        
        
        echo '<meta http-equiv="refresh" content="2;url=view_employee.php?id=' . $id . '">';
    } else {
        $message = "❌ Error updating employee: " . mysqli_error($conn);
    }
    }
}
?>

<div class="container edit_employee card p-4">
   <center><h2>Edit Employee</h2></center> 

    <?php if (!empty($message)): ?>
      <div class="alert alert-info text-center mt-3"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" id="updateForm" onsubmit="return confirm('Are you sure you want to update details for <?php echo addslashes($employee_name); ?>?');">
        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="fullname" class="form-control" value="<?php echo $data['fullname']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $data['username']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo $data['email']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Mobile</label>
            <input type="text" name="mobile" class="form-control" value="<?php echo $data['mobile']; ?>" required>
        </div>

       
          <div class=" mb-3">
            <label>Department</label>
            <select name="department" class="form-control" required>
              <option value="" disabled>Select Department</option>
              <?php
              $dept_query = mysqli_query($conn, "SELECT * FROM department ORDER BY department_name ASC");
              while ($dept = mysqli_fetch_assoc($dept_query)) {
                  $selected = ($dept['department_id'] == $data['department_id']) ? "selected" : "";
                  echo "<option value='".$dept['department_id']."' $selected>".$dept['department_name']."</option>";
              }
              ?>
            </select>
        

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="staff" <?php if($data['role'] == 'staff') echo 'selected'; ?>>Staff</option>
                <option value="admin" <?php if($data['role'] == 'admin') echo 'selected'; ?>>Admin</option>
            </select>
        </div>

        <div class="d-grid gap-2 col-4 mx-auto">
          <button type="submit" class="btn btn-warning text-white">Update</button>
        </div>
    </form>
</div>
            </div>

<?php include FOOTER_PATH; ?>
