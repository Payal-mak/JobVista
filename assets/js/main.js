document.addEventListener("DOMContentLoaded", () => {
  // Initialize all animations and enhanced UI
  initAnimations();
  enhanceUI();

  // Initialize specific page enhancements
  if (document.querySelector(".job-card")) enhanceJobCards();
  if (document.querySelector(".dashboard-sidebar")) enhanceDashboard();
  if (document.querySelector(".profile-photo")) enhanceProfile();
  if (document.querySelector(".apply-job-section")) enhanceApplicationForm();
  if (document.querySelector(".job-details-page")) enhanceJobDetails();

  // Initialize back to top button
  initBackToTop();

  // Initialize form validations
  initFormValidations();

  // Initialize loading effects
  initLoadingEffects();

  // Initialize parallax effects
  initParallaxEffects();

  // Initialize tooltips
  initTooltips();

  // Initialize mobile menu
  initMobileMenu();

  // Initialize dark mode toggle
  initDarkMode();

  // Initialize notification system
  initNotifications();
});

// Function to initialize animations
function initAnimations() {
  // Add animation classes to elements with data-animation attribute
  const animatedElements = document.querySelectorAll("[data-animation]");

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
              element.style.opacity = "1";
            }, delay * 1000);

            observer.unobserve(element);
          }
        });
      },
      { threshold: 0.1 }
    );

    animatedElements.forEach((element) => {
      element.style.opacity = "0";
      observer.observe(element);
    });
  }

  // Add staggered animations to lists
  const staggeredLists = document.querySelectorAll("[data-stagger]");

  staggeredLists.forEach((list) => {
    const items = list.children;
    const animation = list.dataset.animation || "fadeIn";
    const baseDelay = Number.parseFloat(list.dataset.baseDelay) || 0;
    const staggerDelay = Number.parseFloat(list.dataset.stagger) || 0.1;

    Array.from(items).forEach((item, index) => {
      item.style.opacity = "0";
      item.dataset.animation = animation;
      item.dataset.delay = (baseDelay + index * staggerDelay).toString();
    });
  });

  // Add scroll animations to sections
  const sections = document.querySelectorAll("section");

  if (sections.length > 0) {
    const sectionObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("section-visible");
            sectionObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 }
    );

    sections.forEach((section) => {
      if (!section.hasAttribute("data-animation")) {
        section.classList.add("section-animate");
        sectionObserver.observe(section);
      }
    });
  }
}

// Function to enhance UI elements
function enhanceUI() {
  // Enhance buttons with hover effects
  const buttons = document.querySelectorAll(".btn, button:not(.btn-close)");
  buttons.forEach((button) => {
    if (
      !button.classList.contains("btn-hover") &&
      !button.hasAttribute("disabled")
    ) {
      button.classList.add("btn-hover");

      // Add ripple effect
      button.addEventListener("click", createRipple);
    }
  });

  // Enhance cards with hover effects
  const cards = document.querySelectorAll(".card, .job-card, .stat-card");
  cards.forEach((card) => {
    card.classList.add("card-hover");
  });

  // Enhance form controls
  const formControls = document.querySelectorAll(
    ".form-control, input[type='text'], input[type='email'], input[type='password'], textarea, select"
  );
  formControls.forEach((input) => {
    input.classList.add("form-control-enhanced");

    // Add focus animation
    input.addEventListener("focus", function () {
      this.parentElement.classList.add("input-focused");
    });

    input.addEventListener("blur", function () {
      this.parentElement.classList.remove("input-focused");
    });
  });

  // Enhance navigation links
  const navLinks = document.querySelectorAll(".nav-link, .navbar-nav a");
  navLinks.forEach((link) => {
    if (
      !link.classList.contains("dropdown-toggle") &&
      !link.classList.contains("nav-link-animated")
    ) {
      link.classList.add("nav-link-animated");
    }
  });

  // Enhance tables
  const tables = document.querySelectorAll("table");
  tables.forEach((table) => {
    if (!table.classList.contains("table")) {
      table.classList.add("table", "table-enhanced");
    }

    // Add hover effect to table rows
    const tableRows = table.querySelectorAll("tbody tr");
    tableRows.forEach((row) => {
      row.classList.add("table-row-hover");
    });
  });

  // Enhance badges
  const badges = document.querySelectorAll(".badge");
  badges.forEach((badge) => {
    badge.classList.add("badge-enhanced");
  });

  // Enhance alerts
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    if (!alert.querySelector(".alert-icon")) {
      const alertIcon = document.createElement("div");
      alertIcon.className = "alert-icon";

      if (alert.classList.contains("alert-success")) {
        alertIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
      } else if (alert.classList.contains("alert-danger")) {
        alertIcon.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
      } else if (alert.classList.contains("alert-warning")) {
        alertIcon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
      } else if (alert.classList.contains("alert-info")) {
        alertIcon.innerHTML = '<i class="fas fa-info-circle"></i>';
      }

      if (alertIcon.innerHTML) {
        alert.insertBefore(alertIcon, alert.firstChild);
      }
    }
  });
}

