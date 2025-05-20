<?php 
require_once 'includes/config.php'; // Include config to get $pdo
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Job ID is required.";
    $_SESSION['message_type'] = "danger";
    header("Location: jobs.php");
    exit();
}

$job_id = sanitize($_GET['id']);
$job = getJobById($job_id);

if (!$job) {
    $_SESSION['message'] = "Job not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: jobs.php");
    exit();
}

$is_saved = false;
$has_applied = false;

if (isLoggedIn()) {
    // Check if job is saved
    $stmt = $pdo->prepare("SELECT id FROM saved_jobs WHERE job_id = ? AND user_id = ?");
    $stmt->execute([$job_id, $_SESSION['user_id']]);
    $is_saved = $stmt->rowCount() > 0;
    
    // Check if already applied
    if (isJobSeeker()) {
        $stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND user_id = ?");
        $stmt->execute([$job_id, $_SESSION['user_id']]);
        $has_applied = $stmt->rowCount() > 0;
    }
}

// Handle job application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    if (!isJobSeeker()) {
        $_SESSION['message'] = "Only job seekers can apply for jobs.";
        $_SESSION['message_type'] = "danger";
        header("Location: login.php");
        exit();
    }
    
    // Handle file upload
    $resume_path = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $target_dir = UPLOAD_RESUME_PATH;
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        if (!is_writable($target_dir)) {
            $_SESSION['message'] = "Upload directory is not writable.";
            $_SESSION['message_type'] = "danger";
            header("Location: job_details.php?id=" . $job_id);
            exit();
        }

        $file_ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
        $file_name = 'resume_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_ext;
        $target_file = $target_dir . $file_name;
        
        // Check file size and type
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($_FILES['resume']['type'], $allowed_types)) {
            $_SESSION['message'] = "Only PDF, DOC, and DOCX files are allowed.";
            $_SESSION['message_type'] = "danger";
        } elseif ($_FILES['resume']['size'] > MAX_FILE_SIZE) {
            $_SESSION['message'] = "File is too large. Maximum size is 5MB.";
            $_SESSION['message_type'] = "danger";
        } elseif (move_uploaded_file($_FILES['resume']['tmp_name'], $target_file)) {
            $resume_path = $target_file;
        } else {
            $_SESSION['message'] = "There was an error uploading your file.";
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Resume is required.";
        $_SESSION['message_type'] = "danger";
    }
    
    if (!empty($resume_path)) {
        $cover_letter = sanitize($_POST['cover_letter'] ?? '');
        $result = applyForJob($job_id, $_SESSION['user_id'], $resume_path, $cover_letter);
        
        if ($result === true) {
            $_SESSION['message'] = "Application submitted successfully!";
            $_SESSION['message_type'] = "success";
            header("Location: job_seeker/applications.php");
            exit();
        } else {
            $_SESSION['message'] = $result;
            $_SESSION['message_type'] = "danger";
        }
    }
    header("Location: job_details.php?id=" . $job_id);
    exit();
}

// Handle save/unsave job
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_save'])) {
    if (!isLoggedIn()) {
        $_SESSION['message'] = "Please log in to save jobs.";
        $_SESSION['message_type'] = "danger";
        header("Location: login.php");
        exit();
    }
    
    if ($is_saved) {
        // Unsave job
        $stmt = $pdo->prepare("DELETE FROM saved_jobs WHERE job_id = ? AND user_id = ?");
        $stmt->execute([$job_id, $_SESSION['user_id']]);
        $is_saved = false;
        $_SESSION['message'] = "Job removed from saved jobs.";
        $_SESSION['message_type'] = "success";
    } else {
        // Save job
        $result = saveJob($job_id, $_SESSION['user_id']);
        if ($result === true) {
            $is_saved = true;
            $_SESSION['message'] = "Job saved successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = $result;
            $_SESSION['message_type'] = "danger";
        }
    }
    header("Location: job_details.php?id=" . $job_id);
    exit();
}

include 'includes/header.php';
?>

<div class="container job-details">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
        <?php unset($_SESSION['message_type']); ?>
    <?php endif; ?>
    
    <div class="job-header">
        <h1><?= htmlspecialchars($job['title']) ?></h1>
        <p class="company"><?= htmlspecialchars($job['employer_name']) ?></p>
        <p class="location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?></p>
        <p class="type-salary">
            <span class="badge"><?= ucfirst(str_replace('-', ' ', htmlspecialchars($job['type']))) ?></span>
            <span class="salary">$<?= number_format($job['salary']) ?></span>
        </p>
        
        <div class="job-actions">
            <?php if (isLoggedIn()): ?>
                <form method="post" style="display: inline;">
                    <button type="submit" name="toggle_save" class="btn btn-outline-primary">
                        <?= $is_saved ? '<i class="fas fa-bookmark"></i> Saved' : '<i class="far fa-bookmark"></i> Save Job' ?>
                    </button>
                </form>
                
                <?php if (isJobSeeker() && !$has_applied): ?>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyModal">
                        <i class="fas fa-paper-plane"></i> Apply Now
                    </button>
                <?php elseif (isJobSeeker() && $has_applied): ?>
                    <button class="btn btn-success" disabled>
                        <i class="fas fa-check"></i> Applied
                    </button>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_id'] == $job['employer_id']): ?>
                    <a href="employer/manage_jobs.php?edit=<?= htmlspecialchars($job['id']) ?>" class="btn btn-outline-primary">
                        <i class="fas fa-edit"></i> Edit Job
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <form method="post" style="display: inline;">
                    <button type="submit" name="toggle_save" class="btn btn-outline-primary" disabled title="Login to save jobs">
                        <i class="far fa-bookmark"></i> Save Job
                    </button>
                </form>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <i class="fas fa-paper-plane"></i> Apply Now
                </button>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="job-content">
        <div class="job-description">
            <h3>Job Description</h3>
            <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
        </div>
        
        <div class="job-meta">
            <div class="meta-card">
                <h4>Job Type</h4>
                <p><?= ucfirst(str_replace('-', ' ', htmlspecialchars($job['type']))) ?></p>
            </div>
            
            <div class="meta-card">
                <h4>Location</h4>
                <p><?= htmlspecialchars($job['location']) ?></p>
            </div>
            
            <div class="meta-card">
                <h4>Salary</h4>
                <p>$<?= number_format($job['salary']) ?></p>
            </div>
            
            <div class="meta-card">
                <h4>Category</h4>
                <p><?= htmlspecialchars($job['category_name']) ?></p>
            </div>
            
            <div class="meta-card">
                <h4>Posted</h4>
                <p><?= date('M d, Y', strtotime($job['posted_at'])) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Apply Modal -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applyModalLabel">Apply for <?= htmlspecialchars($job['title']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="resume" class="form-label">Upload Resume (PDF, DOC, DOCX)</label>
                        <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required class="form-control">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="cover_letter" class="form-label">Cover Letter</label>
                        <textarea id="cover_letter" name="cover_letter" rows="5" class="form-control" placeholder="Write a cover letter explaining why you're a good fit for this position..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="apply" class="btn btn-primary">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You need to log in as a job seeker to apply for this job.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="register.php" class="btn btn-outline-primary">Register</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>