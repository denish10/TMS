# File: assets/css/style.css

## Purpose

This file contains all custom CSS (Cascading Style Sheets) styles for the Task Management System. It provides styling for all pages including login page, dashboards, forms, tables, buttons, and layout components. The CSS creates a modern, responsive, and visually appealing interface with gradient backgrounds and smooth transitions.

## Key Features

- Gradient backgrounds and color schemes
- Responsive layout with fixed sidebar and header
- Form styling with transparent backgrounds
- Button styling with hover effects
- Card components with shadows and borders
- Table styling for data display
- Dropdown menu animations
- Login page styling
- Page-specific layouts

## Code Breakdown

### 1. Base Styles

```css
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #141e30, #243b55);
    color: white;
}
```

**Explanation:**
- Sets HTML and body to full height
- Uses flexbox for layout
- Sets gradient background (dark blue)
- Sets white text color
- Ensures footer stays at bottom

**Gradient Colors:**
- `#141e30` - Dark blue (left)
- `#243b55` - Lighter blue (right)
- Creates smooth color transition

### 2. Login Page Styles

```css
#login_page {
    background: linear-gradient(to right, #43cea2, #185a9d);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 0 25px rgba(255, 255, 255, 0.1);
}

body#login_background {
    background: linear-gradient(to right, #43cea2, #185a9d);
}
```

**Explanation:**
- Different gradient for login page (green to blue)
- Rounded corners (border-radius: 20px)
- Box shadow for depth
- Centered layout with max-width

**Login Gradient:**
- `#43cea2` - Teal/Green (left)
- `#185a9d` - Blue (right)
- Creates attractive login background

### 3. Header Styles

```css
.header {
    position: fixed;
    background: linear-gradient(to right, #6a11cb, #2575fc);
    height: 60px;
    z-index: 1030;
    margin-left: 230px;
    top: 0;
    left: 0;
    right: 0;
}
```

**Explanation:**
- Fixed position (stays at top when scrolling)
- Purple to blue gradient
- Height of 60px
- Margin left accounts for sidebar (230px)
- High z-index (stays above other content)

**Header Gradient:**
- `#6a11cb` - Purple (left)
- `#2575fc` - Blue (right)
- Creates professional header appearance

### 4. Sidebar Styles

```css
#sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 230px;
    background: linear-gradient(to right, #2575fc, #6a11cb);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
}

#sidebar a:hover {
    background-color: #50907b;
    color: black;
}
```

**Explanation:**
- Fixed position (stays on left side)
- Width of 230px
- Blue to purple gradient (opposite of header)
- Box shadow for depth
- Hover effect changes background color

**Sidebar Features:**
- Always visible on left side
- Menu items have hover effects
- Smooth color transitions
- Professional appearance

### 5. Form Elements

```css
.form-control {
    background-color: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
}

.form-control:focus {
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
    box-shadow: none;
}
```

**Explanation:**
- Semi-transparent white background
- No border (clean look)
- White text color
- Darker background on focus
- Matches page theme

**Form Features:**
- Transparent backgrounds
- White text for visibility
- Focus states for user feedback
- Consistent styling across forms

### 6. Buttons

```css
.btn-primary {
    background-color: #6a11cb;
    border: none;
    font-weight: bold;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #6a11cb, #2575fc);
}
```

**Explanation:**
- Purple background color
- No border
- Bold text
- Gradient on hover
- Smooth transition

**Button Features:**
- Consistent styling
- Hover effects
- Clear visual feedback
- Professional appearance

### 7. Cards

```css
.card {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    border-radius: 17px;
    color: white;
}
```

**Explanation:**
- Very transparent white background (5% opacity)
- Subtle white border
- Box shadow for depth
- Rounded corners (17px)
- White text

**Card Features:**
- Glass-morphism effect
- Subtle borders
- Shadows for depth
- Rounded corners
- Content containers

### 8. Dropdown Menu

```css
.admin-dropdown-menu {
    display: none !important;
    position: absolute;
    background: transparent;
    border-radius: 5px;
}

.admin-dropdown-menu.show {
    display: block !important;
    animation: fadeIn 0.2s;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```

**Explanation:**
- Hidden by default
- Absolute positioning
- Shows when "show" class is added
- Fade-in animation
- Slides down smoothly