// Function to create ripple effect
function createRipple(event) {
  const button = event.currentTarget;

  const circle = document.createElement("span");
  const diameter = Math.max(button.clientWidth, button.clientHeight);
  const radius = diameter / 2;

  circle.style.width = circle.style.height = `${diameter}px`;
  circle.style.left = `${
    event.clientX - button.getBoundingClientRect().left - radius
  }px`;
  circle.style.top = `${
    event.clientY - button.getBoundingClientRect().top - radius
  }px`;
  circle.classList.add("ripple");

  const ripple = button.querySelector(".ripple");

  if (ripple) {
    ripple.remove();
  }

  button.appendChild(circle);
}

// Function to enhance job cards
function enhanceJobCards() {
  const jobCards = document.querySelectorAll(".job-card");

  jobCards.forEach((card, index) => {
    // Add staggered animation if not already set
    if (!card.hasAttribute("data-animation")) {
      card.style.opacity = "0";
      card.dataset.animation = "fadeIn";
      card.dataset.delay = (0.1 * index).toString();
    }

    // Add hover effect to job title
    const jobTitle = card.querySelector("h3 a");
    if (jobTitle) {
      jobTitle.style.transition = "color 0.3s ease";
      jobTitle.addEventListener("mouseover", function () {
        this.style.color = "#1491ea";
      });
      jobTitle.addEventListener("mouseout", function () {
        this.style.color = "";
      });
    }

    // Add animation to save button
    const saveBtn = card.querySelector(".btn-save, .bookmark-job-btn");
    if (saveBtn) {
      saveBtn.addEventListener("click", function (e) {
        const icon = this.querySelector("i");
        if (icon) {
          if (icon.classList.contains("far")) {
            icon.classList.remove("far");
            icon.classList.add("fas");
            icon.classList.add("animate-pulse");
            setTimeout(() => {
              icon.classList.remove("animate-pulse");
            }, 1000);
          } else {
            icon.classList.remove("fas");
            icon.classList.add("far");
          }
        }
      });
    }

    // Add animation to skill tags
    const skillTags = card.querySelectorAll(".skill-tag");
    skillTags.forEach((tag) => {
      tag.style.transition = "all 0.3s ease";
      tag.addEventListener("mouseover", function () {
        this.style.backgroundColor = "#1491ea";
        this.style.color = "white";
      });
      tag.addEventListener("mouseout", function () {
        this.style.backgroundColor = "";
        this.style.color = "";
      });
    });

    // Add hover effect to apply button
    const applyBtn = card.querySelector(".apply-now-btn");
    if (applyBtn) {
      applyBtn.addEventListener("mouseover", function () {
        this.classList.add("apply-btn-hover");
      });

      applyBtn.addEventListener("mouseout", function () {
        this.classList.remove("apply-btn-hover");
      });
    }
  });
}

// Function to enhance application form
function enhanceApplicationForm() {
  // Add animation to form steps
  const formSteps = document.querySelectorAll(".step-content");
  formSteps.forEach((step, index) => {
    if (!step.classList.contains("hidden")) {
      step.style.opacity = "0";
      step.dataset.animation = "fadeIn";
      step.dataset.delay = (0.2 * index).toString();
    }
  });

  // Add animation to form inputs
  const formInputs = document.querySelectorAll(
    ".apply-job-section input, .apply-job-section textarea"
  );
  formInputs.forEach((input, index) => {
    input.addEventListener("focus", function () {
      this.closest(".form-group").classList.add("input-focused");
    });

    input.addEventListener("blur", function () {
      if (!this.value) {
        this.closest(".form-group").classList.remove("input-focused");
      }
    });
  });

  // Add animation to dropzone
  const dropzone = document.getElementById("dropzone");
  if (dropzone) {
    dropzone.addEventListener("dragover", function (e) {
      e.preventDefault();
      this.classList.add("dropzone-active");
    });

    dropzone.addEventListener("dragleave", function () {
      this.classList.remove("dropzone-active");
    });

    dropzone.addEventListener("drop", function (e) {
      e.preventDefault();
      this.classList.remove("dropzone-active");
      this.classList.add("dropzone-success");

      setTimeout(() => {
        this.classList.remove("dropzone-success");
      }, 1500);
    });
  }
}

