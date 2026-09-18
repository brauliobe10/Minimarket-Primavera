// ========== SCRIPT DE ATENCIÓN AL CLIENTE ========== //

document.addEventListener('DOMContentLoaded', function() {
    initializeCustomerService();
});

function initializeCustomerService() {
    initializeFormValidation();
    initializeFAQ();
    initializeAnimations();
    initializeScrollEffects();
    initializeFAQSearch();
}

// ========== VALIDACIÓN DE FORMULARIO ========== //
function initializeFormValidation() {
    const form = document.getElementById('contactForm');
    const submitBtn = document.querySelector('.btn-submit');
    const btnText = document.querySelector('.btn-text');
    const btnLoader = document.querySelector('.btn-loader');
    
    if (!form) return;

    const fields = form.querySelectorAll('input, select, textarea');
    fields.forEach(field => {
        field.addEventListener('blur', () => validateField(field));
        field.addEventListener('input', () => clearFieldError(field));
    });

    form.addEventListener('submit', handleFormSubmit);

    const asuntoSelect = document.getElementById('asunto');
    const asuntoOtroInput = document.getElementById('asunto_otro');
    if (asuntoSelect && asuntoOtroInput) {
        asuntoSelect.addEventListener('change', function() {
            if (this.value === 'otro') {
                asuntoOtroInput.style.display = 'block';
                asuntoOtroInput.setAttribute('required', 'required');
            } else {
                asuntoOtroInput.style.display = 'none';
                asuntoOtroInput.removeAttribute('required');
                asuntoOtroInput.value = ''; // limpiar si se esconde
            }
        });
    }
}

function validateField(field) {
    const fieldName = field.name;
    const value = field.value.trim();
    const formGroup = field.closest('.form-group');
    const errorElement = document.getElementById(`error-${fieldName}`);
    
    let isValid = true;
    let errorMessage = '';

    clearFieldError(field);

    switch(fieldName) {
        case 'asunto':
            if (!value) {
                errorMessage = 'Por favor selecciona un asunto';
                isValid = false;
            }
            break;

        case 'mensaje':
            if (!value) {
                errorMessage = 'El mensaje es obligatorio';
                isValid = false;
            } else if (value.length < 10) {
                errorMessage = 'El mensaje debe tener al menos 10 caracteres';
                isValid = false;
            } else if (value.length > 1000) {
                errorMessage = 'El mensaje no puede exceder 1000 caracteres';
                isValid = false;
            }
            break;

        case 'acepto-terminos':
            if (!field.checked) {
                errorMessage = 'Debes aceptar los términos y condiciones';
                isValid = false;
            }
            break;
    }

    if (!isValid) {
        showFieldError(field, errorMessage);
    } else {
        showFieldSuccess(field);
    }

    return isValid;
}

function showFieldError(field, message) {
    const formGroup = field.closest('.form-group');
    const errorElement = document.getElementById(`error-${field.name}`);
    
    formGroup.classList.add('error');
    formGroup.classList.remove('success');
    
    if (errorElement) {
        errorElement.textContent = message;
    }
    
    field.style.animation = 'shake 0.5s ease-in-out';
    setTimeout(() => {
        field.style.animation = '';
    }, 500);
}

function showFieldSuccess(field) {
    const formGroup = field.closest('.form-group');
    formGroup.classList.remove('error');
    formGroup.classList.add('success');
}

function clearFieldError(field) {
    const formGroup = field.closest('.form-group');
    const errorElement = document.getElementById(`error-${field.name}`);
    
    formGroup.classList.remove('error', 'success');
    
    if (errorElement) {
        errorElement.textContent = '';
    }
}

function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const fields = form.querySelectorAll('input, select, textarea');
    const submitBtn = document.querySelector('.btn-submit');
    const btnText = document.querySelector('.btn-text');
    const btnLoader = document.querySelector('.btn-loader');
    
    let isFormValid = true;

    fields.forEach(field => {
        if (!validateField(field)) {
            isFormValid = false;
        }
    });

    if (!isFormValid) {
        showNotification('Por favor corrige los errores en el formulario', 'error');
        
        const firstError = form.querySelector('.form-group.error');
        if (firstError) {
            firstError.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }
        return;
    }

    enviarFormulario(form, submitBtn, btnText, btnLoader);
}

