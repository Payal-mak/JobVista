<?php 
require_once '../includes/auth.php';
protectEmployerRoute();

$employer_id = $_SESSION['user_id'];
$applications = getEmployerApplications($employer_id);

// Handle application status change
if (isset($_GET['status']) && isset($_GET['id'])) {
    $application_id = sanitize($_GET['id']);
    $status = sanitize($_GET['status']);
    
    // Verify that the application is for this employer's job
    $stmt = $conn->prepare("SELECT a.id 
                           FROM applications a 
                           JOIN jobs j ON a.job_id = j.id 
                           WHERE a.id = ? AND j.employer_id = ?");
    $stmt->execute([$application_id, $employer_id]);
    
    if ($stmt->rowCount() > 0) {
        $result = updateApplicationStatus($application_id, $status);
        
        if ($result === true) {
            $_SESSION['success'] = "Application status updated successfully";
            header("Location: applications.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to update application status";
        }
    } else {
        $_SESSION['error'] = "Application not found or you don't have permission to update it";
    }
}

// Filter by job if specified
if (isset($_GET['job'])) {
    $job_id = sanitize($_GET['job']);
    $applications = array_filter($applications, function($app) use ($job_id) {
        return $app['job_id'] == $job_id;
    });
}

// Filter by status if specified
if (isset($_GET['status'])) {
    $status_filter = sanitize($_GET['status']);
    $applications = array_filter($applications, function($app) use ($status_filter) {
        return $app['status'] == $status_filter;
    });
}

include '../includes/header.php';
?>

<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Job Applications</h1>
            <p>Review and manage applications for your jobs</p>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <div class="applications-actions">
            <div class="applications-filter">
                <label>Filter by status:</label>
                <select class="form-control" onchange="window.location.href='applications.php?status='+this.value">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= isset($_GET['status']) && $_GET['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="shortlisted" <?= isset($_GET['status']) && $_GET['status'] === 'shortlisted' ? 'selected' : '' ?>>Shortlisted</option>
                    <option value="rejected" <?= isset($_GET['status']) && $_GET['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    <option value="accepted" <?= isset($_GET['status']) && $_GET['status'] === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                </select>
            </div>
        </div>
        
        <?php if (empty($applications)): ?>
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <p>No applications found</p>
                <a href="post_job.php" class="btn btn-primary">Post a Job</a>
            </div>
        <?php else: ?>
            <div class="applications-list">
                <?php foreach($applications as $application): ?>
                    <div class="application-card">
                        <div class="application-info">
                            <h3><?= $application['applicant_name'] ?></h3>
                            <p class="job">Applied for <a href="../job_details.php?id=<?= $application['job_id'] ?>"><?= $application['job_title'] ?></a></p>
                            <p class="status <?= $application['status'] ?>">
                                <?= ucfirst($application['status']) ?>
                            </p>
                        </div>
                        <div class="application-meta">
                            <p class="date">Applied on <?= date('M d, Y', strtotime($application['applied_at'])) ?></p>
                            <div class="application-actions">
                                <a href="view_application.php?id=<?= $application['id'] ?>" class="btn btn-outline">View Details</a>
                                
                                <?php if ($application['status'] !== 'accepted'): ?>
                                    <a href="applications.php?status=shortlisted&id=<?= $application['id'] ?>" class="btn btn-primary">Shortlist</a>
                                <?php endif; ?>
                                
                                <?php if ($application['status'] !== 'rejected'): ?>
                                    <a href="applications.php?status=rejected&id=<?= $application['id'] ?>" class="btn btn-danger">Reject</a>
                                <?php endif; ?>
                                
                                <?php if ($application['status'] === 'shortlisted'): ?>
                                    <a href="messages.php?applicant=<?= $application['user_id'] ?>&job=<?= $application['job_id'] ?>" class="btn btn-success">Message</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>