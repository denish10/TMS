<?php
session_start();
require_once("dbsetting/config.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $login_id = $_POST['login_id'];  // Email or username
    $password = $_POST['password'];

    if (!empty($login_id) && !empty($password)) {

        // Query: check both username and email
        $query = "SELECT * FROM users WHERE email='$login_id' OR username='$login_id' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 0) {
            $message = "❌ Email/Username not found.";
        } else {
            $user = mysqli_fetch_assoc($result);

            // Verify password (since DB stores hashed passwords)
            if (!password_verify($password, $user['password'])) {
                $message = "❌ Incorrect password.";
            } else {
                // ✅ Store all needed data in session (use users_id everywhere)
                $_SESSION['users_id'] = $user['users_id'];   // fixed key
                $_SESSION['name']     = $user['fullname'];
                $_SESSION['email']    = $user['email'];
                $_SESSION['role']     = $user['role'];

                // Update last_login timestamp
                $user_id = (int) $user['users_id'];
                $update_login = "UPDATE users SET last_login = NOW() WHERE users_id = $user_id";
                mysqli_query($conn, $update_login);

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    $message = "✅ Login successful. Redirecting Admin Dashboard...";
                    echo '<meta http-equiv="refresh" content="2;url=admin/dashboard.php">';
                } else {
                    $message = "✅ Login successful. Redirecting User Dashboard...";
                    echo '<meta http-equiv="refresh" content="2;url=user/dashboard.php">';
                }
            }
        }
    } else {
        $message = "⚠️ Please fill all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TMS | Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body id="login_background">
<div class="container">
  <div class="row justify-content-center align-items-center vh-100">
    <div class="col-md-3" id="login_page">

      <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center"><?= $message ?></div>
      <?php endif; ?>

      <center><h3>Login</h3></center>
      <form method="POST">
        <div class="mb-3">
          <label for="login_id" class="form-label">Email or Username:</label>
          <input type="text" class="form-control" name="login_id" id="login_id" required>
        </div>
        <div class="mb-3">
          <label for="pwd" class="form-label">Password:</label>
          <input type="password" class="form-control" name="password" id="pwd" required>
        </div>
        
        <button type="submit" name="login" class="btn btn-primary w-100 mb-3">Submit</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