function enviarFormulario(form, submitBtn, btnText, btnLoader) {
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'block';

    const formData = new FormData(form);

    fetch(BASE_URL + '/controllers/AtencionClienteController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        btnText.style.display = 'block';
        btnLoader.style.display = 'none';

        if (data.success) {
            document.querySelector('.contact-form').style.display = 'none';
            document.getElementById('success-message').style.display = 'block';

            document.getElementById('success-message').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            showNotification('¡Mensaje enviado correctamente!', 'success');
            console.log('Formulario guardado en BD');
        } else {
            showNotification(data.message || 'Ocurrió un error al enviar.', 'error');
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        btnText.style.display = 'block';
        btnLoader.style.display = 'none';

        showNotification('Error de conexión. Intenta de nuevo.', 'error');
        console.error(error);
    });
}

function resetForm() {
    const form = document.getElementById('contactForm');
    const successMessage = document.getElementById('success-message');
    const contactForm = document.querySelector('.contact-form');
    
    form.reset();
    
    const formGroups = form.querySelectorAll('.form-group');
    formGroups.forEach(group => {
        group.classList.remove('error', 'success');
    });
    
    const errorMessages = form.querySelectorAll('.error-message');
    errorMessages.forEach(error => {
        error.textContent = '';
    });
    
    successMessage.style.display = 'none';
    contactForm.style.display = 'block';
    
    contactForm.scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
}

// ========== FAQ ========== //
function initializeFAQ() {
    initializeFAQCategories();
    initializeFAQItems();
}

function initializeFAQCategories() {
    const categoryButtons = document.querySelectorAll('.faq-category');
    const categoryContents = document.querySelectorAll('.faq-category-content');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            const category = button.dataset.category;
            
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            categoryContents.forEach(content => content.classList.remove('active'));
            
            button.classList.add('active');

            const targetContent = document.querySelector(
                `.faq-category-content[data-category="${category}"]`
            );

            if (targetContent) {
                targetContent.classList.add('active');
                targetContent.style.animation = 'fadeInUp 0.5s ease-out';
                targetContent.querySelectorAll('.faq-item')
                    .forEach(item => item.classList.remove('active'));
            }
        });
    });
    
    setTimeout(() => {
        const firstButton = document.querySelector('.faq-category');
        if (firstButton) firstButton.click();
    }, 100);
}

function initializeFAQItems() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            const currentCategory = item.closest('.faq-category-content');
            const siblingItems = currentCategory.querySelectorAll('.faq-item');
            siblingItems.forEach(sibling => {
                if (sibling !== item) {
                    sibling.classList.remove('active');
                }
            });
            
            if (isActive) {
                item.classList.remove('active');
            } else {
                item.classList.add('active');
                
                setTimeout(() => {
                    item.scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }, 300);
            }
            
            console.log(`❓ FAQ item ${isActive ? 'cerrado' : 'abierto'}`);
        });
    });
}

// ========== BÚSQUEDA EN FAQ ========== //
function initializeFAQSearch() {
    const searchInput = document.getElementById('faq-search-input');
    const searchBtn = document.getElementById('faq-search-btn');
    const searchResults = document.getElementById('faq-search-results');
    
    if (!searchInput || !searchBtn || !searchResults) return;
    
    searchBtn.addEventListener('click', performFAQSearch);
    
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            performFAQSearch();
        }
    });
    
    let searchTimeout;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        const mainContent = document.querySelector('.main-content');
        const faqSearch = document.querySelector('.faq-search');
        
        if (query === '') {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            mainContent.classList.remove('has-search-results');
            faqSearch.classList.remove('has-results');
            return;
        }
        
        if (query.length >= 3) {
            searchTimeout = setTimeout(() => {
                performFAQSearch();
            }, 500);
        }
    });
}

