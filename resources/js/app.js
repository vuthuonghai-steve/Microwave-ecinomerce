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

// AJAX add-to-cart with toast and mini-cart badge
document.addEventListener('submit', async (e) => {
  const form = e.target;
  if (!(form instanceof HTMLFormElement)) return;
  if (form.action && form.getAttribute('action')?.includes('/cart/add')) {
    // Only intercept forms that explicitly target cart.add (safe check)
    e.preventDefault();
    try {
      const fd = new FormData(form);
      const token = fd.get('_token');
      const res = await fetch(form.action, {
        method: 'POST',
        headers: token ? { 'X-CSRF-TOKEN': token } : {},
        body: fd,
        credentials: 'same-origin'
      });
      if (res.redirected) {
        // Not logged in -> redirected to login
        window.location.href = res.url;
        return;
      }
      // On success, show toast and update cart badge
      showToast('Đã thêm vào giỏ hàng');
      updateCartBadge();
    } catch (err) {
      showToast('Không thể thêm vào giỏ. Thử lại.', true);
    }
  }
});

// AJAX add-to-wishlist with toast and wishlist badge
document.addEventListener('submit', async (e) => {
  const form = e.target;
  if (!(form instanceof HTMLFormElement)) return;
  if (form.action && form.getAttribute('action')?.includes('/wishlist/add')) {
    e.preventDefault();
    try {
      const fd = new FormData(form);
      const token = fd.get('_token');
      const res = await fetch(form.action, {
        method: 'POST',
        headers: token ? { 'X-CSRF-TOKEN': token } : {},
        body: fd,
        credentials: 'same-origin'
      });
      if (res.redirected) { window.location.href = res.url; return; }
      showToast('Đã thêm vào yêu thích');
      updateWishlistBadge();
    } catch (err) {
      showToast('Không thể thêm vào yêu thích. Thử lại.', true);
    }
  }
});

function ensureWishBadge() {
  // let badge = 
  // if (!badge) {
  //   badge = document.createElement('a');
  //   badge.id = 'miniWishBadge';
  //   badge.href = '/my/wishlist';
  //   badge.className = ' fixed top-20 right-4 bg-pink-600 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg';
  //   badge.title = 'Yêu thích';
  //   badge.textContent = '0';
  //   document.body.appendChild(badge);
  // }
  return document.getElementById('miniWishBadge');
}

async function updateWishlistBadge() {
  try {
    const res = await fetch('/wishlist/count', { credentials: 'same-origin' });
    if (!res.ok) return;
    const data = await res.json();
    const badge = ensureWishBadge();
    if (badge) badge.textContent = String(data.count ?? 0);
    const nav = document.getElementById('navWishCount');
    if (nav) nav.textContent = String(data.count ?? 0);
  } catch (e) { /* ignore */ }
}

function ensureCartBadge() {
  // Không tự tạo miniCartBadge nữa, chỉ trả về nếu đã tồn tại trong DOM
  return document.getElementById('miniCartBadge');
}

async function updateCartBadge() {
  try {
    const res = await fetch('/cart/count', { credentials: 'same-origin' });
    if (!res.ok) return;
    const data = await res.json();
    const badge = ensureCartBadge();
    if (badge) badge.textContent = String(data.count ?? 0);
    const nav = document.getElementById('navCartCount');
    if (nav) nav.textContent = String(data.count ?? 0);
  } catch (e) { /* ignore */ }
}

function showToast(message, isError = false) {
  let holder = document.getElementById('toastHolder');
  if (!holder) {
    holder = document.createElement('div');
    holder.id = 'toastHolder';
    holder.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-50';
    document.body.appendChild(holder);
  }
  const toast = document.createElement('div');
  toast.className = (isError ? 'bg-red-600' : 'bg-green-600') + ' text-white px-4 py-2 rounded shadow mb-2';
  toast.textContent = message;
  holder.appendChild(toast);
  setTimeout(() => toast.remove(), 2500);
}

// Initialize cart badge count on load (if authenticated; will no-op if unauthorized)
updateCartBadge();
updateWishlistBadge();
