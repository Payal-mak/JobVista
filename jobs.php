<?php 
require_once 'includes/config.php'; // Include config to get $pdo
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$location = isset($_GET['location']) ? sanitize($_GET['location']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$type = isset($_GET['type']) ? sanitize($_GET['type']) : '';

$jobs = getAllJobs(null, $category, $location, $type, $search);
$categories = getCategories();

include 'includes/header.php';
?>

<div class="container">
    <div class="jobs-header">
        <h1>Job Listings</h1>
        <p>Browse all available job opportunities</p>
    </div>
    
    <div class="jobs-container">
        <div class="jobs-sidebar">
            <form method="get" action="jobs.php">
                <div class="filter-card">
                    <h4>Search</h4>
                    <div class="form-group">
                        <input type="text" name="search" placeholder="Keywords" value="<?= htmlspecialchars($search) ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="text" name="location" placeholder="Location" value="<?= htmlspecialchars($location) ?>" class="form-control">
                    </div>
                </div>
                
                <div class="filter-card">
                    <h4>Job Type</h4>
                    <div class="form-group">
                        <select name="type" class="form-control">
                            <option value="">All Types</option>
                            <option value="full-time" <?= $type === 'full-time' ? 'selected' : '' ?>>Full Time</option>
                            <option value="part-time" <?= $type === 'part-time' ? 'selected' : '' ?>>Part Time</option>
                            <option value="contract" <?= $type === 'contract' ? 'selected' : '' ?>>Contract</option>
                            <option value="internship" <?= $type === 'internship' ? 'selected' : '' ?>>Internship</option>
                            <option value="remote" <?= $type === 'remote' ? 'selected' : '' ?>>Remote</option>
                        </select>
                    </div>
                </div>
                
                <div class="filter-card">
                    <h4>Category</h4>
                    <div class="form-group">
                        <select name="category" class="form-control">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']) ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Filter Jobs</button>
                    <a href="jobs.php" class="btn btn-outline btn-block">Reset Filters</a>
                </div>
            </form>
        </div>
        
        <div class="jobs-listings">
            <?php if (empty($jobs)): ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No jobs found matching your criteria</h3>
                    <p>Try adjusting your search filters or <a href="jobs.php">browse all jobs</a></p>
                </div>
            <?php else: ?>
                <div class="job-listings-header">
                    <p><?= count($jobs) ?> jobs found</p>
                    <div class="sort-options">
                        <span>Sort by:</span>
                        <select class="form-control">
                            <option>Most Recent</option>
                            <option>Highest Salary</option>
                            <option>Closest Location</option>
                        </select>
                    </div>
                </div>
                
                <div class="jobs-grid">
                    <?php foreach($jobs as $job): ?>
                        <div class="job-card">
                            <div class="job-card-header">
                                <h3><a href="job_details.php?id=<?= htmlspecialchars($job['id']) ?>"><?= htmlspecialchars($job['title']) ?></a></h3>
                                <p class="company"><?= htmlspecialchars($job['employer_name']) ?></p>
                                <p class="location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?></p>
                            </div>
                            
                            <div class="job-card-body">
                                <div class="job-meta">
                                    <span class="type"><?= ucfirst(str_replace('-', ' ', htmlspecialchars($job['type']))) ?></span>
                                    <span class="salary">$<?= number_format($job['salary']) ?></span>
                                </div>
                                
                                <p class="job-excerpt"><?= htmlspecialchars(substr($job['description'], 0, 100)) ?>...</p>
                                
                                <div class="job-skills">
                                    <?php 
                                    $skills = explode(',', $job['skills']);
                                    foreach(array_slice($skills, 0, 3) as $skill): 
                                    ?>
                                        <span class="skill-tag"><?= htmlspecialchars(trim($skill)) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($skills) > 3): ?>
                                        <span class="skill-tag">+<?= count($skills) - 3 ?> more</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="job-card-footer">
                                <a href="job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="btn btn-primary">View Details</a>
                                <form method="post" action="job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="save-form">
                                    <input type="hidden" name="toggle_save">
                                    <button type="submit" class="btn-save">
                                        <i class="far fa-bookmark"></i> Save
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>