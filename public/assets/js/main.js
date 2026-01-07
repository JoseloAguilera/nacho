// Sistema Nacho - Main JavaScript

document.addEventListener('DOMContentLoaded', function () {
    // Mobile menu toggle with overlay
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');

    if (menuToggle && sidebar) {
        // Create overlay for mobile
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);

        // Toggle sidebar
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        // Close sidebar when clicking overlay
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        // Close sidebar when clicking a link on mobile
        if (window.innerWidth <= 768) {
            const navLinks = sidebar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function () {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            });
        }
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Confirm delete actions
    const deleteLinks = document.querySelectorAll('a[href*="/delete/"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            if (!confirm('¿Está seguro de que desea eliminar este elemento?')) {
                e.preventDefault();
            }
        });
    });

    // Format currency inputs
    const currencyInputs = document.querySelectorAll('input[type="number"][step="0.01"]');
    currencyInputs.forEach(input => {
        input.addEventListener('blur', function () {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });

    // Form validation helper
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos');
            }
        });
    });

    // Prevent iOS zoom on input focus
    if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        const viewportMeta = document.querySelector('meta[name="viewport"]');
        if (viewportMeta) {
            viewportMeta.content = 'width=device-width, initial-scale=1, maximum-scale=1';
        }
    }
});

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS'
    }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('es-AR').format(new Date(date));
}
