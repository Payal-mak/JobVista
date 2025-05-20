<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Get the category ID from the URL
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Fetch category details to display the category name
$categories = getCategories();
$category_name = 'Unknown Category';
foreach ($categories as $category) {
    if ($category['id'] == $category_id) {
        $category_name = $category['name'];
        break;
    }
}

// Fetch jobs for the selected category
$jobs = getAllJobs(null, $category_id);

// Set page title
$title = "$category_name Jobs";
?>

<?php include 'includes/header.php'; ?>

<!-- Category Jobs Section -->
<section class="category-jobs-section py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="section-header flex justify-between items-center mb-8">
            <h2 class="section-title text-3xl font-bold text-gray-800 relative pl-4 animate-slideInLeft">
                <?= htmlspecialchars($category_name) ?> Jobs
            </h2>
            <a href="jobs.php" class="view-all text-blue-600 font-medium hover:text-blue-700 flex items-center gap-2">
                Browse All Jobs <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <!-- Jobs Grid -->
        <?php if (empty($jobs)): ?>
            <div class="text-center text-gray-600">
                <p>No jobs found in the <?= htmlspecialchars($category_name) ?> category.</p>
                <a href="jobs.php" class="text-blue-600 hover:text-blue-700 mt-4 inline-block">Browse All Jobs</a>
            </div>
        <?php else: ?>
            <div class="jobs-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($jobs as $index => $job): ?>
                    <div class="job-card bg-white rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 animate-fadeIn" style="animation-delay: <?= $index * 100 ?>ms;">
                        <div class="job-card-header p-6 border-b border-gray-200">
                            <h3><a href="job_details.php?id=<?= $job['id'] ?>" class="text-xl font-semibold text-gray-800 hover:text-blue-600"><?= htmlspecialchars($job['title']) ?></a></h3>
                            <p class="company text-gray-600 mt-1"><?= htmlspecialchars($job['employer_name']) ?></p>
                            <p class="location text-gray-500 flex items-center gap-1 mt-1"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?></p>
                        </div>
                        <div class="job-card-body p-6">
                            <div class="job-meta mb-4">
                                <p class="salary text-green-700 font-semibold mb-2">₹<?= number_format($job['salary'], 0, ',', ',') ?> a year</p>
                                <p class="job-type text-gray-600"><strong>Job type:</strong> <?= ucfirst(str_replace('-', ' ', $job['type'])) ?></p>
                                <p class="schedule text-gray-600"><strong>Schedule:</strong> <?= htmlspecialchars($job['schedule']) ?></p>
                            </div>
                            <div class="benefits mb-4">
                                <p class="text-gray-600"><strong>Benefits:</strong></p>
                                <ul class="list-disc list-inside text-gray-600">
                                    <?php
                                    $benefits = explode(',', $job['benefits']);
                                    foreach ($benefits as $benefit):
                                    ?>
                                        <li><?= htmlspecialchars(trim($benefit)) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <p class="description text-gray-600"><?= substr(htmlspecialchars($job['description']), 0, 150) ?>...</p>
                        </div>
                        <div class="job-card-footer p-4 bg-gray-50 flex justify-between items-center">
                            <a href="apply_job.php?job_id=<?= $job['id'] ?>" class="apply-now-btn bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                                Apply Now
                            </a>
                            <button class="bookmark-job-btn text-gray-500 hover:text-blue-600 flex items-center gap-2" data-job-id="<?= $job['id'] ?>">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>