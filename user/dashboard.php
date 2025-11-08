<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['users_id'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

require_once __DIR__ . '/../dbsetting/config.php';
require_once __DIR__ . '/common/activity_logger.php';

include(USER_HEADER_PATH);
include(USER_SIDEBAR_PATH);

$user_id = (int) $_SESSION['users_id'];

// ========== USER TASK STATISTICS ==========
$total_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE users_id = $user_id";
$total_tasks_result = mysqli_query($conn, $total_tasks_query);
$total_tasks = mysqli_fetch_assoc($total_tasks_result)['total'] ?? 0;

$completed_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE users_id = $user_id AND status = 'Completed'";
$completed_tasks_result = mysqli_query($conn, $completed_tasks_query);
$completed_tasks = mysqli_fetch_assoc($completed_tasks_result)['total'] ?? 0;

$pending_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE users_id = $user_id AND status IN ('Not Started', 'In Progress', 'Pending')";
$pending_tasks_result = mysqli_query($conn, $pending_tasks_query);
$pending_tasks = mysqli_fetch_assoc($pending_tasks_result)['total'] ?? 0;

$in_progress_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE users_id = $user_id AND status = 'In Progress'";
$in_progress_tasks_result = mysqli_query($conn, $in_progress_tasks_query);
$in_progress_tasks = mysqli_fetch_assoc($in_progress_tasks_result)['total'] ?? 0;

$on_hold_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE users_id = $user_id AND status = 'On Hold'";
$on_hold_tasks_result = mysqli_query($conn, $on_hold_tasks_query);
$on_hold_tasks = mysqli_fetch_assoc($on_hold_tasks_result)['total'] ?? 0;

$task_completion_rate = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100, 1) : 0;

// ========== USER LEAVE STATISTICS ==========
$total_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE users_id = $user_id";
$total_leaves_result = mysqli_query($conn, $total_leaves_query);
$total_leaves = mysqli_fetch_assoc($total_leaves_result)['total'] ?? 0;

$approved_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE users_id = $user_id AND status = 'Approved'";
$approved_leaves_result = mysqli_query($conn, $approved_leaves_query);
$approved_leaves = mysqli_fetch_assoc($approved_leaves_result)['total'] ?? 0;

$pending_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE users_id = $user_id AND status = 'Pending'";
$pending_leaves_result = mysqli_query($conn, $pending_leaves_query);
$pending_leaves = mysqli_fetch_assoc($pending_leaves_result)['total'] ?? 0;

$rejected_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE users_id = $user_id AND status = 'Rejected'";
$rejected_leaves_result = mysqli_query($conn, $rejected_leaves_query);
$rejected_leaves = mysqli_fetch_assoc($rejected_leaves_result)['total'] ?? 0;

// ========== RECENT DATA ==========
// Recent tasks
$recent_tasks_query = "
    SELECT ta.*, tm.task_title, tm.priority, tm.start_date, tm.end_date
    FROM task_assign ta
    JOIN task_manage tm ON ta.task_id = tm.task_id
    WHERE ta.users_id = $user_id
    ORDER BY ta.record_id DESC
    LIMIT 5
";
$recent_tasks_result = mysqli_query($conn, $recent_tasks_query);

// Recent leave applications
$recent_leaves_query = "
    SELECT * FROM leave_apply 
    WHERE users_id = $user_id 
    ORDER BY created_date DESC 
    LIMIT 5
";
$recent_leaves_result = mysqli_query($conn, $recent_leaves_query);

// Get user info
$user_info_query = "SELECT fullname, email, last_login FROM users WHERE users_id = $user_id";
$user_info_result = mysqli_query($conn, $user_info_query);
$user_info = mysqli_fetch_assoc($user_info_result);
?>

