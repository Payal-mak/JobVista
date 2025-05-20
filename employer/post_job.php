<?php 
require_once '../includes/auth.php';
protectEmployerRoute();

$categories = getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category_id = sanitize($_POST['category_id']);
    $location = sanitize($_POST['location']);
    $salary = sanitize($_POST['salary']);
    $type = sanitize($_POST['type']);
    
    $result = postJob($_SESSION['user_id'], $title, $description, $category_id, $location, $salary, $type);
    
    if ($result === true) {
        $_SESSION['success'] = "Job posted successfully! It will be visible after admin approval.";
        header("Location: manage_jobs.php");
        exit();
    } else {
        $error = "Failed to post job. Please try again.";
    }
}

include '../includes/header.php';
?>

<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Post a New Job</h1>
            <p>Fill in the details of your job opening</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post" class="job-form">
            <div class="form-group">
                <label for="title">Job Title</label>
                <input type="text" id="title" name="title" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="description">Job Description</label>
                <textarea id="description" name="description" rows="8" required class="form-control"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required class="form-control">
                        <option value="">Select a category</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required class="form-control">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="salary">Salary</label>
                    <input type="text" id="salary" name="salary" class="form-control" placeholder="e.g. 50000 or Negotiable">
                </div>
                
                <div class="form-group">
                    <label for="type">Job Type</label>
                    <select id="type" name="type" required class="form-control">
                        <option value="">Select job type</option>
                        <option value="full-time">Full Time</option>
                        <option value="part-time">Part Time</option>
                        <option value="contract">Contract</option>
                        <option value="internship">Internship</option>
                        <option value="remote">Remote</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Post Job</button>
                <a href="dashboard.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>