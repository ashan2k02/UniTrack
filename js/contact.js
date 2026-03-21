'use strict';

const $ = id => document.getElementById(id);

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
}

(function initNavbarScroll() {
  const navbar = document.getElementById('mainNavbar');
  if (!navbar) return;

  window.addEventListener('scroll', () => {
    navbar.style.background = window.scrollY > 50 ? 'rgba(11,13,26,0.95)' : 'rgba(11,13,26,0.75)';
  }, { passive: true });
})();

(function initContactForm() {
  const form = $('contactForm');
  const successMsg = $('contactSuccess');
  const submitBtn = $('contactSubmitBtn');

  if (!form) return;

  form.addEventListener('submit', e => {
    e.preventDefault();
    form.classList.add('was-validated');

    const email = $('contactEmail');
    const message = $('contactMessage');

    if (email && !isValidEmail(email.value)) {
      email.setCustomValidity('Please enter a valid email address.');
    } else if (email) {
      email.setCustomValidity('');
    }

    if (message && message.value.trim().length < 20) {
      message.setCustomValidity('Too short.');
    } else if (message) {
      message.setCustomValidity('');
    }

    if (!form.checkValidity()) return;

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
    }

    setTimeout(() => {
      form.reset();
      form.classList.remove('was-validated');

      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-send me-2"></i>Send Message';
      }

      if (successMsg) {
        successMsg.classList.remove('d-none');
        setTimeout(() => successMsg.classList.add('d-none'), 6000);
        successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }, 1200);
  });

  const emailInput = $('contactEmail');
  if (emailInput) {
    emailInput.addEventListener('input', () => {
      if (emailInput.value && !isValidEmail(emailInput.value)) {
        emailInput.setCustomValidity('invalid');
      } else {
        emailInput.setCustomValidity('');
      }
    });
  }
})();
