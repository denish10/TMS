<?php
session_start();

require_once __DIR__ . '/../dbsetting/config.php';
require_once __DIR__ . '/common/activity_logger.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

include HEADER_PATH;
include SIDEBAR_PATH;

// Get filter parameters
$filter_type = isset($_GET['type']) ? mysqli_real_escape_string($conn, $_GET['type']) : '';
$filter_user = isset($_GET['user']) ? intval($_GET['user']) : 0;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($conn, $_GET['date_from']) : '';

// Check if table exists
$table_exists = false;
$check_query = "SHOW TABLES LIKE 'activity_logs'";
$check_result = mysqli_query($conn, $check_query);
if ($check_result && mysqli_num_rows($check_result) > 0) {
    $table_exists = true;
}

// Build query (only if table exists)
$query = "SELECT * FROM activity_logs WHERE 1=1";

if (!empty($filter_type)) {
    $query .= " AND activity_type = '$filter_type'";
}

if ($filter_user > 0) {
    $query .= " AND user_id = $filter_user";
}

if (!empty($date_from)) {
    $query .= " AND DATE(created_at) >= '$date_from'";
}

if (!empty($search)) {
    // Enhanced search across multiple fields
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $query .= " AND (
        log_id LIKE '%$search_escaped%' OR
        activity_type LIKE '%$search_escaped%' OR
        description LIKE '%$search_escaped%' OR
        user_name LIKE '%$search_escaped%' OR
        user_id LIKE '%$search_escaped%' OR
        related_table LIKE '%$search_escaped%' OR
        related_id LIKE '%$search_escaped%' OR
        DATE_FORMAT(created_at, '%Y-%m-%d') LIKE '%$search_escaped%' OR
        DATE_FORMAT(created_at, '%M %d, %Y') LIKE '%$search_escaped%' OR
        DATE_FORMAT(created_at, '%h:%i %p') LIKE '%$search_escaped%'
    )";
}

$query .= " ORDER BY created_at DESC LIMIT 100";

$result = $table_exists ? mysqli_query($conn, $query) : false;

// Get all activity types for filter
$types_query = "SELECT DISTINCT activity_type FROM activity_logs ORDER BY activity_type";
$types_result = $table_exists ? mysqli_query($conn, $types_query) : false;

// Get all users for filter
$users_query = "SELECT DISTINCT user_id, user_name FROM activity_logs WHERE user_id IS NOT NULL ORDER BY user_name";
$users_result = $table_exists ? mysqli_query($conn, $users_query) : false;
?>

