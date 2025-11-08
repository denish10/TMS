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


$query = "SELECT * FROM department WHERE department_id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
$department_name = $data['department_name'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_name = trim($_POST['department_name']);

    if (!empty($department_name)) {
        // No changes detection
        if ($department_name === ($data['department_name'] ?? '')) {
            $message = "ℹ️ No changes detected.";
        } else {
            $updateQuery = "UPDATE department SET department_name = '$department_name' WHERE department_id = $id";
            if (mysqli_query($conn, $updateQuery)) {
                $message = "✅ Department updated successfully. Redirecting...";
                echo '<meta http-equiv="refresh" content="2;url=manage_department.php">';
            } else {
                $message = "❌ Error updating department: " . mysqli_error($conn);
            }
        }
    } else {
        $message = "⚠️ Department name cannot be empty.";
    }
}
?>

<div class="container card " style="max-width: 600px; margin-top: 80px;  padding: 20px;">
    <center><h2>Edit Department</h2></center>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center mt-3"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" id="updateDeptForm" onsubmit="return confirmUpdate();">
        <div class="mb-3">
            <label for="department_name" class="form-label fw-bold">Department Name</label>
            <input type="text" name="department_name" id="department_name" class="form-control" value="<?php echo $department_name; ?>" required>
        </div>

        <div class="d-grid gap-2 col-4 mx-auto">
            <button type="submit" class="btn btn-warning">Update</button>
            <a href="manage_department.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function confirmUpdate() {
    let deptName = document.getElementById("department_name").value;
    return confirm("Are you sure you want to update this department to '" + deptName + "'?");
}
</script>

<?php include FOOTER_PATH; ?>
