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

$record_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$redirect_url = $_GET['redirect'] ?? 'manage_task.php';
$errorMessage = '';
$infoMessage = '';
$successMessage = '';
$redirectAfter = '';

$employee_name = $task_title = $description = $start_date = $end_date = $priority = $status = '';

// Fetch task details
if ($record_id > 0) {
    $query = "
        SELECT 
            ta.record_id, 
            ta.status,
            tm.task_title, 
            tm.task_description, 
            tm.start_date, 
            tm.end_date, 
            tm.priority,
            u.fullname
        FROM task_assign ta
        JOIN task_manage tm ON ta.task_id = tm.task_id
        JOIN users u ON ta.users_id = u.users_id
        WHERE ta.record_id = $record_id
        LIMIT 1
    ";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $task = mysqli_fetch_assoc($result);
        $employee_name = $task['fullname'];
        $task_title    = $task['task_title'];
        $description   = $task['task_description'];
        $start_date    = $task['start_date'];
        $end_date      = $task['end_date'];
        $priority      = $task['priority'];
        $status        = $task['status'];
    } else {
        $errorMessage = "Task not found.";
    }
}

// Update task
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $record_id    = isset($_POST['record_id']) ? (int) $_POST['record_id'] : 0;
    $task_title   = trim($_POST['task_title'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $start_date   = trim($_POST['start_date'] ?? '');
    $end_date     = trim($_POST['end_date'] ?? '');
    $priority     = trim($_POST['task_priority'] ?? '');
    $status       = trim($_POST['status'] ?? '');

    $errors = [];
    if ($task_title === '') {
        $errors[] = 'Task title is required.';
    } elseif (strlen($task_title) > 255) {
        $errors[] = 'Task title must be less than 255 characters.';
    }
    if ($description === '') {
        $errors[] = 'Description is required.';
    }
    $dateRegex = '/^\d{4}-\d{2}-\d{2}$/';
    if (!preg_match($dateRegex, $start_date) || !preg_match($dateRegex, $end_date)) {
        $errors[] = 'Please provide valid dates (YYYY-MM-DD).';
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        $errors[] = 'Start date cannot be after end date.';
    }
    $allowedPriorities = ['High','Medium','Low'];
    if (!in_array($priority, $allowedPriorities, true)) {
        $errors[] = 'Please choose a valid priority.';
    }
    $allowedStatuses = ['Not Started','In Progress','Completed','On Hold','Cancelled','Pending'];
    if (!in_array($status, $allowedStatuses, true)) {
        $errors[] = 'Please choose a valid status.';
    }

    if (!empty($errors)) {
        $msg = '<ul class="mb-0">';
        foreach ($errors as $e) {
            $msg .= '<li>' . htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $msg .= '</ul>';
        $errorMessage = $msg;
    } else {
        // Fetch current values to detect no-op edits
        $currentSql = "
            SELECT tm.task_title, tm.task_description, tm.start_date, tm.end_date, tm.priority, ta.status
            FROM task_assign ta
            JOIN task_manage tm ON ta.task_id = tm.task_id
            WHERE ta.record_id = $record_id
            LIMIT 1
        ";
        $currentRes = mysqli_query($conn, $currentSql);
        if ($currentRes && ($current = mysqli_fetch_assoc($currentRes))) {
            if (
                $current['task_title'] === $task_title &&
                $current['task_description'] === $description &&
                $current['start_date'] === $start_date &&
                $current['end_date'] === $end_date &&
                $current['priority'] === $priority &&
                $current['status'] === $status
            ) {
                $infoMessage = 'No changes detected.';
            } else {
                // Escape values for DB
                $titleEsc   = mysqli_real_escape_string($conn, $task_title);
                $descEsc   = mysqli_real_escape_string($conn, $description);
                $startEsc  = mysqli_real_escape_string($conn, $start_date);
                $endEsc    = mysqli_real_escape_string($conn, $end_date);
                $prioEsc   = mysqli_real_escape_string($conn, $priority);
                $statusEsc = mysqli_real_escape_string($conn, $status);

                // Update task_manage
                $update1 = "
                    UPDATE task_manage 
                    SET task_title = '$titleEsc',
                        task_description = '$descEsc',
                        start_date = '$startEsc',
                        end_date = '$endEsc',
                        priority = '$prioEsc'
                    WHERE task_id = (SELECT task_id FROM task_assign WHERE record_id = $record_id)
                ";

                // Update task_assign
                $update2 = "UPDATE task_assign SET status = '$statusEsc' WHERE record_id = $record_id";

                if (mysqli_query($conn, $update1) && mysqli_query($conn, $update2)) {
                    $successMessage = 'Task updated successfully. Redirecting...';
                    $redirectAfter = 'view_task.php?id=' . $record_id;
                } else {
                    $errorMessage = 'Update failed.';
                }
            }
        } else {
            $errorMessage = 'Unable to load current task for comparison.';
        }
    }
}

?>

<div class="container-fluid content-wrapper ">
    <div class="row">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: transparent;">
                    <h3 class="card-title mb-0">✏️ Edit Task</h3>
                </div>
                <div class="card-body" style="background: transparent;">
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($successMessage)): ?>
                        <div class="alert alert-success"><?php echo $successMessage; ?></div>
                        <meta http-equiv="refresh" content="2;url=<?php echo htmlspecialchars($redirectAfter); ?>">
                    <?php endif; ?>
                    <?php if (!empty($infoMessage)): ?>
                        <div class="alert alert-info"><?php echo $infoMessage; ?></div>
                    <?php endif; ?>

                    <form method="post" action="">
            <div class="mb-3">
                <label class="form-label fw-bold">Task Title</label>
                <input type="text" class="form-control" name="task_title" value="<?php echo htmlspecialchars($task_title); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Assigned To</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($employee_name); ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">End Date</label>
                    <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold d-block">Priority</label>
                <div class="d-flex gap-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="task_priority" value="High" <?php echo ($priority == 'High') ? 'checked' : ''; ?>>
                        <label class="form-check-label text-danger fw-bold">High</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="task_priority" value="Medium" <?php echo ($priority == 'Medium') ? 'checked' : ''; ?>>
                        <label class="form-check-label text-warning fw-bold">Medium</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="task_priority" value="Low" <?php echo ($priority == 'Low') ? 'checked' : ''; ?>>
                        <label class="form-check-label text-success fw-bold">Low</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Status</label>
                <select class="form-select" name="status" required>
                    <option value="">Select Status</option>
                    <option value="Not Started" <?php echo ($status == 'Not Started') ? 'selected' : ''; ?>>Not Started</option>
                    <option value="In Progress" <?php echo ($status == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="Completed" <?php echo ($status == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="On Hold" <?php echo ($status == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                    <option value="Cancelled" <?php echo ($status == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="Pending" <?php echo ($status == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                </select>
            </div>

            <input type="hidden" name="record_id" value="<?php echo $record_id; ?>">
            <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary btn-lg px-4">Update Task</button>
                <a href="<?php echo htmlspecialchars($redirect_url); ?>" class="btn btn-outline-secondary btn-lg px-4">Back</a>
            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include FOOTER_PATH; ?>
