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
$redirect = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department_name = trim($_POST['department_name']);

    if (!empty($department_name)) {
      
        $sql = "INSERT INTO department (department_name) VALUES ('$department_name')";
        if (mysqli_query($conn, $sql)) {
            $message = "✅ Department added successfully! Redirecting...";
            $redirect = true;
        } else {
            $message = "❌ Error: " . mysqli_error($conn);
        }
    } else {
        $message = "⚠️ Department name is required!";
    }
}
?>

<div class="container card " style="max-width: 600px; margin-top: 80px;  padding: 20px;">
    <center><h2>Add Department</h2></center>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center mt-3"><?= $message ?></div>
        <?php if ($redirect): ?>
            <meta http-equiv="refresh" content="2;url=manage_department.php">
        <?php endif; ?>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label for="department_name" class="form-label fw-bold">Department Name</label>
            <input type="text" class="form-control" id="department_name" name="department_name" placeholder="Enter department name" required>
        </div>

        <div class="d-grid gap-2 col-4 mx-auto">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="manage_department.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include FOOTER_PATH; ?>
