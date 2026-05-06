<?php
/**
 * FIX: Status label/class mapping removed from this view.
 * Now uses UserModel::statusLabel() and UserModel::statusClass() — data logic belongs in Model.
 */
require_once __DIR__ . '/layout_back.php';
?>

      <div class="back-section active" id="back-users">
        <div class="back-header">
          <div><h2>Utilisateurs</h2><p>Gérez tous les comptes de la plateforme</p></div>
          <div style="display:flex; gap:10px;">
            <a href="index.php?ctrl=user&action=exportPdf" class="btn btn-outline btn-sm">📄 Export PDF</a>
          </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
          <div style="padding:15px; background:rgba(72,187,120,0.1); border:1px solid #48bb78; border-radius:10px; color:#48bb78; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
            <span>✅</span>
            <span>
              <?php 
                if($_GET['success'] === 'ban') echo "Statut de bannissement mis à jour.";
                elseif($_GET['success'] === 'verify') echo "Statut de vérification mis à jour.";
                elseif($_GET['success'] === 'creation') echo "Utilisateur créé avec succès.";
                elseif($_GET['success'] === 'modification') echo "Utilisateur modifié avec succès.";
                elseif($_GET['success'] === 'suppression') echo "Utilisateur supprimé.";
              ?>
            </span>
          </div>
        <?php endif; ?>

        <div class="table-card">
          <div class="table-toolbar">
            <form method="GET" action="index.php" style="display:contents;">
              <input type="hidden" name="ctrl" value="user" />
              <input type="hidden" name="action" value="index" />
              <div style="display:flex; align-items:center; gap:10px;">
                <div class="search-wrap" style="flex:1; margin:0;">
                  <span class="search-icon">🔍</span>
                  <input type="text" name="search"
                         placeholder="Rechercher par nom, email, rôle…"
                         value="<?= htmlspecialchars($search ?? '') ?>" />
                </div>
                <button type="submit" class="btn btn-sm" style="height:42px; border-radius:10px; padding:0 20px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.15); color:var(--text); font-weight:500; cursor:pointer;">
                  🔍 Rechercher
                </button>
              </div>
              <div class="toolbar-filters">
                <select name="sort" onchange="this.form.submit()" style="min-width:140px;">
                  <option value="id" <?= ($sort ?? 'id') === 'id' ? 'selected' : '' ?>>🔢 Trier par ID</option>
                  <option value="nom" <?= ($sort ?? '') === 'nom' ? 'selected' : '' ?>>🔠 Nom (A-Z)</option>
                </select>
              </div>
            </form>
          </div>

          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Utilisateur</th><th>Email</th><th>Rôle</th>
                  <th>Type de user</th><th>Actions</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($users as $u): ?>
                <tr>

                  <td>
                    <div class="user-cell">
                      <div class="user-mini-avatar" style="background:#6C3FC5">
                        <?= htmlspecialchars(strtoupper(substr($u->getNom(),0,1).substr($u->getPrenom(),0,1))) ?>
                      </div>
                      <span class="user-name" style="<?= $u->getIsBanned() ? 'text-decoration:line-through; color:red;' : '' ?>">
                        <?= htmlspecialchars($u->getNom().' '.$u->getPrenom()) ?>
                      </span>
                      <?php if ($u->getIsVerified()): ?>
                        <span title="Vérifié" style="margin-left:5px; vertical-align: middle;">
                          <svg viewBox="0 0 24 24" width="16" height="16" style="fill: #0095f6; display: inline-block;">
                            <path d="M22.5 12.5c0-.85-.68-1.55-1.53-1.55h-.16c.38-.63.6-1.37.6-2.15 0-2.33-1.89-4.22-4.22-4.22-.78 0-1.52.22-2.15.6v-.16c0-.85-.7-1.53-1.55-1.53h-2.98c-.85 0-1.55.68-1.55 1.53v.16c-.63-.38-1.37-.6-2.15-.6-2.33 0-4.22 1.89-4.22 4.22 0 .78.22 1.52.6 2.15h-.16c-.85 0-1.53.7-1.53 1.55v2.98c0 .85.68 1.55 1.53 1.55h.16c-.38.63-.6 1.37-.6 2.15 0 2.33 1.89 4.22 4.22 4.22.78 0 1.52-.22 2.15-.6v.16c0 .85.7 1.53 1.55 1.53h2.98c.85 0 1.55-.68 1.55-1.53v-.16c.63.38 1.37.6 2.15.6 2.33 0 4.22-1.89 4.22-4.22 0-.78-.22-1.52-.6-2.15h.16c.85 0 1.53-.7 1.53-1.55v-2.98zm-12.03 5.45l-4.14-4.14 1.41-1.41 2.73 2.73 6.64-6.64 1.41 1.41-8.05 8.05z"></path>
                          </svg>
                        </span>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($u->getMail()) ?></td>
                  <td><span class="role-badge"><?= htmlspecialchars($u->getRole()) ?></span></td>
                  <td>
                    <span class="role-badge" style="background:rgba(108,63,197,0.1); color:#6C3FC5;">
                      <?= htmlspecialchars(ucfirst($u->getTypeCompte())) ?>
                    </span>
                  </td>
                  <td>
                    <div class="table-actions">
                      <a href="index.php?ctrl=user&action=edit&id=<?= (int)$u->getId() ?>" class="action-btn" title="Modifier" style="text-decoration:none;">
                        ✏️
                      </a>
                      <a href="index.php?ctrl=user&action=toggleBan&id=<?= (int)$u->getId() ?>" class="action-btn" title="<?= $u->getIsBanned() ? 'Débannir' : 'Bannir' ?>" style="text-decoration:none;">
                        <?= $u->getIsBanned() ? '🔓' : '🔨' ?>
                      </a>
                      <a href="index.php?ctrl=user&action=toggleVerify&id=<?= (int)$u->getId() ?>" class="action-btn" title="<?= $u->getIsVerified() ? 'Enlever Vérification' : 'Vérifier' ?>" style="text-decoration:none; color: <?= $u->getIsVerified() ? '#0095f6' : 'inherit' ?>;">
                          <?php if ($u->getIsVerified()): ?>
                            <svg viewBox="0 0 24 24" width="18" height="18" style="fill: #0095f6; vertical-align: middle;">
                              <path d="M22.5 12.5c0-.85-.68-1.55-1.53-1.55h-.16c.38-.63.6-1.37.6-2.15 0-2.33-1.89-4.22-4.22-4.22-.78 0-1.52.22-2.15.6v-.16c0-.85-.7-1.53-1.55-1.53h-2.98c-.85 0-1.55.68-1.55 1.53v.16c-.63-.38-1.37-.6-2.15-.6-2.33 0-4.22 1.89-4.22 4.22 0 .78.22 1.52.6 2.15h-.16c-.85 0-1.53.7-1.53 1.55v2.98c0 .85.68 1.55 1.53 1.55h.16c-.38.63-.6 1.37-.6 2.15 0 2.33 1.89 4.22 4.22 4.22.78 0 1.52-.22 2.15-.6v.16c0 .85.7 1.53 1.55 1.53h2.98c.85 0 1.55-.68 1.55-1.53v-.16c.63.38 1.37.6 2.15.6 2.33 0 4.22-1.89 4.22-4.22 0-.78-.22-1.52-.6-2.15h.16c.85 0 1.53-.7 1.53-1.55v-2.98zm-12.03 5.45l-4.14-4.14 1.41-1.41 2.73 2.73 6.64-6.64 1.41 1.41-8.05 8.05z"></path>
                            </svg>
                          <?php else: ?>
                            🔘
                          <?php endif; ?>
                      </a>
                      <a href="index.php?ctrl=user&action=delete&id=<?= (int)$u->getId() ?>"
                         class="action-btn del" title="Supprimer" style="text-decoration:none;"
                         onclick="return window.confirm('Confirmer la suppression ?')">
                        🗑
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="table-footer">
            <span><?= $total ?> utilisateur<?= $total > 1 ? 's' : '' ?></span>
            <div class="pagination">
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <a href="index.php?ctrl=user&action=index&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&role=<?= urlencode($roleFilter ?? '') ?>&status=<?= urlencode($statusFilter ?? '') ?>">
                <button class="page-btn <?= $i === (int)$currentPage ? 'active' : '' ?>"><?= $i ?></button>
              </a>
              <?php endfor; ?>
            </div>
          </div>
        </div>
      </div>

