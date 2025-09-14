import './bootstrap';

// Global confirm handler for forms with data-confirm attribute
document.addEventListener('submit', (e) => {
  const form = e.target;
  if (form instanceof HTMLFormElement) {
    const message = form.getAttribute('data-confirm');
    if (message) {
      if (!window.confirm(message)) {
        e.preventDefault();
        e.stopPropagation();
      }
    }
  }
});
