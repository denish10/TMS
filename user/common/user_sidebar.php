
<nav class="col-md-2  text-white vh-100" id="sidebar" ">
    <div class="text-center mb-4">
        <h4>Employee Panel</h4>
        <hr class="bg-light">
    </div>

    <ul class="nav flex-column px-3">
        <li class="nav-item mb-2">
            <a href="<?php echo BASE_URL; ?>/user/dashboard.php" class="nav-link text-white">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
       
        <li class="nav-item mb-2">
            <a href="<?php echo BASE_URL;?>/user/task/task_manage.php" class="nav-link text-white">
                <i class="fas fa-pen me-2"></i> View Task
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="<?php echo BASE_URL; ?>/user/leave/apply_leave.php" class="nav-link text-white">
                <i class="fas fa-calendar-check me-2"></i> Apply Leave
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="<?php echo BASE_URL; ?>/user/leave/view_leave_status.php" class="nav-link text-white">
                <i class="fas fa-eye me-2"></i> Leave Status
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/user/logout.php" class="nav-link text-white">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li>
    </ul>
</nav>

