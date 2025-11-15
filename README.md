# Task Management System (TMS)

## 1. Project Overview

### Project Name
**Task Management System (TMS)**

### Short Description
A comprehensive web-based task and leave management system designed for organizations to efficiently manage employee tasks, track work progress, and handle leave applications. The system provides separate interfaces for administrators and staff members with role-based access control.

### Purpose / Objective
The primary objective of this system is to:
- Streamline task assignment and tracking within an organization
- Facilitate efficient leave application and approval processes
- Provide real-time dashboard analytics for administrators
- Enable employees to manage their tasks and leave requests
- Maintain a complete audit trail of all system activities
- Organize employees by departments for better task distribution

### Technologies Used
- **Backend:** PHP 7.4+
- **Database:** MySQL (via mysqli)
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework/Library:** Bootstrap 5.3.0
- **Icons:** Font Awesome
- **Server:** XAMPP (Apache, MySQL, PHP)
- **Session Management:** PHP Sessions

### Target Users or System Type
- **Web-based Task Management System**
- **Target Users:**
  - **Administrators:** Manage employees, departments, tasks, and approve/reject leave applications
  - **Staff/Employees:** View assigned tasks, update task status, apply for leave, and view leave status

---

## 2. System Features

### Authentication & Authorization
- User login with email or username
- Password hashing for security
- Role-based access control (Admin/Staff)
- Session management
- Automatic redirect based on user role
- Last login timestamp tracking

### Dashboard Features
- **Admin Dashboard:**
  - Employee statistics (total, active employees)
  - Department statistics
  - Task statistics (total, completed, pending, in progress, on hold, cancelled)
  - Leave statistics (total, approved, pending, rejected)
  - Top performing employees ranking
  - Recent tasks list
  - Recent pending leaves
  - Task status distribution charts
  - Leave status distribution charts
  - Quick access links

- **User Dashboard:**
  - Personal task statistics
  - Personal leave statistics
  - Task completion rate
  - Recent tasks assigned
  - Recent leave applications
  - Task status distribution
  - Leave status distribution
  - Quick access links

### Task Management
- Create tasks with title, description, priority (High/Medium/Low)
- Assign tasks to multiple employees from selected department
- Set task start and end dates
- View all tasks with search functionality
- Edit task details (description, dates, priority, status)
- Update task status (Not Started, In Progress, Completed, On Hold, Cancelled, Pending)
- Delete tasks
- View task details
- Filter tasks by employee, priority, or status
- Employees can view their assigned tasks
- Employees can update their task status

### Leave Management
- Employees can apply for leave with subject, message, and date range
- View leave application status (Pending/Approved/Rejected)
- Admin can view all leave applications
- Admin can approve or reject leave applications
- Admin can delete leave applications
- Search functionality for leave applications
- Date validation (start date cannot be after end date)

### Employee Management
- Add new employees with full details (name, username, email, mobile, department, role)
- View all employees
- Edit employee information
- Delete employees
- Reset employee passwords
- View employee profile
- Edit employee profile picture
- Manage employee-department relationships

### Department Management
- Add new departments
- View all departments
- Edit department information
- Delete departments
- Organize employees by departments

### Reporting & Analytics
- **Performance Reports:** Employee performance metrics based on task completion
- **Leave Reports:** Employee leave statistics and approval rates
- **Filterable Reports:** Filter by employee, month, and year
- **Print Functionality:** Print reports for documentation
- **Completion Rate Tracking:** Visual indicators for task completion rates
- **Color-coded Metrics:** Visual representation of performance levels

### Additional Features
- 
- Real-time search functionality
- Form validation and error handling
- Success/error message alerts
- Auto-refresh dashboard
- Profile management (employees can edit their own profile)
- Password hashing and verification
- CSV export functionality

---

## 3. Folder & File Structure

