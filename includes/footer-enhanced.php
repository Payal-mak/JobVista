</main> <!-- Close the main tag that was opened in header.php -->

<!-- Footer -->
<footer class="footer">
    <div class="container mx-auto px-4">
        <!-- Footer Content -->
        <div class="footer-content">
            <!-- About Column -->
            <div class="footer-column animate-fadeIn">
                <a href="index.php" class="footer-logo text-white text-2xl font-bold flex items-center gap-2 animate-pulse">
                    <span>JobVista</span>
                </a>
                <p class="footer-description mt-4">
                    Connecting talented job seekers with top employers worldwide. Explore opportunities and grow your career with us.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link animate-float">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link animate-float" style="animation-delay: 0.2s">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/payal-makwana-a2b73829a/" class="social-link animate-float" style="animation-delay: 0.4s">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-link animate-float" style="animation-delay: 0.6s">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>

            <!-- For Job Seekers Column -->
            <div class="footer-column animate-fadeIn" style="animation-delay: 0.2s">
                <h3 class="footer-heading text-white">For Job Seekers</h3>
                <ul class="footer-links" data-stagger="0.1" data-animation="slideInLeft">
                    <li><a href="jobs.php">Browse Jobs</a></li>
                    <li><a href="register.php">Create Account</a></li>
                    <li><a href="job_seeker/dashboard.php">Job Seeker Dashboard</a></li>
                    <li><a href="faq.php">Job Search Tips</a></li>
                </ul>
            </div>

            <!-- For Employers Column -->
            <div class="footer-column animate-fadeIn" style="animation-delay: 0.4s">
                <h3 class="footer-heading text-white">For Employers</h3>
                <ul class="footer-links" data-stagger="0.1" data-animation="slideInLeft">
                    <li><a href="employer/post_job.php">Post a Job</a></li>
                    <li><a href="register.php?type=employer">Employer Account</a></li>
                    <li><a href="employer/dashboard.php">Employer Dashboard</a></li>
                    <li><a href="pricing.php">Pricing Plans</a></li>
                </ul>
            </div>

            <!-- Contact Us Column -->
            <div class="footer-column animate-fadeIn" style="animation-delay: 0.6s">
                <h3 class="footer-heading text-white">Contact Us</h3>
                <ul class="contact-info" data-stagger="0.1" data-animation="slideInRight">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>123 Job Street, Career City</span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>+1 (234) 567-8900</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>info@jobvista.com</span>
                    </li>
                </ul>
                <!-- Newsletter Subscription -->
                <div class="newsletter mt-6 animate-fadeIn" style="animation-delay: 0.8s">
                    <h4 class="text-white text-sm font-semibold">Subscribe to Our Newsletter</h4>
                    <form class="newsletter-form mt-2" action="#" method="post">
                        <input type="email" class="newsletter-input form-control-enhanced" placeholder="Your Email" required>
                        <button type="submit" class="newsletter-button btn-hover">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="copyright animate-fadeIn">
                © <?php echo date('Y'); ?> JobVista. All rights reserved.
            </div>
            <div class="footer-bottom-links animate-fadeIn" style="animation-delay: 0.2s">
                <a href="privacy.php">Privacy Policy</a>
                <a href="terms.php">Terms of Service</a>
                <a href="sitemap.php">Sitemap</a>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<a href="#" class="back-to-top fixed bottom-5 right-5 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-colors duration-200 hidden animate-float">
    <i class="fas fa-arrow-up"></i>
</a>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="assets/js/animations.js"></script>
<script src="assets/js/enhanced-ui.js"></script>
<script src="assets/js/main.js"></script>

<!-- Initialize Animations -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add animation classes to elements with data-animation attribute
        const animatedElements = document.querySelectorAll('[data-animation]');
        
        if (animatedElements.length > 0) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
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
            }, { threshold: 0.1 });
            
            animatedElements.forEach(element => {
                observer.observe(element);
            });
        }
        
        // Back to top button
        const backToTop = document.querySelector('.back-to-top');
        if (backToTop) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTop.classList.remove('hidden');
                } else {
                    backToTop.classList.add('hidden');
                }
            });

            backToTop.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });
</script>
</body>
</html>
