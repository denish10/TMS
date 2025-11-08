# Task Management System - Documentation

Welcome to the Task Management System (TMS) documentation. This documentation provides detailed explanations of all PHP files in the project, making it easy to understand the code structure, functionality, and implementation details for presentations and viva voce examinations.

## 📚 Documentation Structure

This documentation is organized by file functionality and includes:

- **Purpose** - What the file does and its role in the system
- **Key Features** - Main functionalities and capabilities
- **Code Breakdown** - Detailed explanation of code sections
- **Output/Result** - What users see when the file runs
- **Additional Notes** - Security, validation, and implementation details

## 📁 Files Documented

### Core Files

1. **[index.php](index.md)** - Main login page for user authentication
2. **[dbsetting/connection.php](connection.md)** - Database connection configuration
3. **[dbsetting/config.php](config.md)** - System configuration and constants
4. **[admin/common/activity_logger.php](activity_logger.md)** - Activity logging functionality (Admin)
5. **[user/common/activity_logger.php](activity_logger.md)** - Activity logging functionality (User)

### Dashboard Files

6. **[admin/dashboard.php](dashboard.md)** - Admin dashboard with statistics
7. **[user/dashboard.php](dashboard.md)** - User dashboard with personal statistics
8. **[admin/activity_logs.php](activity_logs.md)** - View and filter system activity logs
9. **[admin/report.php](report.md)** - Performance and leave reports
10. **[user/edit_profile.php](edit_profile.md)** - Edit own profile (User)

### Task Management Files

11. **[admin/task/create_task.php](create_task.md)** - Create and assign tasks to employees
12. **[admin/task/manage_task.php](manage_task.md)** - View and manage all tasks (Admin)
13. **[admin/task/edit_task.php](edit_task.md)** - Edit existing tasks
14. **[admin/task/view_task.php](view_task.md)** - View task details
15. **[admin/task/delete_task.php](delete_task.md)** - Delete tasks
16. **[user/task/task_manage.php](task_manage.md)** - User task management page
17. **[user/task/task_view.php](task_view.md)** - User task viewing page
18. **[user/task/task_status.php](task_status.md)** - Update task status (User)

### Leave Management Files

19. **[user/leave/apply_leave.php](apply_leave.md)** - Submit leave applications
20. **[user/leave/view_leave_status.php](view_leave_status.md)** - View leave application status
21. **[admin/leave/leave_application.php](leave_application.md)** - Admin leave management page
22. **[admin/leave/approve_leave.php](approve_leave.md)** - Approve leave applications
23. **[admin/leave/reject_leave.php](reject_leave.md)** - Reject leave applications

## 🎯 Quick Navigation

### For Students Preparing for Presentation/Viva:

1. **Start Here:** Read [index.php](index.md) to understand the login system
2. **Core Files:** Review [dbsetting/connection.php](connection.md) and [dbsetting/config.php](config.md) for system setup
3. **Main Features:** Study [admin/dashboard.php](dashboard.md) for overview functionality
4. **Activity Logs:** Review [admin/activity_logs.php](activity_logs.md) for activity tracking
5. **Reports:** Explore [admin/report.php](report.md) for reporting features
6. **Task Management:** Explore [create_task.md](create_task.md) and [manage_task.md](manage_task.md)
7. **Leave Management:** Review [apply_leave.md](apply_leave.md) and [view_leave_status.md](view_leave_status.md)
8. **User Profile:** Check [user/edit_profile.php](edit_profile.md) for profile management

### For Understanding Specific Features:

- **Authentication:** [index.php](index.md)
- **Database Operations:** [dbsetting/connection.php](connection.md)
- **System Configuration:** [dbsetting/config.php](config.md)
- **Task Creation:** [admin/task/create_task.php](create_task.md)
- **Task Management:** [admin/task/manage_task.php](manage_task.md)
- **Leave Applications:** [user/leave/apply_leave.php](apply_leave.md)
- **Activity Logging:** [admin/common/activity_logger.php](activity_logger.md)
- **Activity Logs View:** [admin/activity_logs.php](activity_logs.md)
- **Reports:** [admin/report.php](report.md)
- **Profile Management:** [user/edit_profile.php](edit_profile.md)

## 🔍 Key Concepts Covered

### Session Management
- How sessions are used for user authentication
- Session variables and their purposes
- Role-based access control

### Database Operations
- MySQL database connections
- SQL queries and JOIN operations
- Data insertion, update, and deletion
- SQL injection prevention

### Security Features
- Password hashing and verification
- Input validation and sanitization
- XSS (Cross-Site Scripting) prevention
- Role-based access control

### User Interface
- Bootstrap styling and components
- Responsive design
- Form validation
- AJAX functionality

### Activity Logging
- System activity tracking
- User action logging
- Audit trail functionality

## 💡 Presentation Tips

### When Presenting the Project:

1. **Start with Overview:** Explain the system purpose and main features
2. **Show Login:** Demonstrate the authentication system
3. **Dashboard:** Show statistics and overview
4. **Task Management:** Demonstrate task creation and management
5. **Leave Management:** Show leave application and approval process
6. **Security:** Highlight security features and validation

### Key Points to Emphasize:

- **Session Management:** How user sessions are maintained
- **Database Design:** Table relationships and JOIN operations
- **Security:** Input validation, SQL injection prevention, XSS prevention
- **User Roles:** Admin vs Staff functionality
- **Activity Logging:** System monitoring and auditing

## 📖 How to Use This Documentation

1. **Read the Purpose:** Understand what each file does
2. **Review Key Features:** Know the main functionalities
3. **Study Code Breakdown:** Understand the implementation
4. **Check Output/Result:** Know what users will see
5. **Read Additional Notes:** Understand security and validation

## 🔐 Security Highlights

- **Password Hashing:** Passwords stored as hashes, never plain text
- **SQL Injection Prevention:** Input sanitization and escaping
- **XSS Prevention:** Output encoding with htmlspecialchars()
- **Session Security:** Role-based access control
- **Input Validation:** Comprehensive validation of all inputs

## 🎓 For Viva Voce Examination

### Common Questions and Answers:

**Q: How does user authentication work?**
A: See [index.php](index.md) - Users login with email/username and password, sessions are created, and users are redirected based on role.

**Q: How are tasks assigned to employees?**
A: See [create_task.php](create_task.md) - Admin selects department, chooses employees, enters task details, and task is assigned to selected employees.

**Q: How is activity logging implemented?**
A: See [activity_logger.php](activity_logger.md) - All important activities are logged with user ID, activity type, description, and timestamp.

**Q: How is security handled?**
A: See security sections in each file - Input validation, SQL injection prevention, XSS prevention, and role-based access control.

## 📝 Notes

- All documentation is written in simple, clear language
- Code snippets are explained line by line
- Security features are highlighted in each file
- Database logic is explained in detail
- Session usage is documented throughout

## 🚀 Getting Started

1. Read the [README.md](README.md) (this file) for overview
2. Start with [index.php](index.md) for login system
3. Review [dbsetting/config.php](config.md) and [dbsetting/connection.php](connection.md) for system setup
4. Review [admin/dashboard.php](dashboard.md) for main features
5. Explore [admin/activity_logs.php](activity_logs.md) for activity tracking
6. Review [admin/report.php](report.md) for reporting features
7. Explore task and leave management files
8. Check [user/edit_profile.php](edit_profile.md) for profile management
9. Study security and validation implementations

## 📞 Support

For questions or clarifications about the documentation:
- Review the specific file documentation
- Check the "Additional Notes" section
- Refer to code comments in the actual PHP files

---

**Happy Learning and Good Luck with Your Presentation! 🎉**

