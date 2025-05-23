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

<!-- Category Jobs Hero Section -->
<section class="category-hero bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16 relative overflow-hidden">
    <div class="absolute inset-0 bg-pattern opacity-10"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl mx-auto text-center animate-fadeIn" data-animation="fadeIn">
            <h1 class="text-4xl md:text-5xl font-bold mb-4"><?= htmlspecialchars($category_name) ?> Jobs</h1>
            <p class="text-xl text-blue-100 mb-8">Discover the best <?= htmlspecialchars($category_name) ?> opportunities that match your skills and experience</p>
            
            <!-- Search Bar -->
            <div class="bg-white p-4 rounded-xl shadow-lg flex flex-col md:flex-row gap-4 animate-slideInUp" data-animation="slideInUp" data-delay="0.2">
                <div class="flex-grow relative">
                    <span class="absolute left-3 top-3 text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" placeholder="Search <?= htmlspecialchars($category_name) ?> jobs..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div class="flex-shrink-0">
                    <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-transform">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Wave Divider -->
    <div class="absolute bottom-0 left-0 w-full">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" class="w-full h-auto">
            <path fill="#f9fafb" fill-opacity="1" d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
        </svg>
    </div>
</section>

<!-- Category Stats Section -->
<section class="category-stats py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="stat-card bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 animate-slideInUp" data-animation="slideInUp" data-delay="0.1">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                        <i class="fas fa-briefcase text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800 counter-animate" data-count="<?= count($jobs) ?>"><?= count($jobs) ?></h3>
                        <p class="text-gray-600">Available Jobs</p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 animate-slideInUp" data-animation="slideInUp" data-delay="0.2">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                        <i class="fas fa-building text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800 counter-animate" data-count="<?= min(count($jobs) * 3, 50) ?>"><?= min(count($jobs) * 3, 50) ?></h3>
                        <p class="text-gray-600">Companies Hiring</p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 animate-slideInUp" data-animation="slideInUp" data-delay="0.3">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                        <i class="fas fa-user-tie text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800 counter-animate" data-count="<?= min(count($jobs) * 20, 500) ?>"><?= min(count($jobs) * 20, 500) ?></h3>
                        <p class="text-gray-600">Job Seekers</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Jobs Section -->
<section class="category-jobs-section py-16 bg-white">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="section-header flex justify-between items-center mb-10 animate-fadeIn" data-animation="fadeIn">
            <h2 class="section-title text-3xl font-bold text-gray-800 relative pl-4">
                <span class="absolute left-0 top-0 bottom-0 w-1 bg-blue-600 rounded-full"></span>
                <?= htmlspecialchars($category_name) ?> Jobs
            </h2>
            <a href="jobs.php" class="view-all text-blue-600 font-medium hover:text-blue-700 flex items-center gap-2 group">
                Browse All Jobs <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
            </a>
        </div>

        <!-- Jobs Grid -->
        <?php if (empty($jobs)): ?>
            <div class="text-center py-12 animate-fadeIn" data-animation="fadeIn">
                <div class="text-6xl text-gray-300 mb-4">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No jobs found in the <?= htmlspecialchars($category_name) ?> category</h3>
                <p class="text-gray-600 mb-6">Check back later or browse other job categories</p>
                <a href="jobs.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-300 shadow-md">
                    Browse All Jobs
                </a>
            </div>
        <?php else: ?>
            <div class="jobs-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" data-stagger="0.1" data-animation="slideInUp">
                <?php foreach($jobs as $index => $job): ?>
                    <div class="job-card bg-white rounded-xl shadow-md hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300 border border-gray-100 overflow-hidden animate-slideInUp" style="animation-delay: <?= $index * 0.1 ?>s;">
                        <!-- Job Card Header -->
                        <div class="job-card-header p-6 border-b border-gray-100">
                            <div class="flex items-start gap-4">
                                <!-- Company Logo -->
                                <div class="company-logo flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                                    <?php if (isset($job['company_logo']) && !empty($job['company_logo'])): ?>
                                        <img src="<?= htmlspecialchars($job['company_logo']) ?>" alt="<?= htmlspecialchars($job['employer_name']) ?>" class="w-full h-full object-contain">
                                    <?php else: ?>
                                        <div class="text-xl text-gray-400">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Job Title and Company -->
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-800 mb-1 hover:text-blue-600 transition-colors">
                                        <a href="job_details.php?id=<?= $job['id'] ?>"><?= htmlspecialchars($job['title']) ?></a>
                                    </h3>
                                    <p class="text-gray-600"><?= htmlspecialchars($job['employer_name']) ?></p>
                                </div>
                            </div>
                            
                            <!-- Job Meta -->
                            <div class="job-meta mt-4 space-y-2">
                                <p class="location text-gray-600 flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i> <?= htmlspecialchars($job['location']) ?>
                                </p>
                                <div class="flex items-center justify-between">
                                    <p class="job-type text-gray-600 flex items-center gap-2">
                                        <i class="fas fa-briefcase text-blue-600"></i> <?= ucfirst(str_replace('-', ' ', $job['type'])) ?>
                                    </p>
                                    <p class="salary text-green-600 font-semibold">
                                        $<?= number_format($job['salary'], 0, ',', ',') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Job Card Body -->
                        <div class="job-card-body p-6">
                            <!-- Job Description -->
                            <p class="description text-gray-600 mb-4 line-clamp-3"><?= htmlspecialchars(substr($job['description'], 0, 150)) ?>...</p>
                            
                            <!-- Job Skills -->
                            <div class="job-skills flex flex-wrap gap-2 mb-6">
                                <?php 
                                $skills = isset($job['skills']) ? explode(',', $job['skills']) : ['Communication', 'Teamwork', 'Problem Solving'];
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
                            
                            <!-- Job Card Footer -->
                            <div class="job-card-footer flex justify-between items-center">
                                <!-- Fix: Changed apply_job.php to apply_jobs.php to match the actual file name -->
                                <a href="apply_jobs.php?job_id=<?= $job['id'] ?>" class="apply-now-btn bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-transform">
                                    Apply Now <i class="fas fa-paper-plane"></i>
                                </a>
                                <button class="bookmark-job-btn text-gray-400 hover:text-blue-600 transition-colors" data-job-id="<?= $job['id'] ?>">
                                    <i class="far fa-bookmark text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- View More Button -->
            <div class="text-center mt-12 animate-fadeIn" data-animation="fadeIn" data-delay="0.5">
                <a href="jobs.php?category=<?= $category_id ?>" class="inline-flex items-center gap-2 bg-white border border-blue-600 text-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition-colors duration-300 font-medium">
                    View All <?= htmlspecialchars($category_name) ?> Jobs <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Related Categories Section -->
