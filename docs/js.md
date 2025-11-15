# File: assets/js/script.js

## Purpose

This file contains all JavaScript functionality for the Task Management System. It is a consolidated JavaScript file that handles table searching, task creation, dashboard auto-refresh, and form submissions. The code is written in simple, readable JavaScript suitable for college projects and presentations.

## Key Features

- Universal table search functionality
- Task creation with employee dropdown
- Dashboard auto-refresh
- Department edit confirmation
- Form loading states
- Automatic initialization on page load

## Code Breakdown

### 1. Table Search Functionality

```javascript
function initTableSearch() {
    var searchInput = document.getElementById('searchInput');
    
    if (!searchInput) {
        return;
    }
    
    // Find the table and add search functionality
    // ...
}
```

**Explanation:**
- Gets the search input box by ID
- Finds the associated table on the page
- Adds event listener to search input
- Filters table rows based on search text
- Shows matching rows, hides non-matching rows

**How it works:**
1. User types in search box
2. JavaScript gets the search value
3. Loops through all table rows
4. Compares row text with search value
5. Shows/hides rows based on match

**Used in:**
- Manage Tasks page
- Manage Employees page
- Manage Departments page
- Leave Applications page
- User Task Management page
- User Leave Status page

### 2. Task Creation - Employee Dropdown

```javascript
function initTaskCreation() {
    var departmentSelect = document.getElementById('department_id');
    // When department changes, load employees via AJAX
    // Handle employee selection
    // Update dropdown text
}
```

**Explanation:**
- Handles employee selection when creating tasks
- Loads employees from selected department using AJAX
- Updates dropdown button text based on selection
- Handles "Select All" functionality

**How it works:**
1. User selects a department
2. JavaScript sends AJAX request to get employees
3. Updates employee list with checkboxes
4. User selects employees
5. Dropdown text updates to show selection count
6. Handles "Select All" checkbox

**AJAX Request:**
- **URL:** `get_employees.php?department_id=X`
- **Method:** GET
- **Response:** HTML list of employee checkboxes

**Used in:**
- Create Task page (admin/task/create_task.php)

### 3. Dashboard Auto-Refresh

```javascript
function initDashboardRefresh() {
    var isDashboard = window.location.pathname.includes('dashboard.php');
    
    if (isDashboard) {
        setInterval(function() {
            location.reload();
        }, 300000); // 5 minutes
    }
}
```

**Explanation:**
- Checks if user is on dashboard page
- Sets up automatic page refresh every 5 minutes
- Keeps dashboard statistics up-to-date

**Why it's useful:**
- Keeps statistics current without manual refresh
- Updates task counts, leave counts automatically
- Shows latest data to users

**Used in:**
- Admin Dashboard (admin/dashboard.php)
- User Dashboard (user/dashboard.php)

### 4. Department Edit Confirmation

```javascript
window.confirmUpdate = function() {
    var deptNameInput = document.getElementById('department_name');
    var deptName = deptNameInput.value;
    return confirm("Are you sure you want to update this department to '" + deptName + "'?");
};
```

**Explanation:**
- Shows confirmation dialog before updating department
- Gets department name from input field
- Returns true/false based on user confirmation
- Prevents accidental updates

**How it works:**
1. User clicks Update button
2. JavaScript gets department name
3. Shows confirmation dialog with department name
4. If user confirms, form submits
5. If user cancels, form doesn't submit

**Used in:**
- Edit Department page (admin/department/edit_department.php)

### 5. Form Loading States

```javascript
function initFormLoading() {
    var forms = document.querySelectorAll('form');
    
    for (var i = 0; i < forms.length; i++) {
        forms[i].addEventListener('submit', function() {
            // Disable submit button
            // Show loading text
        });
    }
}
```

**Explanation:**
- Adds loading state to all forms
- Disables submit button when form is submitted
- Shows "Processing..." text
- Prevents double submissions

**How it works:**
1. User submits a form
2. JavaScript disables submit button
3. Changes button text to "Processing..."
4. Form submits to server
5. Prevents user from clicking submit multiple times

**Used in:**
- All forms in the application

## Output / Result

**When the JavaScript file loads:**

1. **Table Search:**
   - Search boxes become functional
   - Users can filter table rows by typing
   - Rows show/hide based on search text

2. **Task Creation:**
   - Department dropdown loads employees
   - Employee selection updates dropdown text
   - "Select All" functionality works

3. **Dashboard:**
   - Auto-refreshes every 5 minutes
   - Statistics stay up-to-date

4. **Forms:**
   - Show loading states on submit
   - Prevent double submissions

## Additional Notes

### JavaScript Concepts Used:

1. **DOM Manipulation:**
   - `document.getElementById()` - Get element by ID
   - `document.querySelector()` - Get element by selector
   - `addEventListener()` - Add event listeners
   - `innerHTML` - Change element content
   - `style.display` - Show/hide elements

2. **Event Handling:**
   - `keyup` event - When user types
   - `change` event - When dropdown changes
   - `submit` event - When form submits
   - `click` event - When button clicked

3. **AJAX (Asynchronous JavaScript and XML):**
   - `XMLHttpRequest` - Make HTTP requests
   - Load data without page refresh
   - Update page content dynamically

4. **Timing Functions:**
   - `setTimeout()` - Execute code after delay
   - `setInterval()` - Execute code repeatedly
   - `clearTimeout()` - Cancel timeout

5. **String Manipulation:**
   - `toLowerCase()` - Convert to lowercase
   - `includes()` - Check if string contains text
   - `replace()` - Replace text in string
   - Regular expressions for pattern matching


### Code Organization:

- **Functions:** Each feature is in its own function
- **Initialization:** All functions initialize on page load
- **Comments:** Clear comments explain each section
- **Simple Logic:** Easy to understand if-else statements
- **Traditional Loops:** Uses for loops instead of forEach

### Browser Compatibility:

- Works in all modern browsers
- Uses standard JavaScript (ES5)
- No external dependencies
- Compatible with older browsers

### Performance:

- **Debounced Search:** Reduces server requests
- **Event Delegation:** Efficient event handling
- **Conditional Initialization:** Only runs needed code
- **Lightweight:** Small file size, fast loading

## Usage Examples

### Example 1: Table Search

**HTML:**
```html
<input type="text" id="searchInput" placeholder="Search...">
<table id="task_table">
    <tbody>
        <tr><td>Task 1</td></tr>
        <tr><td>Task 2</td></tr>
    </tbody>
</table>
```

**JavaScript automatically:**
- Finds search input and table
- Adds search functionality
- Filters rows as user types

### Example 2: Task Creation

**HTML:**
```html
<select id="department_id">
    <option value="1">IT Department</option>
</select>
<ul id="employee_list"></ul>
```

**JavaScript automatically:**
- Listens for department change
- Loads employees via AJAX
- Updates employee list
- Handles employee selection

## For College Projects

### Key Points to Explain:

1. **Event-Driven Programming:**
   - JavaScript responds to user events
   - Events trigger functions
   - Functions modify page content

2. **DOM Manipulation:**
   - JavaScript can change HTML content
   - Can show/hide elements
   - Can add/remove elements

3. **AJAX:**
   - Load data without page refresh
   - Makes websites more interactive
   - Improves user experience

4. **Form Validation:**
   - Client-side validation
   - Prevents invalid submissions
   - Provides instant feedback

5. **Dynamic Content:**
   - Content updates without refresh
   - Real-time search and filtering
   - Interactive user interface

### Common Questions:

**Q: How does table search work?**
A: JavaScript listens for typing in search box, gets search value, loops through table rows, compares text, shows/hides rows based on match.

**Q: What is debounce?**
A: Debounce waits for user to stop typing (500ms) before searching. This prevents too many searches while user is typing.

**Q: How does AJAX work?**
A: JavaScript sends HTTP request to server, server returns data, JavaScript updates page without refreshing. Used to load employees when department is selected.

**Q: How does employee dropdown work in task creation?**
A: When a department is selected, JavaScript sends an AJAX request to load employees from that department, then updates the employee list with checkboxes for selection.

## Security Notes

- **Client-Side Only:** All validation should also be done on server
- **XSS Prevention:** Data is escaped when displayed
- **Input Validation:** Always validate on server side
- **No Sensitive Data:** JavaScript runs in browser, don't store secrets

## Best Practices

- **Separation of Concerns:** JavaScript in separate file
- **Code Organization:** Functions grouped by functionality
- **Comments:** Clear comments explain code
- **Error Handling:** Checks if elements exist before using
- **Performance:** Uses efficient methods and debouncing

## Related Files

- **assets/js/script.js** - This file
- **admin/common/header.php** - Contains header JavaScript (dropdown menu)
- **user/common/user_header.php** - Contains user header JavaScript
- **admin/common/footer.php** - Includes this JavaScript file
- **user/common/user_footer.php** - Includes this JavaScript file

## Notes for Students

- **Read the Comments:** Comments explain what each section does
- **Start Simple:** Understand basic concepts first
- **Test Incrementally:** Test each function separately
- **Use Browser Console:** Check for errors in browser console
- **Experiment:** Try modifying code to see what happens

---

**This JavaScript file makes the website interactive and user-friendly!**

