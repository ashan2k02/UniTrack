

'use strict';


const $ = id => document.getElementById(id);

function showError(el, msg) {
    if (!el) return;
    el.textContent = msg;
}
function clearError(el) {
    if (!el) return;
    el.textContent = '';
}

function setInvalid(input) {
    input.classList.add('is-invalid');
    input.classList.remove('is-valid');
}
function setValid(input) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
}
function clearState(input) {
    input.classList.remove('is-valid', 'is-invalid');
}


function bindPasswordToggle(btnId, iconId, inputId) {
    const btn = $(btnId);
    const icon = $(iconId);
    const inp = $(inputId);
    if (!btn || !inp) return;
    btn.addEventListener('click', () => {
        const show = inp.type === 'password';
        inp.type = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill';
    });
}


function setLoading(btn, loading) {
    const text = btn.querySelector('.btn-text');
    const loader = btn.querySelector('.btn-loader');
    btn.disabled = loading;
    text.classList.toggle('d-none', loading);
    loader.classList.toggle('d-none', !loading);
}


function showSuccessOverlay(panel, title, sub, redirect, delay = 1800) {
    const overlay = document.createElement('div');
    overlay.className = 'auth-success-overlay';
    overlay.innerHTML = `
    <div class="auth-success-icon"><i class="bi bi-check-lg"></i></div>
    <p class="auth-success-title">${title}</p>
    <p class="auth-success-sub">${sub}</p>`;
    panel.appendChild(overlay);
    requestAnimationFrame(() => overlay.classList.add('visible'));
    setTimeout(() => { window.location.href = redirect; }, delay);
}



(function initLogin() {
    const form = $('loginForm');
    if (!form) return;                      

    const emailInput = $('loginEmail');
    const pwInput = $('loginPassword');
    const submitBtn = $('loginSubmitBtn');
    const panel = document.querySelector('.auth-form-panel');

    bindPasswordToggle('toggleLoginPw', 'toggleLoginPwIcon', 'loginPassword');

    
    emailInput.addEventListener('blur', () => validateLoginEmail());
    pwInput.addEventListener('blur', () => validateLoginPw());
    emailInput.addEventListener('input', () => { clearState(emailInput); clearError($('emailError')); });
    pwInput.addEventListener('input', () => { clearState(pwInput); clearError($('passwordError')); });

    function validateLoginEmail() {
        const val = emailInput.value.trim();
        if (!val) {
            setInvalid(emailInput);
            showError($('emailError'), 'Email is required.');
            return false;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
            setInvalid(emailInput);
            showError($('emailError'), 'Please enter a valid email address.');
            return false;
        }
        setValid(emailInput);
        clearError($('emailError'));
        return true;
    }

    function validateLoginPw() {
        const val = pwInput.value;
        if (!val) {
            setInvalid(pwInput);
            showError($('passwordError'), 'Password is required.');
            return false;
        }
        if (val.length < 8) {
            setInvalid(pwInput);
            showError($('passwordError'), 'Password must be at least 8 characters.');
            return false;
        }
        setValid(pwInput);
        clearError($('passwordError'));
        return true;
    }

    
    form.addEventListener('submit', e => {
        e.preventDefault();
        const emailOk = validateLoginEmail();
        const pwOk = validateLoginPw();
        if (!emailOk || !pwOk) return;

        setLoading(submitBtn, true);

        setTimeout(() => {
            setLoading(submitBtn, false);
            showSuccessOverlay(
                panel,
                'Welcome back! 🎉',
                'Redirecting to your dashboard…',
                'dashboard.html'
            );
        }, 1600);
    });

    
    const social = ['btnGoogleLogin', 'btnGithubLogin'];
    social.forEach(id => {
        const btn = $(id);
        if (btn) btn.addEventListener('click', () => alert('Social login coming soon!'));
    });
})();