// Function to enhance job details page
function enhanceJobDetails() {
  // Add animation to job actions
  const jobActions = document.querySelectorAll(
    ".job-actions button, .job-actions a"
  );
  jobActions.forEach((action) => {
    action.addEventListener("mouseover", function () {
      this.classList.add("action-hover");
    });

    action.addEventListener("mouseout", function () {
      this.classList.remove("action-hover");
    });
  });

  // Add animation to job meta items
  const jobMetaItems = document.querySelectorAll(".job-meta > div");
  jobMetaItems.forEach((item, index) => {
    item.style.opacity = "0";
    item.dataset.animation = "fadeIn";
    item.dataset.delay = (0.2 + index * 0.1).toString();
  });

  // Add animation to similar jobs
  const similarJobs = document.querySelectorAll(".similar-jobs a");
  similarJobs.forEach((job, index) => {
    job.style.opacity = "0";
    job.dataset.animation = "slideInUp";
    job.dataset.delay = (0.3 + index * 0.1).toString();

    job.addEventListener("mouseover", function () {
      this.classList.add("similar-job-hover");
    });

    job.addEventListener("mouseout", function () {
      this.classList.remove("similar-job-hover");
    });
  });
}

// Function to enhance dashboard
function enhanceDashboard() {
  // Add animation to sidebar menu items
  const sidebarItems = document.querySelectorAll(".sidebar-menu li");
  sidebarItems.forEach((item, index) => {
    item.style.opacity = "0";
    item.style.transform = "translateX(-20px)";
    item.style.transition = "all 0.3s ease";
    item.style.transitionDelay = `${0.1 + index * 0.05}s`;

    setTimeout(() => {
      item.style.opacity = "1";
      item.style.transform = "translateX(0)";
    }, 100);
  });

  // Add animation to stat cards
  const statCards = document.querySelectorAll(".stat-card");
  statCards.forEach((card, index) => {
    card.style.opacity = "0";
    card.dataset.animation = "slideInUp";
    card.dataset.delay = (0.2 + index * 0.1).toString();

    // Add counter animation to stat values
    const statValue = card.querySelector(".stat-value, h3");
    if (statValue && !isNaN(Number.parseInt(statValue.textContent))) {
      const targetValue = Number.parseInt(statValue.textContent);
      statValue.textContent = "0";
      statValue.classList.add("counter-animate");
      statValue.dataset.count = targetValue.toString();

      // Initialize counter animation
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              animateCounter(statValue, targetValue);
              observer.unobserve(statValue);
            }
          });
        },
        { threshold: 0.1 }
      );

      observer.observe(statValue);
    }
  });

  // Add animation to activity items
  const activityItems = document.querySelectorAll(".activity-item");
  activityItems.forEach((item, index) => {
    item.style.opacity = "0";
    item.dataset.animation = "slideInRight";
    item.dataset.delay = (0.3 + index * 0.1).toString();
  });

  // Add toggle functionality for mobile sidebar
  const sidebarToggle = document.querySelector(".sidebar-toggle");
  const sidebar = document.querySelector(".dashboard-sidebar");

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.classList.toggle("sidebar-mobile-open");
    });

    // Close sidebar when clicking outside
    document.addEventListener("click", (e) => {
      if (!sidebar.contains(e.target) && e.target !== sidebarToggle) {
        sidebar.classList.remove("sidebar-mobile-open");
      }
    });
  }

  // Add animation to dashboard cards
  const dashboardCards = document.querySelectorAll(".dashboard-card");
  dashboardCards.forEach((card, index) => {
    card.style.opacity = "0";
    card.dataset.animation = "fadeIn";
    card.dataset.delay = (0.2 + index * 0.1).toString();

    card.addEventListener("mouseover", function () {
      this.classList.add("dashboard-card-hover");
    });

    card.addEventListener("mouseout", function () {
      this.classList.remove("dashboard-card-hover");
    });
  });
}

// Function to enhance profile
function enhanceProfile() {
  const profilePhoto = document.querySelector(
    ".profile-photo, .rounded-circle"
  );
  if (profilePhoto) {
    profilePhoto.style.transition = "all 0.3s ease";
    profilePhoto.style.border = "4px solid #f8f9fa";
    profilePhoto.style.boxShadow = "0 4px 10px rgba(0, 0, 0, 0.1)";

    profilePhoto.addEventListener("mouseover", function () {
      this.style.transform = "scale(1.05)";
      this.style.border = "4px solid #1491ea";
    });

    profilePhoto.addEventListener("mouseout", function () {
      this.style.transform = "";
      this.style.border = "4px solid #f8f9fa";
    });
  }

  // Add animation to profile sections
  const profileSections = document.querySelectorAll(".profile-section");
  profileSections.forEach((section, index) => {
    section.style.opacity = "0";
    section.dataset.animation = "fadeIn";
    section.dataset.delay = (0.2 + index * 0.1).toString();
  });

  // Add animation to profile edit buttons
  const editButtons = document.querySelectorAll(".edit-profile-btn");
  editButtons.forEach((button) => {
    button.addEventListener("mouseover", function () {
      this.classList.add("edit-btn-hover");
    });

    button.addEventListener("mouseout", function () {
      this.classList.remove("edit-btn-hover");
    });
  });
}

