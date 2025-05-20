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
        header("Location: dashboard.php");
        exit();
    }
}

$saved_jobs = getSavedJobs($user_id) ?: [];
$applications = getJobSeekerApplications($user_id) ?: [];
$unread_messages = getUnreadMessageCount($user_id) ?: 0;
$jobs = getAllJobs(3) ?: [];
$categories = getCategories() ?: [];

include '../includes/header.php';
?>

<!-- Main Content Wrapper -->
<div class="flex flex-col min-h-screen bg-gray-100">
    <!-- Job Seeker Dashboard Section -->
    <section class="py-8 flex-grow">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-6">
                <?php include 'sidebar.php'; ?>
                
                <div class="flex-grow">
                    <!-- Dashboard Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-blue-600">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
                        <p class="text-gray-600 mt-2">Here's what's happening with your job search as of <?= date('F j, Y') ?>.</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white rounded-lg shadow-md p-6 flex items-center gap-4">
                            <div class="flex-shrink-0 p-3 bg-blue-100 rounded-full">
                                <i class="fas fa-briefcase text-2xl text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800"><?= count($applications) ?></h3>
                                <p class="text-gray-600">Applications</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-md p-6 flex items-center gap-4">
                            <div class="flex-shrink-0 p-3 bg-green-100 rounded-full">
                                <i class="fas fa-bookmark text-2xl text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800"><?= count($saved_jobs) ?></h3>
                                <p class="text-gray-600">Saved Jobs</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-md p-6 flex items-center gap-4">
                            <div class="flex-shrink-0 p-3 bg-orange-100 rounded-full">
                                <i class="fas fa-envelope text-2xl text-orange-600"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800"><?= $unread_messages ?></h3>
                                <p class="text-gray-600">Unread Messages</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recommended Jobs Section -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-blue-600">Recommended Jobs</h2>
                            <a href="../jobs.php" class="text-blue-600 hover:underline">View All Jobs</a>
                        </div>
                        
                        <?php if (empty($jobs)): ?>
                            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                                <i class="fas fa-search text-5xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600 mb-4">No jobs available at the moment.</p>
                                <a href="../jobs.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors duration-200">Browse Jobs</a>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach ($jobs as $job): ?>
                                    <?php $is_saved = isJobSaved($user_id, $job['id']); ?>
                                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                        <div class="p-6">
                                            <h3 class="text-lg font-semibold text-blue-600 mb-2">
                                                <a href="../job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="hover:underline">
                                                    <?= htmlspecialchars($job['title']) ?>
                                                </a>
                                            </h3>
                                            <p class="text-gray-600 mb-1"><?= htmlspecialchars($job['employer_name']) ?></p>
                                            <p class="text-gray-600 mb-2">
                                                <i class="fas fa-map-marker-alt mr-1"></i> <?= htmlspecialchars($job['location']) ?>
                                            </p>
                                            <div class="flex gap-2 mb-3">
                                                <span class="badge bg-blue-100 text-blue-800 px-3 py-1 rounded-full"><?= ucfirst(str_replace('-', ' ', htmlspecialchars($job['type']))) ?></span>
                                                <span class="badge bg-green-100 text-green-800 px-3 py-1 rounded-full">$<?= number_format($job['salary']) ?></span>
                                            </div>
                                            <p class="text-gray-600 mb-3 line-clamp-2"><?= htmlspecialchars(substr($job['description'], 0, 100)) ?>...</p>
                                            <div class="flex flex-wrap gap-2">
                                                <?php 
                                                $skills = explode(',', $job['skills']);
                                                foreach (array_slice($skills, 0, 3) as $skill): 
                                                ?>
                                                    <span class="badge bg-gray-100 text-gray-800 px-2 py-1 rounded"><?= htmlspecialchars(trim($skill)) ?></span>
                                                <?php endforeach; ?>
                                                <?php if (count($skills) > 3): ?>
                                                    <span class="badge bg-gray-100 text-gray-800 px-2 py-1 rounded">+<?= count($skills) - 3 ?> more</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="p-4 bg-gray-50 flex gap-3">
                                            <a href="../job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors flex-grow text-center">Apply Now</a>
                                            <form method="post" action="dashboard.php" class="save-form">
                                                <input type="hidden" name="toggle_save" value="1">
                                                <input type="hidden" name="job_id" value="<?= htmlspecialchars($job['id']) ?>">
                                                <button type="submit" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
                                                    <i class="<?= $is_saved ? 'fas' : 'far' ?> fa-bookmark"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Explore Categories Section -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-blue-600">Explore Categories</h2>
                            <a href="../jobs.php" class="text-blue-600 hover:underline">View All Categories</a>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                            <?php foreach ($categories as $index => $category): ?>
                                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                                    <a href="../category_jobs.php?category=<?= htmlspecialchars($category['id']) ?>">
                                        <div class="p-6 flex flex-col items-center text-center">
                                            <i class="<?= htmlspecialchars($category['icon']) ?> text-4xl text-blue-600 mb-4"></i>
                                            <h3 class="text-lg font-semibold text-gray-800 mb-2"><?= htmlspecialchars($category['name']) ?></h3>
                                            <p class="text-gray-600 text-sm line-clamp-2"><?= htmlspecialchars($category['description']) ?></p>
                                        </div>
                                    </a>
                                    <div class="p-4 bg-gray-50 flex justify-between items-center">
                                        <a href="../category_jobs.php?category=<?= htmlspecialchars($category['id']) ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                            Explore Jobs
                                        </a>
                                        <button class="text-gray-500 hover:text-blue-600" data-category-id="<?= htmlspecialchars($category['id']) ?>">
                                            <i class="far fa-bookmark"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
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
                                <p class="text-gray-600 mb-4">You haven't applied to any jobs yet.</p>
                                <a href="../jobs.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors">Browse Jobs</a>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 gap-6">
                                <?php foreach (array_slice($applications, 0, 5) as $application): ?>
                                    <div class="bg-white rounded-lg shadow-md p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                        <div class="flex-1">
                                            <h5 class="text-lg font-semibold text-gray-800 mb-1"><?= htmlspecialchars($application['job_title']) ?></h5>
                                            <p class="text-gray-600 mb-1"><?= htmlspecialchars($application['employer_name']) ?></p>
                                            <span class="badge <?= $application['status'] === 'accepted' ? 'bg-green-100 text-green-800' : ($application['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?> px-3 py-1 rounded-full">
                                                <?= ucfirst($application['status']) ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <p class="text-gray-600">Applied on <?= date('M d, Y', strtotime($application['applied_at'])) ?></p>
                                            <a href="../job_details.php?id=<?= htmlspecialchars($application['job_id']) ?>" class="text-blue-600 hover:underline">View Job</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Saved Jobs Section -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-blue-600">Saved Jobs</h2>
                            <a href="saved_jobs.php" class="text-blue-600 hover:underline">View All</a>
                        </div>
                        
                        <?php if (empty($saved_jobs)): ?>
                            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                                <i class="fas fa-bookmark text-5xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600 mb-4">You haven't saved any jobs yet.</p>
                                <a href="../jobs.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors">Browse Jobs</a>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach (array_slice($saved_jobs, 0, 4) as $job): ?>
                                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                        <div class="p-6">
                                            <h3 class="text-lg font-semibold text-blue-600 mb-2">
                                                <a href="../job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="hover:underline">
                                                    <?= htmlspecialchars($job['title']) ?>
                                                </a>
                                            </h3>
                                            <p class="text-gray-600 mb-1"><?= htmlspecialchars($job['employer_name']) ?></p>
                                            <p class="text-gray-600 mb-2">
                                                <i class="fas fa-map-marker-alt mr-1"></i> <?= htmlspecialchars($job['location']) ?>
                                            </p>
                                            <div class="flex gap-2 mb-3">
                                                <span class="badge bg-blue-100 text-blue-800 px-3 py-1 rounded-full"><?= ucfirst(str_replace('-', ' ', htmlspecialchars($job['type']))) ?></span>
                                                <span class="badge bg-green-100 text-green-800 px-3 py-1 rounded-full">$<?= number_format($job['salary']) ?></span>
                                            </div>
                                            <p class="text-gray-600 mb-3 line-clamp-2"><?= htmlspecialchars(substr($job['description'], 0, 100)) ?>...</p>
                                            <div class="flex flex-wrap gap-2">
                                                <?php 
                                                $skills = explode(',', $job['skills']);
                                                foreach (array_slice($skills, 0, 3) as $skill): 
                                                ?>
                                                    <span class="badge bg-gray-100 text-gray-800 px-2 py-1 rounded"><?= htmlspecialchars(trim($skill)) ?></span>
                                                <?php endforeach; ?>
                                                <?php if (count($skills) > 3): ?>
                                                    <span class="badge bg-gray-100 text-gray-800 px-2 py-1 rounded">+<?= count($skills) - 3 ?> more</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="p-4 bg-gray-50 flex gap-3">
                                            <a href="../job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors flex-grow text-center">Apply Now</a>
                                            <form method="post" action="dashboard.php" class="save-form">
                                                <input type="hidden" name="toggle_save" value="1">
                                                <input type="hidden" name="job_id" value="<?= htmlspecialchars($job['id']) ?>">
                                                <button type="submit" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
                                                    <i class="fas fa-bookmark"></i>
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
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>