function performFAQSearch() {
    const searchInput = document.getElementById('faq-search-input');
    const searchResults = document.getElementById('faq-search-results');
    const query = searchInput.value.trim().toLowerCase();
    
    if (!query) {
        searchResults.innerHTML = '<p class="faq-search-empty">Por favor ingresa un término de búsqueda.</p>';
        searchResults.style.display = 'block';
        return;
    }
    
    if (query.length < 3) {
        searchResults.innerHTML = '<p class="faq-search-empty">Ingresa al menos 3 caracteres para buscar.</p>';
        searchResults.style.display = 'block';
        return;
    }
    
    const allFAQItems = document.querySelectorAll('.faq-item');
    const results = [];
    
    allFAQItems.forEach((item, index) => {
        const question = item.querySelector('.faq-question span').textContent.toLowerCase();
        const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();
        
        if (question.includes(query) || answer.includes(query)) {
            const categoryContent = item.closest('.faq-category-content');
            const categoryName = categoryContent.dataset.category;
            
            results.push({
                element: item,
                category: categoryName,
                question: item.querySelector('.faq-question span').textContent,
                answer: item.querySelector('.faq-answer p').textContent
            });
        }
    });
    
    displaySearchResults(results, query);
    searchResults.style.display = 'block';
    
    setTimeout(() => {
        searchResults.scrollIntoView({ 
            behavior: 'smooth',
            block: 'nearest'
        });
    }, 100);
}

function displaySearchResults(results, query) {
    const searchResults = document.getElementById('faq-search-results');
    const mainContent = document.querySelector('.main-content');
    const faqSearch = document.querySelector('.faq-search');
    
    if (results.length === 0) {
        searchResults.innerHTML = `
            <div class="faq-search-no-results">
                <strong class="faq-search-no-results-title">
                    <i class="fas fa-search"></i> No se encontraron resultados
                </strong>
                <p class="faq-search-no-results-text">
                    No encontramos preguntas relacionadas con "<strong>${query}</strong>". 
                    <a href="#formulario-contacto"
                       onclick="scrollToSection('formulario-contacto')"
                       class="faq-search-contact-link">
                       Contáctanos directamente
                    </a>
                    para obtener ayuda personalizada.
                </p>
            </div>
        `;
        mainContent.classList.add('has-search-results');
        faqSearch.classList.add('has-results');
        return;
    }
    
    let resultsHTML = `
        <div class="faq-search-results-header">
            <strong class="faq-search-results-title">
                <i class="fas fa-check-circle"></i> ${results.length} resultado${results.length > 1 ? 's' : ''} encontrado${results.length > 1 ? 's' : ''}
            </strong>
        </div>
        <div class="faq-search-results-list">
    `;
    
    results.forEach((result, index) => {
        const categoryLabel = getCategoryLabel(result.category);
        const itemIndex = getItemIndex(result.element);
        const previewText = result.answer.length > 200 
            ? result.answer.substring(0, 200) + '...' 
            : result.answer;
        
        resultsHTML += `
            <div class="search-result-item" 
                 onclick="goToFAQItem('${result.category}', ${itemIndex})">
                <div class="faq-search-result-category">
                    <i class="fas fa-folder-open"></i> ${categoryLabel}
                </div>
                <div class="faq-search-result-title">
                    ${highlightQuery(result.question, query)}
                </div>
                <div class="faq-search-result-preview">
                    ${highlightQuery(previewText, query)}
                </div>
                <div class="faq-search-result-link">
                    <i class="fas fa-arrow-right"></i> Ver respuesta completa
                </div>
            </div>
        `;
    });
    
    resultsHTML += '</div>';
    searchResults.innerHTML = resultsHTML;
    
    mainContent.classList.add('has-search-results');
    faqSearch.classList.add('has-results');
    
    console.log(`🔍 Búsqueda FAQ realizada: "${query}" - ${results.length} resultados`);
}

function highlightQuery(text, query) {
    const regex = new RegExp(`(${query})`, 'gi');
    return text.replace(regex, '<mark class="faq-highlight">$1</mark>');
}