**Animation:**
- Starts invisible and moved up
- Fades in and slides down
- Takes 0.2 seconds
- Smooth transition

### 9. Page-Specific Layouts

```css
.manage_task {
    width: 80vw;
    margin-left: 263px;
    margin-top: 83px;
    padding: 20px;
    padding-bottom: 120px;
}

.manage_employee {
    width: 80vw;
    margin-top: 83px;
    margin-left: 263px;
    padding: 20px;
    padding-bottom: 120px;
}
```

**Explanation:**
- Width of 80% of viewport
- Margin left accounts for sidebar (263px = 230px sidebar + 33px spacing)
- Margin top accounts for header (83px)
- Padding for content spacing
- Extra bottom padding to prevent footer overlap

**Layout Structure:**
- Sidebar: 230px (fixed left)
- Header: 60px (fixed top)
- Content: 80vw width, starts after sidebar
- Footer: At bottom

### 10. Task Priorities

```css
.priority-high {
    background-color: #e74c3c !important;
}

.priority-medium {
    background-color: #f39c12 !important;
}

.priority-low {
    background-color: #27ae60 !important;
}
```

**Explanation:**
- High priority: Red (#e74c3c)
- Medium priority: Orange (#f39c12)
- Low priority: Green (#27ae60)
- Color-coded for quick identification

**Priority Colors:**
- Red = Urgent/High
- Orange = Medium
- Green = Low/Normal
- Visual indicators for task importance

## Output / Result

**When CSS is applied:**

1. **Overall Design:**
   - Dark gradient background (blue tones)
   - White text for contrast
   - Modern, professional appearance
   - Consistent color scheme

2. **Layout:**
   - Fixed sidebar on left (230px)
   - Fixed header at top (60px)
   - Content area in center
   - Footer at bottom

3. **Components:**
   - Transparent form inputs
   - Gradient buttons
   - Card containers with shadows
   - Animated dropdown menus

4. **Responsive:**
   - Works on different screen sizes
   - Flexible layouts
   - Readable text
   - Proper spacing

## Additional Notes

### CSS Concepts Used:

1. **Flexbox:**
   - `display: flex` - Flexible layout
   - `flex-direction: column` - Vertical layout
   - `flex: 1 0 auto` - Grow to fill space
   - Keeps footer at bottom

2. **Positioning:**
   - `position: fixed` - Stays in place when scrolling
   - `position: absolute` - Relative to parent
   - `z-index` - Layer order
   - Creates sticky header and sidebar

3. **Gradients:**
   - `linear-gradient()` - Color transitions
   - Multiple color stops
   - Direction (to right, to bottom)
   - Creates modern appearance

4. **Transparency:**
   - `rgba()` - Red, Green, Blue, Alpha
   - Alpha controls transparency (0-1)
   - `rgba(255, 255, 255, 0.1)` - 10% white
   - Creates glass-morphism effect

5. **Animations:**
   - `@keyframes` - Define animation
   - `animation` - Apply animation
   - `transition` - Smooth changes
   - Creates smooth interactions

6. **Box Model:**
   - `margin` - Outer spacing
   - `padding` - Inner spacing
   - `border` - Border around element
   - `width/height` - Element size

### Color Scheme:

**Primary Colors:**
- Dark Blue: `#141e30`, `#243b55` (Background)
- Purple: `#6a11cb` (Buttons, Header)
- Blue: `#2575fc` (Gradients)
- Teal: `#43cea2` (Login page)

**Accent Colors:**
- Red: `#e74c3c` (High priority, errors)
- Orange: `#f39c12` (Medium priority, warnings)
- Green: `#27ae60` (Low priority, success)
- White: `#ffffff` (Text, highlights)

### Layout Structure:

```
┌─────────────────────────────────────┐
│         HEADER (60px)                │
├──────┬──────────────────────────────┤
│      │                              │
│ SIDE │      CONTENT AREA            │
│ BAR  │      (80vw width)            │
│(230px│                              │
│)     │                              │
│      │                              │
├──────┴──────────────────────────────┤
│         FOOTER                       │
└─────────────────────────────────────┘
```

### Responsive Design:

- **Viewport Units:** Uses `vw` (viewport width) for responsive sizing
- **Flexible Layouts:** Content adjusts to screen size
- **Fixed Elements:** Sidebar and header stay fixed
- **Padding:** Extra padding prevents content overlap

### Browser Compatibility:

- Works in all modern browsers
- Uses standard CSS properties
- No experimental features
- Fallbacks for older browsers

## Usage Examples

### Example 1: Creating a Card

**HTML:**
```html
<div class="card">
    <div class="card-body">
        <h5>Task Title</h5>
        <p>Task Description</p>
    </div>
</div>
```

**CSS automatically applies:**
- Transparent background
- Rounded corners
- Box shadow
- White text
- Padding and spacing

### Example 2: Creating a Button

**HTML:**
```html
<button class="btn btn-primary">Submit</button>
```

**CSS automatically applies:**
- Purple background
- White text
- Hover gradient effect
- Rounded corners
- Padding

### Example 3: Form Input

**HTML:**
```html
<input type="text" class="form-control" placeholder="Enter name">
```

**CSS automatically applies:**
- Transparent background
- White text
- Rounded corners
- Focus effects
- Placeholder styling

## For College Projects

### Key Points to Explain:

1. **CSS Purpose:**
   - Styles HTML elements
   - Controls appearance
   - Creates visual design
   - Makes website attractive

2. **Selectors:**
   - `#id` - Select by ID
   - `.class` - Select by class
   - `element` - Select by tag name
   - Combinations for specific targeting

3. **Properties:**
   - `color` - Text color
   - `background` - Background color/gradient
   - `padding` - Inner spacing
   - `margin` - Outer spacing
   - `border` - Border around element

4. **Layout:**
   - `position: fixed` - Sticky elements
   - `display: flex` - Flexible layouts
   - `width/height` - Element sizing
   - `margin/padding` - Spacing

5. **Effects:**
   - Gradients for backgrounds
   - Shadows for depth
   - Transitions for smooth changes
   - Animations for movement

### Common Questions:

**Q: What is a gradient?**
A: A smooth transition between two or more colors. Creates modern, attractive backgrounds.

**Q: What is rgba()?**
A: Red, Green, Blue, Alpha. Alpha controls transparency. rgba(255,255,255,0.1) = 10% white.

**Q: What is flexbox?**
A: A layout method that arranges elements in flexible containers. Makes it easy to create responsive layouts.

**Q: What is z-index?**
A: Controls which elements appear on top. Higher z-index = appears above other elements.

**Q: What is position: fixed?**
A: Element stays in same position when scrolling. Used for header and sidebar.

## CSS Best Practices

- **Organization:** Group related styles together
- **Comments:** Use comments to separate sections
- **Consistency:** Use same colors and spacing throughout
- **Reusability:** Use classes for repeated styles
- **Specificity:** Use IDs for unique elements, classes for multiple

## Color Psychology

- **Blue:** Trust, professionalism, calm
- **Purple:** Creativity, luxury, innovation
- **Green:** Success, growth, positive
- **Red:** Urgency, importance, attention
- **Orange:** Energy, enthusiasm, warning
- **Dark:** Professional, modern, focused

## Responsive Considerations

- **Viewport Width (vw):** Responsive to screen size
- **Fixed Sidebar:** May need adjustment on small screens
- **Content Width:** 80vw allows for sidebar
- **Padding:** Prevents content from touching edges
- **Footer:** Stays at bottom with flexbox

## Related Files

- **assets/css/style.css** - This file
- **admin/common/header.php** - Uses header styles
- **admin/common/sidebar.php** - Uses sidebar styles
- **user/common/user_header.php** - Uses header styles
- **user/common/user_sidebar.php** - Uses sidebar styles
- **Bootstrap CSS** - External framework (loaded in headers)

## Notes for Students

- **CSS is Styling:** CSS controls how HTML looks
- **Cascade:** Later styles override earlier ones
- **Specificity:** More specific selectors override general ones
- **Inheritance:** Child elements inherit parent styles
- **Box Model:** Margin, Border, Padding, Content
- **Experiment:** Change values to see effects

## CSS File Structure

```
1. Base Styles (body, html)
2. Login Page Styles
3. Footer Styles
4. Form Elements
5. Buttons
6. Header
7. Dropdown Menu
8. Sidebar
9. Cards
10. Task Priorities
11. Admin Pages (task, employee, department)
12. User Pages
```

---

**This CSS file makes the website look modern and professional!**

