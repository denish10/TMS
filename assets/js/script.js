/**
 * Task Management System - JavaScript File
 * This file contains all JavaScript functionality for the TMS application
 * Simple and readable code for college projects
 */

// ============================================================================
// 1. TABLE SEARCH FUNCTIONALITY
// ============================================================================
// This function adds search functionality to any table on the page
// It works when you have an input with id="searchInput" and a table

function initTableSearch() {
    // Get the search input box
    var searchInput = document.getElementById('searchInput');
    
    // If search input doesn't exist, do nothing
    if (!searchInput) {
        return;
    }
    
    // Skip if this is activity logs page (it has its own search)
    if (document.getElementById('filterForm')) {
        return;
    }
    
    // Find the table in the page
    var table = null;
    
    // Try to find table by common IDs
    if (document.getElementById('task_table')) {
        table = document.getElementById('task_table');
    } else if (document.getElementById('employee_table')) {
        table = document.getElementById('employee_table');
    } else if (document.getElementById('department_table')) {
        table = document.getElementById('department_table');
    } else if (document.getElementById('leave_table')) {
        table = document.getElementById('leave_table');
    } else {
        // Find any table near the search input
        var container = searchInput.parentElement;
        while (container && !table) {
            table = container.querySelector('table');
            container = container.parentElement;
        }
    }
    
    // If table found, add search functionality
    if (table) {
        var tbody = table.querySelector('tbody');
        
        if (tbody) {
            // When user types in search box
            searchInput.addEventListener('keyup', function() {
                var searchValue = this.value.toLowerCase();
                var rows = tbody.querySelectorAll('tr');
                
                // Loop through each row
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    var rowText = row.textContent.toLowerCase();
                    
                    // Show row if it contains search text, hide if not
                    if (rowText.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
    }
}

// ============================================================================
// 2. TASK CREATION - EMPLOYEE DROPDOWN
// ============================================================================
// This function handles employee selection when creating tasks

function initTaskCreation() {
    var departmentSelect = document.getElementById('department_id');
    var employeeList = document.getElementById('employee_list');
    var employeeDropdownText = document.getElementById('employeeDropdownText');
    
    // Only run on task creation page
    if (!departmentSelect || !employeeList) {
        return;
    }
    
    // When department is selected
    departmentSelect.addEventListener('change', function() {
        var deptId = this.value;
        
        // If no department selected, clear employee list
        if (!deptId) {
            employeeList.innerHTML = '<li><p class="dropdown-item text-muted mb-0">Select a department first...</p></li>';
            updateEmployeeText();
            return;
        }
        
        // Load employees from selected department using AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "get_employees.php?department_id=" + deptId, true);
        
        xhr.onload = function() {
            if (this.status == 200) {
                employeeList.innerHTML = this.responseText;
                initEmployeeCheckboxes();
            } else {
                employeeList.innerHTML = '<li><p class="dropdown-item text-danger mb-0">Could not load employees</p></li>';
            }
        };
        
        xhr.send();
    });
    
    // Initialize employee checkboxes
    function initEmployeeCheckboxes() {
        var empChecks = document.querySelectorAll('.emp-check');
        var selectAll = document.getElementById('select_all');
        
        // Add event listener to each checkbox
        for (var i = 0; i < empChecks.length; i++) {
            empChecks[i].addEventListener('change', updateEmployeeText);
        }
        
        // Handle "Select All" checkbox
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                for (var i = 0; i < empChecks.length; i++) {
                    empChecks[i].checked = this.checked;
                }
                updateEmployeeText();
            });
        }
        
        updateEmployeeText();
    }
    
    // Update the dropdown button text based on selected employees
    function updateEmployeeText() {
        if (!employeeDropdownText) {
            return;
        }
        
        var empChecks = document.querySelectorAll('.emp-check');
        var selectAll = document.getElementById('select_all');
        
        if (empChecks.length === 0) {
            employeeDropdownText.innerText = 'Choose Employees';
            return;
        }
        
        // Count checked employees
        var checkedCount = 0;
        for (var i = 0; i < empChecks.length; i++) {
            if (empChecks[i].checked) {
                checkedCount++;
            }
        }
        
        // Update button text based on selection
        if (checkedCount === 0) {
            employeeDropdownText.innerText = 'Choose Employees';
            if (selectAll) selectAll.checked = false;
        } else if (checkedCount === empChecks.length) {
            employeeDropdownText.innerText = 'All Employees Selected';
            if (selectAll) selectAll.checked = true;
        } else if (checkedCount === 1) {
            // Show name of selected employee
            for (var i = 0; i < empChecks.length; i++) {
                if (empChecks[i].checked) {
                    var label = empChecks[i].nextElementSibling;
                    employeeDropdownText.innerText = label ? label.innerText.trim() : '1 employee selected';
                    break;
                }
            }
            if (selectAll) selectAll.checked = false;
        } else {
            employeeDropdownText.innerText = checkedCount + ' employees selected';
            if (selectAll) selectAll.checked = false;
        }
    }
    
    // Initialize if employees are already loaded (when page refreshes)
    if (employeeList.querySelector('.emp-check')) {
        initEmployeeCheckboxes();
    }
}

// ============================================================================
// 3. DASHBOARD AUTO-REFRESH
// ============================================================================
// This function refreshes the dashboard every 5 minutes

function initDashboardRefresh() {
    // Check if we are on dashboard page
    var isDashboard = window.location.pathname.includes('dashboard.php');
    
    if (isDashboard) {
        // Refresh page every 5 minutes (300000 milliseconds)
        setInterval(function() {
            location.reload();
        }, 300000);
    }
}

// ============================================================================
// 4. DEPARTMENT EDIT CONFIRMATION
// ============================================================================
// This function shows confirmation dialog when editing department

window.confirmUpdate = function() {
    var deptNameInput = document.getElementById('department_name');
    
    if (!deptNameInput) {
        return true;
    }
    
    var deptName = deptNameInput.value;
    var message = "Are you sure you want to update this department to '" + deptName + "'?";
    
    return confirm(message);
};

// ============================================================================
// 5. FORM LOADING STATES
// ============================================================================
// This function shows loading state when forms are submitted

function initFormLoading() {
    var forms = document.querySelectorAll('form');
    
    for (var i = 0; i < forms.length; i++) {
        forms[i].addEventListener('submit', function() {
            var submitBtn = this.querySelector('button[type="submit"], input[type="submit"]');
            
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                
                if (submitBtn.tagName === 'BUTTON') {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
                } else {
                    submitBtn.value = 'Processing...';
                }
            }
        });
    }
}

// ============================================================================
// INITIALIZE ALL FUNCTIONS WHEN PAGE LOADS
// ============================================================================

// Wait for page to load completely
document.addEventListener('DOMContentLoaded', function() {
    // Initialize table search
    initTableSearch();
    
    // Initialize task creation (if on that page)
    initTaskCreation();
    
    // Initialize dashboard refresh (if on dashboard)
    initDashboardRefresh();
    
    // Initialize form loading states
    initFormLoading();
});
