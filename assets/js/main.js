/**
 * Main JavaScript file for the Job Portal
 * Handles common functionality across all pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize common components
    initMobileMenu();
    initSearchForm();
    initAuthForms();
    initJobApplications();
    initJobBookmarks();
    initCategoryBookmarks(); // Added
    initMessageSystem();
    initNotifications();
    
    // Setup any global event listeners
    setupGlobalListeners();
});

/**
 * Initialize mobile menu functionality (unchanged)
 */
function initMobileMenu() {
    const mobileMenuToggle = document.querySelector('.navbar-toggler');
    const mobileMenu = document.querySelector('.navbar-collapse');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('show');
        });
    }
    
    // Close mobile menu when clicking a nav link
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                mobileMenu.classList.remove('show');
            }
        });
    });
}

/**
 * Initialize search form functionality (unchanged)
 */
function initSearchForm() {
    const searchForm = document.querySelector('.search-form');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchInput = this.querySelector('input[name="search"]');
            const locationInput = this.querySelector('input[name="location"]');
            const categorySelect = this.querySelector('select[name="category"]');
            
            const searchTerm = searchInput.value.trim();
            const location = locationInput.value.trim();
            const category = categorySelect.value;
            
            // Build query string
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            if (location) params.append('location', location);
            if (category) params.append('category', category);
            
            if (searchTerm || location || category) {
                window.location.href = `jobs.php?${params.toString()}`;
            }
        });
    }
}

/**
 * Initialize authentication forms (login/register) (unchanged)
 */
function initAuthForms() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAuthForm(this, 'login');
        });
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAuthForm(this, 'register');
        });
        
        // Handle role selection
        const roleOptions = document.querySelectorAll('.role-option');
        roleOptions.forEach(option => {
            option.addEventListener('click', function() {
                roleOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('role').value = this.dataset.role;
            });
        });
    }
}

/**
 * Submit authentication form via AJAX (unchanged)
 */
function submitAuthForm(form, action) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    fetch(`includes/ajax.php?action=${action}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message and redirect
            Swal.fire({
                title: 'Success',
                text: data.message || (action === 'login' ? 'Login successful!' : 'Registration successful!'),
                icon: 'success'
            }).then(() => {
                window.location.href = data.redirect || 'index.php';
            });
        } else {
            // Show error message
            Swal.fire({
                title: 'Error',
                text: data.message || 'Something went wrong',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error',
            text: 'Failed to process request',
            icon: 'error'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

/**
 * Initialize job application functionality (unchanged)
 */
function initJobApplications() {
    document.querySelectorAll('.apply-job-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const jobId = this.dataset.jobId;
            showApplicationModal(jobId);
        });
    });
}

/**
 * Show job application modal (unchanged)
 */
function showApplicationModal(jobId) {
    // Fetch job details first
    fetch(`includes/ajax.php?action=get_job_details&job_id=${jobId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Populate modal with job details
            const modal = new bootstrap.Modal(document.getElementById('applicationModal'));
            const modalTitle = document.getElementById('applicationModalLabel');
            const jobTitleElement = document.getElementById('modalJobTitle');
            
            modalTitle.textContent = `Apply for ${data.job.title}`;
            jobTitleElement.textContent = data.job.title;
            document.getElementById('job_id').value = jobId;
            
            // Show the modal
            modal.show();
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'Failed to load job details',
                icon: 'error'
            });
        }
    });
    
    // Handle form submission
    const applicationForm = document.getElementById('applicationForm');
    if (applicationForm) {
        applicationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitJobApplication(this);
        });
    }
}

/**
 * Submit job application via AJAX (unchanged)
 */
function submitJobApplication(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    fetch('includes/ajax.php?action=apply_job', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message and close modal
            Swal.fire({
                title: 'Success',
                text: data.message || 'Application submitted successfully!',
                icon: 'success'
            }).then(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('applicationModal'));
                modal.hide();
                
                // Update UI if needed
                const applyBtn = document.querySelector(`.apply-job-btn[data-job-id="${formData.get('job_id')}"]`);
                if (applyBtn) {
                    applyBtn.disabled = true;
                    applyBtn.innerHTML = '<i class="fas fa-check"></i> Applied';
                }
            });
        } else {
            // Show error message
            Swal.fire({
                title: 'Error',
                text: data.message || 'Failed to submit application',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error',
            text: 'Failed to process request',
            icon: 'error'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

/**
 * Initialize job bookmark functionality (unchanged)
 */
function initJobBookmarks() {
    document.querySelectorAll('.bookmark-job-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const jobId = this.dataset.jobId;
            const isSaved = this.classList.contains('saved');
            
            toggleJobBookmark(jobId, isSaved, this);
        });
    });
}

