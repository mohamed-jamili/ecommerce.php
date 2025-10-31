// script.js - JavaScript pour CompteStore

// Configuration
const CONFIG = {
    CURRENCY: 'DH',
    ANIMATION_DELAY: 100
};

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initAnimations();
    initCartInteractions();
    initForms();
    initSearch();
});

// Animations
function initAnimations() {
    // Animation au scroll
    const animatedElements = document.querySelectorAll('.animate__animated');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const animation = entry.target.getAttribute('data-animation') || 'fadeInUp';
                entry.target.classList.add(`animate__${animation}`);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    animatedElements.forEach(el => observer.observe(el));
}

// Gestion panier
function initCartInteractions() {
    // Ajout au panier avec animation
    const addToCartForms = document.querySelectorAll('form[action="cart.php"]');
    
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            if (button) {
                button.classList.add('loading');
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Ajout...';
                
                setTimeout(() => {
                    button.classList.remove('loading');
                    button.classList.add('success-animation');
                    button.innerHTML = '<i class="fas fa-check me-1"></i>Ajouté !';
                    
                    setTimeout(() => {
                        button.classList.remove('success-animation');
                        button.innerHTML = '<i class="fas fa-cart-plus me-1"></i>Ajouter';
                    }, 1000);
                }, 500);
            }
        });
    });
    
    // Mise à jour quantité panier
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value < 1) this.value = 1;
            if (this.value > 99) this.value = 99;
        });
    });
}

// Gestion formulaires
function initForms() {
    // Validation formulaires
    const forms = document.querySelectorAll('form[method="POST"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    showFieldError(field, 'Ce champ est obligatoire');
                } else {
                    clearFieldError(field);
                }
            });
            
            // Validation téléphone
            const phoneField = this.querySelector('input[type="tel"]');
            if (phoneField && phoneField.value) {
                const phoneRegex = /^[0-9]{10}$/;
                if (!phoneRegex.test(phoneField.value)) {
                    valid = false;
                    showFieldError(phoneField, 'Le téléphone doit contenir 10 chiffres');
                }
            }
            
            if (!valid) {
                e.preventDefault();
                showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
            }
        });
    });
    
    // Masquer les messages après 5 secondes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    });
}

// Recherche
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 2 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 500);
        });
    }
}

// Notifications
function showNotification(message, type = 'info') {
    // Supprimer les notifications existantes
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Créer nouvelle notification
    const alert = document.createElement('div');
    alert.className = `custom-alert alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alert.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    `;
    
    alert.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${getNotificationIcon(type)} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.appendChild(alert);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

function getNotificationIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-triangle',
        'warning': 'exclamation-circle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

// Gestion erreurs champs
function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('is-invalid');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('is-invalid');
    
    const existingError = field.parentNode.querySelector('.invalid-feedback');
    if (existingError) {
        existingError.remove();
    }
}

// Utilitaires
function formatPrice(price) {
    return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
    }).format(price);
}

// Compteur produits panier
function updateCartCount(count) {
    const cartCounts = document.querySelectorAll('#cartCount');
    cartCounts.forEach(element => {
        element.textContent = count;
        element.classList.add('pulse-animation');
        setTimeout(() => element.classList.remove('pulse-animation'), 600);
    });
}

// Animation pulse
const style = document.createElement('style');
style.textContent = `
    .pulse-animation {
        animation: pulse 0.6s ease;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);

// Export global pour usage dans HTML
window.CompteStore = {
    showNotification,
    formatPrice,
    updateCartCount
};