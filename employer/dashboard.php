<?php 
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

protectEmployerRoute();

$employer_id = $_SESSION['user_id'];
$jobs = getEmployerJobs($employer_id);
$applications = getEmployerApplications($employer_id);
$unread_messages = getUnreadMessageCount($employer_id);
$all_jobs = getAllJobs(3);
$categories = getCategories();

include '../includes/header.php';

// Function to map category to image path
function getCategoryImage($category_name) {
    $category_name = strtolower($category_name);
    if (strpos($category_name, 'it') !== false || strpos($category_name, 'software') !== false) {
        return 'assets/images/IT & Software.jpg';
    } elseif (strpos($category_name, 'marketing') !== false) {
        return 'assets/images/Marketing.jpg';
    } elseif (strpos($category_name, 'finance') !== false) {
        return 'assets/images/Finance.jpg';
    } elseif (strpos($category_name, 'healthcare') !== false) {
        return 'assets/images/Healthcare.jpg';
    } elseif (strpos($category_name, 'education') !== false) {
        return 'assets/images/Education.jpg';
    } else {
        return 'assets/images/default_category.jpg';
    }
}
?>

<!-- Main Content Wrapper -->
<div class="flex flex-col min-h-screen">
    <!-- Employer Dashboard Section -->
    <section class="bg-gray-100 py-8 flex-grow">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-6">
                <?php include 'sidebar.php'; ?>
                
                <div class="flex-grow">
                    <!-- Dashboard Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-blue-600">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
                        <p class="text-gray-600 mt-2">Manage your job postings and applications as of <?= date('F j, Y') ?>.</p>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="stat-card bg-white rounded-lg shadow-md p-6 flex items-center gap-4">
                            <div class="stat-icon blue">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="stat-info">
                                <h3 class="text-2xl font-bold"><?= count($jobs) ?></h3>
                                <p class="text-gray-600">Active Jobs</p>
                            </div>
                        </div>
                        <div class="stat-card bg-white rounded-lg shadow-md p-6 flex items-center gap-4">
                            <div class="stat-icon green">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-info">
                                <h3 class="text-2xl font-bold"><?= count($applications) ?></h3>
                                <p class="text-gray-600">Total Applications</p>
                            </div>
                        </div>
                        <div class="stat-card bg-white rounded-lg shadow-md p-6 flex items-center gap-4">
                            <div class="stat-icon orange">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="stat-info">
                                <h3 class="text-2xl font-bold"><?= $unread_messages ?></h3>
                                <p class="text-gray-600">Unread Messages</p>
                            </div>
                        </div>
                    </div>

                    <!-- Categories Section -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-blue-600">Explore Categories</h2>
                            <a href="../jobs.php" class="text-blue-600 hover:underline">View All Categories</a>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                            <?php foreach ($categories as $index => $category): ?>
                                <div class="category-card bg-white rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 animate-fadeIn overflow-hidden" style="animation-delay: <?= $index * 100 ?>ms;">
                                    <a href="../category_jobs.php?category=<?= htmlspecialchars($category['id']) ?>">
                                        <div class="p-4 flex flex-col items-center text-center">
                                            <i class="<?= htmlspecialchars($category['icon']) ?> text-4xl text-blue-600 mb-4"></i>
                                            <h3 class="category-title text-lg font-semibold text-gray-800 mb-2"><?= htmlspecialchars($category['name']) ?></h3>
                                            <p class="category-description text-gray-600 text-sm"><?= htmlspecialchars($category['description']) ?></p>
                                        </div>
                                    </a>
                                    <div class="p-4 bg-gray-50 flex justify-between items-center">
                                        <a href="../category_jobs.php?category=<?= htmlspecialchars($category['id']) ?>" class="apply-now-btn bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                            Explore Jobs
                                        </a>
                                        <button class="bookmark-category-btn text-gray-500 hover:text-blue-600" data-category-id="<?= htmlspecialchars($category['id']) ?>">
                                            <i class="far fa-bookmark"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Job Listings Section -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-blue-600">Explore Other Jobs</h2>
                            <a href="../jobs.php" class="text-blue-600 hover:underline">View All Jobs</a>
                        </div>
                        
                        <?php if (empty($all_jobs)): ?>
                            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                                <i class="fas fa-search text-5xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600 mb-4">No jobs available at the moment.</p>
                                <a href="../jobs.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors duration-200">Browse Jobs</a>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach ($all_jobs as $job): ?>
                                    <div class="job-card bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                                        <div class="p-6">
                                            <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                                <a href="../job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="text-blue-600 hover:underline">
                                                    <?= htmlspecialchars($job['title']) ?>
                                                </a>
                                            </h3>
                                            <p class="text-gray-600 mb-1"><?= htmlspecialchars($job['employer_name']) ?></p>
                                            <p class="text-gray-600 mb-3"><i class="fas fa-map-marker-alt mr-2"></i><?= htmlspecialchars($job['location']) ?></p>
                                            <div class="flex flex-wrap gap-2 mb-3">
                                                <span class="badge bg-blue-100 text-blue-800 px-3 py-1 rounded-full"><?= ucfirst(str_replace('-', ' ', htmlspecialchars($job['type']))) ?></span>
                                                <span class="badge bg-green-100 text-green-800 px-3 py-1 rounded-full">$<?= number_format($job['salary']) ?></span>
                                            </div>
                                            <p class="text-gray-600"><?= htmlspecialchars(substr($job['description'], 0, 100)) ?>...</p>
                                        </div>
                                        <div class="p-4 bg-gray-50 flex justify-between items-center">
                                            <a href="../job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">View Details</a>
                                            <form method="post" action="../job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="save-form">
                                                <input type="hidden" name="toggle_save">
                                                <button type="submit" class="bookmark-job-btn text-gray-500 hover:text-blue-600" data-job-id="<?= htmlspecialchars($job['id']) ?>">
                                                    <i class="far fa-bookmark"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Job Postings Section -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-blue-600">Your Job Postings</h2>
                            <a href="post_job.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">Post a New Job</a>
                        </div>
                        
                        <?php if (empty($jobs)): ?>
                            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                                <i class="fas fa-briefcase text-5xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600 mb-4">You haven't posted any jobs yet.</p>
                                <a href="post_job.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors duration-200">Post Your First Job</a>
                            </div>
                        <?php else: ?>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="p-4">Job Title</th>
                                                <th class="p-4">Applications</th>
                                                <th class="p-4">Status</th>
                                                <th class="p-4">Posted</th>
                                                <th class="p-4">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($jobs, 0, 5) as $job): ?>
                                                <tr class="border-t">
                                                    <td class="p-4">
                                                        <a href="../job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="text-blue-600 hover:underline">
                                                            <?= htmlspecialchars($job['title']) ?>
                                                        </a>
                                                        <p class="text-gray-600 text-sm">
                                                            <span><?= htmlspecialchars($job['category_name']) ?></span> | 
                                                            <span><?= ucfirst(str_replace('-', ' ', htmlspecialchars($job['type']))) ?></span>
                                                        </p>
                                                    </td>
                                                    <td class="p-4"><?= $job['application_count'] ?></td>
                                                    <td class="p-4">
                                                        <span class="badge <?= $job['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?> px-3 py-1 rounded-full">
                                                            <?= ucfirst($job['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="p-4"><?= date('M d, Y', strtotime($job['posted_at'])) ?></td>
                                                    <td class="p-4 flex gap-2">
                                                        <a href="manage_jobs.php?edit=<?= htmlspecialchars($job['id']) ?>" class="text-blue-600 hover:underline" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="applications.php?job=<?= htmlspecialchars($job['id']) ?>" class="text-blue-600 hover:underline" title="View Applications">
                                                            <i class="fas fa-users"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="text-right mt-4">
                                <a href="manage_jobs.php" class="text-blue-600 hover:underline">View All Jobs</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Recent Applications Section -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-blue-600">Recent Applications</h2>
                            <a href="applications.php" class="text-blue-600 hover:underline">View All</a>
                        </div>
                        
                        <?php if (empty($applications)): ?>
                            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                                <i class="fas fa-file-alt text-5xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600 mb-0">No applications received yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach (array_slice($applications, 0, 5) as $application): ?>
                                    <div class="bg-white rounded-lg shadow-md p-6 flex justify-between items-center">
                                        <div class="application-info">
                                            <h5 class="text-lg font-semibold text-gray-800 mb-1"><?= htmlspecialchars($application['applicant_name']) ?></h5>
                                            <p class="text-gray-600 mb-1">Applied for <?= htmlspecialchars($application['job_title']) ?></p>
                                            <span class="badge <?= $application['status'] === 'accepted' ? 'bg-green-100 text-green-800' : ($application['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?> px-3 py-1 rounded-full">
                                                <?= ucfirst($application['status']) ?>
                                            </span>
                                        </div>
                                        <div class="application-meta flex items-center gap-4">
                                            <p class="text-gray-600">Applied on <?= date('M d, Y', strtotime($application['applied_at'])) ?></p>
                                            <div class="flex gap-2">
                                                <a href="applications.php?view=<?= htmlspecialchars($application['id']) ?>" class="text-blue-600 hover:underline">View</a>
                                                <?php if ($application['status'] === 'pending'): ?>
                                                    <a href="applications.php?shortlist=<?= htmlspecialchars($application['id']) ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">Shortlist</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>