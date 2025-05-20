<?php 
require_once '../includes/auth.php';
protectAdminRoute();

$jobs = getAllJobsForAdmin();

// Handle job approval/rejection
if (isset($_GET['approve'])) {
    $job_id = sanitize($_GET['approve']);
    if (updateJobStatus($job_id, 'active')) {
        $_SESSION['success'] = "Job approved successfully";
    } else {
        $_SESSION['error'] = "Failed to approve job";
    }
    header("Location: manage_jobs.php");
    exit();
}

if (isset($_GET['reject'])) {
    $job_id = sanitize($_GET['reject']);
    if (updateJobStatus($job_id, 'inactive')) {
        $_SESSION['success'] = "Job rejected successfully";
    } else {
        $_SESSION['error'] = "Failed to reject job";
    }
    header("Location: manage_jobs.php");
    exit();
}

include '../includes/header.php';
?>

<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Manage Jobs</h1>
            <p>Review and manage all job postings in the system</p>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <div class="jobs-filter">
            <label>Filter by status:</label>
            <select class="form-control" onchange="window.location.href='manage_jobs.php?status='+this.value">
                <option value="">All Statuses</option>
                <option value="active" <?= isset($_GET['status']) && $_GET['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= isset($_GET['status']) && $_GET['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="pending" <?= isset($_GET['status']) && $_GET['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            </select>
        </div>
        
        <?php if (empty($jobs)): ?>
            <div class="empty-state">
                <i class="fas fa-briefcase"></i>
                <p>No jobs found</p>
            </div>
        <?php else: ?>
            <div class="jobs-table">
                <table>
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Employer</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Posted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($jobs as $job): ?>
                            <tr>
                                <td>
                                    <a href="../job_details.php?id=<?= $job['id'] ?>"><?= $job['title'] ?></a>
                                    <p class="job-meta">
                                        <span><?= $job['location'] ?></span>
                                        <span><?= ucfirst(str_replace('-', ' ', $job['type'])) ?></span>
                                    </p>
                                </td>
                                <td><?= $job['employer_name'] ?></td>
                                <td><?= $job['category_name'] ?></td>
                                <td>
                                    <span class="status-badge <?= $job['status'] ?>">
                                        <?= ucfirst($job['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($job['posted_at'])) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <?php if ($job['status'] === 'pending'): ?>
                                            <a href="manage_jobs.php?approve=<?= $job['id'] ?>" class="btn-approve" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="manage_jobs.php?reject=<?= $job['id'] ?>" class="btn-reject" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php elseif ($job['status'] === 'active'): ?>
                                            <a href="manage_jobs.php?reject=<?= $job['id'] ?>" class="btn-reject" title="Deactivate">
                                                <i class="fas fa-eye-slash"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="manage_jobs.php?approve=<?= $job['id'] ?>" class="btn-approve" title="Activate">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="../job_details.php?id=<?= $job['id'] ?>" class="btn-view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>