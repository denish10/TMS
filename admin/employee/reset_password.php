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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$message_type = ''; // 'success' or 'danger'
$username = '';
$redirect = false;

// Load simple context for the page (username)
if ($id > 0) {
    $result = mysqli_query($conn, "SELECT username FROM users WHERE users_id = $id");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $username = $row['username'];
    }
}

// Handle form submit with simple validations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];
    if ($new_password === '' || $confirm_password === '') {
        $errors[] = 'Both password fields are required.';
    }
    if ($new_password !== '' && strlen($new_password) < 6) {
        $errors[] = 'Password should be at least 6 characters.';
    }
    if ($new_password !== '' && $confirm_password !== '' && $new_password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password='$hashed' WHERE users_id=$id";
        if (mysqli_query($conn, $query)) {
            $message = "✅ Password for <strong>$username</strong> reset successfully. Redirecting...";
            $message_type = 'success';
            $redirect = true;
        } else {
            $message = "❌ Failed to reset password for <strong>$username</strong>.";
            $message_type = 'danger';
        }
    } else {
        $message = implode('<br>', $errors);
        $message_type = 'danger';
    }
}
?>

<div class="container card " style="max-width: 600px; margin-top: 80px;  padding: 20px;">
    <center><h2>🔐 Reset Password <?php echo $username ? "for <span style='color:blue;'>$username</span>" : ''; ?></h2></center>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type ?: 'info'; ?> text-center mt-3"><?php echo $message; ?></div>
        <?php if ($redirect): ?>
            <meta http-equiv="refresh" content="2;url=manage_employee.php">
        <?php endif; ?>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label fw-bold">New Password for <?php echo $username ? "<strong>$username</strong>" : 'this user'; ?></label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <div class="d-grid gap-2 col-4 mx-auto">
            <button type="submit" class="btn btn-warning">Reset Password</button>
            <a href="manage_employee.php" class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>

<?php include FOOTER_PATH; ?>
