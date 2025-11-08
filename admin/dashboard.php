<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

require_once __DIR__ . '/../dbsetting/config.php';
require_once __DIR__ . '/common/activity_logger.php';

include(HEADER_PATH);
include(SIDEBAR_PATH);

// Log dashboard access
logActivity('dashboard_viewed', 'Admin viewed the dashboard');

// ========== EMPLOYEE MODULE STATISTICS ==========
$total_employees_query = "SELECT COUNT(*) as total FROM users WHERE role = 'staff'";
$total_employees_result = mysqli_query($conn, $total_employees_query);
$total_employees = mysqli_fetch_assoc($total_employees_result)['total'] ?? 0;

$active_employees_query = "SELECT COUNT(DISTINCT users_id) as total FROM users WHERE role = 'staff' AND last_login IS NOT NULL AND last_login != '0000-00-00 00:00:00'";
$active_employees_result = mysqli_query($conn, $active_employees_query);
$active_employees = mysqli_fetch_assoc($active_employees_result)['total'] ?? 0;

// ========== DEPARTMENT MODULE STATISTICS ==========
$total_departments_query = "SELECT COUNT(*) as total FROM department";
$total_departments_result = mysqli_query($conn, $total_departments_query);
$total_departments = mysqli_fetch_assoc($total_departments_result)['total'] ?? 0;

// ========== TASK MODULE STATISTICS ==========
$total_tasks_query = "SELECT COUNT(*) as total FROM task_assign";
$total_tasks_result = mysqli_query($conn, $total_tasks_query);
$total_tasks = mysqli_fetch_assoc($total_tasks_result)['total'] ?? 0;

$completed_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE status = 'Completed'";
$completed_tasks_result = mysqli_query($conn, $completed_tasks_query);
$completed_tasks = mysqli_fetch_assoc($completed_tasks_result)['total'] ?? 0;

$pending_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE status IN ('Not Started', 'In Progress', 'Pending')";
$pending_tasks_result = mysqli_query($conn, $pending_tasks_query);
$pending_tasks = mysqli_fetch_assoc($pending_tasks_result)['total'] ?? 0;

$in_progress_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE status = 'In Progress'";
$in_progress_tasks_result = mysqli_query($conn, $in_progress_tasks_query);
$in_progress_tasks = mysqli_fetch_assoc($in_progress_tasks_result)['total'] ?? 0;

$on_hold_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE status = 'On Hold'";
$on_hold_tasks_result = mysqli_query($conn, $on_hold_tasks_query);
$on_hold_tasks = mysqli_fetch_assoc($on_hold_tasks_result)['total'] ?? 0;

$cancelled_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE status = 'Cancelled'";
$cancelled_tasks_result = mysqli_query($conn, $cancelled_tasks_query);
$cancelled_tasks = mysqli_fetch_assoc($cancelled_tasks_result)['total'] ?? 0;

$task_completion_rate = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100, 1) : 0;

// ========== LEAVE MODULE STATISTICS ==========
$total_leaves_query = "SELECT COUNT(*) as total FROM leave_apply";
$total_leaves_result = mysqli_query($conn, $total_leaves_query);
$total_leaves = mysqli_fetch_assoc($total_leaves_result)['total'] ?? 0;

$approved_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE status = 'Approved'";
$approved_leaves_result = mysqli_query($conn, $approved_leaves_query);
$approved_leaves = mysqli_fetch_assoc($approved_leaves_result)['total'] ?? 0;

$pending_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE status = 'Pending'";
$pending_leaves_result = mysqli_query($conn, $pending_leaves_query);
$pending_leaves = mysqli_fetch_assoc($pending_leaves_result)['total'] ?? 0;

$rejected_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE status = 'Rejected'";
$rejected_leaves_result = mysqli_query($conn, $rejected_leaves_query);
$rejected_leaves = mysqli_fetch_assoc($rejected_leaves_result)['total'] ?? 0;

// ========== ACTIVITY LOGS STATISTICS ==========
$total_activities_query = "SELECT COUNT(*) as total FROM activity_logs";
$total_activities_result = mysqli_query($conn, $total_activities_query);
$total_activities = mysqli_fetch_assoc($total_activities_result)['total'] ?? 0;

$today_activities_query = "SELECT COUNT(*) as total FROM activity_logs WHERE DATE(created_at) = CURDATE()";
$today_activities_result = mysqli_query($conn, $today_activities_query);
$today_activities = mysqli_fetch_assoc($today_activities_result)['total'] ?? 0;

