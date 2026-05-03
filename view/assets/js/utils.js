/* ============================================================
   CREATORSPACE — UTILITIES
============================================================ */

function showToast(message, type = 'info') {
  const icons = { success: '✅', error: '❌', info: 'ℹ️', warning: '⚠️' };
  const container = document.getElementById('toast-container');
  if (!container) return;
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `
    <span class="toast-icon">${icons[type] || 'ℹ️'}</span>
    <span class="toast-msg">${message}</span>
    <span class="toast-close" onclick="this.parentElement.remove()">✕</span>
  `;
  container.appendChild(toast);
  setTimeout(() => {
    if (toast.parentElement) {
      toast.style.animation = 'fadeOut 0.3s ease forwards';
      setTimeout(() => toast.remove(), 300);
    }
  }, 3800);
}

function openModal(id) {
  document.getElementById('modal-overlay').classList.add('active');
  const modal = document.getElementById(id);
  if (modal) modal.classList.add('active');
}

function closeAllModals() {
  const overlay = document.getElementById('modal-overlay');
  if (overlay) overlay.classList.remove('active');
  document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
}

function toggleTheme() {
  const html = document.documentElement;
  const isDark = html.getAttribute('data-theme') === 'dark';
  html.setAttribute('data-theme', isDark ? 'light' : 'dark');
  document.getElementById('themeBtn').textContent = isDark ? '☀️' : '🌙';
  showToast(isDark ? 'Mode clair activé ☀️' : 'Mode sombre activé 🌙', 'info');
  localStorage.setItem('cs-theme', isDark ? 'light' : 'dark');
}

function loadTheme() {
  const saved = localStorage.getItem('cs-theme');
  if (saved) {
    document.documentElement.setAttribute('data-theme', saved);
    const btn = document.getElementById('themeBtn');
    if (btn) btn.textContent = saved === 'light' ? '☀️' : '🌙';
  }
}

function animateCounter(el, target) {
  const duration = 1200;
  const start = performance.now();
  const update = (now) => {
    const elapsed = now - start;
    const progress = Math.min(elapsed / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    el.textContent = Math.floor(eased * target).toLocaleString('fr-FR');
    if (progress < 1) requestAnimationFrame(update);
  };
  requestAnimationFrame(update);
}

function updatePasswordStrength(value) {
  const bar   = document.getElementById('pwd-bar');
  const label = document.getElementById('pwd-label');
  if (!bar || !label) return;
  let strength = 0;
  if (value.length >= 8)           strength++;
  if (/[A-Z]/.test(value))         strength++;
  if (/[0-9]/.test(value))         strength++;
  if (/[^A-Za-z0-9]/.test(value))  strength++;
  const levels = [
    { w: '0%',   bg: 'transparent', txt: '' },
    { w: '25%',  bg: '#e53e3e',     txt: 'Très faible' },
    { w: '50%',  bg: '#ed8936',     txt: 'Faible' },
    { w: '75%',  bg: '#00C2CB',     txt: 'Bon' },
    { w: '100%', bg: '#38a169',     txt: 'Excellent' },
  ];
  const lvl = levels[strength];
  bar.style.width = lvl.w;
  bar.style.background = lvl.bg;
  label.textContent = lvl.txt;
  label.style.color = lvl.bg;
}

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeAllModals();
});