// Function to initialize back to top button
function initBackToTop() {
  const backToTop = document.querySelector(".back-to-top");

  if (backToTop) {
    window.addEventListener("scroll", () => {
      if (window.pageYOffset > 300) {
        backToTop.style.display = "flex";
        setTimeout(() => {
          backToTop.style.opacity = "1";
        }, 10);
      } else {
        backToTop.style.opacity = "0";
        setTimeout(() => {
          backToTop.style.display = "none";
        }, 300);
      }
    });
  } else {
    // Create back to top button if it doesn't exist
    const btn = document.createElement("a");
    btn.href = "#";
    btn.className = "back-to-top";
    btn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    btn.style.position = "fixed";
    btn.style.bottom = "20px";
    btn.style.right = "20px";
    btn.style.backgroundColor = "#1491ea";
    btn.style.color = "white";
    btn.style.width = "40px";
    btn.style.height = "40px";
    btn.style.borderRadius = "50%";
    btn.style.display = "none";
    btn.style.alignItems = "center";
    btn.style.justifyContent = "center";
    btn.style.boxShadow = "0 2px 10px rgba(0, 0, 0, 0.1)";
    btn.style.zIndex = "999";
    btn.style.opacity = "0";
    btn.style.transition = "opacity 0.3s ease, background-color 0.3s ease";

    document.body.appendChild(btn);

    window.addEventListener("scroll", () => {
      if (window.pageYOffset > 300) {
        btn.style.display = "flex";
        setTimeout(() => {
          btn.style.opacity = "1";
        }, 10);
      } else {
        btn.style.opacity = "0";
        setTimeout(() => {
          btn.style.display = "none";
        }, 300);
      }
    });

    btn.addEventListener("click", (e) => {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: "smooth" });
    });

    btn.addEventListener("mouseover", function () {
      this.style.backgroundColor = "#0d7dd1";
    });

    btn.addEventListener("mouseout", function () {
      this.style.backgroundColor = "#1491ea";
    });
  }
}

// Function to initialize form validations
function initFormValidations() {
  const forms = document.querySelectorAll("form");

  forms.forEach((form) => {
    const inputs = form.querySelectorAll("input, textarea, select");

    inputs.forEach((input) => {
      if (input.required) {
        input.addEventListener("blur", function () {
          if (!this.value.trim()) {
            this.classList.add("is-invalid");

            // Add error message if it doesn't exist
            let errorMessage = this.nextElementSibling;
            if (
              !errorMessage ||
              !errorMessage.classList.contains("invalid-feedback")
            ) {
              errorMessage = document.createElement("div");
              errorMessage.className = "invalid-feedback";
              errorMessage.textContent = "This field is required";
              this.parentNode.insertBefore(errorMessage, this.nextSibling);
            }
          } else {
            this.classList.remove("is-invalid");

            // Remove error message if it exists
            const errorMessage = this.nextElementSibling;
            if (
              errorMessage &&
              errorMessage.classList.contains("invalid-feedback")
            ) {
              errorMessage.remove();
            }
          }
        });
      }

      if (input.type === "email") {
        input.addEventListener("blur", function () {
          if (this.value.trim() && !isValidEmail(this.value)) {
            this.classList.add("is-invalid");

            // Add error message if it doesn't exist
            let errorMessage = this.nextElementSibling;
            if (
              !errorMessage ||
              !errorMessage.classList.contains("invalid-feedback")
            ) {
              errorMessage = document.createElement("div");
              errorMessage.className = "invalid-feedback";
              errorMessage.textContent = "Please enter a valid email address";
              this.parentNode.insertBefore(errorMessage, this.nextSibling);
            }
          } else if (this.value.trim()) {
            this.classList.remove("is-invalid");

            // Remove error message if it exists
            const errorMessage = this.nextElementSibling;
            if (
              errorMessage &&
              errorMessage.classList.contains("invalid-feedback")
            ) {
              errorMessage.remove();
            }
          }
        });
      }

      // Add password strength meter for password fields
      if (
        input.type === "password" &&
        input.id.includes("password") &&
        !input.id.includes("confirm")
      ) {
        input.addEventListener("input", function () {
          const strength = calculatePasswordStrength(this.value);

          // Create or update password strength meter
          let strengthMeter = this.nextElementSibling;
          if (
            !strengthMeter ||
            !strengthMeter.classList.contains("password-strength")
          ) {
            strengthMeter = document.createElement("div");
            strengthMeter.className = "password-strength mt-2";

            const strengthBar = document.createElement("div");
            strengthBar.className = "strength-bar";

            const strengthText = document.createElement("div");
            strengthText.className = "strength-text text-sm mt-1";

            strengthMeter.appendChild(strengthBar);
            strengthMeter.appendChild(strengthText);

            this.parentNode.insertBefore(strengthMeter, this.nextSibling);
          }

          const strengthBar = strengthMeter.querySelector(".strength-bar");
          const strengthText = strengthMeter.querySelector(".strength-text");

          // Update strength bar
          strengthBar.style.width = `${strength.score * 25}%`;
          strengthBar.className = "strength-bar";
          strengthBar.classList.add(`strength-${strength.level}`);

          // Update strength text
          strengthText.textContent = `Password strength: ${strength.text}`;
          strengthText.className = "strength-text text-sm mt-1";
          strengthText.classList.add(`text-${strength.color}`);
        });
      }
    });

    form.addEventListener("submit", (e) => {
      let isValid = true;

      inputs.forEach((input) => {
        if (input.required && !input.value.trim()) {
          input.classList.add("is-invalid");
          isValid = false;

          // Add error message if it doesn't exist
          let errorMessage = input.nextElementSibling;
          if (
            !errorMessage ||
            !errorMessage.classList.contains("invalid-feedback")
          ) {
            errorMessage = document.createElement("div");
            errorMessage.className = "invalid-feedback";
            errorMessage.textContent = "This field is required";
            input.parentNode.insertBefore(errorMessage, input.nextSibling);
          }
        }

        if (
          input.type === "email" &&
          input.value.trim() &&
          !isValidEmail(input.value)
        ) {
          input.classList.add("is-invalid");
          isValid = false;

          // Add error message if it doesn't exist
          let errorMessage = input.nextElementSibling;
          if (
            !errorMessage ||
            !errorMessage.classList.contains("invalid-feedback")
          ) {
            errorMessage = document.createElement("div");
            errorMessage.className = "invalid-feedback";
            errorMessage.textContent = "Please enter a valid email address";
            input.parentNode.insertBefore(errorMessage, input.nextSibling);
          }
        }
      });

      if (!isValid) {
        e.preventDefault();
      }
    });
  });
}