<section class="related-categories py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-800 mb-10 text-center animate-fadeIn" data-animation="fadeIn">
            Explore Related Categories
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" data-stagger="0.1" data-animation="slideInUp">
            <?php 
            // Get related categories (excluding current category)
            $relatedCategories = array_filter($categories, function($cat) use ($category_id) {
                return $cat['id'] != $category_id;
            });
            
            // Display up to 8 related categories
            $relatedCategories = array_slice($relatedCategories, 0, 8);
            
            foreach($relatedCategories as $index => $cat): 
            ?>
                <a href="category_jobs.php?category=<?= $cat['id'] ?>" class="category-card bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center group animate-slideInUp" style="animation-delay: <?= $index * 0.1 ?>s;">
                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <i class="<?= getCategoryIcon($cat['name']) ?> text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors"><?= htmlspecialchars($cat['name']) ?></h3>
                    <p class="text-gray-600 text-sm"><?= rand(5, 50) ?> open positions</p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Job Seeker CTA Section -->
<section class="job-seeker-cta py-16 bg-blue-600 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-pattern opacity-10"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center animate-fadeIn" data-animation="fadeIn">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Find Your Next <?= htmlspecialchars($category_name) ?> Job?</h2>
            <p class="text-xl text-blue-100 mb-8">Create an account to get personalized job recommendations, save your favorite jobs, and apply with just one click.</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="register.php" class="px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition-colors duration-300 font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-transform">
                    Create an Account
                </a>
                <a href="login.php" class="px-8 py-4 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors duration-300 font-bold">
                    Sign In
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript for the category jobs page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bookmark job functionality
    const bookmarkButtons = document.querySelectorAll('.bookmark-job-btn');
    
    bookmarkButtons.forEach(button => {
        button.addEventListener('click', function() {
            const jobId = this.dataset.jobId;
            const icon = this.querySelector('i');
            
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-blue-600');
                
                // Add pulse animation
                icon.classList.add('animate-pulse');
                setTimeout(() => {
                    icon.classList.remove('animate-pulse');
                }, 1000);
                
                // Show toast notification
                showToast('Job saved to your bookmarks', 'success');
            } else {
                icon.classList.remove('fas', 'text-blue-600');
                icon.classList.add('far');
                
                // Show toast notification
                showToast('Job removed from your bookmarks', 'info');
            }
            
            // In a real implementation, you would send an AJAX request to save/unsave the job
            // For now, we'll just simulate it
            console.log('Toggle bookmark for job ID:', jobId);
        });
    });
    
    // Counter animation for stats
    const counterElements = document.querySelectorAll('.counter-animate');
    
    if (counterElements.length > 0) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        const target = parseInt(element.dataset.count);
                        animateCounter(element, target);
                        observer.unobserve(element);
                    }
                });
            },
            { threshold: 0.5 }
        );
        
        counterElements.forEach((element) => {
            observer.observe(element);
        });
    }
    
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
    
    // Function to animate counter
    function animateCounter(element, target, duration = 2000) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                clearInterval(timer);
                element.textContent = target;
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }
    
    // Function to show toast notification
    function showToast(message, type = 'info') {
        // Check if toast container exists, if not create it
        let toastContainer = document.querySelector('.toast-container');
        
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container fixed bottom-4 right-4 z-50';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast flex items-center p-4 mb-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ${getToastClass(type)}`;
        
        // Toast icon
        const icon = document.createElement('div');
        icon.className = 'mr-3';
        icon.innerHTML = getToastIcon(type);
        
        // Toast message
        const messageEl = document.createElement('div');
        messageEl.className = 'flex-grow';
        messageEl.textContent = message;
        
        // Close button
        const closeBtn = document.createElement('button');
        closeBtn.className = 'ml-4 text-gray-400 hover:text-gray-600 transition-colors';
        closeBtn.innerHTML = '<i class="fas fa-times"></i>';
        closeBtn.addEventListener('click', () => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                toast.remove();
            }, 300);
        });
        
        // Append elements to toast
        toast.appendChild(icon);
        toast.appendChild(messageEl);
        toast.appendChild(closeBtn);
        
        // Add toast to container
        toastContainer.appendChild(toast);
        
        // Show toast
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);
        
        // Auto hide toast after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 5000);
    }
    
    // Helper function to get toast class based on type
    function getToastClass(type) {
        switch (type) {
            case 'success':
                return 'bg-green-600 text-white';
            case 'error':
                return 'bg-red-600 text-white';
            case 'warning':
                return 'bg-yellow-500 text-white';
            case 'info':
            default:
                return 'bg-blue-600 text-white';
        }
    }
    
    // Helper function to get toast icon based on type
    function getToastIcon(type) {
        switch (type) {
            case 'success':
                return '<i class="fas fa-check-circle text-2xl"></i>';
            case 'error':
                return '<i class="fas fa-exclamation-circle text-2xl"></i>';
            case 'warning':
                return '<i class="fas fa-exclamation-triangle text-2xl"></i>';
            case 'info':
            default:
                return '<i class="fas fa-info-circle text-2xl"></i>';
        }
    }
});