(function initRegister() {
    const form = $('registerForm');
    if (!form) return;                      

    const firstNameIn = $('regFirstName');
    const lastNameIn = $('regLastName');
    const emailIn = $('regEmail');
    const yearIn = $('regYear');
    const pwIn = $('regPassword');
    const confirmPwIn = $('regConfirmPw');
    const termsIn = $('agreeTerms');
    const submitBtn = $('registerSubmitBtn');
    const panel = document.querySelector('.auth-form-panel');

    bindPasswordToggle('toggleRegPw', 'toggleRegPwIcon', 'regPassword');
    bindPasswordToggle('toggleConfirmPw', 'toggleConfirmPwIcon', 'regConfirmPw');

    
    const strengthFill = $('strengthFill');
    const strengthLabel = $('strengthLabel');

    pwIn.addEventListener('input', () => {
        const val = pwIn.value;
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const states = ['', 'strength-weak', 'strength-fair', 'strength-good', 'strength-strong'];
        const labels = ['—', 'Weak', 'Fair', 'Good', 'Strong'];
        const colors = ['', '#f87171', '#fb923c', '#22d3ee', '#34d399'];

        strengthFill.className = `auth-strength-fill ${states[score] || ''}`;
        strengthLabel.textContent = labels[score];
        strengthLabel.style.color = colors[score];
    });

    
    function validateFirstName() {
        const v = firstNameIn.value.trim();
        if (!v) { setInvalid(firstNameIn); showError($('firstNameError'), 'First name required.'); return false; }
        setValid(firstNameIn); clearError($('firstNameError')); return true;
    }
    function validateLastName() {
        const v = lastNameIn.value.trim();
        if (!v) { setInvalid(lastNameIn); showError($('lastNameError'), 'Last name required.'); return false; }
        setValid(lastNameIn); clearError($('lastNameError')); return true;
    }
    function validateEmail() {
        const v = emailIn.value.trim();
        if (!v) { setInvalid(emailIn); showError($('regEmailError'), 'Email is required.'); return false; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) {
            setInvalid(emailIn); showError($('regEmailError'), 'Please enter a valid email.'); return false;
        }
        setValid(emailIn); clearError($('regEmailError')); return true;
    }
    function validateYear() {
        if (!yearIn.value) { setInvalid(yearIn); showError($('yearError'), 'Please select your year.'); return false; }
        setValid(yearIn); clearError($('yearError')); return true;
    }
    function validatePw() {
        const v = pwIn.value;
        if (!v) { setInvalid(pwIn); showError($('regPasswordError'), 'Password is required.'); return false; }
        if (v.length < 8) { setInvalid(pwIn); showError($('regPasswordError'), 'Min. 8 characters required.'); return false; }
        setValid(pwIn); clearError($('regPasswordError')); return true;
    }
    function validateConfirm() {
        const v = confirmPwIn.value;
        if (!v) { setInvalid(confirmPwIn); showError($('confirmPwError'), 'Please confirm your password.'); return false; }
        if (v !== pwIn.value) { setInvalid(confirmPwIn); showError($('confirmPwError'), 'Passwords do not match.'); return false; }
        setValid(confirmPwIn); clearError($('confirmPwError')); return true;
    }
    function validateTerms() {
        if (!termsIn.checked) { showError($('termsError'), 'You must agree to the terms to continue.'); return false; }
        clearError($('termsError')); return true;
    }

    
    firstNameIn.addEventListener('blur', validateFirstName);
    lastNameIn.addEventListener('blur', validateLastName);
    emailIn.addEventListener('blur', validateEmail);
    yearIn.addEventListener('change', validateYear);
    pwIn.addEventListener('blur', validatePw);
    confirmPwIn.addEventListener('blur', validateConfirm);

    
    [firstNameIn, lastNameIn, emailIn, pwIn, confirmPwIn].forEach(inp => {
        inp.addEventListener('input', () => {
            clearState(inp);
            const errMap = {
                regFirstName: 'firstNameError',
                regLastName: 'lastNameError',
                regEmail: 'regEmailError',
                regPassword: 'regPasswordError',
                regConfirmPw: 'confirmPwError',
            };
            clearError($(errMap[inp.id]));
        });
    });

    
    form.addEventListener('submit', e => {
        e.preventDefault();
        const ok =
            validateFirstName() &
            validateLastName() &
            validateEmail() &
            validateYear() &
            validatePw() &
            validateConfirm() &
            validateTerms();

        if (!ok) return;

        setLoading(submitBtn, true);

        setTimeout(() => {
            setLoading(submitBtn, false);
            showSuccessOverlay(
                panel,
                'Account created! 🚀',
                'Welcome to UniTrack. Redirecting…',
                'dashboard.html'
            );
        }, 1800);
    });

    
    const social = ['btnGoogleReg', 'btnGithubReg'];
    social.forEach(id => {
        const btn = $(id);
        if (btn) btn.addEventListener('click', () => alert('Social signup coming soon!'));
    });
})();
