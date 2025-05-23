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

<div class="jobs-page bg-gradient-to-b from-gray-50 to-white py-10">
    <div class="container mx-auto px-4">
        <!-- Jobs Header -->
        <div class="jobs-header text-center mb-12 animate-fadeIn" data-animation="fadeIn">
            <h1 class="text-4xl font-bold text-gray-800 mb-3">Find Your Dream Job</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Browse through thousands of full-time and part-time jobs near you</p>
        </div>
        
        <!-- Search Bar -->
        <div class="search-bar bg-white p-6 rounded-xl shadow-lg mb-10 animate-slideInUp" data-animation="slideInUp">
            <form method="get" action="jobs.php" class="job-search-form">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="form-group">
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" placeholder="Job title or keywords" value="<?= htmlspecialchars($search) ?>" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <input type="text" name="location" placeholder="Location" value="<?= htmlspecialchars($location) ?>" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400">
                                <i class="fas fa-briefcase"></i>
                            </span>
                            <select name="type" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none">
                                <option value="">All Job Types</option>
                                <option value="full-time" <?= $type === 'full-time' ? 'selected' : '' ?>>Full Time</option>
                                <option value="part-time" <?= $type === 'part-time' ? 'selected' : '' ?>>Part Time</option>
                                <option value="contract" <?= $type === 'contract' ? 'selected' : '' ?>>Contract</option>
                                <option value="internship" <?= $type === 'internship' ? 'selected' : '' ?>>Internship</option>
                                <option value="remote" <?= $type === 'remote' ? 'selected' : '' ?>>Remote</option>
                            </select>
                            <span class="absolute right-3 top-3 text-gray-400 pointer-events-none">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-transform">
                            <i class="fas fa-search"></i> Search Jobs
                        </button>
                    </div>
                </div>
                
                <!-- Advanced Search Toggle -->
                <div class="mt-4 text-center">
                    <button type="button" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-1 mx-auto" id="advancedSearchToggle">
                        <i class="fas fa-sliders-h"></i> Advanced Search <i class="fas fa-chevron-down text-xs ml-1" id="advancedSearchIcon"></i>
                    </button>
                </div>
                
                <!-- Advanced Search Options -->
                <div class="advanced-search-options hidden mt-6 pt-6 border-t border-gray-200" id="advancedSearchOptions">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="category" class="block text-gray-700 font-medium mb-2">Category</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">
                                    <i class="fas fa-tag"></i>
                                </span>
                                <select name="category" id="category" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none">
                                    <option value="">All Categories</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat['id']) ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="absolute right-3 top-3 text-gray-400 pointer-events-none">
                                    <i class="fas fa-chevron-down"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="salary" class="block text-gray-700 font-medium mb-2">Minimum Salary</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                                <input type="number" name="min_salary" id="salary" placeholder="Minimum salary" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="experience" class="block text-gray-700 font-medium mb-2">Experience Level</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">
                                    <i class="fas fa-user-graduate"></i>
                                </span>
                                <select name="experience" id="experience" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none">
                                    <option value="">All Experience Levels</option>
                                    <option value="entry">Entry Level</option>
                                    <option value="mid">Mid Level</option>
                                    <option value="senior">Senior Level</option>
                                    <option value="executive">Executive Level</option>
                                </select>
                                <span class="absolute right-3 top-3 text-gray-400 pointer-events-none">
                                    <i class="fas fa-chevron-down"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <a href="jobs.php" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors mr-3">
                            Reset Filters
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="jobs-container grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Filters (Mobile Toggle) -->
            <div class="lg:hidden mb-6">
                <button type="button" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium flex items-center justify-between shadow-sm" id="mobileFilterToggle">
                    <span><i class="fas fa-filter mr-2"></i> Filter Jobs</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            
            <!-- Sidebar Filters -->
            <div class="jobs-sidebar lg:col-span-1 hidden lg:block" id="jobsSidebar">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6 sticky top-24 animate-slideInLeft" data-animation="slideInLeft">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-3 border-b border-gray-200">Filter Jobs</h3>
                    
                    <form method="get" action="jobs.php" class="filter-form">
                        <!-- Job Type Filter -->
                        <div class="filter-section mb-6">
                            <h4 class="font-semibold text-gray-700 mb-3">Job Type</h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="" <?= $type === '' ? 'checked' : '' ?> class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span>All Types</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="full-time" <?= $type === 'full-time' ? 'checked' : '' ?> class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span>Full Time</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="part-time" <?= $type === 'part-time' ? 'checked' : '' ?> class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span>Part Time</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="contract" <?= $type === 'contract' ? 'checked' : '' ?> class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span>Contract</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="internship" <?= $type === 'internship' ? 'checked' : '' ?> class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span>Internship</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="remote" <?= $type === 'remote' ? 'checked' : '' ?> class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span>Remote</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="filter-section mb-6">
                            <h4 class="font-semibold text-gray-700 mb-3">Category</h4>
                            <div class="relative">
                                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none">
                                    <option value="">All Categories</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat['id']) ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="absolute right-3 top-2 text-gray-400 pointer-events-none">
                                    <i class="fas fa-chevron-down"></i>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Location Filter -->
                        <div class="filter-section mb-6">
                            <h4 class="font-semibold text-gray-700 mb-3">Location</h4>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-400">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <input type="text" name="location" placeholder="Enter location" value="<?= htmlspecialchars($location) ?>" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            </div>
                        </div>
                        
                        <!-- Search Keywords -->
                        <div class="filter-section mb-6">
                            <h4 class="font-semibold text-gray-700 mb-3">Keywords</h4>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-400">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="search" placeholder="Job title or keywords" value="<?= htmlspecialchars($search) ?>" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            </div>
                        </div>
                        
                        <!-- Filter Actions -->
                        <div class="filter-actions">
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-300 mb-3 flex items-center justify-center gap-2">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <a href="jobs.php" class="w-full bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors duration-300 text-center block">
                                Reset Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Jobs Listings -->
            <div class="jobs-listings lg:col-span-3">
                <?php if (empty($jobs)): ?>
                    <div class="no-results bg-white rounded-xl shadow-lg p-10 text-center animate-fadeIn" data-animation="fadeIn">
                        <div class="text-6xl text-gray-300 mb-4">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">No jobs found matching your criteria</h3>
                        <p class="text-gray-600 mb-6">Try adjusting your search filters or browse all available jobs</p>
                        <a href="jobs.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-300 shadow-md">
                            View All Jobs
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Jobs Header -->
                    <div class="jobs-listings-header flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 animate-fadeIn" data-animation="fadeIn">
                        <p class="text-gray-600 mb-3 sm:mb-0"><span class="font-semibold text-gray-800"><?= count($jobs) ?></span> jobs found</p>
                        <div class="sort-options flex items-center">
                            <span class="text-gray-600 mr-2">Sort by:</span>
                            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none pr-8 bg-white">
                                <option>Most Recent</option>
                                <option>Highest Salary</option>
                                <option>Closest Location</option>
                            </select>
                            <span class="relative right-6 text-gray-400 pointer-events-none">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Jobs Grid -->
                    <div class="jobs-grid space-y-6" data-stagger="0.1" data-animation="slideInUp">
                        <?php foreach($jobs as $index => $job): ?>
                            <div class="job-card bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden animate-slideInUp" style="animation-delay: <?= $index * 0.1 ?>s;">
                                <div class="p-6">
                                    <div class="flex flex-col md:flex-row md:items-start gap-4">
                                        <!-- Company Logo -->
                                        <div class="company-logo flex-shrink-0 w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                                            <?php if (isset($job['company_logo']) && !empty($job['company_logo'])): ?>
                                                <img src="<?= htmlspecialchars($job['company_logo']) ?>" alt="<?= htmlspecialchars($job['employer_name']) ?>" class="w-full h-full object-contain">
                                            <?php else: ?>
                                                <div class="text-2xl text-gray-400">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Job Details -->
                                        <div class="flex-grow">
                                            <h3 class="text-xl font-bold text-gray-800 mb-1 hover:text-blue-600 transition-colors">
                                                <a href="job_details.php?id=<?= htmlspecialchars($job['id']) ?>"><?= htmlspecialchars($job['title']) ?></a>
                                            </h3>
                                            <p class="text-gray-600 mb-2"><?= htmlspecialchars($job['employer_name']) ?></p>
                                            
                                            <div class="flex flex-wrap items-center gap-3 text-gray-500 text-sm mb-3">
                                                <span class="flex items-center">
                                                    <i class="fas fa-map-marker-alt mr-1 text-blue-600"></i> <?= htmlspecialchars($job['location']) ?>
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-briefcase mr-1 text-blue-600"></i> <?= ucfirst(str_replace('-', ' ', htmlspecialchars($job['type']))) ?>
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-money-bill-wave mr-1 text-blue-600"></i> $<?= number_format($job['salary']) ?>
                                                </span>
                                            </div>
                                            
                                            <p class="job-excerpt text-gray-600 mb-4"><?= htmlspecialchars(substr($job['description'], 0, 150)) ?>...</p>
                                            
                                            <!-- Job Skills -->
                                            <div class="job-skills flex flex-wrap gap-2 mb-4">
                                                <?php 
                                                $skills = explode(',', $job['skills'] ?? 'Communication,Teamwork,Problem Solving');
                                                foreach(array_slice($skills, 0, 3) as $skill): 
                                                ?>
                                                    <span class="skill-tag px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm hover:bg-blue-200 transition-colors">
                                                        <?= htmlspecialchars(trim($skill)) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                                <?php if (count($skills) > 3): ?>
                                                    <span class="skill-tag px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                                        +<?= count($skills) - 3 ?> more
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Job Actions -->
                                            <div class="job-actions flex flex-wrap items-center justify-between">
                                                <a href="job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium transition-colors">
                                                    View Details <i class="fas fa-arrow-right ml-2"></i>
                                                </a>
                                                
                                                <div class="flex items-center gap-3">
                                                    <span class="text-gray-500 text-sm">
                                                        <i class="far fa-clock mr-1"></i> <?= time_elapsed_string($job['posted_at']) ?>
                                                    </span>
                                                    
                                                    <form method="post" action="job_details.php?id=<?= htmlspecialchars($job['id']) ?>" class="save-form">
                                                        <input type="hidden" name="toggle_save">
                                                        <button type="submit" class="btn-save text-gray-400 hover:text-blue-600 transition-colors">
                                                            <i class="far fa-bookmark"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="pagination flex justify-center mt-10 animate-fadeIn" data-animation="fadeIn" data-delay="0.5">
                        <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-600 text-sm font-medium text-white">2</a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">3</a>
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">8</a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">9</a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">10</a>
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Custom JavaScript for the jobs page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Advanced search toggle
    const advancedSearchToggle = document.getElementById('advancedSearchToggle');
    const advancedSearchOptions = document.getElementById('advancedSearchOptions');
    const advancedSearchIcon = document.getElementById('advancedSearchIcon');
    
    if (advancedSearchToggle && advancedSearchOptions) {
        advancedSearchToggle.addEventListener('click', function() {
            advancedSearchOptions.classList.toggle('hidden');
            advancedSearchIcon.classList.toggle('fa-chevron-down');
            advancedSearchIcon.classList.toggle('fa-chevron-up');
        });
    }
    
    // Mobile filter toggle
    const mobileFilterToggle = document.getElementById('mobileFilterToggle');
    const jobsSidebar = document.getElementById('jobsSidebar');
    
    if (mobileFilterToggle && jobsSidebar) {
        mobileFilterToggle.addEventListener('click', function() {
            jobsSidebar.classList.toggle('hidden');
            
            // Toggle icon
            const icon = mobileFilterToggle.querySelector('i.fas:last-child');
            if (icon) {
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            }
        });
    }
    
    // Save job functionality
    const saveButtons = document.querySelectorAll('.btn-save');
    
    saveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const icon = this.querySelector('i');
            if (icon) {
                if (icon.classList.contains('far')) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    icon.classList.add('text-blue-600');
                    
                    // Add pulse animation
                    icon.classList.add('animate-pulse');
                    setTimeout(() => {
                        icon.classList.remove('animate-pulse');
                    }, 1000);
                } else {
                    icon.classList.remove('fas', 'text-blue-600');
                    icon.classList.add('far');
                }
            }
        });
    });
    
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
    
    // Add staggered animations to lists
    const staggeredLists = document.querySelectorAll('[data-stagger]');
    
    staggeredLists.forEach((list) => {
        const items = list.children;
        const animation = list.dataset.animation || 'fadeIn';
        const baseDelay = parseFloat(list.dataset.baseDelay) || 0;
        const staggerDelay = parseFloat(list.dataset.stagger) || 0.1;
        
        Array.from(items).forEach((item, index) => {
            item.style.opacity = '0';
            item.dataset.animation = animation;
            item.dataset.delay = (baseDelay + index * staggerDelay).toString();
        });
    });
});

