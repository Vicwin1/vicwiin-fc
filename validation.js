const registrationForm = document.querySelector('#register-form');

if (registrationForm) {
    const password = registrationForm.querySelector('#password');
    const confirmation = registrationForm.querySelector('#confirm_password');
    const hint = registrationForm.querySelector('#password-hint');

    const updatePasswordHint = () => {
        const matches = confirmation.value === '' || password.value === confirmation.value;
        hint.textContent = matches ? '' : 'The passwords do not match.';
        confirmation.setCustomValidity(matches ? '' : 'The passwords do not match.');
    };

    password.addEventListener('input', updatePasswordHint);
    confirmation.addEventListener('input', updatePasswordHint);
}