// Function to calculate password strength
function calculatePasswordStrength(password) {
  let score = 0;
  let level = "weak";
  let text = "Weak";
  let color = "red-500";

  if (!password) {
    return { score, level, text, color };
  }

  // Length check
  if (password.length >= 8) {
    score += 1;
  }

  // Complexity checks
  if (/[A-Z]/.test(password)) {
    score += 1;
  }

  if (/[0-9]/.test(password)) {
    score += 1;
  }

  if (/[^A-Za-z0-9]/.test(password)) {
    score += 1;
  }

  // Determine level based on score
  if (score === 1) {
    level = "weak";
    text = "Weak";
    color = "red-500";
  } else if (score === 2) {
    level = "medium";
    text = "Medium";
    color = "yellow-500";
  } else if (score === 3) {
    level = "good";
    text = "Good";
    color = "blue-500";
  } else if (score === 4) {
    level = "strong";
    text = "Strong";
    color = "green-500";
  }

  return { score, level, text, color };
}

// Function to validate email
function isValidEmail(email) {
  const re =
    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}

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

// Function to initialize loading effects
function initLoadingEffects() {
  // Add loading spinner to buttons when clicked
  const submitButtons = document.querySelectorAll('button[type="submit"]');

  submitButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const form = this.closest("form");

      if (form && form.checkValidity()) {
        const originalText = this.innerHTML;
        this.disabled = true;
        this.innerHTML =
          '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

        // Reset button after 3 seconds if form submission takes too long
        setTimeout(() => {
          if (this.disabled) {
            this.disabled = false;
            this.innerHTML = originalText;
          }
        }, 3000);
      }
    });
  });

  // Add page transition effect
  const pageLinks = document.querySelectorAll(
    'a:not([target="_blank"]):not([href^="#"]):not([href^="javascript"]):not(.no-transition)'
  );

  pageLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      const href = this.getAttribute("href");

      // Skip if it's a download link or has no href
      if (!href || href.includes("download") || this.hasAttribute("download")) {
        return;
      }

      e.preventDefault();

      // Create page transition overlay
      const overlay = document.createElement("div");
      overlay.className = "page-transition-overlay";
      document.body.appendChild(overlay);

      // Animate overlay
      setTimeout(() => {
        overlay.style.opacity = "1";
      }, 10);

      // Navigate to new page after animation
      setTimeout(() => {
        window.location.href = href;
      }, 300);
    });
  });
}

// Function to initialize parallax effects
function initParallaxEffects() {
  const parallaxElements = document.querySelectorAll(".parallax-bg");

  if (parallaxElements.length > 0) {
    window.addEventListener("scroll", () => {
      parallaxElements.forEach((element) => {
        const scrollPosition = window.pageYOffset;
        const speed = element.dataset.speed || 0.5;
        element.style.transform = `translateY(${scrollPosition * speed}px)`;
      });
    });
  }

  // Add parallax effect to hero sections
  const heroSections = document.querySelectorAll(
    ".hero-section, .category-hero"
  );

  if (heroSections.length > 0) {
    window.addEventListener("scroll", () => {
      heroSections.forEach((section) => {
        const scrollPosition = window.pageYOffset;
        const heroContent = section.querySelector("h1, .hero-content");

        if (heroContent) {
          heroContent.style.transform = `translateY(${scrollPosition * 0.2}px)`;
          heroContent.style.opacity = 1 - scrollPosition * 0.003;
        }
      });
    });
  }
}