<div class="manage_task" style="width: 80vw; margin-left: 263px; margin-top: 83px; padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="fas fa-history me-2"></i> Activity Logs</h3>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" id="filterForm" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-search me-1"></i> Search</label>
                    <input type="text" name="search" id="searchInput" class="form-control" 
                           placeholder="Search by ID, type, description, user, date..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           autocomplete="off">
                    <small class="text-muted">Search across all fields</small>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-filter me-1"></i> Activity Type</label>
                    <select name="type" id="typeFilter" class="form-select">
                        <option value="">All Types</option>
                        <?php
                        if ($types_result && mysqli_num_rows($types_result) > 0) {
                            while ($type_row = mysqli_fetch_assoc($types_result)) {
                                $selected = ($filter_type == $type_row['activity_type']) ? 'selected' : '';
                                echo "<option value='{$type_row['activity_type']}' $selected>" . ucfirst(str_replace('_', ' ', $type_row['activity_type'])) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-user me-1"></i> User</label>
                    <select name="user" id="userFilter" class="form-select">
                        <option value="0">All Users</option>
                        <?php
                        if ($users_result && mysqli_num_rows($users_result) > 0) {
                            while ($user_row = mysqli_fetch_assoc($users_result)) {
                                $selected = ($filter_user == $user_row['user_id']) ? 'selected' : '';
                                echo "<option value='{$user_row['user_id']}' $selected>" . htmlspecialchars($user_row['user_name']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-calendar me-1"></i> Date Range</label>
                    <input type="date" name="date_from" id="dateFrom" class="form-control" 
                           value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : ''; ?>"
                           placeholder="From">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                        <a href="activity_logs.php" class="btn btn-secondary" title="Reset Filters">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!$table_exists): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Activity logs table not found!</strong> 
                    Please run the setup script to create the table: 
                    <a href="setup_activity_logs.php" class="alert-link">Setup Activity Logs</a>
                </div>
            <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="badge bg-info" id="resultCount">
                        <?php 
                        $count_query = str_replace("ORDER BY created_at DESC LIMIT 100", "", $query);
                        $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM ($count_query) as filtered");
                        $total_count = $count_result ? mysqli_fetch_assoc($count_result)['total'] : 0;
                        echo "Showing " . ($result && mysqli_num_rows($result) > 0 ? mysqli_num_rows($result) : 0) . " of $total_count results";
                        ?>
                    </span>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportTable()">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="activityTable">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Type</th>
                            <th width="35%">Description</th>
                            <th width="15%">User</th>
                            <th width="15%">Related</th>
                            <th width="20%">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            $sno = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $icon_data = getActivityIcon($row['activity_type']);
                                $related_info = '';
                                if ($row['related_table'] && $row['related_id']) {
                                    $related_info = $row['related_table'] . ' #' . $row['related_id'];
                                }
                        ?>
                            <tr>
                                <td><?php echo $row['log_id']; ?></td>
                                <td>
                                    <span class="badge <?php echo $icon_data['color']; ?>">
                                        <i class="fas <?php echo $icon_data['icon']; ?> me-1"></i>
                                        <?php echo ucfirst(str_replace('_', ' ', $row['activity_type'])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <?php if ($row['user_name']): ?>
                                        <strong><?php echo htmlspecialchars($row['user_name']); ?></strong>
                                        <?php if ($row['user_id']): ?>
                                            <br><small class="text-muted">ID: <?php echo $row['user_id']; ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">System</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($related_info): ?>
                                        <small><?php echo htmlspecialchars($related_info); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo date('M j, Y', strtotime($row['created_at'])); ?></strong>
                                    <br><small class="text-muted"><?php echo date('h:i A', strtotime($row['created_at'])); ?></small>
                                    <br><small class="text-info"><?php echo timeAgo($row['created_at']); ?></small>
                                </td>
                            </tr>
                        <?php
                                $sno++;
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>No activity logs found.</p>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card mt-3">
        <div class="card-body">
            <h6 class="text-white"><i class="fas fa-info-circle me-2"></i> About Activity Logs</h6>
            <p class="mb-0 small" style="color: #ffffff;">
                This page displays all system activities and user actions. You can filter by activity type, user, date range, or search across all fields (ID, type, description, user, date, time). 
                Only the most recent 100 activities are shown. For older logs, query the database directly.
            </p>
        </div>
    </div>
</div>

<style>
.highlight {
    background-color: yellow;
    font-weight: bold;
}

#searchInput:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.table tbody tr {
    transition: background-color 0.2s;
}

.table tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.05);
}
</style>

<script>
// Real-time search with debounce
let searchTimeout;
const searchInput = document.getElementById('searchInput');
const typeFilter = document.getElementById('typeFilter');
const userFilter = document.getElementById('userFilter');
const dateFrom = document.getElementById('dateFrom');

// Debounced search function
function performSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        // Auto-submit form after 500ms of no typing
        if (searchInput.value.length >= 2 || searchInput.value.length === 0) {
            document.getElementById('filterForm').submit();
        }
    }, 500);
}

// Event listeners for real-time filtering
if (searchInput) {
    searchInput.addEventListener('input', performSearch);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            document.getElementById('filterForm').submit();
        }
    });
}

// Auto-submit on filter change
if (typeFilter) {
    typeFilter.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
}

if (userFilter) {
    userFilter.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
}

if (dateFrom) {
    dateFrom.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
}

// Highlight search terms in table
function highlightSearchTerms() {
    const searchTerm = '<?php echo addslashes($search); ?>';
    if (searchTerm.length < 2) return;
    
    const tableBody = document.getElementById('tableBody');
    if (!tableBody) return;
    
    const rows = tableBody.getElementsByTagName('tr');
    const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    
    Array.from(rows).forEach(row => {
        const cells = row.getElementsByTagName('td');
        Array.from(cells).forEach(cell => {
            const originalText = cell.textContent;
            if (regex.test(originalText)) {
                cell.innerHTML = originalText.replace(regex, '<span class="highlight">$1</span>');
            }
        });
    });
}

// Export table to CSV
function exportTable() {
    const table = document.getElementById('activityTable');
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        
        csv.push(row.join(','));
    }
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'activity_logs_' + new Date().toISOString().split('T')[0] + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    highlightSearchTerms();
    
    // Add loading state
    const form = document.getElementById('filterForm');
    if (form) {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading...';
                submitBtn.disabled = true;
            }
        });
    }
});
</script>

<?php include FOOTER_PATH; ?>

