// Navbar JavaScript dengan perbaikan hamburger menu
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    const userDropdown = document.querySelector('.user-dropdown');
    const userDropdownBtn = document.querySelector('.user-dropdown-btn');
    
    // Toggle hamburger menu
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle classes
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
            
            // Toggle body scroll
            if (navMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
    }
    
    // Handle user dropdown for mobile
    if (userDropdownBtn && userDropdown) {
        userDropdownBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });
    }
    
    // Close menu when clicking outside (mobile)
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            // Close nav menu if clicking outside
            if (navMenu && hamburger && 
                !navMenu.contains(e.target) && 
                !hamburger.contains(e.target) &&
                navMenu.classList.contains('active')) {
                
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            // Close user dropdown if clicking outside
            if (userDropdown && userDropdownBtn &&
                !userDropdown.contains(e.target) &&
                userDropdown.classList.contains('active')) {
                
                userDropdown.classList.remove('active');
            }
        }
    });
    
    // Close menu when clicking on nav links (mobile)
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            // Reset mobile menu states
            if (navMenu) navMenu.classList.remove('active');
            if (hamburger) hamburger.classList.remove('active');
            if (userDropdown) userDropdown.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
    
    // Prevent menu close when clicking inside dropdown content
    if (userDropdown) {
        const dropdownContent = userDropdown.querySelector('.user-dropdown-content');
        if (dropdownContent) {
            dropdownContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }
    
    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (navMenu && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
                document.body.style.overflow = '';
            }
            if (userDropdown && userDropdown.classList.contains('active')) {
                userDropdown.classList.remove('active');
            }
        }
    });
});