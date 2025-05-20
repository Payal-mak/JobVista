<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

protectJobSeekerRoute();

// Handle bookmark toggle
$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_save'])) {
    $job_id = $_POST['job_id'] ?? null;
    if ($job_id) {
        if (isJobSaved($user_id, $job_id)) {
            unsaveJob($user_id, $job_id);
        } else {
            saveJob($user_id, $job_id);
        }
        // Refresh the page to update the saved jobs list
        header("Location: saved_jobs.php");
        exit();
    }
}

$saved_jobs = getSavedJobs($user_id);

include '../includes/header.php';
?>

<!-- Saved Jobs Section -->
<section class="min-vh-100 bg-light py-5">
    <div class="container">
        <div class="jobs-header mb-5">
            <h1 class="display-6 fw-bold text-primary">Saved Jobs</h1>
            <p class="text-muted">View all jobs you have saved</p>
        </div>

        <div class="jobs-listings">
            <?php if (empty($saved_jobs)): ?>
                <div class="no-results text-center">
                    <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                    <h3>No saved jobs found</h3>
                    <p class="text-muted">Explore and save jobs that interest you!</p>
                    <a href="../jobs.php" class="btn btn-primary">Browse Jobs</a>
                </div>
            <?php else: ?>
                <div class="job-listings-header mb-4">
                    <p><?= count($saved_jobs) ?> saved jobs</p>
                </div>
                
                <div class="jobs-grid row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach($saved_jobs as $job): ?>
                        <?php $is_saved = isJobSaved($user_id, $job['id']); ?>
                        <div class="col">
                            <div class="job-card card shadow-sm border-0 h-100">
                                <div class="job-card-header card-body">
                                    <h3 class="card-title h5">
                                        <a href="../job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="text-primary">
                                            <?= htmlspecialchars($job['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="company text-muted mb-1"><?= htmlspecialchars($job['employer_name']) ?></p>
                                    <p class="location text-muted mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($job['location']) ?>
                                    </p>
                                </div>
                                
                                <div class="job-card-body card-body pt-0">
                                    <div class="job-meta d-flex gap-2 mb-3">
                                        <span class="type badge bg-info"><?= ucfirst(str_replace('-', ' ', htmlspecialchars($job['type']))) ?></span>
                                        <span class="salary badge bg-success">$<?= number_format($job['salary']) ?></span>
                                    </div>
                                    
                                    <p class="job-excerpt text-muted"><?= htmlspecialchars(substr($job['description'], 0, 100)) ?>...</p>
                                    
                                    <div class="job-skills d-flex flex-wrap gap-2">
                                        <?php 
                                        $skills = explode(',', $job['skills']);
                                        foreach(array_slice($skills, 0, 3) as $skill): 
                                        ?>
                                            <span class="skill-tag badge bg-light text-dark border"><?= htmlspecialchars(trim($skill)) ?></span>
                                        <?php endforeach; ?>
                                        <?php if (count($skills) > 3): ?>
                                            <span class="skill-tag badge bg-light text-dark border">+<?= count($skills) - 3 ?> more</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="job-card-footer card-footer bg-white border-0 d-flex gap-2">
                                    <a href="../job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="btn btn-primary btn-sm flex-grow-1">View Details</a>
                                    <form method="post" action="saved_jobs.php" class="save-form">
                                        <input type="hidden" name="toggle_save" value="1">
                                        <input type="hidden" name="job_id" value="<?= htmlspecialchars($job['id']) ?>">
                                        <button type="submit" class="btn btn-outline-primary btn-sm bookmark-job-btn" data-job-id="<?= htmlspecialchars($job['id']) ?>">
                                            <i class="<?= $is_saved ? 'fas' : 'far' ?> fa-bookmark"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>