// Function to initialize tooltips
function initTooltips() {
  const tooltipElements = document.querySelectorAll("[data-tooltip]");

  tooltipElements.forEach((element) => {
    const tooltipText = element.dataset.tooltip;

    // Create tooltip element
    const tooltip = document.createElement("div");
    tooltip.className = "tooltip";
    tooltip.textContent = tooltipText;
    tooltip.style.position = "absolute";
    tooltip.style.backgroundColor = "#29366f";
    tooltip.style.color = "white";
    tooltip.style.padding = "0.5rem 1rem";
    tooltip.style.borderRadius = "4px";
    tooltip.style.fontSize = "0.875rem";
    tooltip.style.zIndex = "1000";
    tooltip.style.opacity = "0";
    tooltip.style.visibility = "hidden";
    tooltip.style.transition = "all 0.3s ease";

    // Add tooltip to element
    element.style.position = "relative";
    element.appendChild(tooltip);

    // Show tooltip on hover
    element.addEventListener("mouseenter", () => {
      tooltip.style.opacity = "1";
      tooltip.style.visibility = "visible";

      // Position tooltip
      const elementRect = element.getBoundingClientRect();
      tooltip.style.bottom = "calc(100% + 10px)";
      tooltip.style.left = "50%";
      tooltip.style.transform = "translateX(-50%)";

      // Add arrow
      tooltip.style.setProperty("--tooltip-arrow", "");
      tooltip.style.setProperty("--tooltip-arrow", "block");
      tooltip.style.setProperty("--tooltip-arrow-color", "#29366f");
    });

    // Hide tooltip on mouse leave
    element.addEventListener("mouseleave", () => {
      tooltip.style.opacity = "0";
      tooltip.style.visibility = "hidden";
    });
  });
}

// Function to initialize mobile menu
function initMobileMenu() {
  const mobileMenuToggle = document.querySelector(".navbar-toggler");
  const navbarCollapse = document.querySelector(".navbar-collapse");

  if (mobileMenuToggle && navbarCollapse) {
    mobileMenuToggle.addEventListener("click", () => {
      navbarCollapse.classList.toggle("show");

      if (navbarCollapse.classList.contains("show")) {
        // Add animation to nav items
        const navItems = navbarCollapse.querySelectorAll(".nav-item");
        navItems.forEach((item, index) => {
          item.style.opacity = "0";
          item.style.transform = "translateY(-10px)";
          item.style.transition = "all 0.3s ease";
          item.style.transitionDelay = `${0.1 + index * 0.05}s`;

          setTimeout(() => {
            item.style.opacity = "1";
            item.style.transform = "translateY(0)";
          }, 100);
        });
      }
    });

    // Close mobile menu when clicking outside
    document.addEventListener("click", (e) => {
      if (
        navbarCollapse.classList.contains("show") &&
        !navbarCollapse.contains(e.target) &&
        e.target !== mobileMenuToggle
      ) {
        navbarCollapse.classList.remove("show");
      }
    });
  }
}

