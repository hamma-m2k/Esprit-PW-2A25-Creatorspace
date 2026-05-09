/* ============================================================
   CREATORSPACE — APP INIT
============================================================ */

document.addEventListener('DOMContentLoaded', () => {
  loadTheme();

  // Animer les barres de progression
  setTimeout(() => {
    document.querySelectorAll('.completion-fill').forEach(el => {
      const w = el.style.width;
      el.style.width = '0';
      setTimeout(() => { el.style.width = w; }, 100);
    });
    document.querySelectorAll('.mini-bar div').forEach(el => {
      const w = el.style.width;
      el.style.width = '0';
      setTimeout(() => { el.style.width = w; }, 100);
    });
  }, 300);

  // Fermer modal sur overlay
  const overlay = document.getElementById('modal-overlay');
  if (overlay) overlay.addEventListener('click', closeAllModals);

  console.log('%c✦ CreatorSpace v2.0 — MVC PHP', 'color:#9B5DE5;font-weight:bold;font-size:14px;');
  console.log('%cDémo : sophie.martin@gmail.com / password123', 'color:#00C2CB;font-size:12px;');
});
