document.addEventListener('DOMContentLoaded', function() {
    // User dropdown
    const userDropdown = document.querySelector('.user-dropdown');
    if (userDropdown) {
        const dropdownBtn = userDropdown.querySelector('.user-dropdown-btn');
        const dropdownContent = userDropdown.querySelector('.user-dropdown-content');

        dropdownBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // biar klik ga langsung ketutup
            userDropdown.classList.toggle('active');
        });

        // klik di luar nutup menu
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
    }

    // Hamburger menu toggle
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }
});
