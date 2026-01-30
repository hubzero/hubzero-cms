/**
 * HUBzero Installation Wizard JavaScript
 */
(function() {
    'use strict';

    // Form validation
    document.querySelectorAll('.step-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var isValid = true;
            var firstError = null;

            // Check required fields
            form.querySelectorAll('[required]').forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                    if (!firstError) firstError = field;
                } else {
                    field.classList.remove('error');
                }
            });

            // Check password confirmation
            var password = form.querySelector('#password');
            var confirm = form.querySelector('#password_confirm');
            if (password && confirm && password.value !== confirm.value) {
                isValid = false;
                confirm.classList.add('error');
                if (!firstError) firstError = confirm;
            }

            if (!isValid) {
                e.preventDefault();
                if (firstError) {
                    firstError.focus();
                }
            }
        });
    });

    // Clear error state on input
    document.querySelectorAll('input, select, textarea').forEach(function(field) {
        field.addEventListener('input', function() {
            this.classList.remove('error');
            var group = this.closest('.form-group');
            if (group) {
                group.classList.remove('has-error');
            }
        });
    });

    // Password strength indicator (optional enhancement)
    var passwordField = document.querySelector('#password');
    if (passwordField) {
        passwordField.addEventListener('input', function() {
            var strength = calculatePasswordStrength(this.value);
            // Could add a strength indicator here
        });
    }

    function calculatePasswordStrength(password) {
        var strength = 0;
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;
        return strength;
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Auto-hide success/info messages after 8 seconds, but keep error messages visible longer (30 seconds)
    document.querySelectorAll('.message').forEach(function(msg) {
        var isError = msg.classList.contains('message-error');
        var timeout = isError ? 30000 : 8000;
        setTimeout(function() {
            msg.style.opacity = '0';
            msg.style.transition = 'opacity 0.3s';
            setTimeout(function() {
                msg.style.display = 'none';
            }, 300);
        }, timeout);
    });

})();
