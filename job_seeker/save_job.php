<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
protectJobSeekerRoute();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id'])) {
    $job_id = sanitize($_POST['job_id']);
    $user_id = $_SESSION['user_id'];
    
    // Check if job exists
    $job = getJobById($job_id);
    if (!$job) {
        $_SESSION['message'] = 'Job not found';
        $_SESSION['message_type'] = 'danger';
        header('Location: jobs.php');
        exit();
    }
    
    // Check if already saved
    $stmt = $conn->prepare("SELECT * FROM saved_jobs WHERE job_id = ? AND user_id = ?");
    $stmt->execute([$job_id, $user_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Unsave the job
        $stmt = $conn->prepare("DELETE FROM saved_jobs WHERE job_id = ? AND user_id = ?");
        $stmt->execute([$job_id, $user_id]);
        $_SESSION['message'] = 'Job removed from saved jobs';
    } else {
        // Save the job
        $stmt = $conn->prepare("INSERT INTO saved_jobs (job_id, user_id, saved_at) VALUES (?, ?, NOW())");
        $stmt->execute([$job_id, $user_id]);
        $_SESSION['message'] = 'Job saved successfully';
    }
    
    $_SESSION['message_type'] = 'success';
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

header('Location: jobs.php');
exit();