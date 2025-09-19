// Enhanced Page Transition Script
document.addEventListener('DOMContentLoaded', function() {
    // Handle navigation links with smooth transitions
    const navLinks = document.querySelectorAll('a[href="login.php"], a[href="register.php"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const currentForm = document.querySelector('.login-form, .register-form');
            const targetUrl = this.getAttribute('href');
            
            // Add exit animation
            if (currentForm) {
                currentForm.style.transform = 'translateX(-30px)';
                currentForm.style.opacity = '0';
                currentForm.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                
                // Navigate after animation completes
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 600);
            } else {
                window.location.href = targetUrl;
            }
        });
    });

    // Enhanced form input animations
    const inputs = document.querySelectorAll('.form-group input');
    inputs.forEach((input, index) => {
        // Add stagger animation delay
        input.style.animationDelay = `${index * 0.1}s`;
        
        // Focus and blur effects
        input.addEventListener('focus', function() {
            this.style.transform = 'translateY(-2px)';
            this.parentElement.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.style.transform = 'translateY(0)';
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Enhanced password toggle functionality
    window.togglePassword = function(inputId) {
        let passwordInput;
        let toggleButton;
        
        if (inputId) {
            // For register page with multiple password fields
            passwordInput = document.getElementById(inputId);
            toggleButton = passwordInput.parentElement.querySelector('.password-toggle i');
        } else {
            // For login page with single password field
            passwordInput = document.getElementById('password');
            toggleButton = document.querySelector('.password-toggle i');
        }
        
        if (passwordInput && toggleButton) {
            // Add transition class
            toggleButton.style.transform = 'translateY(-50%) scale(0.8)';
            
            setTimeout(() => {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleButton.classList.remove('fa-eye');
                    toggleButton.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    toggleButton.classList.remove('fa-eye-slash');
                    toggleButton.classList.add('fa-eye');
                }
                
                // Reset transform
                setTimeout(() => {
                    toggleButton.style.transform = 'translateY(-50%) scale(1)';
                }, 50);
            }, 100);
        }
    };

    // Form submission enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.login-btnn, .register-btn');
            if (submitBtn) {
                submitBtn.style.transform = 'scale(0.98)';
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                
                // Disable button to prevent double submission
                submitBtn.disabled = true;
            }
        });
    });

    // Add ripple effect to buttons
    function addRippleEffect(button) {
        button.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('span');
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
                z-index: 10;
            `;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    }

    // Add ripple CSS animation
    const rippleStyle = document.createElement('style');
    rippleStyle.textContent = `
        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
        
        .login-btnn, .register-btn {
            position: relative;
            overflow: hidden;
        }

        .floating-particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
            animation: floatParticle linear infinite;
        }

        @keyframes floatParticle {
            0% {
                transform: translateY(100vh) translateX(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) translateX(50px) rotate(360deg);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(rippleStyle);

    // Apply ripple effect to buttons
    const buttons = document.querySelectorAll('.login-btnn, .register-btn');
    buttons.forEach(addRippleEffect);

    // Create floating particles effect
    function createFloatingParticles() {
        const container = document.querySelector('.login-container, .register-container');
        if (!container) return;
        
        for (let i = 0; i < 15; i++) {
            const particle = document.createElement('div');
            particle.classList.add('floating-particle');
            particle.style.cssText = `
                width: ${Math.random() * 3 + 1}px;
                height: ${Math.random() * 3 + 1}px;
                left: ${Math.random() * 100}%;
                top: 100%;
                animation-duration: ${Math.random() * 8 + 12}s;
                animation-delay: ${Math.random() * 5}s;
                z-index: 1;
            `;
            container.appendChild(particle);
        }
    }

    // Create particles
    createFloatingParticles();
});