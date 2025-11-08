<?php
session_start();

require_once __DIR__ . '/../../dbsetting/config.php';
require_once __DIR__ . '/../common/activity_logger.php';

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

if (isset($_POST['task_assign'])) {
    $department_id = isset($_POST['department_id']) ? (int) $_POST['department_id'] : 0;
    $users_ids = isset($_POST['users_id']) ? $_POST['users_id'] : [];
    $task_title = trim($_POST['task_title'] ?? '');
    $task_description = trim($_POST['task_description'] ?? '');
    $task_priority = trim($_POST['task_priority'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');

    // Validation
    if ($department_id <= 0) {
        $message = "⚠️ Please select a valid department.";
        $alertType = "danger";
    } elseif (empty($users_ids)) {
        $message = "⚠️ Please select at least one employee.";
        $alertType = "danger";
    } elseif (empty($task_title)) {
        $message = "⚠️ Task title is required.";
        $alertType = "danger";
    } elseif (strlen($task_title) > 255) {
        $message = "⚠️ Task title must be less than 255 characters.";
        $alertType = "danger";
    } elseif (empty($task_description)) {
        $message = "⚠️ Task description is required.";
        $alertType = "danger";
    } elseif (!in_array($task_priority, ['High', 'Medium', 'Low'])) {
        $message = "⚠️ Please select a valid priority.";
        $alertType = "danger";
    } elseif (empty($start_date) || empty($end_date)) {
        $message = "⚠️ Please provide both start and end dates.";
        $alertType = "danger";
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        $message = "⚠️ Start date cannot be after end date.";
        $alertType = "danger";
    } else {
        // Escape data for database
        $task_title = mysqli_real_escape_string($conn, $task_title);
        $task_description = mysqli_real_escape_string($conn, $task_description);
        $start_date = mysqli_real_escape_string($conn, $start_date);
        $end_date = mysqli_real_escape_string($conn, $end_date);
        $task_priority = mysqli_real_escape_string($conn, $task_priority);

        // Insert task
        $query_task = "INSERT INTO task_manage 
                       (task_title, task_description, created_date, start_date, end_date, priority) 
                       VALUES 
                       ('$task_title', '$task_description', NOW(), '$start_date', '$end_date', '$task_priority')";

        if (mysqli_query($conn, $query_task)) {
            $task_id = mysqli_insert_id($conn);

            // Assign task to employees
            $assigned_users = [];
            foreach ($users_ids as $user_id) {
                $user_id = (int) $user_id;
                if ($user_id > 0) {
                    $query_assign = "INSERT INTO task_assign (task_id, users_id, status) VALUES ($task_id, $user_id, 'Not Started')";
                    mysqli_query($conn, $query_assign);
                    
                    // Get user name for logging
                    $user_query = "SELECT fullname FROM users WHERE users_id = $user_id LIMIT 1";
                    $user_result = mysqli_query($conn, $user_query);
                    if ($user_row = mysqli_fetch_assoc($user_result)) {
                        $assigned_users[] = $user_row['fullname'];
                    }
                }
            }

            // Log task creation
            $users_list = !empty($assigned_users) ? implode(', ', $assigned_users) : 'employees';
            logActivity('task_created', "Created task: '$task_title' and assigned to $users_list", null, 'task_manage', $task_id);

            $message = "✅ Task assigned successfully! Redirecting...";
            $alertType = "success";
            $redirect = true;
        } else {
            $message = "❌ Error: " . mysqli_error($conn);
            $alertType = "danger";
        }
    }
}
?>

<div class="manage_task" style="width: 80vw; margin-left: 263px; margin-top: 83px; padding: 20px;">
    <div class="row">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <div class="card mt-4">
                <div class="card-header" style="background: transparent;">
                    <h3 class="card-title mb-0">📋 Create A New Task</h3>
                </div>
                <div class="card-body" style="background: transparent;">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $alertType; ?> text-center" role="alert">
                            <?php echo $message; ?>
                        </div>
                        <?php if ($redirect): ?>
                            <meta http-equiv="refresh" content="2;url=manage_task.php">
                        <?php endif; ?>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Select Department:</label>
                            <select name="department_id" id="department_id" class="form-select">
                                <option value="">-- Select Department --</option>
                                <?php
                                $dept = mysqli_query($conn, "SELECT department_id, department_name FROM department");
                                while ($row = mysqli_fetch_assoc($dept)) {
                                    echo "<option value='{$row['department_id']}'>{$row['department_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Employees:</label>
                            <div class="dropdown w-100" data-bs-auto-close="outside">
                                <button class="form-select text-start d-flex justify-content-between align-items-center"
                                        type="button" id="employeeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span id="employeeDropdownText">Choose Employees</span>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="employeeDropdown"
                                    id="employee_list" style="max-height: 250px; overflow-y: auto;">
                                    <li><p class="dropdown-item text-muted mb-0">Select a department first...</p></li>
                                </ul>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="task_title" class="form-label">Task Title:</label>
                            <input type="text" name="task_title" id="task_title" class="form-control" placeholder="Enter task title...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description:</label>
                            <textarea name="task_description" class="form-control" rows="2" placeholder="Enter task description..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Priority:</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="task_priority" value="High">
                                    <label class="form-check-label text-danger fw-bold">High</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="task_priority" value="Medium" checked>
                                    <label class="form-check-label text-warning fw-bold">Medium</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="task_priority" value="Low">
                                    <label class="form-check-label text-success fw-bold">Low</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Start Date:</label>
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">End Date:</label>
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 col-6 mx-auto">
                            <button type="submit" name="task_assign" class="btn btn-warning btn-lg btn-hover">
                                Assign Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<?php include FOOTER_PATH; ?>
