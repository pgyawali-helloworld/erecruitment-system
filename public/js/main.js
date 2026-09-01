/**
 * E-Recruitment Portal Main JS File
 * Contains main frontend animations, navbar transitions, and basic UI utility interactions.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Sticky Header Scroll Effect
    const navbar = document.querySelector('.navbar');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.style.padding = '0.75rem 0';
            navbar.style.boxShadow = 'var(--shadow-md)';
            navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
        } else {
            navbar.style.padding = '1rem 0';
            navbar.style.boxShadow = 'border-bottom: 1px solid var(--border-color)';
            navbar.style.backgroundColor = 'var(--glass-bg)';
        }
    });

    // 2. Add smooth scrolling for anchors
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // 3. Optional: Dynamic Tooltip or popovers initialization
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    console.log('E-Recruitment system frontend initialized.');
});
