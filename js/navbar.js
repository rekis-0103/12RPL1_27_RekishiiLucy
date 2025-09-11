// Mobile menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');
    
    // Toggle mobile menu
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
            
            // Prevent body scroll when menu is open
            if (navMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
    }

    // Close mobile menu when clicking on navigation links
    const navLinks = document.querySelectorAll('.nav-menu a:not(.user-dropdown-btn)');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('.user-dropdown-content');
        const isClickInsideDropdown = event.target.closest('.user-dropdown');
        
        if (!isClickInsideDropdown) {
            dropdowns.forEach(dropdown => {
                dropdown.style.display = 'none';
                if (dropdown.closest('.user-dropdown')) {
                    dropdown.closest('.user-dropdown').classList.remove('active');
                }
            });
        }
    });

    // Prevent dropdown from closing when clicking inside it
    const userDropdowns = document.querySelectorAll('.user-dropdown-content');
    userDropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });
});

// Mobile user dropdown toggle function
function toggleMobileUserDropdown() {
    const mobileDropdown = document.querySelector('.nav-auth-mobile .user-dropdown');
    const dropdownContent = mobileDropdown.querySelector('.user-dropdown-content');
    
    if (dropdownContent.style.display === 'block') {
        dropdownContent.style.display = 'none';
        mobileDropdown.classList.remove('active');
    } else {
        dropdownContent.style.display = 'block';
        mobileDropdown.classList.add('active');
    }
}