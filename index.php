<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$title = "Find Your Dream Job";
$jobs = getAllJobs(6);
$categories = getCategories();

include 'includes/header-enhanced.php';
?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4 animate-slideInUp">Find Your Dream Job Today</h1>
        <p class="text-lg md:text-xl mb-8 animate-slideInUp animation-delay-200">Discover thousands of opportunities from top employers worldwide.</p>
        
        <!-- Search Form -->
        <form class="search-form flex flex-col md:flex-row justify-center items-center gap-4 max-w-4xl mx-auto animate-slideInUp animation-delay-400">
            <input type="text" name="search" placeholder="Job title or keywords" class="w-full md:w-1/3 p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="text" name="location" placeholder="Location" class="w-full md:w-1/3 p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="category" class="w-full md:w-1/3 p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md transition-colors duration-200">
                <i class="fas fa-search mr-2"></i> Search Jobs
            </button>
        </form>
    </div>
</section>

<!-- Login Prompt for Unauthenticated Users -->
<?php if (!isLoggedIn()): ?>
    <section class="bg-gray-100 py-8">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 mb-4">Unlock More Features</h2>
            <p class="text-gray-600 mb-6">Sign in to save jobs, apply directly, and connect with employers.</p>
            <div class="flex justify-center gap-4">
                <a href="login.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
                <a href="register.php" class="bg-gray-600 text-white px-6 py-3 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-user-plus mr-2"></i> Register
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Categories Section -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="section-header flex justify-between items-center mb-8">
            <h2 class="text-3xl font-semibold text-gray-800">Explore Job Categories</h2>
            <a href="jobs.php" class="text-blue-600 hover:underline">View All Categories</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <?php foreach ($categories as $index => $category): ?>
                <div class="category-card bg-white rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 animate-fadeIn overflow-hidden" style="animation-delay: <?= $index * 100 ?>ms;">
                    <a href="category_jobs.php?category=<?= htmlspecialchars($category['id']) ?>">
                        <div class="p-4 flex flex-col items-center text-center">
                            <i class="<?= htmlspecialchars($category['icon']) ?> text-4xl text-blue-600 mb-4"></i>
                            <h3 class="category-title text-lg font-semibold text-gray-800 mb-2"><?= htmlspecialchars($category['name']) ?></h3>
                            <p class="category-description text-gray-600 text-sm"><?= htmlspecialchars($category['description']) ?></p>
                        </div>
                    </a>
                    <div class="p-4 bg-gray-50 flex justify-between items-center">
                        <a href="category_jobs.php?category=<?= htmlspecialchars($category['id']) ?>" class="apply-now-btn bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                            Explore Jobs
                        </a>
                        <?php if (isLoggedIn() && isJobSeeker()): ?>
                            <button class="bookmark-category-btn text-gray-500 hover:text-blue-600" data-category-id="<?= htmlspecialchars($category['id']) ?>">
                                <i class="far fa-bookmark"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Jobs Section -->
<section class="bg-gray-100 py-12">
    <div class="container mx-auto px-4">
        <div class="section-header flex justify-between items-center mb-8">
            <h2 class="text-3xl font-semibold text-gray-800">Featured Jobs</h2>
            <a href="jobs.php" class="text-blue-600 hover:underline">View All Jobs</a>
        </div>
        
        <?php if (empty($jobs)): ?>
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <i class="fas fa-search text-5xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 mb-4">No jobs available at the moment.</p>
                <a href="jobs.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors duration-200">Browse Jobs</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($jobs as $job): ?>
                    <div class="job-card bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                <a href="job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="text-blue-600 hover:underline">
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
                            <a href="job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">View Details</a>
                            <?php if (isLoggedIn() && isJobSeeker()): ?>
                                <form method="post" action="job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="save-form">
                                    <input type="hidden" name="toggle_save">
                                    <button type="submit" class="bookmark-job-btn text-gray-500 hover:text-blue-600" data-job-id="<?= htmlspecialchars($job['id']) ?>">
                                        <i class="far fa-bookmark"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>