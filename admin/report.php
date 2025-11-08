<?php
session_start();

require_once __DIR__ . '/../dbsetting/config.php';
require_once __DIR__ . '/common/activity_logger.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

include HEADER_PATH;
include SIDEBAR_PATH;

// Get filter parameters
$filter_employee = isset($_GET['employee']) ? intval($_GET['employee']) : 0;
$filter_month = isset($_GET['month']) ? mysqli_real_escape_string($conn, $_GET['month']) : date('Y-m');
$filter_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get all employees for filter
$employees_query = "SELECT users_id, fullname FROM users WHERE role = 'staff' ORDER BY fullname";
$employees_result = mysqli_query($conn, $employees_query);
if (!$employees_result) {
    $employees_result = false;
    $employee_error = mysqli_error($conn);
}

// Overall Statistics
$total_employees_query = mysqli_query($conn, "SELECT users_id FROM users WHERE role = 'staff'");
$total_employees = $total_employees_query ? mysqli_num_rows($total_employees_query) : 0;

// Task Statistics
$total_tasks_query = "SELECT COUNT(*) as total FROM task_assign";
$total_tasks_result = mysqli_query($conn, $total_tasks_query);
if (!$total_tasks_result) {
    $total_tasks = 0;
} else {
    $total_tasks = mysqli_fetch_assoc($total_tasks_result)['total'] ?? 0;
}

$completed_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE status = 'Completed'";
$completed_tasks_result = mysqli_query($conn, $completed_tasks_query);
if (!$completed_tasks_result) {
    $completed_tasks = 0;
} else {
    $completed_tasks = mysqli_fetch_assoc($completed_tasks_result)['total'] ?? 0;
}

$pending_tasks_query = "SELECT COUNT(*) as total FROM task_assign WHERE status IN ('Not Started', 'In Progress', 'Pending')";
$pending_tasks_result = mysqli_query($conn, $pending_tasks_query);
if (!$pending_tasks_result) {
    $pending_tasks = 0;
} else {
    $pending_tasks = mysqli_fetch_assoc($pending_tasks_result)['total'] ?? 0;
}

$completion_rate = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100, 2) : 0;

// Leave Statistics
$total_leaves_query = "SELECT COUNT(*) as total FROM leave_apply";
$total_leaves_result = mysqli_query($conn, $total_leaves_query);
if (!$total_leaves_result) {
    $total_leaves = 0;
} else {
    $total_leaves = mysqli_fetch_assoc($total_leaves_result)['total'] ?? 0;
}

$approved_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE status = 'Approved'";
$approved_leaves_result = mysqli_query($conn, $approved_leaves_query);
if (!$approved_leaves_result) {
    $approved_leaves = 0;
} else {
    $approved_leaves = mysqli_fetch_assoc($approved_leaves_result)['total'] ?? 0;
}

$pending_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE status = 'Pending'";
$pending_leaves_result = mysqli_query($conn, $pending_leaves_query);
if (!$pending_leaves_result) {
    $pending_leaves = 0;
} else {
    $pending_leaves = mysqli_fetch_assoc($pending_leaves_result)['total'] ?? 0;
}

$rejected_leaves_query = "SELECT COUNT(*) as total FROM leave_apply WHERE status = 'Rejected'";
$rejected_leaves_result = mysqli_query($conn, $rejected_leaves_query);
if (!$rejected_leaves_result) {
    $rejected_leaves = 0;
} else {
    $rejected_leaves = mysqli_fetch_assoc($rejected_leaves_result)['total'] ?? 0;
}