<!-- Add User Modal -->
<div class="modal" id="add-user-modal">
  <div class="modal-header">
    <h3>👤 Ajouter un utilisateur</h3>
    <button class="modal-close" onclick="closeAllModals()">✕</button>
  </div>
  <div class="modal-body">
    <form method="POST" action="index.php?ctrl=user&action=create">
      <div class="form-row-2">
        <div class="form-group"><label>Nom</label><input type="text" name="nom" required /></div>
        <div class="form-group"><label>Prénom</label><input type="text" name="prenom" required /></div>
      </div>
      <div class="form-group"><label>Email (@gmail.com)</label><input type="email" name="mail" required /></div>
      <div class="form-group"><label>Mot de passe</label><input type="password" name="password" required /></div>
      <div class="form-row-2">
        <div class="form-group">
          <label>Type de compte</label>
          <select name="type_compte">
            <option value="user">Utilisateur</option>
            <option value="societe">Société</option>
            <option value="createur">Créateur</option>
          </select>
        </div>
        <div class="form-group">
          <label>Rôle</label>
          <select name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
      </div>
      <div style="margin-top:20px; text-align:right;">
        <button type="button" class="btn btn-outline btn-sm" onclick="closeAllModals()">Annuler</button>
        <button type="submit" class="btn btn-primary btn-sm">✅ Créer l'utilisateur</button>
      </div>
    </form>
  </div>
