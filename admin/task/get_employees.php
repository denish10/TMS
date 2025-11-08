<?php
session_start();

require_once __DIR__ . '/../../dbsetting/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    echo "<li><p class='dropdown-item text-danger mb-0'>Access denied</p></li>";
    exit;
}

if (isset($_GET['department_id'])) {
    $department_id = intval($_GET['department_id']);

    $query = "
        SELECT u.users_id, u.fullname
        FROM employee_department ed
        INNER JOIN users u ON ed.users_id = u.users_id
        WHERE ed.department_id = $department_id
    ";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {

        // Select All row
        echo '<li class="dropdown-item py-2">
                <div class="form-check">
                  <input type="checkbox" id="select_all" class="form-check-input me-2">
                  <label for="select_all" class="form-check-label fw-bold">Select All</label>
                </div>
              </li>';
        echo '<li><hr class="dropdown-divider m-0"></li>';

        // Employee items
        while ($row = mysqli_fetch_assoc($result)) {
            $uid = intval($row['users_id']);
            $name = htmlspecialchars($row['fullname']);
            // each employee checkbox uses class emp-check and name users_id[]
            echo "<li class='dropdown-item py-2'>
                    <div class='form-check mb-0'>
                      <input class='form-check-input emp-check me-2' type='checkbox'
                             name='users_id[]' value='{$uid}' id='emp{$uid}'>
                      <label class='form-check-label' for='emp{$uid}'>{$name}</label>
                    </div>
                  </li>";
        }
    } else {
        echo "<li><p class='dropdown-item text-danger mb-0'>No employees found</p></li>";
    }
}
?>
