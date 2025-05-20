<?php 
require_once '../includes/auth.php';
protectJobSeekerRoute();

$user_id = $_SESSION['user_id'];
$applications = getJobSeekerApplications($user_id);

include '../includes/header.php';
?>

<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Your Applications</h1>
            <p>Track the status of your job applications</p>
        </div>
        
        <?php if (empty($applications)): ?>
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <p>You haven't applied to any jobs yet</p>
                <a href="../jobs.php" class="btn btn-primary">Browse Jobs</a>
            </div>
        <?php else: ?>
            <div class="applications-list">
                <?php foreach($applications as $application): ?>
                    <div class="application-card">
                        <div class="application-info">
                            <h3><?= $application['job_title'] ?></h3>
                            <p class="company"><?= $application['employer_name'] ?></p>
                            <p class="status <?= $application['status'] ?>">
                                <?= ucfirst($application['status']) ?>
                            </p>
                        </div>
                        <div class="application-meta">
                            <p class="date">Applied on <?= date('M d, Y', strtotime($application['applied_at'])) ?></p>
                            <div class="application-actions">
                                <a href="../job_details.php?id=<?= $application['job_id'] ?>" class="btn btn-outline">View Job</a>
                                <?php if ($application['status'] === 'shortlisted'): ?>
                                    <a href="messages.php?employer=<?= $application['employer_id'] ?>&job=<?= $application['job_id'] ?>" class="btn btn-primary">Reply</a>
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