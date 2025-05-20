<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['message'] = "Please log in to apply for a job.";
    $_SESSION['message_type'] = "warning";
    header("Location: login.php");
    exit();
}

// Get job ID from URL
$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;
$job = getJobById($job_id);

if (!$job) {
    $_SESSION['message'] = "Job not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: category_jobs.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $contact = sanitize($_POST['contact']);
    
    // Handle resume upload
    $resume_path = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/resumes/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = uniqid() . '_' . basename($_FILES['resume']['name']);
        $file_path = $upload_dir . $file_name;
        
        // Validate file type (PDF only)
        $file_type = mime_content_type($_FILES['resume']['tmp_name']);
        if ($file_type !== 'application/pdf') {
            $_SESSION['message'] = "Only PDF files are allowed for resumes.";
            $_SESSION['message_type'] = "danger";
            header("Location: apply_job.php?job_id=$job_id");
            exit();
        }
        
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $file_path)) {
            $resume_path = $file_path;
        } else {
            $_SESSION['message'] = "Failed to upload resume.";
            $_SESSION['message_type'] = "danger";
            header("Location: apply_job.php?job_id=$job_id");
            exit();
        }
    } else {
        $_SESSION['message'] = "Please upload a resume.";
        $_SESSION['message_type'] = "danger";
        header("Location: apply_job.php?job_id=$job_id");
        exit();
    }

    // Save application
    $result = applyForJob($job_id, $_SESSION['user_id'], $name, $email, $contact, $resume_path);
    if ($result === true) {
        $_SESSION['message'] = "Application submitted successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: category_jobs.php?category=" . $job['category_id']);
        exit();
    } else {
        $_SESSION['message'] = $result;
        $_SESSION['message_type'] = "danger";
        header("Location: apply_job.php?job_id=$job_id");
        exit();
    }
}

$title = "Apply for " . htmlspecialchars($job['title']);
?>

<?php include 'includes/header.php'; ?>

<!-- Application Form Section -->
<section class="apply-job-section py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Apply for <?= htmlspecialchars($job['title']) ?></h2>
        <div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
            <form action="apply_job.php?job_id=<?= $job_id ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="contact" class="block text-gray-700 font-medium mb-2">Contact Number</label>
                    <input type="text" id="contact" name="contact" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="resume" class="block text-gray-700 font-medium mb-2">Upload Resume (PDF only)</label>
                    <input type="file" id="resume" name="resume" accept=".pdf" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors duration-200 w-full">Submit Application</button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>