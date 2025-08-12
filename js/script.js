// Mobile Navigation Toggle
const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('nav-menu');

if (hamburger && navMenu) {
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    // Close menu when clicking on a link
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        });
    });
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#ff4444';
            isValid = false;
        } else {
            input.style.borderColor = '#444';
        }
    });

    // Email validation
    const emailInputs = form.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (input.value && !emailRegex.test(input.value)) {
            input.style.borderColor = '#ff4444';
            isValid = false;
        }
    });

    return isValid;
}

// Progress bar animation
function animateProgressBar(elementId, targetValue) {
    const progressBar = document.getElementById(elementId);
    if (!progressBar) return;

    let currentValue = 0;
    const increment = targetValue / 50;
    
    const timer = setInterval(() => {
        currentValue += increment;
        if (currentValue >= targetValue) {
            currentValue = targetValue;
            clearInterval(timer);
        }
        
        progressBar.style.width = currentValue + '%';
        progressBar.textContent = Math.round(currentValue) + '%';
    }, 20);
}

// Motivation level slider
function updateMotivationDisplay(value) {
    const display = document.getElementById('motivation-display');
    const progressBar = document.getElementById('motivation-progress');
    
    if (display) display.textContent = value;
    if (progressBar) {
        progressBar.style.width = value + '%';
        progressBar.textContent = value + '%';
    }
}

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
});

// Search functionality
function searchContent(query, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const items = container.querySelectorAll('.card, .quote-card, .message-container');
    const searchTerm = query.toLowerCase();

    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(searchTerm) || searchTerm === '') {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Real-time character counter for textareas
document.querySelectorAll('textarea[maxlength]').forEach(textarea => {
    const maxLength = textarea.getAttribute('maxlength');
    const counter = document.createElement('div');
    counter.className = 'char-counter';
    counter.style.textAlign = 'right';
    counter.style.fontSize = '0.8rem';
    counter.style.color = '#888';
    counter.style.marginTop = '0.25rem';
    
    textarea.parentNode.appendChild(counter);
    
    function updateCounter() {
        const remaining = maxLength - textarea.value.length;
        counter.textContent = `${textarea.value.length}/${maxLength} characters`;
        counter.style.color = remaining < 50 ? '#ff4444' : '#888';
    }
    
    textarea.addEventListener('input', updateCounter);
    updateCounter();
});