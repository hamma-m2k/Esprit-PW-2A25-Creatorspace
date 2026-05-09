<?php
/**
 * FIX: Role data removed from this view — was hardcoded here (business logic in view).
 * $roles is now passed from BackController.
 */
require_once __DIR__ . '/layout_back.php';
?>

      <div class="back-section active" id="back-roles">
        <div class="back-header">
          <div><h2>Rôles & Permissions</h2><p>Configurez les droits d'accès par rôle</p></div>
          <button class="btn btn-primary btn-sm" onclick="showToast('Modifications sauvegardées !','success')">💾 Sauvegarder tout</button>
        </div>
        <div class="roles-grid">
          <?php foreach ($roles as $role): ?>
          <div class="role-card">
            <div class="role-header">
              <div class="role-icon-wrap" style="background:<?= htmlspecialchars($role['color']) ?>">
                <?= $role['icon'] ?>
              </div>
              <div>
                <div class="role-name"><?= htmlspecialchars($role['name']) ?></div>
                <div class="role-count"><?= htmlspecialchars($role['count']) ?> utilisateurs</div>
              </div>
              <span class="badge <?= htmlspecialchars($role['badge_class']) ?>">
                <?= htmlspecialchars($role['badge']) ?>
              </span>
            </div>
            <div class="permissions-list">
              <?php foreach ($role['perms'] as $perm): ?>
              <div class="perm-row">
                <span><?= htmlspecialchars($perm['label']) ?></span>
                <label class="toggle">
                  <input type="checkbox"
                         <?= $perm['enabled'] ? 'checked' : '' ?>
                         onchange="showToast('Permission mise à jour','info')" />
                  <span class="slider"></span>
                </label>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
