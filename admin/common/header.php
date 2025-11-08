<?php
require_once __DIR__ . '/../../dbsetting/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$userId = $_SESSION['users_id'] ?? 0;
$loggedInName = $_SESSION['name'] ?? "";
$profilePhoto = "default.png";

// Always fetch photo when logged in so it doesn't stay at default
if ($userId > 0) {
    $query = "SELECT fullname, profile_photo FROM users WHERE users_id = $userId LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        if ($loggedInName === "") {
            $loggedInName = $row['fullname'] ?: '';
        }
        $profilePhoto = !empty($row['profile_photo']) ? $row['profile_photo'] : 'default.png';
    }
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>

<header class="d-flex justify-content-between align-items-center px-4 py-3 text-white header">
  <h3 class="m-0">Task Management System</h3>

  <div class="admin-dropdown-container" style="position: relative; display: inline-block;">
    <button class="admin-profile-btn d-flex align-items-center border-0 bg-transparent text-white"
      type="button" id="adminProfileBtn" onclick="toggleAdminMenu(event)" style="cursor: pointer; padding: 5px 10px;">

      <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo htmlspecialchars($profilePhoto); ?>" 
           alt="Profile" width="40" height="40"
           class="rounded-circle me-2 border">
     
      <span><?php echo htmlspecialchars($loggedInName); ?></span>
      <i class="fas fa-chevron-down ms-2"></i>
    </button>

    <div id="adminDropdownMenu" class="admin-dropdown-menu" style="position: absolute; right: 0; top: 100%; background: transparent; min-width: 200px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 5px; margin-top: 5px; z-index: 1000;">
      <a href="<?php echo BASE_URL; ?>/admin/logout.php" class="admin-menu-item text-danger" style="display: block; padding: 12px 15px; color: #dc3545; text-decoration: none; background: white; border-radius: 5px;">
        <i class="fas fa-sign-out-alt me-2"></i> Logout
      </a>
    </div>
  </div>

  <style>
    .admin-menu-item:hover {
      background-color: #f8f9fa !important;
    }
    .admin-dropdown-menu {
      display: none;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.2s, visibility 0.2s;
    }
    .admin-dropdown-menu.show {
      display: block;
      opacity: 1;
      visibility: visible;
      animation: fadeIn 0.2s;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>

  <script>
    function toggleAdminMenu(event) {
      if (event) {
        event.stopPropagation();
      }
      var menu = document.getElementById('adminDropdownMenu');
      if (menu) {
        if (menu.classList.contains('show')) {
          menu.classList.remove('show');
        } else {
          menu.classList.add('show');
        }
      }
    }

    // Close dropdown when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
      document.addEventListener('click', function(event) {
        var container = document.querySelector('.admin-dropdown-container');
        var menu = document.getElementById('adminDropdownMenu');

        if (container && menu && !container.contains(event.target)) {
          menu.classList.remove('show');
        }
      });
    });
  </script>
</header>