// Helper function to format time elapsed
function time_elapsed_string(datetime) {
    const now = new Date();
    const then = new Date(datetime);
    const seconds = Math.floor((now - then) / 1000);
    
    let interval = Math.floor(seconds / 31536000);
    if (interval >= 1) {
        return interval + " year" + (interval === 1 ? "" : "s") + " ago";
    }
    
    interval = Math.floor(seconds / 2592000);
    if (interval >= 1) {
        return interval + " month" + (interval === 1 ? "" : "s") + " ago";
    }
    
    interval = Math.floor(seconds / 86400);
    if (interval >= 1) {
        return interval + " day" + (interval === 1 ? "" : "s") + " ago";
    }
    
    interval = Math.floor(seconds / 3600);
    if (interval >= 1) {
        return interval + " hour" + (interval === 1 ? "" : "s") + " ago";
    }
    
    interval = Math.floor(seconds / 60);
    if (interval >= 1) {
        return interval + " minute" + (interval === 1 ? "" : "s") + " ago";
    }
    
    return "Just now";
}
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

/* Jobs page styling */
.jobs-page {
    min-height: calc(100vh - 200px);
}

/* Form styling */
.form-group {
    position: relative;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Job card styling */
.job-card {
    transition: all 0.3s ease;
}

.job-card:hover {
    transform: translateY(-5px);
}

.skill-tag {
    transition: all 0.2s ease;
}

.skill-tag:hover {
    transform: translateY(-2px);
}

/* Sidebar styling */
@media (min-width: 1024px) {
    .jobs-sidebar {
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }
}

/* Pagination styling */
.pagination a {
    transition: all 0.2s ease;
}

.pagination a:hover:not(.bg-blue-600) {
    background-color: #f3f4f6;
}
</style>

<?php include 'includes/footer.php'; ?>