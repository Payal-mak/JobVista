document.addEventListener("DOMContentLoaded", () => {
  // Enhanced Job Cards
  enhanceJobCards()

  // Enhanced Form Inputs
  enhanceFormInputs()

  // Enhanced Dashboard
  enhanceDashboard()

  // Enhanced Navigation
  enhanceNavigation()

  // Enhanced Profile
  enhanceProfile()

  // Back to Top Button
  initBackToTop()

  // Enhanced Dropdowns
  enhanceDropdowns()

  // Enhanced Modals
  enhanceModals()

  // Enhanced Sidebar
  enhanceSidebar()
})

// Function to enhance job cards
function enhanceJobCards() {
  const jobCards = document.querySelectorAll(".job-card")

  jobCards.forEach((card) => {
    // Add hover animation
    card.classList.add("card-hover")

    // Add staggered animation on page load
    card.classList.add("animate-on-scroll")
    card.dataset.animation = "fadeIn"

    // Add save button animation
    const saveBtn = card.querySelector(".btn-save, .bookmark-job-btn")
    if (saveBtn) {
      saveBtn.addEventListener("click", function (e) {
        const icon = this.querySelector("i")
        if (icon.classList.contains("far")) {
          icon.classList.remove("far")
          icon.classList.add("fas")
          icon.classList.add("animate-pulse")
          setTimeout(() => {
            icon.classList.remove("animate-pulse")
          }, 1000)
        } else {
          icon.classList.remove("fas")
          icon.classList.add("far")
        }
      })
    }
  })
}

// Function to enhance form inputs
function enhanceFormInputs() {
  const formControls = document.querySelectorAll(".form-control")

  formControls.forEach((input) => {
    input.classList.add("form-control-enhanced")

    // Add label animation for text inputs, email, password
    if (input.type === "text" || input.type === "email" || input.type === "password") {
      const parent = input.parentElement
      const label = parent.querySelector("label")

      if (label) {
        label.style.transition = "all 0.3s ease"

        input.addEventListener("focus", () => {
          label.style.color = "#1491ea"
          label.style.transform = "translateY(-5px) scale(0.85)"
          label.style.transformOrigin = "left top"
        })

        input.addEventListener("blur", function () {
          if (!this.value) {
            label.style.color = ""
            label.style.transform = ""
          } else {
            label.style.color = ""
          }
        })

        // Initialize if input has value
        if (input.value) {
          label.style.transform = "translateY(-5px) scale(0.85)"
          label.style.transformOrigin = "left top"
        }
      }
    }
  })

  // Enhance select elements
  const selects = document.querySelectorAll("select.form-control")
  selects.forEach((select) => {
    select.style.cursor = "pointer"
    select.parentElement.style.position = "relative"

    // Add custom arrow
    const arrow = document.createElement("div")
    arrow.innerHTML = '<i class="fas fa-chevron-down"></i>'
    arrow.style.position = "absolute"
    arrow.style.right = "10px"
    arrow.style.top = "50%"
    arrow.style.transform = "translateY(-50%)"
    arrow.style.pointerEvents = "none"
    arrow.style.color = "#6c757d"

    select.parentElement.appendChild(arrow)

    select.addEventListener("focus", () => {
      arrow.style.color = "#1491ea"
    })

    select.addEventListener("blur", () => {
      arrow.style.color = "#6c757d"
    })
  })
}

// Function to enhance dashboard
function enhanceDashboard() {
  // Enhance stat cards
  const statCards = document.querySelectorAll(".stat-card")
  statCards.forEach((card, index) => {
    card.classList.add("animate-on-scroll")
    card.dataset.animation = "slideInUp"
    card.dataset.delay = (index * 0.1).toString()

    // Add counter animation to stat values
    const statValue = card.querySelector(".stat-value, h3")
    if (statValue && !isNaN(Number.parseInt(statValue.textContent))) {
      const targetValue = Number.parseInt(statValue.textContent)
      statValue.textContent = "0"
      statValue.classList.add("counter-animate")
      statValue.dataset.count = targetValue.toString()
    }
  })

  // Enhance activity items
  const activityItems = document.querySelectorAll(".activity-item")
  activityItems.forEach((item, index) => {
    item.classList.add("animate-on-scroll")
    item.dataset.animation = "slideInRight"
    item.dataset.delay = (0.2 + index * 0.1).toString()
  })
}