// ========== RECENT DATA ==========
// Recent activities
$recent_activities = getRecentActivities(8);

// Top performing employees
$top_employees_query = "
    SELECT u.users_id, u.fullname, COUNT(ta.record_id) as total_tasks, 
           SUM(CASE WHEN ta.status = 'Completed' THEN 1 ELSE 0 END) as completed
    FROM users u
    LEFT JOIN task_assign ta ON u.users_id = ta.users_id
    WHERE u.role = 'staff'
    GROUP BY u.users_id, u.fullname
    HAVING total_tasks > 0
    ORDER BY completed DESC, total_tasks DESC
    LIMIT 5
";
$top_employees_result = mysqli_query($conn, $top_employees_query);

// Recent pending leaves
$recent_pending_leaves_query = "
    SELECT la.*, u.fullname 
    FROM leave_apply la
    JOIN users u ON la.users_id = u.users_id
    WHERE la.status = 'Pending'
    ORDER BY la.created_date DESC
    LIMIT 5
";
$recent_pending_leaves_result = mysqli_query($conn, $recent_pending_leaves_query);

// Recent tasks
$recent_tasks_query = "
    SELECT ta.*, tm.task_title, tm.priority, u.fullname
    FROM task_assign ta
    JOIN task_manage tm ON ta.task_id = tm.task_id
    JOIN users u ON ta.users_id = u.users_id
    ORDER BY ta.record_id DESC
    LIMIT 5
";
$recent_tasks_result = mysqli_query($conn, $recent_tasks_query);
?>