function getCategoryLabel(category) {
    const labels = {
        'general': 'General',
        'pedidos': 'Pedidos',
        'entrega': 'Entrega',
        'pagos': 'Pagos',
        'cuenta': 'Mi Cuenta'
    };
    return labels[category] || category;
}

function getItemIndex(item) {
    const siblings = Array.from(item.parentNode.children);
    return siblings.indexOf(item);
}

function goToFAQItem(category, itemIndex) {
    const searchInput = document.getElementById('faq-search-input');
    const searchResults = document.getElementById('faq-search-results');
    const mainContent = document.querySelector('.main-content');
    const faqSearch = document.querySelector('.faq-search');

    if (searchInput) searchInput.value = '';

    if (searchResults) {
        searchResults.innerHTML = '';
        searchResults.style.display = 'none';
    }

    if (mainContent) mainContent.classList.remove('has-search-results');
    if (faqSearch) faqSearch.classList.remove('has-results');

    const categoryButton = document.querySelector(`[data-category="${category}"]`);

    if (categoryButton && categoryButton.classList.contains('faq-category')) {
        categoryButton.click();

        setTimeout(() => {
            const categoryContent = document.querySelector(
                `.faq-category-content[data-category="${category}"]`
            );

            if (!categoryContent) return;

            const faqItems = categoryContent.querySelectorAll('.faq-item');
            const faqItem = faqItems[itemIndex];

            if (faqItem) {
                faqItem.classList.add('active');

                faqItem.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                faqItem.classList.add('faq-highlight-item');

                setTimeout(() => {
                    faqItem.classList.remove('faq-highlight-item');
                }, 2000);
            }
        }, 500);
    }

    console.log(`🎯 Navegando a FAQ: ${category}, item ${itemIndex}`);
}

// ========== ANIMACIONES ========== //
function initializeAnimations() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });

    animatedElements.forEach(element => {
        observer.observe(element);
    });
}

// ========== EFECTOS DE SCROLL ========== //
function initializeScrollEffects() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const target = document.getElementById(targetId);
            
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// ========== FUNCIONES UTILITARIAS ========== //

function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

function openInGoogleMaps() {
    const address = encodeURIComponent('Urb. Los Sauces 6448, Lambayeque, Perú');
    const url = `https://www.google.com/maps/search/?api=1&query=${address}`;
    window.open(url, '_blank');
    
    console.log('🗺️ Abriendo ubicación en Google Maps');
}

function showNotification(message, type = 'info', duration = 3000) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">
                ${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}
            </span>
            <span class="notification-message">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, duration);
}

// ========== DEBUG Y LOGGING ========== //
function logFormData() {
    const form = document.getElementById('contactForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    console.log('📋 Datos del formulario:', data);
    return data;
}

// ========== FUNCIONES GLOBALES EXPUESTAS ========== //
window.CustomerService = {
    scrollToSection,
    resetForm,
    openInGoogleMaps,
    showNotification,
    performFAQSearch,
    goToFAQItem,
    logFormData
};

console.log('🚀 Script de Atención al Cliente cargado exitosamente');

// ========== EASTER EGG: KONAMI CODE ========== //
let konamiCode = [];
const konami = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65];

document.addEventListener('keydown', (e) => {
    konamiCode.push(e.keyCode);
    if (konamiCode.length > konami.length) {
        konamiCode.shift();
    }
    
    if (konamiCode.toString() === konami.toString()) {
        showNotification('🎮 ¡Modo debug activado! Revisa la consola para más información.', 'success', 5000);
        console.log('🛠 Modo DEBUG activado');
        console.log('📊 Estadísticas de la página:', {
            faqItems: document.querySelectorAll('.faq-item').length,
            formFields: document.querySelectorAll('#contactForm input, #contactForm select, #contactForm textarea').length,
            animations: document.querySelectorAll('.animate-on-scroll').length,
            loadTime: performance.now() + 'ms'
        });
        konamiCode = [];
    }
});