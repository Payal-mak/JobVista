<!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0 animate-fadeIn">
                    <h5 class="mb-3 fw-bold">Job Portal</h5>
                    <p class="mb-3">Find your dream job or the perfect candidate with our comprehensive job portal platform.</p>
                    <div class="social-icons d-flex gap-3">
                        <a href="#" class="text-white hover-effect"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white hover-effect"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white hover-effect"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-white hover-effect"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="col-md-2 mb-4 mb-md-0 animate-fadeIn" style="animation-delay: 100ms;">
                    <h5 class="mb-3 fw-bold">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-white text-decoration-none hover-effect">Home</a></li>
                        <li class="mb-2"><a href="jobs.php" class="text-white text-decoration-none hover-effect">Browse Jobs</a></li>
                        <li class="mb-2"><a href="register.php" class="text-white text-decoration-none hover-effect">Register</a></li>
                        <li class="mb-2"><a href="login.php" class="text-white text-decoration-none hover-effect">Login</a></li>
                    </ul>
                </div>
                
                <div class="col-md-2 mb-4 mb-md-0 animate-fadeIn" style="animation-delay: 200ms;">
                    <h5 class="mb-3 fw-bold">For Employers</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="employer/post_job.php" class="text-white text-decoration-none hover-effect">Post a Job</a></li>
                        <li class="mb-2"><a href="employer/dashboard.php" class="text-white text-decoration-none hover-effect">Employer Dashboard</a></li>
                        <li class="mb-2"><a href="employer/manage_jobs.php" class="text-white text-decoration-none hover-effect">Manage Jobs</a></li>
                        <li class="mb-2"><a href="employer/applications.php" class="text-white text-decoration-none hover-effect">View Applications</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4 animate-fadeIn" style="animation-delay: 300ms;">
                    <h5 class="mb-3 fw-bold">Newsletter</h5>
                    <p class="mb-3">Subscribe to our newsletter for the latest job updates.</p>
                    <form class="newsletter-form">
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Your email address" required>
                            <button class="btn btn-primary" type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <hr class="my-4 border-gray-700">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0">&copy; <?= date('Y') ?> Job Portal. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#" class="text-white text-decoration-none hover-effect">Privacy Policy</a></li>
                        <li class="list-inline-item ms-3"><a href="#" class="text-white text-decoration-none hover-effect">Terms of Service</a></li>
                        <li class="list-inline-item ms-3"><a href="#" class="text-white text-decoration-none hover-effect">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced Scripts -->
    <script src="assets/js/enhanced-scripts.js"></script>
    
    <script>
        // Hide preloader when page is loaded
        window.addEventListener('load', function() {
            const preloader = document.querySelector('.preloader');
            preloader.classList.add('hide');
            
            // Remove preloader from DOM after animation completes
            setTimeout(() => {
                preloader.remove();
            }, 500);
        });
        
        // Add hover effect to social icons
        document.querySelectorAll('.hover-effect').forEach(element => {
            element.addEventListener('mouseenter', function() {
                this.style.opacity = '0.8';
                this.style.transform = 'translateY(-3px)';
            });
            
            element.addEventListener('mouseleave', function() {
                this.style.opacity = '1';
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Initialize Bootstrap tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>