<!-- Main Content Section -->
<div class="manage_task" style="width: 80vw; margin-left: 263px; margin-top: 83px; padding: 20px;">
    <div class="row">
        <div class="col-md-12">
            
            <!-- Dashboard Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0 text-white"><i class="fas fa-tachometer-alt me-2"></i> Admin Dashboard</h2>
                    <p class="text-white mb-0 opacity-75">Welcome back, <?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?>!</p>
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
                <!-- Employee Statistics -->
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2 opacity-75">Total Employees</h6>
                                    <h2 class="mb-0"><?php echo $total_employees; ?></h2>
                                    <small class="opacity-75">
                                        <i class="fas fa-user-check me-1"></i> <?php echo $active_employees; ?> Active
                                    </small>
                                </div>
                                <div>
                                    <i class="fas fa-users fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <a href="<?php echo BASE_URL; ?>/admin/employee/manage_employee.php" class="text-white text-decoration-none">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Task Statistics -->
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
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
                            <a href="<?php echo BASE_URL; ?>/admin/task/manage_task.php" class="text-white text-decoration-none">
                                Manage Tasks <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Leave Statistics -->
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
                            <a href="<?php echo BASE_URL; ?>/admin/leave/leave_application.php" class="text-white text-decoration-none">
                                Review Leaves <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Department Statistics -->
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2 opacity-75">Departments</h6>
                                    <h2 class="mb-0"><?php echo $total_departments; ?></h2>
                                    <small class="opacity-75">
                                        <i class="fas fa-building me-1"></i> Active Departments
                                    </small>
                                </div>
                                <div>
                                    <i class="fas fa-sitemap fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <a href="<?php echo BASE_URL; ?>/admin/department/manage_department.php" class="text-white text-decoration-none">
                                Manage <i class="fas fa-arrow-right ms-1"></i>
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
                            <h3 class="text-primary mb-0"><?php echo $today_activities; ?></h3>
                            <small class="text-white opacity-75">Today's Activities</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-8">
                    <!-- Top Performers -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark"><i class="fas fa-trophy text-warning me-2"></i> Top Performers</h5>
                        </div>
                        <div class="card-body bg-white">
                            <?php if ($top_employees_result && mysqli_num_rows($top_employees_result) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-dark">Rank</th>
                                                <th class="text-dark">Employee</th>
                                                <th class="text-center text-dark">Total Tasks</th>
                                                <th class="text-center text-dark">Completed</th>
                                                <th class="text-center text-dark">Rate</th>
                                                <th class="text-center text-dark">Progress</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rank = 1;
                                            while ($emp = mysqli_fetch_assoc($top_employees_result)): 
                                                $completion_rate = $emp['total_tasks'] > 0 ? round(($emp['completed'] / $emp['total_tasks']) * 100, 1) : 0;
                                                $rate_color = 'success';
                                                if ($completion_rate < 50) $rate_color = 'danger';
                                                elseif ($completion_rate < 75) $rate_color = 'warning';
                                            ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-warning text-dark">#<?php echo $rank++; ?></span>
                                                    </td>
                                                    <td><strong class="text-dark"><?php echo htmlspecialchars($emp['fullname']); ?></strong></td>
                                                    <td class="text-center"><span class="badge bg-primary"><?php echo $emp['total_tasks']; ?></span></td>
                                                    <td class="text-center"><span class="badge bg-success"><?php echo $emp['completed']; ?></span></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-<?php echo $rate_color; ?>"><?php echo $completion_rate; ?>%</span>
                                                    </td>
                                                    <td>
                                                        <div class="progress" style="height: 8px;">
                                                            <div class="progress-bar bg-<?php echo $rate_color; ?>" 
                                                                 style="width: <?php echo $completion_rate; ?>%"></div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-dark py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="text-dark">No performance data available.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

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
                                                        <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($task['fullname']); ?>
                                                    </p>
                                                    <div>
                                                        <span class="badge bg-<?php echo $status_color; ?> me-1"><?php echo $task['status']; ?></span>
                                                        <?php if ($task['priority']): ?>
                                                            <span class="badge bg-<?php echo $priority_color; ?>"><?php echo $task['priority']; ?> Priority</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <a href="<?php echo BASE_URL; ?>/admin/task/manage_task.php" class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-dark py-4">
                                    <p class="text-dark">No recent tasks found.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-4">
                    <!-- Pending Leaves -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark"><i class="fas fa-calendar-exclamation text-warning me-2"></i> Pending Leaves</h5>
                        </div>
                        <div class="card-body bg-white" style="max-height: 350px; overflow-y: auto;">
                            <?php if ($recent_pending_leaves_result && mysqli_num_rows($recent_pending_leaves_result) > 0): ?>
                                <?php while ($leave = mysqli_fetch_assoc($recent_pending_leaves_result)): ?>
                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 text-dark"><?php echo htmlspecialchars($leave['fullname']); ?></h6>
                                                <p class="mb-1 text-dark small"><?php echo htmlspecialchars($leave['subject']); ?></p>
                                                <small class="text-info">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('M j', strtotime($leave['start_date'])); ?> - 
                                                    <?php echo date('M j, Y', strtotime($leave['end_date'])); ?>
                                                </small>
                                            </div>
                                            <a href="<?php echo BASE_URL; ?>/admin/leave/leave_application.php" class="btn btn-sm btn-warning">
                                                Review
                                            </a>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center text-dark py-4">
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                    <p class="text-dark">No pending leaves!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark"><i class="fas fa-history me-2"></i> Recent Activity</h5>
                        </div>
                        <div class="card-body bg-white" style="max-height: 350px; overflow-y: auto;">
                            <?php if (!empty($recent_activities)): ?>
                                <?php foreach ($recent_activities as $activity): 
                                    $icon_data = getActivityIcon($activity['activity_type']);
                                ?>
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="activity-icon <?php echo $icon_data['color']; ?> rounded-circle me-3 flex-shrink-0">
                                            <i class="fas <?php echo $icon_data['icon']; ?> text-white"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 small text-dark">
                                                <strong class="text-dark"><?php echo htmlspecialchars($activity['user_name'] ?? 'System'); ?></strong> 
                                                <span class="text-dark"><?php echo htmlspecialchars($activity['description']); ?></span>
                                            </p>
                                            <small class="text-dark opacity-75"><?php echo timeAgo($activity['created_at']); ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-dark py-3">
                                    <p class="text-dark">No recent activities found.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark"><i class="fas fa-bolt text-primary me-2"></i> Quick Links</h5>
                        </div>
                        <div class="card-body bg-white">
                            <div class="d-grid gap-2">
                                <a href="<?php echo BASE_URL; ?>/admin/employee/manage_employee.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-users me-2"></i> Manage Employees
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/task/create_task.php" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-plus-circle me-2"></i> Create Task
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/leave/leave_application.php" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-calendar-check me-2"></i> Leave Applications
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/report.php" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-chart-line me-2"></i> View Reports
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/activity_logs.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-history me-2"></i> Activity Logs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Charts Row -->
            <div class="row mt-4">
                <!-- Task Status Chart -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
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

                <!-- Leave Status Chart -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark"><i class="fas fa-chart-bar me-2"></i> Leave Status Distribution</h5>
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
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* Activity Icon */
.activity-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

/* Card Hover Effects */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Progress Bar Animation */
.progress-bar {
    transition: width 0.6s ease;
}

/* Table Styles */
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

/* List Group Item Spacing */
.list-group-item {
    padding: 1rem 0;
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

<?php include FOOTER_PATH; ?>
