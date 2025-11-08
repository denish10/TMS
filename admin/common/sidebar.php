
<nav class="col-md-2  text-white vh-100" id="sidebar" >
    <div class="text-center mb-4">
        <h4>Admin Panel</h4>
        <hr class="bg-light">
    </div>

    <ul class="nav flex-column px-3">
        <li class="nav-item mb-2">
            <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="nav-link text-white">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>

         <li class="nav-item mb-2">
            <a href="<?php echo BASE_URL; ?>/admin/employee/manage_employee.php" class="nav-link text-white">
                <i class="fas fa-users  me-2"></i> Manage Employee
            </a>
            
        </li>
         <li class="nav-item mb-2">
            <a href="<?php echo BASE_URL; ?>/admin/department/manage_department.php" class="nav-link text-white">
                <i class="fas fa-building  me-2"></i> Manage Depertment
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="<?php echo BASE_URL; ?>/admin/task/create_task.php" class="nav-link text-white">
                <i class="fas fa-pen me-2"></i> Create Task
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="<?php echo BASE_URL; ?>/admin/task/manage_task.php" class="nav-link text-white">
                <i class="fas fa-tasks me-2"></i> Manage Task
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="<?php echo BASE_URL; ?>/admin/leave/leave_application.php" class="nav-link text-white">
                <i class="fas fa-calendar-check me-2"></i> Leave Application
            </a>
        </li>
        
        <li class="nav-item mb-2">
            <a href="<?php echo BASE_URL; ?>/admin/activity_logs.php" class="nav-link text-white">
                <i class="fas fa-history me-2"></i> Activity Logs
            </a>
        </li>
        
        <li class="nav-item mb-2">
            <a href="<?php echo BASE_URL; ?>/admin/report.php" class="nav-link text-white">
                <i class="fas fa-chart-line  me-2"></i> Report
            </a>
        </li>

        
        
        <!-- <li class="nav-item">
            <a href="../logout.php" class="nav-link text-white">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li> -->
    </ul>
</nav>