/**
 * Toggle job bookmark status (unchanged)
 */
function toggleJobBookmark(jobId, isSaved, button) {
    fetch(`includes/ajax.php?action=${isSaved ? 'remove_bookmark' : 'save_bookmark'}&job_id=${jobId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button state
            if (isSaved) {
                button.classList.remove('saved');
                button.innerHTML = '<i class="far fa-bookmark"></i> Save Job';
            } else {
                button.classList.add('saved');
                button.innerHTML = '<i class="fas fa-bookmark"></i> Saved';
            }
            
            // Show toast notification
            const noty = new Noty({
                type: 'success',
                text: data.message || (isSaved ? 'Job removed from saved jobs' : 'Job saved successfully'),
                timeout: 3000
            }).show();
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'Failed to update bookmark',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error',
            text: 'Failed to process request',
            icon: 'error'
        });
    });
}

/**
 * Initialize category bookmark functionality (Added)
 */
function initCategoryBookmarks() {
    document.querySelectorAll('.bookmark-category-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryId = this.dataset.categoryId;
            const isSaved = this.classList.contains('saved');
            
            toggleCategoryBookmark(categoryId, isSaved, this);
        });
    });
}

/**
 * Toggle category bookmark status (Added)
 */
function toggleCategoryBookmark(categoryId, isSaved, button) {
    fetch(`includes/ajax.php?action=${isSaved ? 'remove_category_bookmark' : 'save_category_bookmark'}&category_id=${categoryId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button state
            if (isSaved) {
                button.classList.remove('saved');
                button.innerHTML = '<i class="far fa-bookmark"></i>';
            } else {
                button.classList.add('saved');
                button.innerHTML = '<i class="fas fa-bookmark"></i>';
            }
            
            // Show toast notification
            const noty = new Noty({
                type: 'success',
                text: data.message || (isSaved ? 'Category removed from bookmarks' : 'Category bookmarked successfully'),
                timeout: 3000
            }).show();
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'Failed to update bookmark',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error',
            text: 'Failed to process request',
            icon: 'error'
        });
    });
}

/**
 * Initialize message system functionality (unchanged)
 */
function initMessageSystem() {
    // Handle sending messages
    const messageForm = document.getElementById('messageForm');
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage(this);
        });
    }
    
    // Mark messages as read when viewed
    const messageThread = document.getElementById('messageThread');
    if (messageThread) {
        const unreadMessages = messageThread.querySelectorAll('.message.unread');
        if (unreadMessages.length > 0) {
            const messageIds = Array.from(unreadMessages).map(msg => msg.dataset.messageId);
            markMessagesAsRead(messageIds);
        }
    }
}

/**
 * Send message via AJAX (unchanged)
 */
function sendMessage(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    fetch('includes/ajax.php?action=send_message', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear message input
            form.querySelector('textarea').value = '';
            
            // Add message to thread if on message page
            if (data.message && document.getElementById('messageThread')) {
                addMessageToThread(data.message);
            }
            
            // Show success notification
            const noty = new Noty({
                type: 'success',
                text: data.message || 'Message sent successfully',
                timeout: 3000
            }).show();
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'Failed to send message',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error',
            text: 'Failed to process request',
            icon: 'error'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

/**
 * Initialize notification system (unchanged)
 */
function initNotifications() {
    // Check for new notifications periodically
    if (document.getElementById('notificationsDropdown')) {
        setInterval(checkNewNotifications, 60000); // Check every minute
        
        // Mark notifications as read when dropdown is shown
        const dropdown = new bootstrap.Dropdown(document.getElementById('notificationsDropdown'));
        document.getElementById('notificationsDropdown').addEventListener('shown.bs.dropdown', function() {
            markNotificationsAsRead();
        });
    }
}

/**
 * Setup global event listeners (unchanged)
 */
function setupGlobalListeners() {
    // Handle logout button
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Logout',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = this.href;
                }
            });
        });
    }
    
    // Initialize tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}