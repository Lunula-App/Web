// Mobile nav toggle
const menuBtn = document.getElementById('menu-btn');
const mobileNav = document.getElementById('mobile-nav');
if (menuBtn && mobileNav) {
  menuBtn.addEventListener('click', () => {
    mobileNav.classList.toggle('open');
  });
}

// Support form handling
const supportForm = document.getElementById('support-form');
const formCard = document.getElementById('form-card');
const formSuccess = document.getElementById('form-success');
const formError = document.getElementById('form-error');

function showError(msg) {
  if (formError) {
    formError.textContent = msg;
    formError.style.display = 'block';
  }
}

function clearError() {
  if (formError) {
    formError.textContent = '';
    formError.style.display = 'none';
  }
}

if (supportForm) {
  supportForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearError();

    const btn = supportForm.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Sending…';

    try {
      const res = await fetch('form-handler.php', {
        method: 'POST',
        body: new FormData(supportForm),
      });

      const data = await res.json();

      if (data.success) {
        formCard.style.display = 'none';
        formSuccess.style.display = 'block';
      } else {
        showError(data.error || 'Something went wrong. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Send message';
        if (window.hcaptcha) hcaptcha.reset();
      }
    } catch {
      showError('Network error. Please check your connection and try again.');
      btn.disabled = false;
      btn.textContent = 'Send message';
      if (window.hcaptcha) hcaptcha.reset();
    }
  });
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(link => {
  link.addEventListener('click', (e) => {
    const target = document.querySelector(link.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});
