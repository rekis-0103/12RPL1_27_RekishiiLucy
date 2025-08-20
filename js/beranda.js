// Counter animation for stats
function animateCounter(element, target, duration = 2000) {
  let start = 0
  const increment = target / (duration / 16)

  function updateCounter() {
    start += increment
    if (start < target) {
      element.textContent = Math.floor(start) + "+"
      requestAnimationFrame(updateCounter)
    } else {
      element.textContent = target + "+"
    }
  }

  updateCounter()
}

// Animate counters when they come into view
document.addEventListener("DOMContentLoaded", () => {
  const statItems = document.querySelectorAll(".stat-item h3")

  const statsObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const target = entry.target
          const value = Number.parseInt(target.textContent)

          if (!target.classList.contains("animated")) {
            target.classList.add("animated")
            animateCounter(target, value)
          }
        }
      })
    },
    { threshold: 0.5 },
  )

  statItems.forEach((item) => {
    statsObserver.observe(item)
  })
})

// Hero buttons hover effects
document.addEventListener("DOMContentLoaded", () => {
  const heroButtons = document.querySelectorAll(".hero-buttons .btn-primary, .hero-buttons .btn-secondary")

  heroButtons.forEach((button) => {
    button.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-3px) scale(1.05)"
    })

    button.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(-2px) scale(1)"
    })
  })
})

// Hero Slider Functionality
document.addEventListener("DOMContentLoaded", () => {
  let slideIndex = 0;
  const slides = document.querySelectorAll(".slide");
  const dots = document.querySelectorAll(".slider-dot");
  
  function showSlides() {
    // Hide all slides
    slides.forEach(slide => slide.classList.remove("active"));
    dots.forEach(dot => dot.classList.remove("active"));
    
    // Increment slide index
    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1 }
    
    // Show current slide
    slides[slideIndex-1].classList.add("active");
    dots[slideIndex-1].classList.add("active");
    
    // Change slide every 10 seconds
    setTimeout(showSlides, 10000);
  }
  
  // Manual navigation
  function currentSlide(n) {
    slideIndex = n;
    slides.forEach(slide => slide.classList.remove("active"));
    dots.forEach(dot => dot.classList.remove("active"));
    slides[slideIndex-1].classList.add("active");
    dots[slideIndex-1].classList.add("active");
  }
  
  // Initialize slider
  showSlides();
  
  // Add click events to dots
  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => currentSlide(index + 1));
  });
  
  // Add navigation buttons
  document.querySelector(".next")?.addEventListener("click", () => {
    currentSlide(slideIndex >= slides.length ? 1 : slideIndex + 1);
  });
  
  document.querySelector(".prev")?.addEventListener("click", () => {
    currentSlide(slideIndex <= 1 ? slides.length : slideIndex - 1);
  });
});
