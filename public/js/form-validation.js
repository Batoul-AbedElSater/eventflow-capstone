/* ============================================
   FORM VALIDATION AND ERROR HANDLING
   ============================================ */

class FormValidator {
    constructor(formElement) {
        this.form = formElement;
        this.errors = {};
        this.init();
    }

    init() {
        if (!this.form) return;
        this.form.addEventListener('submit', (e) => this.validate(e));
        this.setupFieldListeners();
    }

    setupFieldListeners() {
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('change', () => this.validateField(input));
        });
    }

    validate(e) {
        this.errors = {};
        const inputs = this.form.querySelectorAll('input, textarea, select');

        inputs.forEach(input => {
            this.validateField(input);
        });

        if (Object.keys(this.errors).length > 0) {
            e.preventDefault();
            this.showErrors();
            return false;
        }

        return true;
    }

    validateField(input) {
        const value = input.value.trim();
        const name = input.getAttribute('name');
        const type = input.getAttribute('type');
        const required = input.hasAttribute('required');

        // Reset error
        delete this.errors[name];
        this.clearFieldError(input);

        // Required validation
        if (required && !value) {
            this.errors[name] = `${this.getFieldLabel(input)} is required`;
            this.setFieldError(input);
            return;
        }

        if (!value) return; // Skip other validations if empty and not required

        // Type-specific validation
        switch (type) {
            case 'email':
                if (!this.isValidEmail(value)) {
                    this.errors[name] = 'Please enter a valid email address';
                }
                break;
            case 'tel':
                if (!this.isValidPhone(value)) {
                    this.errors[name] = 'Please enter a valid phone number';
                }
                break;
            case 'url':
                if (!this.isValidUrl(value)) {
                    this.errors[name] = 'Please enter a valid URL';
                }
                break;
            case 'number':
                if (isNaN(value)) {
                    this.errors[name] = 'Please enter a valid number';
                }
                break;
            case 'password':
                if (value.length < 8) {
                    this.errors[name] = 'Password must be at least 8 characters';
                }
                break;
        }

        // Min length
        const minLength = input.getAttribute('minlength');
        if (minLength && value.length < minLength) {
            this.errors[name] = `${this.getFieldLabel(input)} must be at least ${minLength} characters`;
        }

        // Max length
        const maxLength = input.getAttribute('maxlength');
        if (maxLength && value.length > maxLength) {
            this.errors[name] = `${this.getFieldLabel(input)} must be no more than ${maxLength} characters`;
        }

        // Pattern validation
        const pattern = input.getAttribute('pattern');
        if (pattern && value && !new RegExp(pattern).test(value)) {
            this.errors[name] = `${this.getFieldLabel(input)} format is invalid`;
        }

        if (this.errors[name]) {
            this.setFieldError(input);
        }
    }

    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    isValidPhone(phone) {
        const re = /^\+?[\d\s\-()]{10,}$/;
        return re.test(phone);
    }

    isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }

    getFieldLabel(input) {
        const label = this.form.querySelector(`label[for="${input.id}"]`);
        if (label) return label.textContent.trim().replace(/[*:]/g, '');
        return input.getAttribute('name').replace(/_/g, ' ');
    }

    setFieldError(input) {
        input.style.borderColor = '#f44336';
        input.style.boxShadow = '0 0 0 3px rgba(244, 67, 54, 0.1)';
    }

    clearFieldError(input) {
        input.style.borderColor = '';
        input.style.boxShadow = '';
    }

    showErrors() {
        let errorMessage = 'Please fix the following errors:\n\n';
        for (let [field, message] of Object.entries(this.errors)) {
            errorMessage += `• ${message}\n`;
        }

        alert(errorMessage);
    }
}

// Initialize all forms with validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        if (form.getAttribute('data-validate') !== 'false') {
            new FormValidator(form);
        }
    });
});

// Password confirmation validation
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.querySelector('input[name="new_password"]');
    const confirmField = document.querySelector('input[name="new_password_confirmation"]');

    if (passwordField && confirmField) {
        confirmField.addEventListener('blur', function() {
            if (this.value && this.value !== passwordField.value) {
                this.style.borderColor = '#f44336';
                this.style.boxShadow = '0 0 0 3px rgba(244, 67, 54, 0.1)';
            } else {
                this.style.borderColor = '';
                this.style.boxShadow = '';
            }
        });
    }
});