<?php
session_start();

require_once __DIR__ . '/../dbsetting/config.php';

// Get username before destroying session
$username = $_SESSION['name'] ?? 'User';

// Check if logout is confirmed
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    // Destroy all session data
    session_unset();
    session_destroy();
    
    include USER_HEADER_PATH;
    include USER_SIDEBAR_PATH;
    ?>
    
    <div class="container" style="max-width: 700px; margin-top: 80px;">
        <div class="logout-wrapper p-4" style="background: transparent; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div class="alert alert-success text-center" role="alert">
                ✅ Logged out successfully! Redirecting to login page...
            </div>
            <meta http-equiv="refresh" content="2;url=../index.php">
            <div class="mt-3 text-center">
                <a href="../index.php" class="btn btn-primary">Go to Login Page</a>
            </div>
        </div>
    </div>
    
    <?php include USER_FOOTER_PATH; ?>
    <?php
    exit();
}

// Show Bootstrap alert confirmation
    include USER_HEADER_PATH;
    include USER_SIDEBAR_PATH;
?>

<div class="container" style="max-width: 700px; margin-top: 80px;">
    <div class="logout-wrapper p-4" style="background: transparent; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div class="alert alert-warning text-center" role="alert">
            <h5 class="alert-heading">⚠️ Are you sure you want to logout?</h5>
            <hr>
            <p class="mb-3"><strong>User:</strong> <?php echo $username; ?></p>
            <div class="d-flex justify-content-center gap-2">
                <a href="logout.php?confirm=yes" class="btn btn-danger">Yes, Logout</a>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </div>
</div>

<?php include USER_FOOTER_PATH; ?>
