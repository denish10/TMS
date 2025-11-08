<?php
session_start();

require_once __DIR__ . '/../dbsetting/config.php';

// Check if user is logged in
if (!isset($_SESSION['users_id'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

include USER_HEADER_PATH;
include USER_SIDEBAR_PATH;

$user_id = $_SESSION['users_id'];
$message = "";
$alertType = "info";
$redirect = false;

// Fetch current user data
$query = "SELECT * FROM users WHERE users_id = $user_id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$fullname = $data['fullname'] ?? '';
$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$mobile = $data['mobile'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');

    // Validation
    if ($fullname === '' || $username === '' || $email === '' || $mobile === '') {
        $message = "⚠️ All fields are required.";
        $alertType = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "⚠️ Please enter a valid email address.";
        $alertType = "danger";
    } elseif (!ctype_digit($mobile)) {
        $message = "⚠️ Mobile number should contain only digits.";
        $alertType = "danger";
    } elseif (strlen($mobile) < 10 || strlen($mobile) > 15) {
        $message = "⚠️ Mobile number length should be between 10 and 15 digits.";
        $alertType = "danger";
    } else {
        // Check for duplicate username (excluding current user)
        $checkUser = mysqli_query($conn, "SELECT users_id FROM users WHERE username = '".mysqli_real_escape_string($conn, $username)."' AND users_id != $user_id LIMIT 1");
        if ($checkUser && mysqli_num_rows($checkUser) > 0) {
            $message = "⚠️ Username already exists.";
            $alertType = "danger";
        } else {
            // Check for duplicate email (excluding current user)
            $checkEmail = mysqli_query($conn, "SELECT users_id FROM users WHERE email = '".mysqli_real_escape_string($conn, $email)."' AND users_id != $user_id LIMIT 1");
            if ($checkEmail && mysqli_num_rows($checkEmail) > 0) {
                $message = "⚠️ Email already exists.";
                $alertType = "danger";
            } else {
                // Check if no changes
                if ($fullname === $data['fullname'] && $username === $data['username'] && 
                    $email === $data['email'] && $mobile === $data['mobile']) {
                    $message = "ℹ️ No changes detected.";
                    $alertType = "info";
                } else {
                    // Escape data for database
                    $fullname = mysqli_real_escape_string($conn, $fullname);
                    $username = mysqli_real_escape_string($conn, $username);
                    $email = mysqli_real_escape_string($conn, $email);
                    $mobile = mysqli_real_escape_string($conn, $mobile);

                    // Update user details
                    $update = "UPDATE users SET fullname='$fullname', username='$username', email='$email', mobile='$mobile' WHERE users_id=$user_id";
                    
                    if (mysqli_query($conn, $update)) {
                        // Update session name
                        $_SESSION['name'] = $fullname;
                        
                        $message = "✅ Profile updated successfully! Redirecting...";
                        $alertType = "success";
                        $redirect = true;
                    } else {
                        $message = "❌ Error: " . mysqli_error($conn);
                        $alertType = "danger";
                    }
                }
            }
        }
    }
}
?>

<div class="container-fluid">
    <div class="row justify-content-center align-items-center">
        <div class="card register_page p-4" style="max-width:700px;">
            <center><h3>Edit Your Profile</h3></center>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $alertType; ?> text-center mt-3" role="alert">
                    <?php echo $message; ?>
                </div>
                <?php if ($redirect): ?>
                    <meta http-equiv="refresh" content="2;url=dashboard.php">
                <?php endif; ?>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label for="fullname" class="form-label">Full Name:</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter full name" value="<?php echo $fullname; ?>">
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" value="<?php echo $username; ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email address:</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter email" value="<?php echo $email; ?>">
                </div>

                <div class="mb-3">
                    <label for="mobile" class="form-label">Mobile No:</label>
                    <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter mobile number" value="<?php echo $mobile; ?>">
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-2">Update Profile</button>
                <a href="dashboard.php" class="btn btn-secondary w-100">Back to Dashboard</a>
            </form>
        </div>
    </div>
</div>

<?php include USER_FOOTER_PATH; ?>