<!-- Main Content Section -->
<div class="manage_task" style="width: 80vw; margin-left: 263px; margin-top: 83px; padding: 20px;">
    <div class="row">
        <div class="col-md-12">
            
            <!-- Dashboard Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0 text-white"><i class="fas fa-tachometer-alt me-2"></i> Employee Dashboard</h2>
                    <p class="text-white mb-0 opacity-75">Welcome back, <?php echo htmlspecialchars($_SESSION['name'] ?? 'Employee'); ?>!</p>
                </div>
                <div class="text-end">
                    <div class="text-white">
                        <i class="fas fa-calendar-alt me-2"></i> 
                        <strong><?php echo date('l, F j, Y'); ?></strong>
                    </div>
                    <small class="text-white opacity-75"><?php echo date('h:i A'); ?></small>
                </div>
            </div>

            <!-- Main Statistics Cards -->
            <div class="row mb-4">
                <!-- Task Completion -->
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2 opacity-75">Task Completion</h6>
                                    <h2 class="mb-0"><?php echo $task_completion_rate; ?>%</h2>
                                    <small class="opacity-75">
                                        <i class="fas fa-check-circle me-1"></i> <?php echo $completed_tasks; ?> / <?php echo $total_tasks; ?> Tasks
                                    </small>
                                </div>
                                <div>
                                    <i class="fas fa-tasks fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <a href="<?php echo BASE_URL; ?>/user/task/task_manage.php" class="text-white text-decoration-none">
                                View Tasks <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Total Tasks -->
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2 opacity-75">Total Tasks</h6>
                                    <h2 class="mb-0"><?php echo $total_tasks; ?></h2>
                                    <small class="opacity-75">
                                        <i class="fas fa-list-check me-1"></i> Assigned to You
                                    </small>
                                </div>
                                <div>
                                    <i class="fas fa-clipboard-list fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <a href="<?php echo BASE_URL; ?>/user/task/task_manage.php" class="text-white text-decoration-none">
                                Manage Tasks <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pending Leaves -->
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2 opacity-75">Pending Leaves</h6>
                                    <h2 class="mb-0"><?php echo $pending_leaves; ?></h2>
                                    <small class="opacity-75">
                                        <i class="fas fa-calendar-check me-1"></i> <?php echo $total_leaves; ?> Total
                                    </small>
                                </div>
                                <div>
                                    <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <a href="<?php echo BASE_URL; ?>/user/leave/view_leave_status.php" class="text-white text-decoration-none">
                                View Leaves <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Approved Leaves -->
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2 opacity-75">Approved Leaves</h6>
                                    <h2 class="mb-0"><?php echo $approved_leaves; ?></h2>
                                    <small class="opacity-75">
                                        <i class="fas fa-check-circle me-1"></i> Approved
                                    </small>
                                </div>
                                <div>
                                    <i class="fas fa-calendar-check fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <a href="<?php echo BASE_URL; ?>/user/leave/view_leave_status.php" class="text-white text-decoration-none">
                                View Status <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Statistics Row -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="text-success mb-0"><?php echo $completed_tasks; ?></h3>
                            <small class="text-white opacity-75">Completed Tasks</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="text-warning mb-0"><?php echo $pending_tasks; ?></h3>
                            <small class="text-white opacity-75">Pending Tasks</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="text-info mb-0"><?php echo $in_progress_tasks; ?></h3>
                            <small class="text-white opacity-75">In Progress</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="text-success mb-0"><?php echo $approved_leaves; ?></h3>
                            <small class="text-white opacity-75">Approved Leaves</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="text-danger mb-0"><?php echo $rejected_leaves; ?></h3>
                            <small class="text-white opacity-75">Rejected Leaves</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="text-secondary mb-0"><?php echo $on_hold_tasks; ?></h3>
                            <small class="text-white opacity-75">On Hold Tasks</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-8">
                    <!-- Recent Tasks -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark"><i class="fas fa-list-check me-2"></i> Recent Tasks</h5>
                        </div>
                        <div class="card-body bg-white">
                            <?php if ($recent_tasks_result && mysqli_num_rows($recent_tasks_result) > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php while ($task = mysqli_fetch_assoc($recent_tasks_result)): 
                                        $status_badge = [
                                            'Completed' => 'success',
                                            'In Progress' => 'primary',
                                            'Pending' => 'warning',
                                            'Not Started' => 'secondary',
                                            'On Hold' => 'info',
                                            'Cancelled' => 'danger'
                                        ];
                                        $priority_badge = [
                                            'High' => 'danger',
                                            'Medium' => 'warning',
                                            'Low' => 'info'
                                        ];
                                        $status_color = $status_badge[$task['status']] ?? 'secondary';
                                        $priority_color = $priority_badge[$task['priority']] ?? 'secondary';
                                    ?>
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 text-dark"><?php echo htmlspecialchars($task['task_title']); ?></h6>
                                                    <p class="mb-1 text-dark small">
                                                        <i class="fas fa-calendar me-1"></i> 
                                                        <?php echo date('M j', strtotime($task['start_date'])); ?> - 
                                                        <?php echo date('M j, Y', strtotime($task['end_date'])); ?>
                                                    </p>
                                                    <div>
                                                        <span class="badge bg-<?php echo $status_color; ?> me-1"><?php echo $task['status']; ?></span>
                                                        <?php if ($task['priority']): ?>
                                                            <span class="badge bg-<?php echo $priority_color; ?>"><?php echo $task['priority']; ?> Priority</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <a href="<?php echo BASE_URL; ?>/user/task/task_manage.php" class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-dark py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="text-dark">No tasks assigned yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Task Status Chart -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark"><i class="fas fa-chart-pie me-2"></i> Task Status Distribution</h5>
                        </div>
                        <div class="card-body bg-white">
                            <div class="row text-center mb-3">
                                <div class="col-3">
                                    <div class="p-2">
                                        <h4 class="text-success mb-0"><?php echo $completed_tasks; ?></h4>
                                        <small class="text-dark">Completed</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2">
                                        <h4 class="text-warning mb-0"><?php echo $pending_tasks; ?></h4>
                                        <small class="text-dark">Pending</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2">
                                        <h4 class="text-info mb-0"><?php echo $in_progress_tasks; ?></h4>
                                        <small class="text-dark">In Progress</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2">
                                        <h4 class="text-secondary mb-0"><?php echo $on_hold_tasks; ?></h4>
                                        <small class="text-dark">On Hold</small>
                                    </div>
                                </div>
                            </div>
                            <div class="progress" style="height: 30px;">
                                <?php 
                                $total = $completed_tasks + $pending_tasks + $in_progress_tasks + $on_hold_tasks;
                                $completed_pct = $total > 0 ? ($completed_tasks / $total) * 100 : 0;
                                $pending_pct = $total > 0 ? ($pending_tasks / $total) * 100 : 0;
                                $progress_pct = $total > 0 ? ($in_progress_tasks / $total) * 100 : 0;
                                $hold_pct = $total > 0 ? ($on_hold_tasks / $total) * 100 : 0;
                                ?>
                                <div class="progress-bar bg-success" style="width: <?php echo $completed_pct; ?>%"></div>
                                <div class="progress-bar bg-warning" style="width: <?php echo $pending_pct; ?>%"></div>
                                <div class="progress-bar bg-info" style="width: <?php echo $progress_pct; ?>%"></div>
                                <div class="progress-bar bg-secondary" style="width: <?php echo $hold_pct; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-4">
                    <!-- Recent Leave Applications -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark"><i class="fas fa-calendar-alt me-2"></i> Recent Leave Applications</h5>
                        </div>
                        <div class="card-body bg-white" style="max-height: 350px; overflow-y: auto;">
                            <?php if ($recent_leaves_result && mysqli_num_rows($recent_leaves_result) > 0): ?>
                                <?php while ($leave = mysqli_fetch_assoc($recent_leaves_result)): 
                                    $leave_status_color = [
                                        'Approved' => 'success',
                                        'Pending' => 'warning',
                                        'Rejected' => 'danger'
                                    ];
                                    $status_color = $leave_status_color[$leave['status']] ?? 'secondary';
                                ?>
                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 text-dark"><?php echo htmlspecialchars($leave['subject']); ?></h6>
                                                <p class="mb-1 text-dark small"><?php echo htmlspecialchars($leave['message']); ?></p>
                                                <small class="text-info">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('M j', strtotime($leave['start_date'])); ?> - 
                                                    <?php echo date('M j, Y', strtotime($leave['end_date'])); ?>
                                                </small>
                                                <br>
                                                <span class="badge bg-<?php echo $status_color; ?> mt-1"><?php echo $leave['status']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center text-dark py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="text-dark">No leave applications yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Leave Status Chart -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark"><i class="fas fa-chart-bar me-2"></i> Leave Status</h5>
                        </div>
                        <div class="card-body bg-white">
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="p-2">
                                        <h4 class="text-success mb-0"><?php echo $approved_leaves; ?></h4>
                                        <small class="text-dark">Approved</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2">
                                        <h4 class="text-warning mb-0"><?php echo $pending_leaves; ?></h4>
                                        <small class="text-dark">Pending</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2">
                                        <h4 class="text-danger mb-0"><?php echo $rejected_leaves; ?></h4>
                                        <small class="text-dark">Rejected</small>
                                    </div>
                                </div>
                            </div>
                            <div class="progress" style="height: 30px;">
                                <?php 
                                $approved_pct = $total_leaves > 0 ? ($approved_leaves / $total_leaves) * 100 : 0;
                                $pending_leave_pct = $total_leaves > 0 ? ($pending_leaves / $total_leaves) * 100 : 0;
                                $rejected_pct = $total_leaves > 0 ? ($rejected_leaves / $total_leaves) * 100 : 0;
                                ?>
                                <div class="progress-bar bg-success" style="width: <?php echo $approved_pct; ?>%"></div>
                                <div class="progress-bar bg-warning" style="width: <?php echo $pending_leave_pct; ?>%"></div>
                                <div class="progress-bar bg-danger" style="width: <?php echo $rejected_pct; ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark"><i class="fas fa-bolt text-primary me-2"></i> Quick Links</h5>
                        </div>
                        <div class="card-body bg-white">
                            <div class="d-grid gap-2">
                                <a href="<?php echo BASE_URL; ?>/user/task/task_manage.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-tasks me-2"></i> View Tasks
                                </a>
                                <a href="<?php echo BASE_URL; ?>/user/leave/apply_leave.php" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-calendar-plus me-2"></i> Apply Leave
                                </a>
                                <a href="<?php echo BASE_URL; ?>/user/leave/view_leave_status.php" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-eye me-2"></i> Leave Status
                                </a>
                                <a href="<?php echo BASE_URL; ?>/user/edit_profile.php" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-user-edit me-2"></i> Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.progress-bar {
    transition: width 0.6s ease;
}

@media print {
    .card, .btn {
        page-break-inside: avoid;
    }
    .btn {
        display: none;
    }
}
</style>

<script>
// Auto-refresh dashboard every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>

<?php include(USER_FOOTER_PATH); ?>

