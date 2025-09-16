document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');
    
    // === Mobile menu toggle ===
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
            
            if (navMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
    }

    // === Close menu on nav link click (mobile) ===
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

    // === Reset menu on resize ===
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // === Dropdown toggle (desktop + mobile) ===
    const userDropdownBtns = document.querySelectorAll('.user-dropdown-btn');
    userDropdownBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = btn.closest('.user-dropdown');
            const content = dropdown.querySelector('.user-dropdown-content');
            
            // tutup semua dropdown lain dulu
            document.querySelectorAll('.user-dropdown-content').forEach(d => {
                if (d !== content) {
                    d.style.display = 'none';
                    d.closest('.user-dropdown').classList.remove('active');
                }
            });

            // toggle dropdown ini
            if (content.style.display === 'block') {
                content.style.display = 'none';
                dropdown.classList.remove('active');
            } else {
                content.style.display = 'block';
                dropdown.classList.add('active');
            }
        });
    });

    // === Klik luar nutup dropdown ===
    document.addEventListener('click', function(event) {
        const isClickInsideDropdown = event.target.closest('.user-dropdown');
        
        if (!isClickInsideDropdown) {
            document.querySelectorAll('.user-dropdown-content').forEach(dropdown => {
                dropdown.style.display = 'none';
                if (dropdown.closest('.user-dropdown')) {
                    dropdown.closest('.user-dropdown').classList.remove('active');
                }
            });
        }
    });

    // === Prevent close if click inside dropdown ===
    document.querySelectorAll('.user-dropdown-content').forEach(dropdown => {
        dropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });
});