// Function to enhance navigation
function enhanceNavigation() {
  const navLinks = document.querySelectorAll(".nav-link")

  navLinks.forEach((link) => {
    // Skip dropdown toggles
    if (link.classList.contains("dropdown-toggle")) return

    // Add hover animation
    link.classList.add("nav-link-animated")

    // Add active state animation
    if (link.classList.contains("active")) {
      link.style.position = "relative"
      link.style.overflow = "hidden"

      const activeIndicator = document.createElement("span")
      activeIndicator.style.position = "absolute"
      activeIndicator.style.bottom = "0"
      activeIndicator.style.left = "0"
      activeIndicator.style.width = "100%"
      activeIndicator.style.height = "2px"
      activeIndicator.style.backgroundColor = "#1491ea"
      activeIndicator.style.animation = "slideInLeft 0.3s forwards"

      link.appendChild(activeIndicator)
    }
  })
}

// Function to enhance profile
function enhanceProfile() {
  const profilePhoto = document.querySelector(".rounded-circle")
  if (profilePhoto) {
    profilePhoto.style.transition = "all 0.3s ease"
    profilePhoto.style.border = "4px solid #f8f9fa"
    profilePhoto.style.boxShadow = "0 4px 10px rgba(0, 0, 0, 0.1)"

    profilePhoto.addEventListener("mouseover", function () {
      this.style.transform = "scale(1.05)"
      this.style.border = "4px solid #1491ea"
    })

    profilePhoto.addEventListener("mouseout", function () {
      this.style.transform = ""
      this.style.border = "4px solid #f8f9fa"
    })
  }
}

// Function to initialize back to top button
function initBackToTop() {
  const backToTop = document.querySelector(".back-to-top")

  if (backToTop) {
    window.addEventListener("scroll", () => {
      if (window.pageYOffset > 300) {
        backToTop.style.opacity = "1"
        backToTop.style.visibility = "visible"
      } else {
        backToTop.style.opacity = "0"
        backToTop.style.visibility = "hidden"
      }
    })

    backToTop.addEventListener("click", (e) => {
      e.preventDefault()
      window.scrollTo({ top: 0, behavior: "smooth" })
    })
  } else {
    // Create back to top button if it doesn't exist
    const btn = document.createElement("a")
    btn.href = "#"
    btn.className = "back-to-top"
    btn.innerHTML = '<i class="fas fa-arrow-up"></i>'
    btn.style.position = "fixed"
    btn.style.bottom = "20px"
    btn.style.right = "20px"
    btn.style.backgroundColor = "#1491ea"
    btn.style.color = "white"
    btn.style.width = "40px"
    btn.style.height = "40px"
    btn.style.borderRadius = "50%"
    btn.style.display = "flex"
    btn.style.alignItems = "center"
    btn.style.justifyContent = "center"
    btn.style.boxShadow = "0 2px 10px rgba(0, 0, 0, 0.1)"
    btn.style.zIndex = "999"
    btn.style.opacity = "0"
    btn.style.visibility = "hidden"
    btn.style.transition = "all 0.3s ease"

    document.body.appendChild(btn)

    window.addEventListener("scroll", () => {
      if (window.pageYOffset > 300) {
        btn.style.opacity = "1"
        btn.style.visibility = "visible"
      } else {
        btn.style.opacity = "0"
        btn.style.visibility = "hidden"
      }
    })

    btn.addEventListener("click", (e) => {
      e.preventDefault()
      window.scrollTo({ top: 0, behavior: "smooth" })
    })
  }
}

