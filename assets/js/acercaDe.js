// ========== Market Primavera - Script Acerca de Nosotros ==========

document.addEventListener('DOMContentLoaded', () => {
    
    // ========== CONTADORES ANIMADOS ==========
    const animateCounters = () => {
        const statNumbers = document.querySelectorAll('.stat-number');
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                    const target = parseInt(entry.target.getAttribute('data-target'));
                    const duration = 2000;
                    const increment = target / (duration / 16);
                    
                    let current = 0;
                    const counter = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            entry.target.textContent = target.toLocaleString('es-PE');
                            clearInterval(counter);
                            entry.target.classList.add('animated');
                        } else {
                            entry.target.textContent = Math.floor(current).toLocaleString('es-PE');
                        }
                    }, 16);
                }
            });
        }, observerOptions);

        statNumbers.forEach(number => {
            observer.observe(number);
        });
    };

    animateCounters();

    // ========== OBSERVADOR DE ELEMENTOS ==========
    const observeElements = () => {
        const elements = document.querySelectorAll('.valor-card, .miembro-card, .stat-card');
        
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = `${getRandomAnimation()} 0.8s ease-out forwards`;
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        elements.forEach(element => {
            observer.observe(element);
        });
    };

    const getRandomAnimation = () => {
        const animations = ['fadeInUp', 'scaleIn', 'slideInRight'];
        return animations[Math.floor(Math.random() * animations.length)];
    };

    observeElements();

    // ========== EFECTO PARALLAX ==========
    const parallaxEffect = () => {
        const hero = document.querySelector('.hero');
        if (!hero) return;

        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;
            hero.style.backgroundPosition = `center ${scrollY * 0.5}px`;
        });
    };

    parallaxEffect();

    // ========== SCROLL SUAVE ==========
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // ========== EFECTO HOVER EN TARJETAS ==========
    const enhanceCardHovers = () => {
        const cards = document.querySelectorAll('.valor-card, .miembro-card, .stat-card');

        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = `0 20px 50px rgba(227, 6, 19, 0.25)`;
            });

            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '';
            });

            card.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const lightX = (x / rect.width) * 100;
                const lightY = (y / rect.height) * 100;

                this.style.setProperty('--light-x', `${lightX}%`);
                this.style.setProperty('--light-y', `${lightY}%`);
            });
        });
    };

    enhanceCardHovers();

    // ========== STAGGER ANIMATION ==========
    const staggerAnimation = () => {
        const cards = document.querySelectorAll('.valor-card, .miembro-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    };

    staggerAnimation();
// ========== EFECTO RIPPLE EN BOTONES ==========
const createRippleEffect = () => {
    const buttons = document.querySelectorAll('.btn-cta, button');

    buttons.forEach(button => {
        button.addEventListener('click', function (e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.classList.add('ripple-effect');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            ripple.style.width = `${size}px`;
            ripple.style.height = `${size}px`;

            this.classList.add('ripple-container');
            this.appendChild(ripple);

            setTimeout(() => ripple.remove(), 600);
        });
    });
};

createRippleEffect();

    // ========== VALIDACIÓN DE NAVEGACIÓN ==========
    const highlightActiveNav = () => {
        const navLinks = document.querySelectorAll('.main-nav a');
        const currentPage = window.location.pathname.split('/').pop() || 'index.php';

        navLinks.forEach(link => {
            const href = link.getAttribute('href').split('/').pop();
            if (href === currentPage || (currentPage === '' && href === 'index.php')) {
                link.classList.add('active');
            }
        });
    };

    highlightActiveNav();

    // ========== INTERSECCIÓN OBSERVER MEJORADO ==========
    const setupIntersectionObserver = () => {
        const observerOptions = {
            threshold: [0, 0.25, 0.5, 0.75, 1]
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.intersectionRatio > 0.1) {
                    entry.target.classList.add('in-view');
                } else {
                    entry.target.classList.remove('in-view');
                }
            });
        }, observerOptions);

        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });
    };

    setupIntersectionObserver();

    // ========== SOPORTE PARA DISPOSITIVOS TÁCTILES ==========
    if (window.matchMedia('(pointer:coarse)').matches) {
        document.querySelectorAll('.valor-card, .miembro-card').forEach(card => {
            card.addEventListener('touchstart', function() {
                this.classList.add('touch-active');
            });
            card.addEventListener('touchend', function() {
                this.classList.remove('touch-active');
            });
        });
    }

    // ========== DETECTOR DE TEMA OSCURO ==========
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        console.log('Modo oscuro detectado');
    }

});