// Employee Performance Report
$employee_performance_query = "
    SELECT 
        u.users_id,
        u.fullname,
        u.email,
        COUNT(ta.record_id) as total_tasks,
        SUM(CASE WHEN ta.status = 'Completed' THEN 1 ELSE 0 END) as completed_tasks,
        SUM(CASE WHEN ta.status IN ('Not Started', 'In Progress', 'Pending') THEN 1 ELSE 0 END) as pending_tasks,
        SUM(CASE WHEN ta.status = 'On Hold' THEN 1 ELSE 0 END) as on_hold_tasks,
        SUM(CASE WHEN ta.status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_tasks
    FROM users u
    LEFT JOIN task_assign ta ON u.users_id = ta.users_id
    WHERE u.role = 'staff'
    GROUP BY u.users_id, u.fullname, u.email
    ORDER BY completed_tasks DESC, total_tasks DESC
";

if ($filter_employee > 0) {
    $employee_performance_query = "
        SELECT 
            u.users_id,
            u.fullname,
            u.email,
            COUNT(ta.record_id) as total_tasks,
            SUM(CASE WHEN ta.status = 'Completed' THEN 1 ELSE 0 END) as completed_tasks,
            SUM(CASE WHEN ta.status IN ('Not Started', 'In Progress', 'Pending') THEN 1 ELSE 0 END) as pending_tasks,
            SUM(CASE WHEN ta.status = 'On Hold' THEN 1 ELSE 0 END) as on_hold_tasks,
            SUM(CASE WHEN ta.status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_tasks
        FROM users u
        LEFT JOIN task_assign ta ON u.users_id = ta.users_id
        WHERE u.role = 'staff' AND u.users_id = $filter_employee
        GROUP BY u.users_id, u.fullname, u.email
        ORDER BY completed_tasks DESC, total_tasks DESC
    ";
}

$employee_performance_result = mysqli_query($conn, $employee_performance_query);
if (!$employee_performance_result) {
    $employee_performance_result = false;
}

// Employee Leave Report
$employee_leave_query = "
    SELECT 
        u.users_id,
        u.fullname,
        COUNT(la.leave_id) as total_leaves,
        SUM(CASE WHEN la.status = 'Approved' THEN 1 ELSE 0 END) as approved_leaves,
        SUM(CASE WHEN la.status = 'Pending' THEN 1 ELSE 0 END) as pending_leaves,
        SUM(CASE WHEN la.status = 'Rejected' THEN 1 ELSE 0 END) as rejected_leaves
    FROM users u
    LEFT JOIN leave_apply la ON u.users_id = la.users_id
    WHERE u.role = 'staff'
    GROUP BY u.users_id, u.fullname
    ORDER BY total_leaves DESC
";

if ($filter_employee > 0) {
    $employee_leave_query = "
        SELECT 
            u.users_id,
            u.fullname,
            COUNT(la.leave_id) as total_leaves,
            SUM(CASE WHEN la.status = 'Approved' THEN 1 ELSE 0 END) as approved_leaves,
            SUM(CASE WHEN la.status = 'Pending' THEN 1 ELSE 0 END) as pending_leaves,
            SUM(CASE WHEN la.status = 'Rejected' THEN 1 ELSE 0 END) as rejected_leaves
        FROM users u
        LEFT JOIN leave_apply la ON u.users_id = la.users_id
        WHERE u.role = 'staff' AND u.users_id = $filter_employee
        GROUP BY u.users_id, u.fullname
        ORDER BY total_leaves DESC
    ";
}

$employee_leave_result = mysqli_query($conn, $employee_leave_query);
if (!$employee_leave_result) {
    $employee_leave_result = false;
}

// Log report view
logActivity('report_viewed', 'Admin viewed performance and leave report');
?>

<div class="manage_task" style="width: 80vw; margin-left: 263px; margin-top: 83px; padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-chart-line me-2"></i> Performance & Leave Report</h3>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print me-2"></i> Print Report
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Employee</label>
                    <select name="employee" class="form-select">
                        <option value="0">All Employees</option>
                        <?php
                        if ($employees_result && mysqli_num_rows($employees_result) > 0) {
                            mysqli_data_seek($employees_result, 0); // Reset pointer
                            while ($emp = mysqli_fetch_assoc($employees_result)) {
                                $selected = ($filter_employee == $emp['users_id']) ? 'selected' : '';
                                echo "<option value='{$emp['users_id']}' $selected>" . htmlspecialchars($emp['fullname']) . "</option>";
                            }
                        } elseif (isset($employee_error)) {
                            echo "<option disabled>Error loading employees</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <input type="month" name="month" class="form-control" value="<?php echo htmlspecialchars($filter_month); ?>">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i> Apply Filters
                        </button>
                        <a href="report.php" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Employees</h6>
                    <h3><?php echo $total_employees; ?></h3>
                    <small><i class="fas fa-users"></i> Staff Members</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Task Completion Rate</h6>
                    <h3><?php echo $completion_rate; ?>%</h3>
                    <small><?php echo $completed_tasks; ?> / <?php echo $total_tasks; ?> Tasks</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Pending Tasks</h6>
                    <h3><?php echo $pending_tasks; ?></h3>
                    <small>In Progress / Not Started</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Total Leaves</h6>
                    <h3><?php echo $total_leaves; ?></h3>
                    <small><?php echo $approved_leaves; ?> Approved</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Statistics -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-calendar-check me-2"></i> Leave Statistics</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <h4 class="text-success"><?php echo $approved_leaves; ?></h4>
                        <p class="mb-0">Approved Leaves</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <h4 class="text-warning"><?php echo $pending_leaves; ?></h4>
                        <p class="mb-0">Pending Leaves</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <h4 class="text-danger"><?php echo $rejected_leaves; ?></h4>
                        <p class="mb-0">Rejected Leaves</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <h4><?php echo $total_leaves; ?></h4>
                        <p class="mb-0">Total Applications</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Performance Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-user-chart me-2"></i> Employee Performance Report</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Employee</th>
                            <th>Email</th>
                            <th>Total Tasks</th>
                            <th>Completed</th>
                            <th>Pending</th>
                            <th>On Hold</th>
                            <th>Cancelled</th>
                            <th>Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($employee_performance_result && mysqli_num_rows($employee_performance_result) > 0) {
                            while ($emp = mysqli_fetch_assoc($employee_performance_result)) {
                                $total = (int)$emp['total_tasks'];
                                $completed = (int)$emp['completed_tasks'];
                                $pending = (int)$emp['pending_tasks'];
                                $on_hold = (int)$emp['on_hold_tasks'];
                                $cancelled = (int)$emp['cancelled_tasks'];
                                $rate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
                                
                                // Color coding for completion rate
                                $rate_color = 'success';
                                if ($rate < 50) $rate_color = 'danger';
                                elseif ($rate < 75) $rate_color = 'warning';
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($emp['fullname']); ?></strong></td>
                                <td><?php echo htmlspecialchars($emp['email']); ?></td>
                                <td><span class="badge bg-primary"><?php echo $total; ?></span></td>
                                <td><span class="badge bg-success"><?php echo $completed; ?></span></td>
                                <td><span class="badge bg-warning"><?php echo $pending; ?></span></td>
                                <td><span class="badge bg-info"><?php echo $on_hold; ?></span></td>
                                <td><span class="badge bg-secondary"><?php echo $cancelled; ?></span></td>
                                <td>
                                    <span class="badge bg-<?php echo $rate_color; ?>"><?php echo $rate; ?>%</span>
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-<?php echo $rate_color; ?>" 
                                             style="width: <?php echo $rate; ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>No performance data found.</p>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Employee Leave Details Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-calendar-alt me-2"></i> Employee Leave Details</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Employee</th>
                            <th>Total Leaves</th>
                            <th>Approved</th>
                            <th>Pending</th>
                            <th>Rejected</th>
                            <th>Approval Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($employee_leave_result && mysqli_num_rows($employee_leave_result) > 0) {
                            while ($leave = mysqli_fetch_assoc($employee_leave_result)) {
                                $total_leave = (int)$leave['total_leaves'];
                                $approved = (int)$leave['approved_leaves'];
                                $pending = (int)$leave['pending_leaves'];
                                $rejected = (int)$leave['rejected_leaves'];
                                $approval_rate = $total_leave > 0 ? round(($approved / $total_leave) * 100, 1) : 0;
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($leave['fullname']); ?></strong></td>
                                <td><span class="badge bg-primary"><?php echo $total_leave; ?></span></td>
                                <td><span class="badge bg-success"><?php echo $approved; ?></span></td>
                                <td><span class="badge bg-warning"><?php echo $pending; ?></span></td>
                                <td><span class="badge bg-danger"><?php echo $rejected; ?></span></td>
                                <td>
                                    <span class="badge bg-info"><?php echo $approval_rate; ?>%</span>
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-info" 
                                             style="width: <?php echo $approval_rate; ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>No leave data found.</p>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card">
        <div class="card-body">
            <h6><i class="fas fa-info-circle me-2"></i> Report Summary</h6>
            <p class="mb-0 small" style="color: #ffffff;">
                This report shows employee performance metrics based on task completion and leave statistics. 
                Use the filters above to view specific employee data or time periods. 
                Completion rates are color-coded: Green (75%+), Yellow (50-74%), Red (<50%).
            </p>
        </div>
    </div>
</div>

<style>
@media print {
    .card, .btn, form {
        page-break-inside: avoid;
    }
    .btn {
        display: none;
    }
}
</style>

<?php include FOOTER_PATH; ?>

