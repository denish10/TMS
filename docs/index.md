# Documentation Index

This folder contains detailed documentation for each PHP file in the Task Management System.

## 📚 Available Documentation

### Core System Files
- [index.php](index.md) - Login and authentication page
- [dbsetting/connection.php](connection.md) - Database connection setup
- [dbsetting/config.php](config.md) - System configuration and constants
- [admin/common/activity_logger.php](activity_logger.md) - Activity logging functions (Admin)
- [user/common/activity_logger.php](activity_logger.md) - Activity logging functions (User)

### Dashboard Files
- [admin/dashboard.php](admin_dashboard.md) - Admin dashboard with statistics
- [user/dashboard.php](user_dashboard.md) - Employee dashboard
- [admin/activity_logs.php](activity_logs.md) - View and filter activity logs
- [admin/report.php](report.md) - Performance and leave reports

### Task Management Files
- [admin/task/create_task.php](create_task.md) - Create and assign tasks
- [admin/task/manage_task.php](manage_task.md) - View and manage all tasks
- [admin/task/edit_task.php](edit_task.md) - Edit task details
- [admin/task/view_task.php](view_task.md) - View task details
- [admin/task/delete_task.php](delete_task.md) - Delete tasks
- [user/task/task_manage.php](task_manage.md) - Employee task list
- [user/task/task_view.php](task_view.md) - Employee view task details
- [user/task/task_status.php](task_status.md) - Update task status

### Leave Management Files
- [user/leave/apply_leave.php](apply_leave.md) - Submit leave applications
- [user/leave/view_leave_status.php](view_leave_status.md) - View leave status
- [admin/leave/leave_application.php](leave_application.md) - Admin leave management
- [admin/leave/approve_leave.php](approve_leave.md) - Approve leave
- [admin/leave/reject_leave.php](reject_leave.md) - Reject leave

### Employee Management Files
- [admin/employee/add_employee.php](add_employee.md) - Add new employees
- [admin/employee/manage_employee.php](manage_employee.md) - View all employees
- [admin/employee/edit_employee.php](edit_employee.md) - Edit employee details
- [admin/employee/delete_employee.php](delete_employee.md) - Delete employees
- [admin/employee/view_employee.php](view_employee.md) - View employee profile

### User Profile Files
- [user/edit_profile.php](edit_profile.md) - Edit own profile (User)

### Department Management Files
- [admin/department/add_department.php](add_department.md) - Add departments
- [admin/department/manage_department.php](manage_department.md) - View departments
- [admin/department/edit_department.php](edit_department.md) - Edit departments
- [admin/department/delete_department.php](delete_department.md) - Delete departments

## 📝 Documentation Format

Each documentation file includes:

1. **Purpose** - What the file does and its role in the system
2. **Key Features** - Main functionalities and capabilities
3. **Code Breakdown** - Detailed explanation of code sections with PHP snippets
4. **Output/Result** - What users see when the file runs
5. **Additional Notes** - Security, validation, session usage, and alerts

## 🚀 Quick Start

1. Start with [index.php](index.md) to understand authentication
2. Review [dbsetting/connection.php](connection.md) and [dbsetting/config.php](config.md) for system setup
3. Check [admin/dashboard.php](admin_dashboard.md) for admin features
4. Explore [admin/activity_logs.php](activity_logs.md) for activity logging
5. Review [admin/report.php](report.md) for reporting features
6. Explore task and leave management files for core functionality

## 📖 For Presentation/Viva

When presenting your project:

1. **Begin with Overview:** Explain the system purpose (see main README.md)
2. **Show Login:** Demonstrate authentication (index.php)
3. **Dashboard:** Show statistics and overview
4. **Task Management:** Demonstrate task creation and assignment
5. **Leave Management:** Show leave application and approval process
6. **Security:** Highlight validation and security features

## 🔍 Need More Documentation?

If you need documentation for additional files not listed here, please request them and they will be created following the same format.

---

**Note:** Some files may have similar functionality. Refer to the most relevant documentation file for understanding.