```
TMS/
│
├── admin/                          # Admin-only functionality
│   ├── dashboard.php              # Admin dashboard with statistics
│   ├── report.php                  # Generate reports
│   ├── logout.php                  # Admin logout
│   │
│   ├── department/                 # Department management
│   │   ├── add_department.php      # Add new department
│   │   ├── manage_department.php   # View all departments
│   │   ├── edit_department.php     # Edit department
│   │   └── delete_department.php   # Delete department
│   │
│   ├── employee/                   # Employee management
│   │   ├── add_employee.php        # Add new employee
│   │   ├── manage_employee.php     # View all employees
│   │   ├── edit_employee.php       # Edit employee details
│   │   ├── delete_employee.php     # Delete employee
│   │   ├── view_employee.php       # View employee profile
│   │   └── reset_password.php      # Reset employee password
│   │
│   ├── leave/                      # Leave management
│   │   ├── leave_application.php   # View all leave applications
│   │   ├── approve_leave.php       # Approve leave request
│   │   ├── reject_leave.php        # Reject leave request
│   │   └── delete_leave_application.php # Delete leave application
│   │
│   └── task/                       # Task management
│       ├── create_task.php         # Create and assign tasks
│       ├── manage_task.php         # View all tasks
│       ├── edit_task.php           # Edit task details
│       ├── view_task.php           # View task details
│       ├── delete_task.php         # Delete task
│       └── get_employees.php       # AJAX: Get employees by department
│
├── user/                           # Staff/Employee functionality
│   ├── dashboard.php               # Employee dashboard
│   ├── edit_profile.php            # Edit own profile
│   ├── logout.php                  # User logout
│   │
│   ├── leave/                      # Leave management
│   │   ├── apply_leave.php         # Apply for leave
│   │   └── view_leave_status.php  # View leave application status
│   │
│   └── task/                       # Task management
│       ├── task_manage.php         # View assigned tasks
│       ├── task_view.php           # View task details
│       └── task_status.php         # Update task status
│
├── dbsetting/                      # Database configuration
│   ├── connection.php              # Database connection
│   └── config.php                  # Configuration constants
│
├── admin/common/                   # Admin shared files
│   ├── header.php                  # Admin header/navigation
│   ├── sidebar.php                 # Admin sidebar menu
│   └── footer.php                  # Admin footer
│
├── user/common/                    # User shared files
│   ├── user_header.php             # User header/navigation
│   ├── user_sidebar.php            # User sidebar menu
│   └── user_footer.php             # User footer
│
├── assets/                         # Static assets
│   ├── css/
│   │   └── style.css               # Custom styles
│   └── js/                         # JavaScript files
│
│
└── index.php                       # Login page (entry point)
```

### File Responsibilities

- **index.php:** Main entry point, handles user authentication
- **admin/dashboard.php:** Admin dashboard with comprehensive statistics
- **admin/report.php:** Generate performance and leave reports
- **user/dashboard.php:** Employee dashboard with personal statistics
- **user/edit_profile.php:** Employees can edit their own profile
- **dbsetting/connection.php:** Establishes MySQL database connection
- **dbsetting/config.php:** Defines system constants and configuration
- **admin/task/create_task.php:** Create and assign tasks to employees
- **admin/task/manage_task.php:** View and manage all tasks
- **user/leave/apply_leave.php:** Submit leave applications
- **admin/leave/leave_application.php:** View and manage leave applications
- **admin/employee/add_employee.php:** Add new employees to system
- **admin/department/add_department.php:** Add new departments

---

## 4. Functional Description (File-wise Explanation)

Detailed documentation for each PHP file is available in the `/docs/` folder. Each file includes:
- File name and purpose
- Key features
- Code breakdown with explanations
- Output/Result description
- Notes on validations, sessions, and alerts

### Key Files Documentation:
- [index.php](docs/index.md) - Login and authentication
- [dbsetting/connection.php](docs/connection.md) - Database connection
- [dbsetting/config.php](docs/config.md) - System configuration
- [admin/dashboard.php](docs/dashboard.md) - Admin dashboard
- [admin/report.php](docs/report.md) - Performance reports
- [user/dashboard.php](docs/dashboard.md) - User dashboard
- [user/edit_profile.php](docs/edit_profile.md) - Edit profile
- [admin/task/create_task.php](docs/create_task.md) - Task creation
- [user/leave/apply_leave.php](docs/apply_leave.md) - Leave application
- [admin/task/manage_task.php](docs/manage_task.md) - Task management
- [And more...](docs/)

---

## 5. Database Overview

### Database Name
`db_task_management_system`

### Tables Structure

#### 1. `users` Table
Stores user account information (both admin and staff).

| Column | Type | Description |
|--------|------|-------------|
| users_id | INT (Primary Key, Auto Increment) | Unique user identifier |
| fullname | VARCHAR | User's full name |
| username | VARCHAR (Unique) | Login username |
| email | VARCHAR (Unique) | Email address |
| mobile | VARCHAR | Mobile number |
| role | ENUM('admin', 'staff') | User role |
| password | VARCHAR | Hashed password |
| last_login | DATETIME | Last login timestamp |
| created_at | DATETIME | Account creation date |

**Relationships:**
- One-to-many with `task_assign` (users_id)
- One-to-many with `leave_apply` (users_id)
- One-to-many with `employee_department` (users_id)

#### 2. `department` Table
Stores department information.

| Column | Type | Description |
|--------|------|-------------|
| department_id | INT (Primary Key, Auto Increment) | Unique department identifier |
| department_name | VARCHAR | Department name |

**Relationships:**
- One-to-many with `employee_department` (department_id)