// Helper function to get category icon based on category name
function getCategoryIcon(categoryName) {
    const categoryIcons = {
        'Technology': 'fas fa-laptop-code',
        'Healthcare': 'fas fa-heartbeat',
        'Finance': 'fas fa-chart-line',
        'Education': 'fas fa-graduation-cap',
        'Marketing': 'fas fa-bullhorn',
        'Sales': 'fas fa-handshake',
        'Customer Service': 'fas fa-headset',
        'Engineering': 'fas fa-cogs',
        'Design': 'fas fa-paint-brush',
        'Human Resources': 'fas fa-users',
        'Administrative': 'fas fa-tasks',
        'Legal': 'fas fa-balance-scale',
        'Construction': 'fas fa-hard-hat',
        'Retail': 'fas fa-shopping-cart',
        'Hospitality': 'fas fa-concierge-bell',
        'Transportation': 'fas fa-truck',
        'Manufacturing': 'fas fa-industry',
        'Media': 'fas fa-photo-video',
        'Real Estate': 'fas fa-home',
        'Science': 'fas fa-flask'
    };
    
    // Return icon for category or default icon
    return categoryIcons[categoryName] || 'fas fa-briefcase';
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

/* Background pattern */
.bg-pattern {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.2'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
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

/* Line clamp for text truncation */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Category card styling */
.category-card {
    transition: all 0.3s ease;
}

.category-card:hover {
    transform: translateY(-5px);
}

/* Toast styling */
.toast-container {
    pointer-events: none;
}

.toast {
    pointer-events: auto;
    max-width: 350px;
}
</style>

<?php include 'includes/footer.php'; ?>