// Function to initialize dark mode
function initDarkMode() {
  const darkModeToggle = document.querySelector(".dark-mode-toggle");
  const htmlElement = document.documentElement;

  // Check for saved dark mode preference
  const savedDarkMode = localStorage.getItem("darkMode");

  if (savedDarkMode === "enabled") {
    htmlElement.classList.add("dark-mode");
    if (darkModeToggle) {
      darkModeToggle.querySelector("i").classList.remove("fa-moon");
      darkModeToggle.querySelector("i").classList.add("fa-sun");
    }
  }

  if (darkModeToggle) {
    darkModeToggle.addEventListener("click", () => {
      htmlElement.classList.toggle("dark-mode");

      // Update icon
      const icon = darkModeToggle.querySelector("i");
      if (icon.classList.contains("fa-moon")) {
        icon.classList.remove("fa-moon");
        icon.classList.add("fa-sun");
        localStorage.setItem("darkMode", "enabled");
      } else {
        icon.classList.remove("fa-sun");
        icon.classList.add("fa-moon");
        localStorage.setItem("darkMode", "disabled");
      }
    });
  } else {
    // Create dark mode toggle if it doesn't exist
    const toggle = document.createElement("button");
    toggle.className = "dark-mode-toggle";
    toggle.innerHTML = '<i class="fas fa-moon"></i>';
    toggle.style.position = "fixed";
    toggle.style.bottom = "70px";
    toggle.style.right = "20px";
    toggle.style.backgroundColor = "#1491ea";
    toggle.style.color = "white";
    toggle.style.width = "40px";
    toggle.style.height = "40px";
    toggle.style.borderRadius = "50%";
    toggle.style.display = "flex";
    toggle.style.alignItems = "center";
    toggle.style.justifyContent = "center";
    toggle.style.boxShadow = "0 2px 10px rgba(0, 0, 0, 0.1)";
    toggle.style.zIndex = "999";
    toggle.style.border = "none";
    toggle.style.cursor = "pointer";
    toggle.style.transition = "background-color 0.3s ease";

    // Update icon if dark mode is enabled
    if (htmlElement.classList.contains("dark-mode")) {
      toggle.innerHTML = '<i class="fas fa-sun"></i>';
    }

    document.body.appendChild(toggle);

    toggle.addEventListener("click", () => {
      htmlElement.classList.toggle("dark-mode");

      // Update icon
      const icon = toggle.querySelector("i");
      if (icon.classList.contains("fa-moon")) {
        icon.classList.remove("fa-moon");
        icon.classList.add("fa-sun");
        localStorage.setItem("darkMode", "enabled");
      } else {
        icon.classList.remove("fa-sun");
        icon.classList.add("fa-moon");
        localStorage.setItem("darkMode", "disabled");
      }
    });

    toggle.addEventListener("mouseover", function () {
      this.style.backgroundColor = "#0d7dd1";
    });

    toggle.addEventListener("mouseout", function () {
      this.style.backgroundColor = "#1491ea";
    });
  }

  // Add dark mode styles
  const darkModeStyles = document.createElement("style");
  darkModeStyles.textContent = `
    .dark-mode {
      --bg-primary: #121212;
      --bg-secondary: #1e1e1e;
      --text-primary: #f5f5f5;
      --text-secondary: #aaaaaa;
      --border-color: #333333;
    }
    
    .dark-mode body {
      background-color: var(--bg-primary);
      color: var(--text-primary);
    }
    
    .dark-mode .bg-white {
      background-color: var(--bg-secondary) !important;
    }
    
    .dark-mode .bg-gray-50, .dark-mode .bg-gray-100 {
      background-color: var(--bg-primary) !important;
    }
    
    .dark-mode .text-gray-800, .dark-mode .text-gray-700 {
      color: var(--text-primary) !important;
    }
    
    .dark-mode .text-gray-600, .dark-mode .text-gray-500 {
      color: var(--text-secondary) !important;
    }
    
    .dark-mode .border, .dark-mode .border-gray-200, .dark-mode .border-gray-300 {
      border-color: var(--border-color) !important;
    }
    
    .dark-mode .shadow-md, .dark-mode .shadow-lg {
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;
    }
    
    .dark-mode .form-control, .dark-mode input, .dark-mode select, .dark-mode textarea {
      background-color: var(--bg-primary);
      color: var(--text-primary);
      border-color: var(--border-color);
    }
    
    .dark-mode .navbar {
      background-color: var(--bg-secondary) !important;
    }
    
    .dark-mode .dropdown-menu {
      background-color: var(--bg-secondary);
      border-color: var(--border-color);
    }
    
    .dark-mode .dropdown-item {
      color: var(--text-primary);
    }
    
    .dark-mode .dropdown-item:hover {
      background-color: var(--bg-primary);
    }
  `;

  document.head.appendChild(darkModeStyles);
}