// Function to enhance dropdowns
function enhanceDropdowns() {
  const dropdowns = document.querySelectorAll(".dropdown-menu")

  dropdowns.forEach((dropdown) => {
    // Add animation
    dropdown.style.transition = "all 0.3s ease"
    dropdown.style.transformOrigin = "top center"

    // Add shadow and border
    dropdown.style.boxShadow = "0 10px 15px rgba(0, 0, 0, 0.1)"
    dropdown.style.border = "1px solid rgba(0, 0, 0, 0.08)"

    // Add animation to dropdown items
    const items = dropdown.querySelectorAll(".dropdown-item")
    items.forEach((item, index) => {
      item.style.transition = "all 0.2s ease"
      item.style.opacity = "0"
      item.style.transform = "translateY(10px)"
      item.style.animationDelay = `${index * 0.05}s`
      item.style.animationFillMode = "forwards"
    })

    // Add animation when dropdown is shown
    const parent = dropdown.parentElement
    if (parent) {
      const toggle = parent.querySelector(".dropdown-toggle")
      if (toggle) {
        toggle.addEventListener("click", () => {
          setTimeout(() => {
            if (dropdown.classList.contains("show")) {
              items.forEach((item, index) => {
                setTimeout(() => {
                  item.style.opacity = "1"
                  item.style.transform = "translateY(0)"
                }, index * 50)
              })
            } else {
              items.forEach((item) => {
                item.style.opacity = "0"
                item.style.transform = "translateY(10px)"
              })
            }
          }, 0)
        })
      }
    }
  })
}

// Function to enhance modals
function enhanceModals() {
  const modals = document.querySelectorAll(".modal")

  modals.forEach((modal) => {
    // Add animation
    modal.style.transition = "all 0.3s ease"

    // Add animation to modal dialog
    const dialog = modal.querySelector(".modal-dialog")
    if (dialog) {
      dialog.style.transition = "all 0.3s ease"
      dialog.style.transform = "scale(0.8)"
      dialog.style.opacity = "0"
    }

    // Add animation when modal is shown
    modal.addEventListener("show.bs.modal", () => {
      setTimeout(() => {
        if (dialog) {
          dialog.style.transform = "scale(1)"
          dialog.style.opacity = "1"
        }
      }, 150)
    })

    // Reset animation when modal is hidden
    modal.addEventListener("hidden.bs.modal", () => {
      if (dialog) {
        dialog.style.transform = "scale(0.8)"
        dialog.style.opacity = "0"
      }
    })
  })
}

// Function to enhance sidebar
function enhanceSidebar() {
  const sidebar = document.querySelector(".dashboard-sidebar")

  if (sidebar) {
    // Add animation to sidebar items
    const menuItems = sidebar.querySelectorAll(".sidebar-menu li")
    menuItems.forEach((item, index) => {
      item.style.opacity = "0"
      item.style.transform = "translateX(-20px)"
      item.style.transition = "all 0.3s ease"
      item.style.transitionDelay = `${index * 0.05}s`

      setTimeout(() => {
        item.style.opacity = "1"
        item.style.transform = "translateX(0)"
      }, 100)
    })

    // Add hover effect to menu items
    const menuLinks = sidebar.querySelectorAll(".sidebar-menu li a")
    menuLinks.forEach((link) => {
      link.style.transition = "all 0.3s ease"

      link.addEventListener("mouseover", function () {
        if (!this.parentElement.classList.contains("active")) {
          this.style.paddingLeft = "25px"
          this.style.backgroundColor = "rgba(255, 255, 255, 0.1)"
        }
      })

      link.addEventListener("mouseout", function () {
        if (!this.parentElement.classList.contains("active")) {
          this.style.paddingLeft = ""
          this.style.backgroundColor = ""
        }
      })
    })

    // Add toggle functionality for mobile
    const sidebarToggle = document.querySelector(".sidebar-toggle")
    if (sidebarToggle) {
      sidebarToggle.addEventListener("click", () => {
        sidebar.classList.toggle("sidebar-mobile-open")

        if (sidebar.classList.contains("sidebar-mobile-open")) {
          sidebar.style.transform = "translateX(0)"
        } else {
          sidebar.style.transform = "translateX(-100%)"
        }
      })
    }
  }
}
