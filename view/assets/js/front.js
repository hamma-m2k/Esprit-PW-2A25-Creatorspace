/* ============================================================
   CREATORSPACE — FRONT OFFICE LOGIC
============================================================ */

function switchProfileTab(tab, btn) {
  document.querySelectorAll('.profile-tab').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  document.querySelectorAll('.profile-tab-content').forEach(el => el.classList.remove('active'));
  const content = document.getElementById('ptab-' + tab);
  if (content) content.classList.add('active');
}

function handleFollow(btn) {
  const isFollowing = btn.textContent.includes('Suivi');
  if (isFollowing) {
    btn.textContent = '+ Suivre';
    btn.classList.remove('btn-outline');
    btn.classList.add('btn-primary');
    showToast('Abonnement annulé.', 'info');
  } else {
    btn.textContent = '✓ Suivi';
    btn.classList.remove('btn-primary');
    btn.classList.add('btn-outline');
    showToast('Abonnement confirmé ! 🎉', 'success');
  }
}