#### 3. `employee_department` Table
Junction table linking employees to departments.

| Column | Type | Description |
|--------|------|-------------|
| id | INT (Primary Key, Auto Increment) | Unique identifier |
| users_id | INT (Foreign Key) | References users.users_id |
| department_id | INT (Foreign Key) | References department.department_id |

#### 4. `task_manage` Table
Stores task information.

| Column | Type | Description |
|--------|------|-------------|
| task_id | INT (Primary Key, Auto Increment) | Unique task identifier |
| task_title | VARCHAR | Task title |
| task_description | TEXT | Task description |
| created_date | DATETIME | Task creation date |
| start_date | DATE | Task start date |
| end_date | DATE | Task end date |
| priority | ENUM('High', 'Medium', 'Low') | Task priority |

**Relationships:**
- One-to-many with `task_assign` (task_id)

#### 5. `task_assign` Table
Stores task assignments to employees.

| Column | Type | Description |
|--------|------|-------------|
| record_id | INT (Primary Key, Auto Increment) | Unique assignment identifier |
| task_id | INT (Foreign Key) | References task_manage.task_id |
| users_id | INT (Foreign Key) | References users.users_id |
| status | ENUM('Not Started', 'In Progress', 'Completed', 'On Hold', 'Cancelled', 'Pending') | Task status |

#### 6. `leave_apply` Table
Stores leave applications.

| Column | Type | Description |
|--------|------|-------------|
| leave_id | INT (Primary Key, Auto Increment) | Unique leave identifier |
| users_id | INT (Foreign Key) | References users.users_id |
| subject | VARCHAR | Leave subject |
| message | TEXT | Leave message/description |
| start_date | DATE | Leave start date |
| end_date | DATE | Leave end date |
| created_date | DATETIME | Application creation date |
| status | ENUM('Pending', 'Approved', 'Rejected') | Leave status |

### Database Relationships

```
users (1) ────< (many) task_assign
users (1) ────< (many) leave_apply
users (1) ────< (many) employee_department
department (1) ────< (many) employee_department
task_manage (1) ────< (many) task_assign
```

---

## 6. Working Process / Flow

### System Workflow

#### 1. **User Registration/Login Flow**
```
User visits index.php
    ↓
Enters email/username and password
    ↓
System validates credentials
    ↓
If valid:
    - Create session variables (users_id, name, email, role)
    - Update last_login timestamp
    - Redirect based on role:
        * Admin → admin/dashboard.php
        * Staff → user/dashboard.php
If invalid:
    - Display error message
    - Stay on login page
```

#### 2. **Task Management Flow (Admin)**
```
Admin logs in → Admin Dashboard
    ↓
Navigate to "Create Task"
    ↓
Select Department
    ↓
Select Employees (from selected department)
    ↓
Enter Task Details:
    - Title
    - Description
    - Priority (High/Medium/Low)
    - Start Date
    - End Date
    ↓
Submit Task
    ↓
System:
    - Creates task in task_manage table
    - Assigns task to selected employees in task_assign table
    - Sets initial status as 'Not Started'
    ↓
Redirect to Manage Tasks page
```

#### 3. **Task Management Flow (Employee)**
```
Employee logs in → User Dashboard
    ↓
Navigate to "View Tasks"
    ↓
View assigned tasks with:
    - Task ID, Title, Description
    - Start Date, End Date
    - Current Status
    ↓
Click "Update" to change status
    ↓
Select new status:
    - Not Started
    - In Progress
    - Completed
    - On Hold
    - Cancelled
    ↓
Submit Update
    ↓
System updates task_assign table
    ↓
Redirect to task list
```

#### 4. **Leave Management Flow (Employee)**
```
Employee logs in → User Dashboard
    ↓
Navigate to "Apply Leave"
    ↓
Fill Leave Application Form:
    - Subject
    - Message
    - Start Date
    - End Date
    ↓
Submit Application
    ↓
System:
    - Validates dates
    - Creates record in leave_apply table
    - Sets status as 'Pending'
    ↓
Redirect to "View Leave Status"
    ↓
Employee can see application status
```

#### 5. **Leave Approval Flow (Admin)**
```
Admin logs in → Admin Dashboard
    ↓
Navigate to "Leave Applications"
    ↓
View all leave applications with:
    - Employee Name
    - Subject, Message
    - Dates
    - Current Status
    ↓
Click "Approve" or "Reject"
    ↓
System:
    - Updates status in leave_apply table
    ↓
Redirect back to leave applications list
```

