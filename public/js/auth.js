// Toggle between Sign In and Sign Up
const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');

signUpButton.addEventListener('click', () => {
    container.classList.add('right-panel-active');
});

signInButton.addEventListener('click', () => {
    container.classList.remove('right-panel-active');
});

// Toggle Password Visibility
function togglePassword(fieldId, icon) {
    const field = document.getElementById(fieldId);
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Auto-switch to Register if validation errors exist on register form
document.addEventListener('DOMContentLoaded', function() {
    const registerErrors = document.querySelector('.sign-up-container .error-msg');
    if (registerErrors) {
        container.classList.add('right-panel-active');
    }
});