</div>

<!-- AI Insights Modal -->
<div class="modal" id="ai-insights-modal" style="max-width: 600px;">
  <div class="modal-header">
    <div style="display:flex; align-items:center; gap:12px;">
      <span style="font-size:1.5rem;">✨</span>
      <div>
        <h3 style="margin:0;">Analyse Stratégique IA</h3>
        <p style="margin:0; font-size:0.7rem; color:var(--text3);">Généré par Gemini 2.0 Flash</p>
      </div>
    </div>
    <button class="modal-close" onclick="closeAllModals()">✕</button>
  </div>
  <div class="modal-body" id="ai-insights-body" style="line-height:1.6;">
    <div style="text-align:center; padding:30px;">
      <p>Cliquez sur le bouton pour lancer l'analyse...</p>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-outline btn-sm" onclick="closeAllModals()">Fermer</button>
  </div>
</div>

<script>
async function runAiInsights() {
  openModal('ai-insights-modal');
  const body = document.getElementById('ai-insights-body');
  body.innerHTML = `
    <div style="text-align:center; padding:40px;">
      <div style="width:40px; height:40px; border:3px solid rgba(108,63,197,0.2); border-top-color:#6C3FC5; border-radius:50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
      <p style="color:var(--text2); font-size:0.9rem;">L'IA analyse les tendances de la plateforme...</p>
    </div>
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
  `;
  
  try {
    const response = await fetch('index.php?ctrl=user&action=generateAiInsights', { method: 'POST' });
    const data = await response.json();
    if (data.error) {
      body.innerHTML = `<div style="padding:20px; background:rgba(229,62,62,0.1); border-radius:10px; color:#fc8181; font-size:0.9rem;">❌ ${data.error}</div>`;
    } else {
      body.innerHTML = `<div style="font-size:0.95rem; color:var(--text);">${data.insights}</div>`;
    }
  } catch (e) {
    body.innerHTML = `<div style="padding:20px; background:rgba(229,62,62,0.1); border-radius:10px; color:#fc8181; font-size:0.9rem;">❌ Erreur de communication avec le serveur.</div>`;
  }
}
</script>

<!-- View User Modal -->
<div class="modal" id="view-user-modal">
  <div class="modal-header">
    <h3>👁️ Détails utilisateur</h3>
    <button class="modal-close" onclick="closeAllModals()">✕</button>
  </div>
  <div class="modal-body" id="view-user-body"></div>
  <div class="modal-footer">
    <button class="btn btn-outline btn-sm" onclick="closeAllModals()">Fermer</button>
  </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal" id="confirm-delete-modal">
  <div class="modal-header">
    <h3>🗑️ Confirmer la suppression</h3>
    <button class="modal-close" onclick="closeAllModals()">✕</button>
  </div>
  <div class="modal-body">
    <p style="color:var(--text2);">Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-outline btn-sm" onclick="closeAllModals()">Annuler</button>
    <button class="btn btn-danger btn-sm" id="confirm-delete-btn">🗑️ Supprimer</button>
  </div>
</div>



<?php require_once __DIR__ . '/layout_back_end.php'; ?>
