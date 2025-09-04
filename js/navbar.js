document.addEventListener("DOMContentLoaded", function () {
  const hamburger = document.querySelector(".hamburger");
  const navMenu = document.querySelector(".nav-menu");
  const userDropdown = document.querySelector(".user-dropdown");
  const userDropdownBtn = document.querySelector(".user-dropdown-btn");
  const userDropdownContent = document.querySelector(".user-dropdown-content");

  // Toggle mobile menu
  if (hamburger && navMenu) {
    hamburger.addEventListener("click", function (e) {
      e.stopPropagation();
      navMenu.classList.toggle("active");
      this.classList.toggle("active");

      // Close user dropdown when menu opens/closes
      if (userDropdown) {
        userDropdown.classList.remove("active");
      }
    });
  }

  // Handle user dropdown 
  if (userDropdownBtn && userDropdown) {
    userDropdownBtn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      
      console.log("Dropdown button clicked"); // Debug log
      
      // Toggle dropdown
      const isActive = userDropdown.classList.contains("active");
      userDropdown.classList.toggle("active");
      
      console.log("Dropdown is now:", isActive ? "closed" : "open"); // Debug log
    });
  }

  // Close dropdown when clicking outside
  document.addEventListener("click", function (e) {
    if (userDropdown && userDropdown.classList.contains("active")) {
      // Check if click is outside dropdown
      if (!userDropdown.contains(e.target)) {
        userDropdown.classList.remove("active");
        console.log("Dropdown closed by outside click"); // Debug log
      }
    }
  });

  // Handle window resize
  window.addEventListener("resize", function () {
    if (window.innerWidth > 768) {
      // Reset mobile states when switching to desktop
      if (navMenu) navMenu.classList.remove("active");
      if (hamburger) hamburger.classList.remove("active");
      if (userDropdown) userDropdown.classList.remove("active");
    }
  });

  // Prevent dropdown links from closing the dropdown immediately
  if (userDropdownContent) {
    const dropdownLinks = userDropdownContent.querySelectorAll("a");
    dropdownLinks.forEach((link) => {
      link.addEventListener("click", function (e) {
        // Allow navigation but prevent immediate closure
        e.stopPropagation();
        
        // Close dropdown after a short delay to allow navigation
        setTimeout(() => {
          if (userDropdown) {
            userDropdown.classList.remove("active");
          }
        }, 100);
      });
    });
  }

  // Debug: Log current screen size
  console.log("Current screen width:", window.innerWidth);
});