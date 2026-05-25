lucide.createIcons();

// Mobile menu toggle
const menuBtn = document.getElementById('menu-btn');
const navMenu = document.getElementById('nav-menu');

if (menuBtn && navMenu) {
    menuBtn.addEventListener('click', () => {
        navMenu.classList.toggle('is-active');
        const icon = menuBtn.querySelector('i');
        icon.setAttribute('data-lucide', navMenu.classList.contains('is-active') ? 'x' : 'menu');
        lucide.createIcons();
    });

    // Close menu when clicking a link
    navMenu.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('is-active');
            const icon = menuBtn.querySelector('i');
            icon.setAttribute('data-lucide', 'menu');
            lucide.createIcons();
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!navMenu.contains(e.target) && !menuBtn.contains(e.target)) {
            navMenu.classList.remove('is-active');
            const icon = menuBtn.querySelector('i');
            icon.setAttribute('data-lucide', 'menu');
            lucide.createIcons();
        }
    });
}

// Scroll reveal (Intersection Observer)
const revealElements = document.querySelectorAll('.reveal');
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

revealElements.forEach(el => revealObserver.observe(el));

// Animate progress bars (only on skills page)
const progressBars = document.querySelectorAll('.progress-fill');
const progressObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const bar = entry.target;
            bar.style.width = bar.getAttribute('data-width') + '%';
            progressObserver.unobserve(bar);
        }
    });
}, { threshold: 0.5 });

progressBars.forEach(bar => progressObserver.observe(bar));

// Contact form feedback
function handleFormSubmit(event) {
    event.preventDefault();
    const btn = event.target.querySelector('button[type="submit"]');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="check"></i> Message Sent!';
    btn.style.background = '#4caf50';
    btn.style.color = '#fff';
    btn.disabled = true;
    lucide.createIcons();

    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.style.background = '';
        btn.style.color = '';
        btn.disabled = false;
        event.target.reset();
        lucide.createIcons();
    }, 3000);
}