<?php
session_start();

require_once __DIR__ . '/../../dbsetting/config.php';

// Check if user is logged in
if (!isset($_SESSION['users_id'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

include USER_HEADER_PATH;
include USER_SIDEBAR_PATH;

$redirect_url = 'task_manage.php';
$message = "";

// Get task_id from URL and user_id from session
$task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = isset($_SESSION['users_id']) ? intval($_SESSION['users_id']) : 0;

// Validate inputs
if ($task_id <= 0 || $user_id <= 0) {
    $data = null;
} else {
    // Fetch task assignment details for the logged-in user
    $query = "SELECT t.record_id, t.status, t.task_id, u.fullname, tm.task_title
              FROM task_assign t
              JOIN users u ON t.users_id = u.users_id
              JOIN task_manage tm ON t.task_id = tm.task_id
              WHERE t.task_id = $task_id AND t.users_id = $user_id
              LIMIT 1";

    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        $data = null;
    }
}

$employee_name = $data['fullname'] ?? '';
$task_title = $data['task_title'] ?? '';
$status = $data['status'] ?? '';
$record_id = $data['record_id'] ?? 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $record_id = isset($_POST['record_id']) ? intval($_POST['record_id']) : 0;
    $status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';
    $user_id = isset($_SESSION['users_id']) ? intval($_SESSION['users_id']) : 0;

    // Validate and update - ensure user can only update their own tasks
    if ($record_id > 0 && $user_id > 0 && !empty($status)) {
        $update = "UPDATE task_assign SET status = '$status' 
                   WHERE record_id = $record_id AND users_id = $user_id";
        if (mysqli_query($conn, $update)) {
            $message = "✅ Task status updated successfully. Redirecting...";
            echo '<meta http-equiv="refresh" content="2;url=' . $redirect_url . '">';
        } else {
            $message = "❌ Error updating task: " . mysqli_error($conn);
        }
    } else {
        $message = "❌ Invalid request. Please try again.";
    }
}
?>

<div class="container card " style="max-width: 600px; margin-top: 80px;  padding: 20px;">
    <center><h2>Update Task Status</h2></center>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center mt-3"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($data): ?>
        <form method="POST" 
              onsubmit="return confirm('Are you sure you want to update the task status for <?php echo addslashes($employee_name); ?>?');">
            
            <div class="mb-3">
                <label class="form-label fw-bold">Task Title</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($task_title); ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Assigned Employee</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($employee_name); ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Status</label>
                <select class="form-select" name="status" required>
                    <option value="" disabled>Select Status</option>
                    <?php
                    $options = ['Not Started', 'In Progress', 'Completed', 'On Hold', 'Cancelled', 'Pending'];
                    foreach ($options as $opt) {
                        $selected = ($status == $opt) ? 'selected' : '';
                        echo "<option value='$opt' $selected>$opt</option>";
                    }
                    ?>
                </select>
            </div>

            <input type="hidden" name="record_id" value="<?php echo $record_id; ?>">

            <div class="d-grid gap-2 col-4 mx-auto">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="<?php echo $redirect_url; ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-danger text-center mt-3">❌ Task not found.</div>
    <?php endif; ?>
</div>

<?php include USER_FOOTER_PATH; ?>
