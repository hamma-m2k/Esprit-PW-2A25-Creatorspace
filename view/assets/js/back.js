/* ============================================================
   CREATORSPACE — BACK OFFICE LOGIC (modals & UI)
============================================================ */

function viewUser(id) {
  const u = CS.users.find(x => x.id === id);
  if (!u) return;
  const body = document.getElementById('view-user-body');
  if (!body) return;
  const statusLabel = {'active':'● Actif','inactive':'● Inactif','pending':'● En attente'};
  body.innerHTML = `
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
      <div class="user-mini-avatar" style="background:${u.color};width:56px;height:56px;font-size:1.1rem;">${u.initials}</div>
      <div>
        <div style="font-size:1.1rem;font-weight:700;">${u.name}</div>
        <div style="font-size:0.82rem;color:var(--text3);">${u.email}</div>
      </div>
    </div>
    <div class="user-detail-grid">
      <div class="user-detail-item"><label>Rôle</label><p><span class="role-badge">${u.role}</span></p></div>
      <div class="user-detail-item"><label>Statut</label><p>${statusLabel[u.status] || u.status}</p></div>
      <div class="user-detail-item"><label>Inscrit le</label><p>${u.date}</p></div>
      <div class="user-detail-item"><label>Abonnés</label><p>${u.followers}</p></div>
      <div class="user-detail-item"><label>Vues totales</label><p>${u.views}</p></div>
      <div class="user-detail-item"><label>Contenus</label><p>${u.content}</p></div>
    </div>
    <div style="margin-top:16px;">
      <div class="completion-label"><span>Complétion du profil</span><span>${u.completion}%</span></div>
      <div class="completion-bar"><div class="completion-fill" style="width:${u.completion}%"></div></div>
    </div>
  `;
  openModal('view-user-modal');
}

function confirmDelete(id) {
  openModal('confirm-delete-modal');
  document.getElementById('confirm-delete-btn').onclick = () => {
    closeAllModals();
    showToast('Utilisateur supprimé.', 'error');
  };
}
