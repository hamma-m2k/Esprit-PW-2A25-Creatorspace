<?php
// view/backoffice/detail.php — admin only, read-only user detail
require_once __DIR__ . '/layout_back.php';
?>

<div class="back-section active" id="back-detail">

  <div class="back-header">
    <div>
      <h2 style="font-size:1.6rem; color:var(--text);">👁️ Détail utilisateur</h2>
      <p style="color:var(--text3); font-size:0.9rem; margin-top:4px;">Informations en lecture seule</p>
    </div>
    <a href="index.php?ctrl=user&action=index">
      <button class="btn btn-outline btn-sm">← Retour à la liste</button>
    </a>
  </div>

  <div style="max-width:560px;">
    <div style="background:rgba(255,255,255,0.06);
                border:1px solid rgba(108,63,197,0.4);
                border-radius:16px; padding:2rem;
                box-shadow:0 8px 32px rgba(108,63,197,0.15);">

      <!-- AVATAR -->
      <?php
      $initiales = strtoupper(
          substr($item->getNom()    ?: 'U', 0, 1) .
          substr($item->getPrenom() ?: 'U', 0, 1)
      );
      ?>
      <div style="display:flex; align-items:center; gap:20px; margin-bottom:28px;">
        <div style="width:80px; height:80px; border-radius:50%;
                    background:linear-gradient(135deg, var(--primary), var(--secondary));
                    display:flex; align-items:center; justify-content:center;
                    font-family:'Syne',sans-serif; font-size:2rem; font-weight:700;
                    color:#fff; flex-shrink:0; box-shadow:0 4px 20px rgba(108,63,197,0.4);
                    <?php if($item->getProfilePicture()): ?>background: url('<?= htmlspecialchars($item->getProfilePicture()) ?>') center/cover; color: transparent;<?php endif; ?>">
          <?php if(!$item->getProfilePicture()): ?><?= $initiales ?><?php endif; ?>
        </div>
        <div>
          <div style="font-size:1.3rem; font-weight:700; color:var(--text);">
            <?= htmlspecialchars($item->getNom() . ' ' . $item->getPrenom()) ?>
            <?php if ($item->getIsVerified()): ?>
              <svg viewBox="0 0 24 24" width="20" height="20" style="vertical-align: middle; margin-left: 6px; fill: #1DA1F2;">
                <path d="M22.5 12.5c0-.85-.68-1.55-1.53-1.55h-.16c.38-.63.6-1.37.6-2.15 0-2.33-1.89-4.22-4.22-4.22-.78 0-1.52.22-2.15.6v-.16c0-.85-.7-1.53-1.55-1.53h-2.98c-.85 0-1.55.68-1.55 1.53v.16c-.63-.38-1.37-.6-2.15-.6-2.33 0-4.22 1.89-4.22 4.22 0 .78.22 1.52.6 2.15h-.16c-.85 0-1.53.7-1.53 1.55v2.98c0 .85.68 1.55 1.53 1.55h.16c-.38.63-.6 1.37-.6 2.15 0 2.33 1.89 4.22 4.22 4.22.78 0 1.52-.22 2.15-.6v.16c0 .85.7 1.53 1.55 1.53h2.98c.85 0 1.55-.68 1.55-1.53v-.16c.63.38 1.37.6 2.15.6 2.33 0 4.22-1.89 4.22-4.22 0-.78-.22-1.52-.6-2.15h.16c.85 0 1.53-.7 1.53-1.55v-2.98zm-12.03 5.45l-4.14-4.14 1.41-1.41 2.73 2.73 6.64-6.64 1.41 1.41-8.05 8.05z"></path>
              </svg>
            <?php endif; ?>
          </div>
          <div style="color:var(--text3); font-size:0.82rem; margin-top:4px;">
            ID #<?= $item->getId() ?>
          </div>
        </div>
      </div>

      <!-- FIELDS -->
      <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:28px;">

        <div style="display:flex; justify-content:space-between; align-items:center;
                    padding:12px 16px; background:rgba(255,255,255,0.04);
                    border-radius:8px; border:1px solid var(--border);">
          <span style="color:var(--text3); font-size:0.85rem;">Nom complet</span>
          <span style="color:var(--text); font-weight:600;">
            <span style="<?= $item->getIsBanned() ? 'text-decoration: line-through; color: red;' : '' ?>">
              <?= htmlspecialchars($item->getNom() . ' ' . $item->getPrenom()) ?>
            </span>
          </span>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center;
                    padding:12px 16px; background:rgba(255,255,255,0.04);
                    border-radius:8px; border:1px solid var(--border);">
          <span style="color:var(--text3); font-size:0.85rem;">Email</span>
          <span style="color:var(--accent); font-size:0.9rem;">
            <?= htmlspecialchars($item->getMail()) ?>
          </span>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center;
                    padding:12px 16px; background:rgba(255,255,255,0.04);
                    border-radius:8px; border:1px solid var(--border);">
          <span style="color:var(--text3); font-size:0.85rem;">Rôle</span>
          <?php if ($item->getRole() === 'admin'): ?>
            <span class="badge badge-pro">Admin</span>
          <?php else: ?>
            <span class="badge badge-verified">User</span>
          <?php endif; ?>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center;
                    padding:12px 16px; background:rgba(255,255,255,0.04);
                    border-radius:8px; border:1px solid var(--border);">
          <span style="color:var(--text3); font-size:0.85rem;">ID</span>
          <span style="color:var(--text2); font-size:0.9rem;">#<?= $item->getId() ?></span>
        </div>

      </div>

      <!-- ACTIONS -->
      <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <a href="index.php?ctrl=user&action=edit&id=<?= $item->getId() ?>">
          <button class="btn btn-primary btn-sm">✏️ Modifier</button>
        </a>
        <a href="index.php?ctrl=user&action=delete&id=<?= $item->getId() ?>"
           onclick="return window.confirm('Supprimer cet utilisateur définitivement ?')">
          <button class="btn btn-sm"
                  style="background:rgba(229,62,62,0.15); color:var(--danger);
                         border:1px solid var(--danger);">
            🗑️ Supprimer
          </button>
        </a>
      </div>

    </div>
  </div>

</div>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
