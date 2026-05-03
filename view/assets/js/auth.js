/* ============================================================
   CREATORSPACE — AUTH (client-side UI only)
   La vraie auth est gérée côté PHP
============================================================ */

function switchAuthTab(tab) {
  document.querySelectorAll('.auth-tab').forEach((btn, i) => {
    btn.classList.toggle('active', (tab === 'login' && i === 0) || (tab === 'register' && i === 1));
  });
  document.getElementById('tab-login').classList.toggle('active', tab === 'login');
  document.getElementById('tab-register').classList.toggle('active', tab === 'register');
}

function selectAccountType(type) {
  document.getElementById('type-creator').classList.toggle('active', type === 'creator');
  document.getElementById('type-brand').classList.toggle('active', type === 'brand');
  // Mettre à jour le radio button
  const radio = document.querySelector(`input[name="account_type"][value="${type}"]`);
  if (radio) radio.checked = true;
}

// Gestion visuelle du sélecteur de type de compte
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.account-type').forEach(el => {
    el.addEventListener('click', function() {
      document.querySelectorAll('.account-type').forEach(e => e.classList.remove('active'));
      this.classList.add('active');
    });
  });
});
