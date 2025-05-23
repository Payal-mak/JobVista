<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['message'] = "Please log in to apply for a job.";
    $_SESSION['message_type'] = "warning";
    header("Location: login.php");
    exit();
}

// Get job ID from URL
$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;
$job = getJobById($job_id);

if (!$job) {
    $_SESSION['message'] = "Job not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: category_jobs.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $contact = sanitize($_POST['contact']);
    
    // Handle resume upload
    $resume_path = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/resumes/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = uniqid() . '_' . basename($_FILES['resume']['name']);
        $file_path = $upload_dir . $file_name;
        
        // Validate file type (PDF only)
        $file_type = mime_content_type($_FILES['resume']['tmp_name']);
        if ($file_type !== 'application/pdf') {
            $_SESSION['message'] = "Only PDF files are allowed for resumes.";
            $_SESSION['message_type'] = "danger";
            header("Location: apply_job.php?job_id=$job_id");
            exit();
        }
        
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $file_path)) {
            $resume_path = $file_path;
        } else {
            $_SESSION['message'] = "Failed to upload resume.";
            $_SESSION['message_type'] = "danger";
            header("Location: apply_job.php?job_id=$job_id");
            exit();
        }
    } else {
        $_SESSION['message'] = "Please upload a resume.";
        $_SESSION['message_type'] = "danger";
        header("Location: apply_job.php?job_id=$job_id");
        exit();
    }

    // Save application
    $result = applyForJob($job_id, $_SESSION['user_id'], $name, $email, $contact, $resume_path);
    if ($result === true) {
        $_SESSION['message'] = "Application submitted successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: category_jobs.php?category=" . $job['category_id']);
        exit();
    } else {
        $_SESSION['message'] = $result;
        $_SESSION['message_type'] = "danger";
        header("Location: apply_job.php?job_id=$job_id");
        exit();
    }
}

$title = "Apply for " . htmlspecialchars($job['title']);
?>

<?php include 'includes/header.php'; ?>

