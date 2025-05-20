<?php
require_once '../includes/auth.php';
protectJobSeekerRoute();

if (!isset($_GET['job_id'])) {
    $_SESSION['message'] = 'No job specified';
    $_SESSION['message_type'] = 'danger';
    header('Location: jobs.php');
    exit();
}

$job_id = sanitize($_GET['job_id']);
$job = getJobById($job_id);
$user_id = $_SESSION['user_id'];

// Check if already applied
$stmt = $conn->prepare("SELECT * FROM applications WHERE job_id = ? AND user_id = ?");
$stmt->execute([$job_id, $user_id]);
$existing_application = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_application) {
    $_SESSION['message'] = 'You have already applied for this job';
    $_SESSION['message_type'] = 'warning';
    header("Location: job_details.php?id=$job_id");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file upload
    $resume_path = null;
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/resumes/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
        $file_name = "resume_{$user_id}_" . time() . ".$file_ext";
        $target_path = $upload_dir . $file_name;
        
        // Validate file type
        $allowed_types = ['pdf', 'doc', 'docx'];
        if (in_array(strtolower($file_ext), $allowed_types)) {
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $target_path)) {
                $resume_path = $target_path;
            } else {
                $_SESSION['message'] = 'Failed to upload resume';
                $_SESSION['message_type'] = 'danger';
            }
        } else {
            $_SESSION['message'] = 'Only PDF, DOC, and DOCX files are allowed';
            $_SESSION['message_type'] = 'danger';
        }
    }
    
    if ($resume_path || !empty($_POST['cover_letter'])) {
        $cover_letter = sanitize($_POST['cover_letter']);
        
        $stmt = $conn->prepare("INSERT INTO applications 
                              (job_id, user_id, resume_path, cover_letter, status, applied_at) 
                              VALUES (?, ?, ?, ?, 'applied', NOW())");
        $stmt->execute([$job_id, $user_id, $resume_path, $cover_letter]);
        
        $_SESSION['message'] = 'Application submitted successfully!';
        $_SESSION['message_type'] = 'success';
        header("Location: job_details.php?id=$job_id");
        exit();
    } else {
        $_SESSION['message'] = 'Please upload a resume or write a cover letter';
        $_SESSION['message_type'] = 'danger';
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="apply-header">
        <h1>Apply for <?= $job['title'] ?></h1>
        <p>at <?= $job['employer_name'] ?></p>
    </div>
    
    <div class="apply-container">
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="resume">Upload Resume (PDF, DOC, DOCX)</label>
                <input type="file" name="resume" id="resume" class="form-control" accept=".pdf,.doc,.docx">
                <small class="form-text">Max file size: 2MB</small>
            </div>
            
            <div class="form-group">
                <label for="cover_letter">Cover Letter</label>
                <textarea name="cover_letter" id="cover_letter" rows="8" class="form-control" 
                          placeholder="Write your cover letter here..."></textarea>
            </div>
            
            <div class="form-actions">
                <a href="job_details.php?id=<?= $job_id ?>" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Application</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>