<?php 
require_once '../includes/auth.php';
protectAdminRoute();

// Get all reports from the database
$reports = getReports();

// Handle report actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $report_id = $_GET['id'];
    $action = $_GET['action'];
    
    switch ($action) {
        case 'resolve':
            resolveReport($report_id);
            header('Location: reports.php');
            exit();
            break;
        case 'delete':
            deleteReport($report_id);
            header('Location: reports.php');
            exit();
            break;
        case 'ban_user':
            $user_id = $_GET['user_id'];
            banUser($user_id);
            resolveReport($report_id);
            header('Location: reports.php');
            exit();
            break;
        case 'remove_job':
            $job_id = $_GET['job_id'];
            removeJob($job_id);
            resolveReport($report_id);
            header('Location: reports.php');
            exit();
            break;
    }
}

include '../includes/header.php';
?>

<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Report Management</h1>
            <p>Review and take action on reported content</p>
        </div>
        
        <div class="dashboard-section">
            <div class="section-header">
                <h2>Active Reports</h2>
                <div class="report-filters">
                    <form method="get" action="reports.php">
                        <select name="filter" onchange="this.form.submit()">
                            <option value="all" <?= (!isset($_GET['filter']) || $_GET['filter'] === 'all' ? 'selected' : '') ?>>All Reports</option>
                            <option value="unresolved" <?= (isset($_GET['filter']) && $_GET['filter'] === 'unresolved' ? 'selected' : '') ?>>Unresolved Only</option>
                            <option value="jobs" <?= (isset($_GET['filter']) && $_GET['filter'] === 'jobs' ? 'selected' : '') ?>>Job Reports</option>
                            <option value="users" <?= (isset($_GET['filter']) && $_GET['filter'] === 'users' ? 'selected' : '') ?>>User Reports</option>
                        </select>
                    </form>
                </div>
            </div>
            
            <?php if (empty($reports)): ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No reports found</p>
                </div>
            <?php else: ?>
                <div class="reports-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Report Type</th>
                                <th>Reported Content</th>
                                <th>Reason</th>
                                <th>Reported By</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($reports as $report): ?>
                                <tr>
                                    <td><?= ucfirst($report['type']) ?></td>
                                    <td>
                                        <?php if ($report['type'] === 'job'): ?>
                                            <a href="../job_details.php?id=<?= $report['job_id'] ?>" target="_blank">
                                                <?= $report['job_title'] ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="#" onclick="viewUserProfile(<?= $report['user_id'] ?>)">
                                                <?= $report['user_name'] ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $report['reason'] ?></td>
                                    <td>
                                        <a href="#" onclick="viewUserProfile(<?= $report['reporter_id'] ?>)">
                                            <?= $report['reporter_name'] ?>
                                        </a>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($report['created_at'])) ?></td>
                                    <td>
                                        <span class="status-badge <?= $report['status'] ?>">
                                            <?= ucfirst($report['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <?php if ($report['status'] === 'pending'): ?>
                                                <div class="dropdown">
                                                    <button class="btn-action dropdown-toggle" type="button" data-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="reports.php?action=resolve&id=<?= $report['id'] ?>">
                                                            <i class="fas fa-check"></i> Mark as Resolved
                                                        </a>
                                                        <?php if ($report['type'] === 'job'): ?>
                                                            <a class="dropdown-item" href="reports.php?action=remove_job&id=<?= $report['id'] ?>&job_id=<?= $report['job_id'] ?>" onclick="return confirm('Are you sure you want to remove this job?')">
                                                                <i class="fas fa-trash"></i> Remove Job
                                                            </a>
                                                        <?php else: ?>
                                                            <a class="dropdown-item" href="reports.php?action=ban_user&id=<?= $report['id'] ?>&user_id=<?= $report['user_id'] ?>" onclick="return confirm('Are you sure you want to ban this user?')">
                                                                <i class="fas fa-ban"></i> Ban User
                                                            </a>
                                                        <?php endif; ?>
                                                        <a class="dropdown-item" href="reports.php?action=delete&id=<?= $report['id'] ?>" onclick="return confirm('Are you sure you want to delete this report?')">
                                                            <i class="fas fa-trash"></i> Delete Report
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <a href="reports.php?action=delete&id=<?= $report['id'] ?>" class="btn-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this report?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="reports.php?page=<?= $page - 1 ?>" class="page-link">&laquo; Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="reports.php?page=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="reports.php?page=<?= $page + 1 ?>" class="page-link">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- User Profile Modal -->
<div class="modal fade" id="userProfileModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="userProfileContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewUserProfile(userId) {
    $.ajax({
        url: '../includes/get_user_profile.php',
        type: 'GET',
        data: { user_id: userId },
        success: function(response) {
            $('#userProfileContent').html(response);
            $('#userProfileModal').modal('show');
        },
        error: function() {
            alert('Error loading user profile');
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>