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

<div class="job-details-page bg-gradient-to-b from-gray-50 to-white py-10">
    <div class="container mx-auto px-4">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show mb-6 animate-fadeIn" role="alert">
                <div class="flex items-center">
                    <?php if ($_SESSION['message_type'] === 'success'): ?>
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <?php elseif ($_SESSION['message_type'] === 'danger'): ?>
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    <?php elseif ($_SESSION['message_type'] === 'warning'): ?>
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    <?php else: ?>
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    <?php endif; ?>
                    <?= htmlspecialchars($_SESSION['message']) ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
            <?php unset($_SESSION['message_type']); ?>
        <?php endif; ?>
        
        <div class="job-details-container max-w-6xl mx-auto">
            <!-- Back to Jobs Link -->
            <div class="mb-6 animate-fadeIn" data-animation="fadeIn">
                <a href="jobs.php" class="inline-flex items-center text-blue-600 hover:text-blue-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Jobs
                </a>
            </div>
            
            <!-- Job Header Card -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 animate-slideInUp" data-animation="slideInUp">
                <div class="flex flex-col md:flex-row md:items-start gap-6">
                    <!-- Company Logo -->
                    <div class="company-logo flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                        <?php if (isset($job['company_logo']) && !empty($job['company_logo'])): ?>
                            <img src="<?= htmlspecialchars($job['company_logo']) ?>" alt="<?= htmlspecialchars($job['employer_name']) ?>" class="w-full h-full object-contain">
                        <?php else: ?>
                            <div class="text-3xl text-gray-400">
                                <i class="fas fa-building"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Job Title and Company Info -->
                    <div class="flex-grow">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($job['title']) ?></h1>
                        <p class="text-xl text-gray-600 mb-3"><?= htmlspecialchars($job['employer_name']) ?></p>
                        
                        <div class="flex flex-wrap items-center gap-4 text-gray-600 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                                <?= htmlspecialchars($job['location']) ?>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-blue-600 mr-2"></i>
                                <?= ucfirst(str_replace('-', ' ', htmlspecialchars($job['type']))) ?>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-money-bill-wave text-blue-600 mr-2"></i>
                                $<?= number_format($job['salary']) ?>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                                Posted <?= date('M d, Y', strtotime($job['posted_at'])) ?>
                            </div>
                        </div>
                        
                        <!-- Job Actions -->
                        <div class="flex flex-wrap gap-3">
                            <?php if (isLoggedIn()): ?>
                                <form method="post" class="inline-block">
                                    <button type="submit" name="toggle_save" class="btn-save flex items-center gap-2 px-4 py-2 rounded-lg border <?= $is_saved ? 'bg-blue-50 border-blue-200 text-blue-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' ?> transition-colors">
                                        <i class="<?= $is_saved ? 'fas' : 'far' ?> fa-bookmark"></i>
                                        <?= $is_saved ? 'Saved' : 'Save Job' ?>
                                    </button>
                                </form>
                                
                                <?php if (isJobSeeker() && !$has_applied): ?>
                                    <button class="btn-apply flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-transform" data-bs-toggle="modal" data-bs-target="#applyModal">
                                        <i class="fas fa-paper-plane"></i> Apply Now
                                    </button>
                                <?php elseif (isJobSeeker() && $has_applied): ?>
                                    <button class="flex items-center gap-2 px-6 py-2 bg-green-600 text-white rounded-lg cursor-default shadow-md" disabled>
                                        <i class="fas fa-check"></i> Applied
                                    </button>
                                <?php endif; ?>
                                
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $job['employer_id']): ?>
                                    <a href="employer/manage_jobs.php?edit=<?= htmlspecialchars($job['id']) ?>" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-edit"></i> Edit Job
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn-save flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-400 cursor-not-allowed" disabled title="Login to save jobs">
                                    <i class="far fa-bookmark"></i> Save Job
                                </button>
                                <button class="btn-apply flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-transform" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    <i class="fas fa-paper-plane"></i> Apply Now
                                </button>
                            <?php endif; ?>
                            
                            <!-- Share Button -->
                            <div class="relative inline-block" id="shareDropdown">
                                <button type="button" class="share-btn flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-share-alt"></i> Share
                                </button>
                                <div class="share-dropdown absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg p-2 hidden z-10">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md transition-colors">
                                        <i class="fab fa-facebook text-blue-600"></i> Facebook
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>&text=<?= urlencode('Check out this job: ' . $job['title']) ?>" target="_blank" class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md transition-colors">
                                        <i class="fab fa-twitter text-blue-400"></i> Twitter
                                    </a>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md transition-colors">
                                        <i class="fab fa-linkedin text-blue-700"></i> LinkedIn
                                    </a>
                                    <button type="button" class="copy-link flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md transition-colors w-full text-left">
                                        <i class="fas fa-link text-gray-600"></i> Copy Link
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Job Description -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-8 mb-8 animate-slideInUp" data-animation="slideInUp" data-delay="0.2">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-3 border-b border-gray-200">Job Description</h2>
                        <div class="job-description prose prose-blue max-w-none">
                            <?= nl2br(htmlspecialchars($job['description'])) ?>
                        </div>
                    </div>
                    
                    <!-- Job Requirements -->
                    <div class="bg-white rounded-xl shadow-lg p-8 mb-8 animate-slideInUp" data-animation="slideInUp" data-delay="0.3">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-3 border-b border-gray-200">Requirements</h2>
                        <ul class="space-y-3 text-gray-700">
                            <?php 
                            // If requirements are stored in a separate field, use that
                            // Otherwise, create some sample requirements based on the job description
                            $requirements = isset($job['requirements']) ? explode("\n", $job['requirements']) : [
                                "Bachelor's degree in related field",
                                "2+ years of experience in similar role",
                                "Strong communication skills",
                                "Ability to work in a team environment",
                                "Problem-solving skills"
                            ];
                            
                            foreach($requirements as $requirement): 
                            ?>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                    <span><?= htmlspecialchars(trim($requirement)) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- Company Overview -->
                    <div class="bg-white rounded-xl shadow-lg p-8 animate-slideInUp" data-animation="slideInUp" data-delay="0.4">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-3 border-b border-gray-200">About the Company</h2>
                        <div class="company-info">
                            <p class="text-gray-700 mb-4">
                                <?php if (isset($job['company_description']) && !empty($job['company_description'])): ?>
                                    <?= nl2br(htmlspecialchars($job['company_description'])) ?>
                                <?php else: ?>
                                    <?= htmlspecialchars($job['employer_name']) ?> is a leading company in the <?= htmlspecialchars($job['category_name']) ?> industry. We are committed to providing excellent service and innovative solutions to our clients.
                                <?php endif; ?>
                            </p>
                            
                            <?php if (isset($job['company_website']) && !empty($job['company_website'])): ?>
                                <a href="<?= htmlspecialchars($job['company_website']) ?>" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-700 transition-colors">
                                    <i class="fas fa-external-link-alt mr-2"></i> Visit Company Website
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Job Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Job Overview -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-8 animate-slideInUp" data-animation="slideInUp" data-delay="0.2">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Job Overview</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar-alt text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Date Posted</p>
                                    <p class="font-medium"><?= date('M d, Y', strtotime($job['posted_at'])) ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Location</p>
                                    <p class="font-medium"><?= htmlspecialchars($job['location']) ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-briefcase text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Job Type</p>
                                    <p class="font-medium"><?= ucfirst(str_replace('-', ' ', htmlspecialchars($job['type']))) ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-money-bill-wave text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Salary</p>
                                    <p class="font-medium">$<?= number_format($job['salary']) ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-tag text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">Category</p>
                                    <p class="font-medium"><?= htmlspecialchars($job['category_name']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Skills Required -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-8 animate-slideInUp" data-animation="slideInUp" data-delay="0.3">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Skills Required</h3>
                        
                        <div class="flex flex-wrap gap-2">
                            <?php 
                            $skills = isset($job['skills']) ? explode(',', $job['skills']) : ['Communication', 'Teamwork', 'Problem Solving', 'Time Management'];
                            foreach($skills as $skill): 
                            ?>
                                <span class="skill-tag px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm hover:bg-blue-200 transition-colors">
                                    <?= htmlspecialchars(trim($skill)) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Similar Jobs -->
                    <div class="bg-white rounded-xl shadow-lg p-6 animate-slideInUp" data-animation="slideInUp" data-delay="0.4">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Similar Jobs</h3>
                        
                        <div class="space-y-4">
                            <?php 
                            // In a real implementation, you would fetch similar jobs from the database
                            // For now, we'll create some sample similar jobs
                            $similarJobs = [
                                ['id' => 1, 'title' => 'Senior ' . $job['title'], 'company' => 'ABC Company', 'location' => 'New York, NY'],
                                ['id' => 2, 'title' => 'Junior ' . $job['title'], 'company' => 'XYZ Corporation', 'location' => 'San Francisco, CA'],
                                ['id' => 3, 'title' => 'Remote ' . $job['title'], 'company' => '123 Industries', 'location' => 'Remote']
                            ];
                            
                            foreach($similarJobs as $similarJob): 
                            ?>
                                <a href="job_details.php?id=<?= $similarJob['id'] ?>" class="block p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all">
                                    <h4 class="font-semibold text-gray-800 mb-1"><?= htmlspecialchars($similarJob['title']) ?></h4>
                                    <p class="text-gray-600 text-sm"><?= htmlspecialchars($similarJob['company']) ?></p>
                                    <p class="text-gray-500 text-sm flex items-center mt-1">
                                        <i class="fas fa-map-marker-alt mr-1"></i> <?= htmlspecialchars($similarJob['location']) ?>
                                    </p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Apply Modal -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-xl overflow-hidden">
            <div class="modal-header bg-blue-600 text-white">
                <h5 class="modal-title" id="applyModalLabel">Apply for <?= htmlspecialchars($job['title']) ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data" id="applicationForm">
                <div class="modal-body p-6">
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i class="fas fa-building text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">You're applying to</p>
                                <p class="font-medium text-lg"><?= htmlspecialchars($job['title']) ?> at <?= htmlspecialchars($job['employer_name']) ?></p>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 p-4 rounded-lg mb-6">
                            <p class="text-blue-700 text-sm">
                                <i class="fas fa-info-circle mr-2"></i> Your profile information will be shared with the employer.
                            </p>
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="resume" class="block text-gray-700 font-medium mb-2">Upload Resume (PDF, DOC, DOCX)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 transition-colors duration-200 cursor-pointer" id="dropzone">
                            <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required class="hidden">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fas fa-file-upload text-4xl text-gray-400"></i>
                                <div class="text-gray-600">
                                    <p class="font-medium">Drag and drop your resume here</p>
                                    <p class="text-sm">or</p>
                                    <button type="button" id="browseFiles" class="text-blue-600 hover:text-blue-700 font-medium">Browse files</button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Maximum file size: 5MB</p>
                            </div>
                            <div id="file-selected" class="hidden mt-4 p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-file-pdf text-blue-600"></i>
                                    <span class="file-name text-gray-700 font-medium"></span>
                                    <button type="button" id="remove-file" class="ml-auto text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="cover_letter" class="block text-gray-700 font-medium mb-2">Cover Letter (Optional)</label>
                        <textarea id="cover_letter" name="cover_letter" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Write a cover letter explaining why you're a good fit for this position..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 p-4">
                    <button type="button" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="apply" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-transform">
                        <i class="fas fa-paper-plane mr-2"></i> Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-xl overflow-hidden">
            <div class="modal-header bg-blue-600 text-white">
                <h5 class="modal-title" id="loginModalLabel">Login Required</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-600 mb-4">
                        <i class="fas fa-user-lock text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-semibold text-gray-800 mb-2">Login to Apply</h4>
                    <p class="text-gray-600">You need to log in as a job seeker to apply for this job.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="login.php" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-transform">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                    <a href="register.php" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
                        <i class="fas fa-user-plus mr-2"></i> Register
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom JavaScript for the job details page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Share dropdown functionality
    const shareBtn = document.querySelector('.share-btn');
    const shareDropdown = document.querySelector('.share-dropdown');
    
    if (shareBtn && shareDropdown) {
        shareBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            shareDropdown.classList.toggle('hidden');
        });
        
        document.addEventListener('click', function(e) {
            if (!shareDropdown.contains(e.target) && e.target !== shareBtn) {
                shareDropdown.classList.add('hidden');
            }
        });
        
        // Copy link functionality
        const copyLinkBtn = document.querySelector('.copy-link');
        if (copyLinkBtn) {
            copyLinkBtn.addEventListener('click', function() {
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(function() {
                    // Show success message
                    const originalText = copyLinkBtn.innerHTML;
                    copyLinkBtn.innerHTML = '<i class="fas fa-check text-green-600"></i> Link Copied!';
                    
                    setTimeout(function() {
                        copyLinkBtn.innerHTML = originalText;
                    }, 2000);
                });
            });
        }
    }
    
    // File upload functionality for the apply modal
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('resume');
    const browseBtn = document.getElementById('browseFiles');
    const fileSelected = document.getElementById('file-selected');
    const fileName = document.querySelector('.file-name');
    const removeFileBtn = document.getElementById('remove-file');
    
    if (dropzone && fileInput) {
        // Handle click on browse button
        browseBtn.addEventListener('click', function() {
            fileInput.click();
        });
        
        // Handle click on dropzone
        dropzone.addEventListener('click', function(e) {
            if (e.target !== removeFileBtn && e.target !== removeFileBtn.querySelector('i')) {
                fileInput.click();
            }
        });
        
        // Handle drag and drop
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropzone.classList.add('border-blue-500', 'bg-blue-50');
        });
        
        dropzone.addEventListener('dragleave', function() {
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
        });
        
        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateFileInfo();
            }
        });
        
        // Handle file selection
        fileInput.addEventListener('change', updateFileInfo);
        
        function updateFileInfo() {
            if (fileInput.files.length) {
                const file = fileInput.files[0];
                
                // Check if file is valid
                const fileType = file.type;
                const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                
                if (!validTypes.includes(fileType)) {
                    alert('Please upload a PDF, DOC, or DOCX file.');
                    fileInput.value = '';
                    return;
                }
                
                // Check file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB limit.');
                    fileInput.value = '';
                    return;
                }
                
                fileName.textContent = file.name;
                fileSelected.classList.remove('hidden');
                dropzone.classList.add('border-blue-500');
            }
        }
        
        // Handle remove file
        if (removeFileBtn) {
            removeFileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.value = '';
                fileSelected.classList.add('hidden');
                dropzone.classList.remove('border-blue-500');
            });
        }
    }
    
    // Form validation
    const applicationForm = document.getElementById('applicationForm');
    
    if (applicationForm) {
        applicationForm.addEventListener('submit', function(e) {
            const resumeInput = document.getElementById('resume');
            
            if (!resumeInput.files.length) {
                e.preventDefault();
                alert('Please upload your resume.');
                dropzone.classList.add('border-red-500');
            } else {
                // Show loading state
                const submitBtn = applicationForm.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';
            }
        });
    }
    
    // Add animation to elements with data-animation attribute
    const animatedElements = document.querySelectorAll('[data-animation]');
    
    if (animatedElements.length > 0) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        const animation = element.dataset.animation;
                        const delay = element.dataset.delay || 0;
                        
                        setTimeout(() => {
                            element.classList.add(`animate-${animation}`);
                        }, delay * 1000);
                        
                        observer.unobserve(element);
                    }
                });
            },
            { threshold: 0.1 }
        );
        
        animatedElements.forEach((element) => {
            observer.observe(element);
        });
    }
});
</script>

<style>
/* Animation classes */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideInUp {
    from { 
        opacity: 0;
        transform: translateY(30px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInLeft {
    from { 
        opacity: 0;
        transform: translateX(-30px);
    }
    to { 
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.animate-fadeIn {
    animation: fadeIn 0.6s ease forwards;
}

.animate-slideInUp {
    animation: slideInUp 0.6s ease forwards;
}

.animate-slideInLeft {
    animation: slideInLeft 0.6s ease forwards;
}

.animate-pulse {
    animation: pulse 1.5s infinite;
}

/* Job details styling */
.job-details-page {
    min-height: calc(100vh - 200px);
}

.job-description {
    line-height: 1.7;
}

.skill-tag {
    transition: all 0.2s ease;
}

.skill-tag:hover {
    transform: translateY(-2px);
}

/* Modal styling */
.modal-content {
    border: none;
}

.modal-header {
    border-bottom: none;
}

.modal-footer {
    border-top: none;
}

/* Dropzone styling */
#dropzone {
    transition: all 0.2s ease;
}

#dropzone:hover {
    background-color: rgba(37, 99, 235, 0.05);
}
</style>

<?php include 'includes/footer.php'; ?>