// Function to initialize notification system
function initNotifications() {
  // Create notification container if it doesn't exist
  let notificationContainer = document.querySelector(".notification-container");

  if (!notificationContainer) {
    notificationContainer = document.createElement("div");
    notificationContainer.className =
      "notification-container fixed top-4 right-4 z-50";
    document.body.appendChild(notificationContainer);
  }

  // Add global showNotification function
  window.showNotification = function (message, type = "info", duration = 5000) {
    // Create notification element
    const notification = document.createElement("div");
    notification.className = `notification flex items-center p-4 mb-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ${getNotificationClass(
      type
    )}`;

    // Notification icon
    const icon = document.createElement("div");
    icon.className = "mr-3";
    icon.innerHTML = getNotificationIcon(type);

    // Notification message
    const messageEl = document.createElement("div");
    messageEl.className = "flex-grow";
    messageEl.textContent = message;

    // Close button
    const closeBtn = document.createElement("button");
    closeBtn.className =
      "ml-4 text-gray-400 hover:text-gray-600 transition-colors";
    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
    closeBtn.addEventListener("click", () => {
      notification.classList.add("translate-x-full");
      setTimeout(() => {
        notification.remove();
      }, 300);
    });

    // Append elements to notification
    notification.appendChild(icon);
    notification.appendChild(messageEl);
    notification.appendChild(closeBtn);

    // Add notification to container
    notificationContainer.appendChild(notification);

    // Show notification
    setTimeout(() => {
      notification.classList.remove("translate-x-full");
    }, 10);

    // Auto hide notification after duration
    setTimeout(() => {
      notification.classList.add("translate-x-full");
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, duration);
  };

  // Helper function to get notification class based on type
  function getNotificationClass(type) {
    switch (type) {
      case "success":
        return "bg-green-600 text-white";
      case "error":
        return "bg-red-600 text-white";
      case "warning":
        return "bg-yellow-500 text-white";
      case "info":
      default:
        return "bg-blue-600 text-white";
    }
  }

  // Helper function to get notification icon based on type
  function getNotificationIcon(type) {
    switch (type) {
      case "success":
        return '<i class="fas fa-check-circle text-2xl"></i>';
      case "error":
        return '<i class="fas fa-exclamation-circle text-2xl"></i>';
      case "warning":
        return '<i class="fas fa-exclamation-triangle text-2xl"></i>';
      case "info":
      default:
        return '<i class="fas fa-info-circle text-2xl"></i>';
    }
  }
}

// Add global CSS styles
const globalStyles = document.createElement("style");
globalStyles.textContent = `
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
  
  /* Section animations */
  .section-animate {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease, transform 0.6s ease;
  }
  
  .section-visible {
    opacity: 1;
    transform: translateY(0);
  }
  
  /* Button hover effects */
  .btn-hover {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
  }
  
  .btn-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
  
  /* Ripple effect */
  .ripple {
    position: absolute;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.3);
    transform: scale(0);
    animation: ripple 0.6s linear;
    pointer-events: none;
  }
  
  @keyframes ripple {
    to {
      transform: scale(4);
      opacity: 0;
    }
  }
  
  /* Card hover effects */
  .card-hover {
    transition: all 0.3s ease;
  }
  
  .card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }
  
  /* Form control enhancements */
  .form-control-enhanced {
    transition: all 0.3s ease;
  }
  
  .form-control-enhanced:focus {
    box-shadow: 0 0 0 3px rgba(20, 145, 234, 0.2);
  }
  
  .input-focused {
    position: relative;
  }
  
  .input-focused::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #1491ea;
    transform: scaleX(1);
    transition: transform 0.3s ease;
  }
  
  /* Navigation link animations */
  .nav-link-animated {
    position: relative;
    transition: color 0.3s ease;
  }
  
  .nav-link-animated::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #1491ea;
    transform: scaleX(0);
    transition: transform 0.3s ease;
    transform-origin: right;
  }
  
  .nav-link-animated:hover::after {
    transform: scaleX(1);
    transform-origin: left;
  }
  
  /* Table enhancements */
  .table-enhanced {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  }
  
  .table-enhanced th {
    background-color: #f8f9fa;
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
  }
  
  .table-enhanced td {
    padding: 12px 16px;
    border-bottom: 1px solid #dee2e6;
  }
  
  .table-row-hover {
    transition: background-color 0.3s ease;
  }
  
  .table-row-hover:hover {
    background-color: #f8f9fa;
  }
  
  /* Badge enhancements */
  .badge-enhanced {
    display: inline-block;
    padding: 0.25em 0.6em;
    font-size: 75%;
    font-weight: 600;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
    transition: all 0.3s ease;
  }
  
  .badge-enhanced:hover {
    transform: translateY(-2px);
  }
  
  /* Alert enhancements */
  .alert {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
  }
  
  .alert-icon {
    margin-right: 0.75rem;
    font-size: 1.25rem;
  }
  
  /* Dropzone styling */
  .dropzone-active {
    border-color: #1491ea !important;
    background-color: rgba(20, 145, 234, 0.05) !important;
  }
  
  .dropzone-success {
    border-color: #10b981 !important;
    background-color: rgba(16, 185, 129, 0.05) !important;
  }
  
  /* Action button hover effects */
  .action-hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
  
  .apply-btn-hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 15px rgba(20, 145, 234, 0.3) !important;
  }
  
  .edit-btn-hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
  
  .similar-job-hover {
    border-color: #1491ea !important;
    background-color: rgba(20, 145, 234, 0.05) !important;
  }
  
  /* Dashboard card hover effects */
  .dashboard-card-hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }
  
  /* Password strength meter */
  .password-strength {
    margin-top: 0.5rem;
  }
  
  .strength-bar {
    height: 4px;
    border-radius: 2px;
    transition: width 0.3s ease;
  }
  
  .strength-weak {
    background-color: #ef4444;
  }
  
  .strength-medium {
    background-color: #f59e0b;
  }
  
  .strength-good {
    background-color: #3b82f6;
  }
  
  .strength-strong {
    background-color: #10b981;
  }
  
  /* Page transition overlay */
  .page-transition-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #ffffff;
    z-index: 9999;
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  
  /* Notification styling */
  .notification {
    max-width: 350px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
`;

document.head.appendChild(globalStyles);
