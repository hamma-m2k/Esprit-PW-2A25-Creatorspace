<?php require_once __DIR__ . '/layout_back.php'; ?>

<div class="back-section active" id="back-list">

  <!-- PAGE HEADER -->
  <div class="back-header">
    <div>
      <h2 style="font-size:1.6rem; color:var(--text);">👥 Gestion des utilisateurs</h2>
      <p style="color:var(--text3); font-size:0.9rem; margin-top:4px;">
        <?= count($users) ?> utilisateur(s) enregistré(s)
      </p>
    </div>
  </div>

  <?php if (isset($_GET['success'])): ?>
  <div class="toast success" style="position:static;display:flex;margin-bottom:20px;animation:none;">
    <span class="toast-msg">
      <?php
      if     ($_GET['success'] === 'ajout')       echo '✅ Utilisateur ajouté avec succès.';
      elseif ($_GET['success'] === 'modif')       echo '✅ Utilisateur modifié avec succès.';
      elseif ($_GET['success'] === 'suppression') echo '✅ Utilisateur supprimé avec succès.';
      ?>
    </span>
  </div>
  <?php endif; ?>

  <!-- RECHERCHE ET TRI -->
  <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3); border-radius:12px; padding:16px; margin-bottom:20px;">
    <form method="GET" action="index.php" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
      <input type="hidden" name="ctrl" value="user">
      <input type="hidden" name="action" value="index">
      
      <div style="flex:1; min-width:200px;">
        <input type="text" name="search" placeholder="Rechercher par nom, prénom ou email..." 
               value="<?= htmlspecialchars($search ?? '') ?>" 
               style="width:100%; padding:10px 14px; border-radius:8px; border:1px solid rgba(108,63,197,0.4); background:rgba(0,0,0,0.2); color:white; outline:none;">
      </div>
      
      <div>
        <select name="sort" style="padding:10px 14px; border-radius:8px; border:1px solid rgba(108,63,197,0.4); background:rgba(0,0,0,0.2); color:white; outline:none;">
          <option value="id" <?= ($sort ?? '') === 'id' ? 'selected' : '' ?>>Trier par ID (Défaut)</option>
          <option value="alphabet" <?= ($sort ?? '') === 'alphabet' ? 'selected' : '' ?>>Trier de A à Z</option>
          <option value="date" <?= ($sort ?? '') === 'date' ? 'selected' : '' ?>>Trier par Date d'inscription</option>
        </select>
      </div>
      
      <button type="submit" class="btn btn-primary" style="padding:10px 20px;">Filtrer</button>
      <?php if (!empty($search) || ($sort ?? 'id') !== 'id'): ?>
        <a href="index.php?ctrl=user&action=index" class="btn btn-outline" style="padding:10px 20px;">Réinitialiser</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- TABLE -->
  <div class="table-card">
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Mail</th>
            <th>Rôle</th>
            <th>Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
          <tr>
            <td colspan="5" style="text-align:center; color:var(--text3); padding:28px;">
              Aucun utilisateur enregistré.
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($users as $u): ?>
          <tr>
            <td style="font-weight:600; color:var(--text);"><?= htmlspecialchars($u->getNom()) ?></td>
            <td style="color:var(--text2);"><?= htmlspecialchars($u->getPrenom()) ?></td>
            <td style="color:var(--text2);"><?= htmlspecialchars($u->getMail()) ?></td>
            <td>
              <?php if ($u->getRole() === 'admin'): ?>
                <span class="badge badge-pro">Admin</span>
              <?php else: ?>
                <span class="badge badge-verified">User</span>
              <?php endif; ?>
            </td>
            <td>
              <?php
              $types = ['societe'=>'Société','createur'=>'Créateur','user'=>'Utilisateur','admin'=>'Admin'];
              echo htmlspecialchars($types[$u->getTypeCompte() ?: 'user'] ?? 'Utilisateur');
              ?>
            </td>
            <td>
              <div class="table-actions">
                <a href="index.php?ctrl=user&action=detail&id=<?= $u->getId() ?>">
                  <button class="action-btn" title="Voir détail"
                          style="color:var(--secondary); border-color:var(--secondary);">
                    👁️
                  </button>
                </a>
                <?php if ($u->getId() !== (int)($currentUserId ?? 0)): ?>
                <a href="index.php?ctrl=user&action=delete&id=<?= $u->getId() ?>"
                   onclick="return window.confirm('Confirmer la suppression de cet utilisateur ?')">
                  <button class="action-btn del" title="Supprimer">🗑️</button>
                </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
