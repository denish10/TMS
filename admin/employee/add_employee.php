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

$message = "";
$alertType = "info";
$redirect = false;

$fullname = "";
$username = "";
$email = "";
$mobile = "";
$department = "";
$role = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname'] ?? "");
    $username = trim($_POST['username'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $mobile = trim($_POST['mobile'] ?? "");
    $department = trim($_POST['department'] ?? "");
    $role = trim($_POST['role'] ?? "");
    $password = $_POST['password'] ?? "";

    // Validation
    if ($fullname === '' || $username === '' || $email === '' || $mobile === '' || $department === '' || $role === '' || $password === '') {
        $message = "⚠️ All fields are required.";
        $alertType = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "⚠️ Please enter a valid email address.";
        $alertType = "danger";
    } elseif (!ctype_digit($mobile)) {
        $message = "⚠️ Mobile number should contain only digits.";
        $alertType = "danger";
    } elseif (strlen($mobile) < 10 || strlen($mobile) > 15) {
        $message = "⚠️ Mobile number length should be between 10 and 15 digits.";
        $alertType = "danger";
    } elseif (!in_array($role, ['staff', 'admin'])) {
        $message = "⚠️ Invalid role selected.";
        $alertType = "danger";
    } elseif (strlen($password) < 6) {
        $message = "⚠️ Password should be at least 6 characters.";
        $alertType = "danger";
    } else {
        // Check for duplicate username
        $checkUser = mysqli_query($conn, "SELECT users_id FROM users WHERE username = '".mysqli_real_escape_string($conn, $username)."' LIMIT 1");
        if ($checkUser && mysqli_num_rows($checkUser) > 0) {
            $message = "⚠️ Username already exists.";
            $alertType = "danger";
        } else {
            // Check for duplicate email
            $checkEmail = mysqli_query($conn, "SELECT users_id FROM users WHERE email = '".mysqli_real_escape_string($conn, $email)."' LIMIT 1");
            if ($checkEmail && mysqli_num_rows($checkEmail) > 0) {
                $message = "⚠️ Email already exists.";
                $alertType = "danger";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Escape data for database
                $fullname = mysqli_real_escape_string($conn, $fullname);
                $username = mysqli_real_escape_string($conn, $username);
                $email = mysqli_real_escape_string($conn, $email);
                $mobile = mysqli_real_escape_string($conn, $mobile);
                $department = (int) $department;

                // Insert into users
                $sql_user = "INSERT INTO users (fullname, username, email, mobile, role, password, created_at) 
                            VALUES ('$fullname', '$username', '$email', '$mobile', '$role', '$hashed_password', NOW())";

                if (mysqli_query($conn, $sql_user)) {
                    $new_user_id = mysqli_insert_id($conn);

                    // Map department
                    $sql_dept = "INSERT INTO employee_department (users_id, department_id) VALUES ($new_user_id, $department)";
                    mysqli_query($conn, $sql_dept);

                    $message = "✅ Employee added successfully! Redirecting...";
                    $alertType = "success";
                    $redirect = true;
                } else {
                    $message = "❌ Error: " . mysqli_error($conn);
                    $alertType = "danger";
                }
            }
        }
    }
}
?>

<div class="container-fluid">
  <div class="row justify-content-center align-items-center">
    <div class="card register_page p-4" style="max-width:700px;">
      <center><h3>Add Employee</h3></center>

      <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $alertType; ?> text-center mt-3" role="alert">
          <?php echo $message; ?>
        </div>
        <?php if ($redirect): ?>
          <meta http-equiv="refresh" content="2;url=manage_employee.php">
        <?php endif; ?>
      <?php endif; ?>

      <form action="" method="POST">
        <div class="mb-3">
          <label for="fullname" class="form-label">Full Name:</label>
          <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter full name" value="<?php echo $fullname; ?>">
        </div>

        <div class="mb-3">
          <label for="username" class="form-label">Username:</label>
          <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" value="<?php echo $username; ?>">
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email address:</label>
          <input type="text" class="form-control" id="email" name="email" placeholder="Enter email" value="<?php echo $email; ?>">
        </div>

        <div class="mb-3">
          <label for="mobile" class="form-label">Mobile No:</label>
          <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter mobile number" value="<?php echo $mobile; ?>">
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label>Department:</label>
            <select name="department" class="form-control">
              <option value="" <?php echo $department === '' ? 'selected' : ''; ?>>Select department</option>
              <?php
              $dept_query = mysqli_query($conn, "SELECT * FROM department ORDER BY department_name ASC");
              while ($dept = mysqli_fetch_assoc($dept_query)) {
                  $sel = ($department !== '' && $department == $dept['department_id']) ? 'selected' : '';
                  echo "<option value='".$dept['department_id']."' $sel>".$dept['department_name']."</option>";
              }
              ?>
            </select>
          </div>

          <div class="col-md-4 mb-3">
            <label for="role">Role:</label>
            <select name="role" class="form-control" id="role">
              <option value="" <?php echo $role === '' ? 'selected' : ''; ?>>Select role</option>
              <option value="staff" <?php echo $role === 'staff' ? 'selected' : ''; ?>>Staff</option>
              <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password:</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-2">Submit</button>
        <a href="manage_employee.php" class="btn btn-secondary w-100">Back</a>
      </form>
    </div>
  </div>
</div>

<?php include FOOTER_PATH; ?>
