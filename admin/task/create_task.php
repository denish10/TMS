<?php
session_start();
require_once __DIR__ . '/../../dbsetting/config.php';

if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

include HEADER_PATH;
include SIDEBAR_PATH;

$message = "";
$alertType = "info";
$redirect = false;

if (isset($_POST['task_assign'])) {
    $department_id = isset($_POST['department_id']) ? (int) $_POST['department_id'] : 0;
    $users_ids = isset($_POST['users_id']) ? $_POST['users_id'] : [];
    $task_title = trim($_POST['task_title'] ?? '');
    $task_description = trim($_POST['task_description'] ?? '');
    $task_priority = trim($_POST['task_priority'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');

    if ($department_id <= 0) {
        $message = "⚠️ Please select a valid department.";
        $alertType = "danger";
    } elseif (empty($users_ids) || count($users_ids) == 0) {
        $message = "⚠️ Please select at least one employee.";
        $alertType = "danger";
    } elseif (empty($task_title)) {
        $message = "⚠️ Task title is required.";
        $alertType = "danger";
    } elseif (strlen($task_title) > 255) {
        $message = "⚠️ Task title must be less than 255 characters.";
        $alertType = "danger";
    } elseif (empty($task_description)) {
        $message = "⚠️ Task description is required.";
        $alertType = "danger";
    } elseif (!in_array($task_priority, ['High', 'Medium', 'Low'])) {
        $message = "⚠️ Please select a valid priority.";
        $alertType = "danger";
    } elseif (empty($start_date) || empty($end_date)) {
        $message = "⚠️ Please provide both start and end dates.";
        $alertType = "danger";
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        $message = "⚠️ Start date cannot be after end date.";
        $alertType = "danger";
    } else {
        $task_title = mysqli_real_escape_string($conn, $task_title);
        $task_description = mysqli_real_escape_string($conn, $task_description);
        $start_date = mysqli_real_escape_string($conn, $start_date);
        $end_date = mysqli_real_escape_string($conn, $end_date);
        $task_priority = mysqli_real_escape_string($conn, $task_priority);

        $query_task = "INSERT INTO task_manage 
                       (task_title, task_description, created_date, start_date, end_date, priority) 
                       VALUES 
                       ('$task_title', '$task_description', NOW(), '$start_date', '$end_date', '$task_priority')";

        if (mysqli_query($conn, $query_task)) {
            $task_id = mysqli_insert_id($conn);

            $assigned_count = 0;
            foreach ($users_ids as $user_id) {
                $user_id = (int) $user_id;
                if ($user_id > 0) {
                    $query_assign = "INSERT INTO task_assign (task_id, users_id, status) VALUES ($task_id, $user_id, 'Not Started')";
                    if (mysqli_query($conn, $query_assign)) {
                        $assigned_count++;
                    }
                }
            }

            $message = "✅ Task assigned successfully to $assigned_count employees! Redirecting...";
            $alertType = "success";
            $redirect = true;
        } else {
            $message = "❌ Error creating task: " . mysqli_error($conn);
            $alertType = "danger";
        }
    }
}
?>

<div class="manage_task" style="width: 80vw; margin-left: 263px; margin-top: 83px; padding: 20px;">
    <div class="row">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <div class="card mt-4">
                <div class="card-header" style="background: transparent;">
                    <h3 class="card-title mb-0">📋 Create A New Task</h3>
                </div>
                <div class="card-body" style="background: transparent;">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $alertType; ?> text-center" role="alert">
                            <?php echo $message; ?>
                        </div>
                        <?php if ($redirect): ?>
                            <meta http-equiv="refresh" content="2;url=manage_task.php">
                        <?php endif; ?>
                    <?php endif; ?>

                    <form method="POST" action="" id="taskForm">
                        <div class="mb-3">
                            <label class="form-label">Select Department:</label>
                            <select name="department_id" id="department_id" class="form-select" required>
                                <option value="">-- Select Department --</option>
                                <?php
                                $dept = mysqli_query($conn, "SELECT department_id, department_name FROM department");
                                while ($row = mysqli_fetch_assoc($dept)) {
                                    echo "<option value='{$row['department_id']}'>{$row['department_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Employees:</label>
                            
                            <div class="border rounded p-2" id="employee_container" style="min-height: 100px; max-height: 300px; overflow-y: auto;">
                                <p class="text-muted mb-0">Select a department first...</p>
                            </div>
                            
                            <small class="text-muted mt-1 d-block" id="employee_count">No employees selected</small>
                        </div>

                        <div class="mb-3">
                            <label for="task_title" class="form-label">Task Title:</label>
                            <input type="text" name="task_title" id="task_title" class="form-control" 
                                   placeholder="Enter task title..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description:</label>
                            <textarea name="task_description" class="form-control" rows="2" 
                                      placeholder="Enter task description..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Priority:</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="task_priority" value="High">
                                    <label class="form-check-label text-danger fw-bold">High</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="task_priority" value="Medium" checked>
                                    <label class="form-check-label text-warning fw-bold">Medium</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="task_priority" value="Low">
                                    <label class="form-check-label text-success fw-bold">Low</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Start Date:</label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">End Date:</label>
                                    <input type="date" name="end_date" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 col-6 mx-auto">
                            <button type="submit" name="task_assign" class="btn btn-warning btn-lg btn-hover">
                                Assign Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
console.log('Script loaded!');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    const deptSelect = document.getElementById('department_id');
    const employeeContainer = document.getElementById('employee_container');
    const employeeCount = document.getElementById('employee_count');
    const taskForm = document.getElementById('taskForm');

    console.log('Elements found:', {
        deptSelect: !!deptSelect,
        employeeContainer: !!employeeContainer,
        employeeCount: !!employeeCount,
        taskForm: !!taskForm
    });

    if (!deptSelect) {
        console.error('Department select not found!');
        return;
    }

    deptSelect.addEventListener('change', function() {
        const deptId = this.value;
        console.log('Department changed to:', deptId);
        
        if (!deptId) {
            employeeContainer.innerHTML = '<p class="text-muted mb-0">Select a department first...</p>';
            employeeCount.textContent = 'No employees selected';
            return;
        }

        console.log('Loading employees for department:', deptId);
        employeeContainer.innerHTML = '<p class="text-info mb-0">Loading employees...</p>';
        
        fetch('get_employees.php?department_id=' + deptId)
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response OK:', response.ok);
                return response.text();
            })
            .then(html => {
                console.log('Received HTML length:', html.length);
                console.log('First 200 chars:', html.substring(0, 200));
                
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                employeeContainer.innerHTML = '';
                
                const listItems = tempDiv.querySelectorAll('li');
                console.log('Found list items:', listItems.length);
                
                if (listItems.length === 0) {
                    employeeContainer.innerHTML = '<p class="text-danger mb-0">No employees found</p>';
                    return;
                }
                
                let selectAllCheckbox = null;
                const employeeCheckboxes = [];
                
                listItems.forEach((li, index) => {
                    console.log('Processing list item', index);
                    
                    const checkbox = li.querySelector('input[type="checkbox"]');
                    const label = li.querySelector('label');
                    
                    if (checkbox && label) {
                        if (checkbox.id === 'select_all' || checkbox.value === 'on') {
                            const div = document.createElement('div');
                            div.className = 'form-check mb-2 pb-2 border-bottom';
                            
                            const newCheckbox = checkbox.cloneNode(true);
                            newCheckbox.removeAttribute('name');
                            const newLabel = label.cloneNode(true);
                            
                            div.appendChild(newCheckbox);
                            div.appendChild(newLabel);
                            employeeContainer.appendChild(div);
                            
                            selectAllCheckbox = newCheckbox;
                            console.log('Added Select All checkbox');
                            return;
                        }
                        
                        const div = document.createElement('div');
                        div.className = 'form-check mb-2';
                        
                        const newCheckbox = checkbox.cloneNode(true);
                        const newLabel = label.cloneNode(true);
                        
                        newCheckbox.className = 'form-check-input emp-check';
                        newCheckbox.type = 'checkbox';
                        newCheckbox.name = 'users_id[]';
                        
                        console.log('Created checkbox:', newCheckbox.value, 'name:', newCheckbox.name);
                        
                        div.appendChild(newCheckbox);
                        div.appendChild(newLabel);
                        employeeContainer.appendChild(div);
                        
                        employeeCheckboxes.push(newCheckbox);
                        
                        newCheckbox.addEventListener('change', updateCount);
                    }
                });
                
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        console.log('Select All clicked:', this.checked);
                        employeeCheckboxes.forEach(cb => cb.checked = this.checked);
                        updateCount();
                    });
                }
                
                updateCount();
                console.log('Employees loaded successfully');
            })
            .catch(error => {
                console.error('Error loading employees:', error);
                employeeContainer.innerHTML = '<p class="text-danger mb-0">Error: ' + error.message + '</p>';
            });
    });

    function updateCount() {
        const checkedBoxes = employeeContainer.querySelectorAll('.emp-check:checked');
        const count = checkedBoxes.length;
        
        console.log('Checked boxes:', count);
        
        if (count === 0) {
            employeeCount.textContent = 'No employees selected';
            employeeCount.className = 'text-muted mt-1 d-block';
        } else {
            employeeCount.textContent = count + ' employee' + (count > 1 ? 's' : '') + ' selected';
            employeeCount.className = 'text-success mt-1 d-block';
        }
    }

    taskForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const employees = formData.getAll('users_id[]');
        
        console.log('=== FORM SUBMISSION DEBUG ===');
        console.log('Employees selected:', employees);
        console.log('Employee count:', employees.length);
        
        if (employees.length === 0) {
            alert('⚠️ Please select at least one employee!');
            return false;
        }
        
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'task_assign';
        hiddenInput.value = '1';
        this.appendChild(hiddenInput);
        
        console.log('Submitting form with', employees.length, 'employees');
        this.submit();
    });
});
</script>

<?php include FOOTER_PATH; ?>