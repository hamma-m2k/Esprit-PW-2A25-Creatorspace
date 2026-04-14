<?php require_once __DIR__ . '/layout_back.php'; ?>

<div class="back-section active" id="back-profile">

  <div class="back-header">
    <div>
      <h2 style="font-size:1.6rem; color:var(--text);">👤 Mon Profil</h2>
      <p style="color:var(--text3); font-size:0.9rem; margin-top:4px;">Gérez vos informations personnelles</p>
    </div>
    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
    <a href="index.php?ctrl=user&action=index">
      <button class="btn btn-outline btn-sm">← Retour à la liste</button>
    </a>
    <?php endif; ?>
  </div>

  <?php if (isset($_GET['success']) && $_GET['success'] === 'modif'): ?>
  <div class="toast success" style="position:static;display:flex;margin-bottom:20px;animation:none;">
    <span class="toast-msg">✅ Profil mis à jour avec succès.</span>
  </div>
  <?php endif; ?>

  <?php if (!empty($_SESSION['success'])): ?>
  <div class="toast success" style="position:static;display:flex;margin-bottom:20px;animation:none;">
    <span class="toast-msg">✅ <?= htmlspecialchars($_SESSION['success']) ?></span>
  </div>
  <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <!-- AVATAR -->
  <?php
  $initiales = strtoupper(
      substr($item['nom'] ?? 'U', 0, 1) .
      substr($item['prenom'] ?? 'U', 0, 1)
  );
  ?>
  <div style="display:flex; align-items:center; gap:20px; margin-bottom:28px;">
    <div style="width:80px; height:80px; border-radius:50%;
                background:linear-gradient(135deg, var(--primary), var(--secondary));
                display:flex; align-items:center; justify-content:center;
                font-family:'Syne',sans-serif; font-size:2rem; font-weight:700;
                color:#fff; flex-shrink:0; box-shadow:0 4px 20px rgba(108,63,197,0.4);">
      <?= $initiales ?>
    </div>
    <div>
      <div style="font-size:1.2rem; font-weight:700; color:var(--text);">
        <?= htmlspecialchars(($item['nom'] ?? '') . ' ' . ($item['prenom'] ?? '')) ?>
      </div>
      <div style="color:var(--accent); font-size:0.85rem;">
        <?= htmlspecialchars($item['mail'] ?? '') ?>
      </div>
    </div>
  </div>

  <!-- FORM CARD -->
  <div style="max-width:560px;">
    <div style="background:rgba(255,255,255,0.06);
                border:1px solid rgba(108,63,197,0.4);
                border-radius:16px; padding:2rem;
                box-shadow:0 8px 32px rgba(108,63,197,0.15);">

      <!-- POST → action=updateProfile — NO HTML5 attributes -->
      <form method="POST" action="index.php?ctrl=user&action=updateProfile">

        <div style="margin-bottom:20px;">
          <label style="display:block; color:var(--text2); font-size:0.85rem; margin-bottom:8px;">Nom</label>
          <input type="text" name="nom"
                 value="<?= htmlspecialchars($item['nom'] ?? '') ?>"
                 style="width:100%; background:rgba(255,255,255,0.08);
                        border:1px solid rgba(108,63,197,0.5); color:var(--text);
                        border-radius:8px; padding:12px 14px; font-size:0.95rem; outline:none;">
          <?php if (!empty($errors['nom'])): ?>
            <span style="color:#ff6b6b; font-size:0.8rem; margin-top:4px; display:block;">
              <?= htmlspecialchars($errors['nom']) ?>
            </span>
          <?php endif; ?>
        </div>

        <div style="margin-bottom:20px;">
          <label style="display:block; color:var(--text2); font-size:0.85rem; margin-bottom:8px;">Prénom</label>
          <input type="text" name="prenom"
                 value="<?= htmlspecialchars($item['prenom'] ?? '') ?>"
                 style="width:100%; background:rgba(255,255,255,0.08);
                        border:1px solid rgba(108,63,197,0.5); color:var(--text);
                        border-radius:8px; padding:12px 14px; font-size:0.95rem; outline:none;">
          <?php if (!empty($errors['prenom'])): ?>
            <span style="color:#ff6b6b; font-size:0.8rem; margin-top:4px; display:block;">
              <?= htmlspecialchars($errors['prenom']) ?>
            </span>
          <?php endif; ?>
        </div>

        <div style="margin-bottom:20px;">
          <label style="display:block; color:var(--text2); font-size:0.85rem; margin-bottom:8px;">Mail</label>
          <!-- type="text" — NOT type="email" -->
          <input type="text" name="mail"
                 value="<?= htmlspecialchars($item['mail'] ?? '') ?>"
                 style="width:100%; background:rgba(255,255,255,0.08);
                        border:1px solid rgba(108,63,197,0.5); color:var(--text);
                        border-radius:8px; padding:12px 14px; font-size:0.95rem; outline:none;">
          <?php if (!empty($errors['mail'])): ?>
            <span style="color:#ff6b6b; font-size:0.8rem; margin-top:4px; display:block;">
              <?= htmlspecialchars($errors['mail']) ?>
            </span>
          <?php endif; ?>
        </div>

        <div style="margin-bottom:28px;">
          <label style="display:block; color:var(--text2); font-size:0.85rem; margin-bottom:8px;">
            Nouveau mot de passe
            <span style="color:var(--text3); font-size:0.75rem;">(laisser vide pour ne pas changer)</span>
          </label>
          <input type="password" name="password"
                 placeholder="Laisser vide pour ne pas changer"
                 style="width:100%; background:rgba(255,255,255,0.08);
                        border:1px solid rgba(108,63,197,0.5); color:var(--text);
                        border-radius:8px; padding:12px 14px; font-size:0.95rem; outline:none;">
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%; padding:13px; font-size:1rem;">
          💾 Sauvegarder
        </button>

      </form>

      <?php if (($_SESSION['role'] ?? '') !== 'admin'): ?>
      <!-- Bouton suppression — masqué pour admin -->
      <div style="margin-top:28px; padding-top:20px; border-top:1px solid var(--border);">
        <p style="color:var(--text3); font-size:0.82rem; margin-bottom:12px;">
          Zone dangereuse — cette action est irréversible.
        </p>
        <a href="index.php?ctrl=user&action=deleteOwn"
           onclick="return window.confirm('Supprimer définitivement votre compte ?')">
          <button class="btn btn-sm"
                  style="background:rgba(229,62,62,0.15); color:var(--danger);
                         border:1px solid var(--danger);">
            🗑️ Supprimer mon compte
          </button>
        </a>
      </div>
      <?php endif; ?>

    </div>
  </div>

</div>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
