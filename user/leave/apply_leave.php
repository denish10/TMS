<?php
session_start();
require_once __DIR__ . '/../../dbsetting/config.php';
include USER_HEADER_PATH;
include USER_SIDEBAR_PATH;

// Make sure user is logged in
if (!isset($_SESSION['users_id'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$message = '';

if (isset($_POST['submit_leave'])) {
    $users_id   = (int) $_SESSION['users_id'];
    $subject    = trim($_POST['subject'] ?? '');
    $user_msg   = trim($_POST['message'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date   = trim($_POST['end_date'] ?? '');
    $created_at = date('Y-m-d H:i:s');
    $status     = 'Pending';

    // Simple validations
    if ($subject === '' || $user_msg === '' || $start_date === '' || $end_date === '') {
        $message = '❌ All fields are required.';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        $message = '⚠️ Please select valid dates.';
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        $message = '⚠️ Start date cannot be after end date.';
    } else {
        // Basic escaping to keep things stable
        $subjectEsc = mysqli_real_escape_string($conn, $subject);
        $messageEsc = mysqli_real_escape_string($conn, $user_msg);
        $startEsc   = mysqli_real_escape_string($conn, $start_date);
        $endEsc     = mysqli_real_escape_string($conn, $end_date);
        $createdEsc = mysqli_real_escape_string($conn, $created_at);
        $statusEsc  = mysqli_real_escape_string($conn, $status);

        $sql = "INSERT INTO leave_apply (users_id, subject, message, start_date, end_date, created_date, status)
                VALUES ($users_id, '$subjectEsc', '$messageEsc', '$startEsc', '$endEsc', '$createdEsc', '$statusEsc')";

        if (mysqli_query($conn, $sql)) {
            $message = '✅ Leave applied successfully. Redirecting...';
            echo '<meta http-equiv="refresh" content="2;url=view_leave_status.php">';
        } else {
            $message = '❌ Error applying leave: ' . mysqli_error($conn);
        }
    }
}
?>

<div class="container card " style="max-width: 600px; margin-top: 80px;  padding: 20px;">
    <center><h2>Apply Leave</h2></center>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center mt-3"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return confirm('Are you sure you want to submit this leave application?');">
        <div class="mb-3">
            <label class="form-label fw-bold">Subject</label>
            <input type="text" class="form-control" name="subject" placeholder="Enter Subject" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Message</label>
            <textarea class="form-control" rows="5" name="message" placeholder="Enter Message" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Start Date</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">End Date</label>
            <input type="date" name="end_date" class="form-control" required>
        </div>

        <div class="d-grid gap-2 col-4 mx-auto">
            <button type="submit" class="btn btn-primary" name="submit_leave" value="1">Submit</button>
            <a href="view_leave_status.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include USER_FOOTER_PATH; ?>
