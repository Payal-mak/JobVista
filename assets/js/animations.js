document.addEventListener("DOMContentLoaded", () => {
  // Initialize animations
  initAnimations()

  // Initialize enhanced UI elements
  initEnhancedUI()

  // Initialize parallax effect
  initParallax()

  // Initialize progress bars
  initProgressBars()

  // Add ripple effect to buttons
  initRippleEffect()
})

// Function to initialize animations
function initAnimations() {
  // Add animation classes to elements when they come into view
  const animatedElements = document.querySelectorAll(".animate-on-scroll")

  if (animatedElements.length > 0) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const element = entry.target
            const animation = element.dataset.animation || "fadeIn"
            const delay = element.dataset.delay || "0"

            element.style.animationDelay = `${delay}s`
            element.classList.add(`animate-${animation}`)

            // Unobserve after animation is added
            observer.unobserve(element)
          }
        })
      },
      { threshold: 0.1 },
    )

    animatedElements.forEach((element) => {
      observer.observe(element)
    })
  }

  // Add typing animation to elements with class 'typing-text'
  const typingElements = document.querySelectorAll(".typing-text")
  typingElements.forEach((element) => {
    element.classList.add("typing-animation")
  })
}

// Function to initialize enhanced UI elements
function initEnhancedUI() {
  // Add hover effects to cards
  const cards = document.querySelectorAll(".card")
  cards.forEach((card) => {
    card.classList.add("card-hover")
  })

  // Add hover effects to buttons
  const buttons = document.querySelectorAll(
    ".btn-primary, .btn-secondary, .btn-success, .btn-danger, .btn-warning, .btn-info",
  )
  buttons.forEach((button) => {
    button.classList.add("btn-hover")
  })

  // Add animated navigation links
  const navLinks = document.querySelectorAll(".nav-link")
  navLinks.forEach((link) => {
    link.classList.add("nav-link-animated")
  })

  // Initialize tooltips
  const tooltips = document.querySelectorAll("[data-tooltip]")
  tooltips.forEach((tooltip) => {
    const text = tooltip.dataset.tooltip
    const tooltipElement = document.createElement("span")
    tooltipElement.className = "tooltip-text"
    tooltipElement.textContent = text
    tooltip.classList.add("tooltip-animated")
    tooltip.appendChild(tooltipElement)
  })

  // Add notification badge animation
  const notificationBadges = document.querySelectorAll(".notification-badge")
  notificationBadges.forEach((badge) => {
    badge.classList.add("notification-badge-animated")
  })
}

// Function to initialize parallax effect
function initParallax() {
  const parallaxContainers = document.querySelectorAll(".parallax-container")

  if (parallaxContainers.length > 0) {
    window.addEventListener("scroll", () => {
      parallaxContainers.forEach((container) => {
        const parallaxBg = container.querySelector(".parallax-bg")
        if (parallaxBg) {
          const containerTop = container.getBoundingClientRect().top
          const windowHeight = window.innerHeight

          if (containerTop < windowHeight && containerTop > -container.offsetHeight) {
            const scrollPosition = containerTop / windowHeight
            parallaxBg.style.transform = `translateY(${scrollPosition * 100}px)`
          }
        }
      })
    })
  }
}

// Function to initialize progress bars
function initProgressBars() {
  const progressBars = document.querySelectorAll(".progress-bar-animated")

  if (progressBars.length > 0) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const element = entry.target
            const progress = element.dataset.progress || "0"

            // Set the progress with a slight delay for animation effect
            setTimeout(() => {
              element.style.setProperty("--progress", `${progress}%`)
            }, 300)

            // Unobserve after animation is added
            observer.unobserve(element)
          }
        })
      },
      { threshold: 0.1 },
    )

    progressBars.forEach((element) => {
      observer.observe(element)
    })
  }
}

// Function to add ripple effect to buttons
function initRippleEffect() {
  const buttons = document.querySelectorAll(
    ".btn-primary, .btn-secondary, .btn-success, .btn-danger, .btn-warning, .btn-info",
  )

  buttons.forEach((button) => {
    button.classList.add("ripple")

    button.addEventListener("click", (e) => {
      const rect = button.getBoundingClientRect()
      const x = e.clientX - rect.left
      const y = e.clientY - rect.top

      const ripple = document.createElement("span")
      ripple.className = "ripple-effect"
      ripple.style.left = `${x}px`
      ripple.style.top = `${y}px`

      button.appendChild(ripple)

      setTimeout(() => {
        ripple.remove()
      }, 600)
    })
  })
}

// Function to show loading spinner
function showLoading(elementId) {
  const element = document.getElementById(elementId)
  if (element) {
    const spinner = document.createElement("div")
    spinner.className = "loading-spinner"
    element.innerHTML = ""
    element.appendChild(spinner)
  }
}

// Function to hide loading spinner
function hideLoading(elementId, content) {
  const element = document.getElementById(elementId)
  if (element) {
    element.innerHTML = content
  }
}

// Function to create shimmer loading effect
function createShimmerLoading(container, type = "card", count = 3) {
  const element = document.getElementById(container)
  if (!element) return

  element.innerHTML = ""

  for (let i = 0; i < count; i++) {
    if (type === "card") {
      const card = document.createElement("div")
      card.className = "card shimmer mb-4"
      card.style.height = "200px"
      element.appendChild(card)
    } else if (type === "list") {
      const item = document.createElement("div")
      item.className = "shimmer mb-3"
      item.style.height = "50px"
      element.appendChild(item)
    } else if (type === "text") {
      const textBlock = document.createElement("div")
      textBlock.innerHTML = `
                <div class="shimmer-title shimmer"></div>
                <div class="shimmer-text shimmer" style="width: 90%"></div>
                <div class="shimmer-text shimmer" style="width: 80%"></div>
                <div class="shimmer-text shimmer" style="width: 85%"></div>
            `
      element.appendChild(textBlock)
    }
  }
}

// Function to animate counting numbers
function animateCounter(element, target, duration = 2000) {
  const start = 0
  const increment = target / (duration / 16)
  let current = start

  const timer = setInterval(() => {
    current += increment
    if (current >= target) {
      clearInterval(timer)
      element.textContent = target
    } else {
      element.textContent = Math.floor(current)
    }
  }, 16)
}

// Function to initialize counter animations
function initCounters() {
  const counters = document.querySelectorAll(".counter-animate")

  if (counters.length > 0) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const element = entry.target
            const target = Number.parseInt(element.dataset.count || "0")
            const duration = Number.parseInt(element.dataset.duration || "2000")

            animateCounter(element, target, duration)

            // Unobserve after animation starts
            observer.unobserve(element)
          }
        })
      },
      { threshold: 0.1 },
    )

    counters.forEach((element) => {
      observer.observe(element)
    })
  }
}

// Initialize counters when document is loaded
document.addEventListener("DOMContentLoaded", () => {
  initCounters()
})