#### 6. **Employee Management Flow (Admin)**
```
Admin logs in → Admin Dashboard
    ↓
Navigate to "Manage Employees"
    ↓
Click "Add Employee"
    ↓
Fill Employee Form:
    - Full Name
    - Username
    - Email
    - Mobile
    - Department
    - Role (Admin/Staff)
    - Password
    ↓
Submit
    ↓
System:
    - Validates all fields
    - Checks for duplicate username/email
    - Hashes password
    - Creates user in users table
    - Links to department in employee_department table
    ↓
Redirect to employee list
```

### Complete User Journey Example

**Scenario: Employee completes a task**

1. Employee logs in → `index.php`
2. Redirected to → `user/dashboard.php`
3. Views tasks → `user/task/task_manage.php`
4. Clicks "Update" on a task → `user/task/task_status.php`
5. Changes status to "Completed" → Updates `task_assign` table
6. Returns to task list → Sees updated status
7. Admin views dashboard → Sees updated task statistics
8. Admin views task → `admin/task/view_task.php` → Sees completed status

---

## 7. Future Enhancements

### 1. **Email Notifications**
- Send email notifications when tasks are assigned
- Notify employees when leave is approved/rejected
- Send reminders for upcoming task deadlines
- Weekly task summary emails

### 2. **Mobile Application**
- Develop native mobile apps (Android/iOS)
- Push notifications for task assignments
- Mobile-optimized interface
- Offline task viewing capability

### 3. **Advanced Analytics & Reporting**
- Generate PDF reports for tasks and leaves
- Export data to Excel/CSV
- Visual charts and graphs (using Chart.js)
- Performance analytics dashboard
- Department-wise performance reports

### 4. **Real-time Features**
- Real-time task status updates
- Live chat between admin and employees
- Real-time notifications (using WebSockets)

### 5. **Enhanced Task Features**
- Task comments and discussions
- File attachments for tasks
- Task dependencies and subtasks
- Task templates for recurring tasks
- Time tracking for tasks

### 6. **Leave Management Enhancements**
- Leave balance tracking
- Leave calendar view
- Leave policy configuration
- Automatic leave balance calculation
- Leave request workflow (multi-level approval)

### 7. **Security Enhancements**
- Two-factor authentication (2FA)
- Password strength requirements
- Account lockout after failed login attempts
- Session timeout configuration
- IP-based access control

### 8. **User Experience Improvements**
- Dark mode theme
- Customizable dashboard widgets
- Drag-and-drop task organization
- Keyboard shortcuts
- Advanced search and filters

### 9. **Integration Features**
- Calendar integration (Google Calendar, Outlook)
- Slack/Teams integration
- API for third-party integrations
- Single Sign-On (SSO) support

### 10. **Administrative Features**
- Bulk operations (bulk task assignment, bulk leave approval)
- Custom user roles and permissions
- Department hierarchy
- Task categories and tags
- Automated task assignment rules

---

## 8. Conclusion

The **Task Management System (TMS)** is a comprehensive web-based solution designed to streamline organizational task and leave management processes. The system successfully fulfills its primary objectives by:

### Key Achievements:

1. **Efficient Task Management:** The system enables administrators to create, assign, and track tasks effectively, while employees can easily view and update their task status, leading to improved productivity and accountability.

2. **Streamlined Leave Processing:** The leave management module simplifies the leave application and approval process, reducing administrative overhead and providing transparency for both employees and administrators.

3. **Role-Based Access Control:** The implementation of separate interfaces for administrators and staff ensures that users only access relevant features, maintaining security and usability.

4. **Comprehensive Dashboard Analytics:** Both admin and user dashboards provide real-time insights into tasks and leaves, enabling data-driven decision-making.

5. **User-Friendly Interface:** The responsive design and intuitive navigation make the system accessible to users of all technical levels.

### System Benefits:

- **For Organizations:** Improved task tracking, better resource allocation, and streamlined leave management.
- **For Administrators:** Centralized control, real-time statistics, efficient employee and department management, and quick decision-making tools.
- **For Employees:** Easy task management, transparent leave process, personal dashboard insights, and user-friendly interface.

### Technical Excellence:

The system demonstrates solid software engineering practices including:
- Secure authentication with password hashing
- SQL injection prevention
- XSS (Cross-Site Scripting) protection
- Session management
- Input validation
- Error handling
- Code organization and modularity

### Future Potential:

With the planned enhancements, the system has the potential to evolve into a comprehensive enterprise-level task and project management solution, competing with commercial alternatives while being customizable to specific organizational needs.

In conclusion, the Task Management System successfully addresses the core requirements of task and leave management in an organizational setting, providing a solid foundation for future enhancements and scalability.

---

## 📚 Documentation

For detailed documentation of individual PHP files, please refer to the `/docs/` folder where each file is explained with:
- Purpose and functionality
- Code breakdown
- Input/output descriptions
- Security and validation notes

---

**Developed with ❤️ for efficient organizational task management**