<!-- Application Form Section -->
<section class="apply-job-section py-16 bg-gradient-to-b from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Job Application Header -->
            <div class="text-center mb-10 animate-fadeIn" data-animation="fadeIn">
                <h2 class="text-3xl font-bold text-gray-800 mb-3 relative inline-block">
                    Apply for <span class="text-blue-600"><?= htmlspecialchars($job['title']) ?></span>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-600 transform scale-x-0 transition-transform duration-300 origin-left hover:scale-x-100"></div>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Complete the form below to submit your application. Make sure your resume is up-to-date and highlights your relevant skills and experience.</p>
            </div>
            
            <!-- Application Form Card -->
            <div class="bg-white p-8 rounded-xl shadow-lg transform transition-all duration-300 hover:shadow-xl animate-slideInUp" data-animation="slideInUp" data-delay="0.2">
                <!-- Progress Steps -->
                <div class="flex justify-between items-center mb-8 relative">
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/2"></div>
                    <div class="flex justify-between w-full relative z-10">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold mb-2 shadow-md">1</div>
                            <span class="text-sm font-medium text-gray-700">Personal Info</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold mb-2">2</div>
                            <span class="text-sm font-medium text-gray-500">Resume</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold mb-2">3</div>
                            <span class="text-sm font-medium text-gray-500">Complete</span>
                        </div>
                    </div>
                </div>
                
                <form action="apply_job.php?job_id=<?= $job_id ?>" method="POST" enctype="multipart/form-data" class="application-form" id="applicationForm">
                    <!-- Step 1: Personal Information -->
                    <div class="step-content" id="step1">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Personal Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="form-group animate-fadeIn" style="animation-delay: 0.1s;">
                                <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-400">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required 
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                </div>
                            </div>
                            
                            <div class="form-group animate-fadeIn" style="animation-delay: 0.2s;">
                                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-400">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['qualification'] ?? ''); ?>"
                                    required 
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-6 animate-fadeIn" style="animation-delay: 0.3s;">
                            <label for="contact" class="block text-gray-700 font-medium mb-2">Contact Number</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="text" id="contact" name="contact" required 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                    placeholder="Enter your phone number">
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6 animate-fadeIn" style="animation-delay: 0.4s;">
                            <button type="button" class="next-step bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-300 flex items-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-transform">
                                Continue to Resume <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Resume Upload -->
                    <div class="step-content hidden" id="step2">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Upload Your Resume</h3>
                        
                        <div class="form-group mb-6 animate-fadeIn">
                            <label for="resume" class="block text-gray-700 font-medium mb-2">Resume (PDF only)</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors duration-200 cursor-pointer" id="dropzone">
                                <input type="file" id="resume" name="resume" accept=".pdf" required class="hidden">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fas fa-file-pdf text-5xl text-gray-400"></i>
                                    <div class="text-gray-600">
                                        <p class="font-medium">Drag and drop your resume here</p>
                                        <p class="text-sm">or</p>
                                        <button type="button" id="browseFiles" class="text-blue-600 hover:text-blue-700 font-medium">Browse files</button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Maximum file size: 5MB</p>
                                </div>
                                <div id="file-selected" class="hidden mt-4 p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-file-pdf text-blue-600"></i>
                                        <span class="file-name text-gray-700 font-medium"></span>
                                        <button type="button" id="remove-file" class="ml-auto text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-6 animate-fadeIn">
                            <button type="button" class="prev-step bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors duration-300 flex items-center gap-2">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-300 flex items-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-transform">
                                Submit Application <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Job Summary Card -->
            <div class="mt-8 bg-white p-6 rounded-xl shadow-md animate-slideInUp" data-animation="slideInUp" data-delay="0.4">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-briefcase text-blue-600"></i> Job Summary
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start gap-3">
                        <div class="text-blue-600"><i class="fas fa-building"></i></div>
                        <div>
                            <p class="text-gray-500 text-sm">Company</p>
                            <p class="font-medium"><?= htmlspecialchars($job['employer_name'] ?? 'Company Name') ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="text-blue-600"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <p class="text-gray-500 text-sm">Location</p>
                            <p class="font-medium"><?= htmlspecialchars($job['location'] ?? 'Location') ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="text-blue-600"><i class="fas fa-money-bill-wave"></i></div>
                        <div>
                            <p class="text-gray-500 text-sm">Salary</p>
                            <p class="font-medium">$<?= number_format($job['salary'] ?? 0) ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="text-blue-600"><i class="fas fa-clock"></i></div>
                        <div>
                            <p class="text-gray-500 text-sm">Job Type</p>
                            <p class="font-medium"><?= ucfirst(str_replace('-', ' ', $job['type'] ?? 'Full Time')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript for the application form -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Multi-step form functionality
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const nextBtn = document.querySelector('.next-step');
    const prevBtn = document.querySelector('.prev-step');
    const progressSteps = document.querySelectorAll('.rounded-full');
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            step1.classList.add('hidden');
            step2.classList.remove('hidden');
            progressSteps[1].classList.remove('bg-gray-200', 'text-gray-600');
            progressSteps[1].classList.add('bg-blue-600', 'text-white');
            
            // Add animation
            step2.classList.add('animate-fadeIn');
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            step2.classList.add('hidden');
            step1.classList.remove('hidden');
            progressSteps[1].classList.add('bg-gray-200', 'text-gray-600');
            progressSteps[1].classList.remove('bg-blue-600', 'text-white');
        });
    }
    
    // File upload functionality
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('resume');
    const browseBtn = document.getElementById('browseFiles');
    const fileSelected = document.getElementById('file-selected');
    const fileName = document.querySelector('.file-name');
    const removeFileBtn = document.getElementById('remove-file');
    
    if (dropzone && fileInput) {
        // Handle click on browse button
        browseBtn.addEventListener('click', function() {
            fileInput.click();
        });
        
        // Handle click on dropzone
        dropzone.addEventListener('click', function(e) {
            if (e.target !== removeFileBtn && e.target !== removeFileBtn.querySelector('i')) {
                fileInput.click();
            }
        });
        
        // Handle drag and drop
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropzone.classList.add('border-blue-500', 'bg-blue-50');
        });
        
        dropzone.addEventListener('dragleave', function() {
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
        });
        
        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateFileInfo();
            }
        });
        
        // Handle file selection
        fileInput.addEventListener('change', updateFileInfo);
        
        function updateFileInfo() {
            if (fileInput.files.length) {
                const file = fileInput.files[0];
                
                // Check if file is PDF
                if (file.type !== 'application/pdf') {
                    alert('Only PDF files are allowed.');
                    fileInput.value = '';
                    return;
                }
                
                // Check file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB limit.');
                    fileInput.value = '';
                    return;
                }
                
                fileName.textContent = file.name;
                fileSelected.classList.remove('hidden');
                dropzone.classList.add('border-blue-500');
            }
        }
        
        // Handle remove file
        if (removeFileBtn) {
            removeFileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.value = '';
                fileSelected.classList.add('hidden');
                dropzone.classList.remove('border-blue-500');
            });
        }
    }
    
    // Form validation
    const applicationForm = document.getElementById('applicationForm');
    
    if (applicationForm) {
        applicationForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const contact = document.getElementById('contact');
            
            let isValid = true;
            
            // Validate name
            if (!name.value.trim()) {
                showError(name, 'Name is required');
                isValid = false;
            } else {
                removeError(name);
            }
            
            // Validate email
            if (!email.value.trim()) {
                showError(email, 'Email is required');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                showError(email, 'Please enter a valid email address');
                isValid = false;
            } else {
                removeError(email);
            }
            
            // Validate contact
            if (!contact.value.trim()) {
                showError(contact, 'Contact number is required');
                isValid = false;
            } else {
                removeError(contact);
            }
            
            // Validate resume
            if (!fileInput.files.length) {
                showError(fileInput, 'Please upload your resume');
                dropzone.classList.add('border-red-500');
                isValid = false;
            } else {
                removeError(fileInput);
                dropzone.classList.remove('border-red-500');
            }
            
            if (!isValid) {
                e.preventDefault();
            } else {
                // Show loading state
                const submitBtn = applicationForm.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                
                // Update progress step
                progressSteps[2].classList.remove('bg-gray-200', 'text-gray-600');
                progressSteps[2].classList.add('bg-blue-600', 'text-white');
            }
        });
    }
    
    function showError(input, message) {
        const formGroup = input.closest('.form-group');
        let errorElement = formGroup.querySelector('.error-message');
        
        if (!errorElement) {
            errorElement = document.createElement('p');
            errorElement.className = 'error-message text-red-500 text-sm mt-1';
            formGroup.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
        input.classList.add('border-red-500');
    }
    
    function removeError(input) {
        const formGroup = input.closest('.form-group');
        const errorElement = formGroup.querySelector('.error-message');
        
        if (errorElement) {
            errorElement.remove();
        }
        
        input.classList.remove('border-red-500');
    }
    
    function isValidEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
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
});
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

/* Form styling */
.form-group {
    position: relative;
}

.form-group input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Transition effects */
.step-content {
    transition: all 0.3s ease;
}

/* Dropzone styling */
#dropzone {
    transition: all 0.2s ease;
}

#dropzone:hover {
    background-color: rgba(37, 99, 235, 0.05);
}
</style>

<?php include 'includes/footer.php'; ?>