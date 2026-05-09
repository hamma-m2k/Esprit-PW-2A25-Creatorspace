<?php require_once __DIR__ . '/layout_back.php'; ?>

<div class="back-section active" id="back-form-edit">

  <div class="back-header">
    <div>
      <h2 style="font-size:1.6rem; color:var(--text);">✏️ Modifier l'utilisateur</h2>
      <p style="color:var(--text3); font-size:0.9rem; margin-top:4px;">Modifiez les informations</p>
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

      <form method="POST" action="index.php?ctrl=user&action=edit&id=<?= $item->getId() ?>">

        <div style="margin-bottom:20px;">
          <label style="display:block; color:var(--text2); font-size:0.85rem; margin-bottom:8px;">Nom</label>
          <!-- type="text" — NO required, NO pattern -->
          <input type="text" name="nom"
                 placeholder="Ex: Marzougui"
                 value="<?= htmlspecialchars($item->getNom() ?: '') ?>"
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
                 placeholder="Ex: Mohamed"
                 value="<?= htmlspecialchars($item->getPrenom() ?: '') ?>"
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
                 placeholder="exemple@gmail.com"
                 value="<?= htmlspecialchars($item->getMail() ?: '') ?>"
                 style="width:100%; background:rgba(255,255,255,0.08);
                        border:1px solid rgba(108,63,197,0.5); color:var(--text);
                        border-radius:8px; padding:12px 14px; font-size:0.95rem; outline:none;">
          <?php if (!empty($errors['mail'])): ?>
            <span style="color:#ff6b6b; font-size:0.8rem; margin-top:4px; display:block;">
              <?= htmlspecialchars($errors['mail']) ?>
            </span>
          <?php endif; ?>
        </div>

        <div style="margin-bottom:20px;">
          <label style="display:block; color:var(--text2); font-size:0.85rem; margin-bottom:8px;">Type de compte</label>
          <select name="type_compte" id="type_compte_select"
                  style="width:100%; background:rgba(255,255,255,0.08);
                         border:1px solid rgba(108,63,197,0.5); color:var(--text);
                         border-radius:8px; padding:12px 14px; font-size:0.95rem; outline:none;">
            <option value="user"     <?= ($item->getTypeCompte() ?: '') === 'user'     ? 'selected' : '' ?>>Utilisateur</option>
            <option value="societe"  <?= ($item->getTypeCompte() ?: '') === 'societe'  ? 'selected' : '' ?>>Société</option>
            <option value="createur" <?= ($item->getTypeCompte() ?: '') === 'createur' ? 'selected' : '' ?>>Créateur</option>
          </select>
          <?php if (!empty($errors['type_compte'])): ?>
            <span style="color:#ff6b6b; font-size:0.8rem; margin-top:4px; display:block;">
              <?= htmlspecialchars($errors['type_compte']) ?>
            </span>
          <?php endif; ?>
        </div>

        <div id="social_media_group" style="margin-bottom:20px; display: <?= ($item->getTypeCompte() === 'createur') ? 'block' : 'none' ?>;">
          <label style="display:block; color:var(--text2); font-size:0.85rem; margin-bottom:8px;">Lien Réseau Social (Instagram, TikTok...)</label>
          <input type="text" name="social_media_link"
                 placeholder="https://instagram.com/..."
                 value="<?= htmlspecialchars($item->getSocialMediaLink() ?: '') ?>"
                 style="width:100%; background:rgba(255,255,255,0.08);
                        border:1px solid rgba(108,63,197,0.5); color:var(--text);
                        border-radius:8px; padding:12px 14px; font-size:0.95rem; outline:none;">
        </div>

        <div style="margin-bottom:28px;">
          <label style="display:block; color:var(--text2); font-size:0.85rem; margin-bottom:8px;">Rôle</label>
          <select name="role"
                  style="width:100%; background:rgba(255,255,255,0.08);
                         border:1px solid rgba(108,63,197,0.5); color:var(--text);
                         border-radius:8px; padding:12px 14px; font-size:0.95rem; outline:none;">
            <option value="user"  <?= ($item->getRole() ?: '') === 'user'  ? 'selected' : '' ?>>Utilisateur</option>
            <option value="admin" <?= ($item->getRole() ?: '') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
          </select>
        </div>

        <script>
          document.getElementById('type_compte_select').addEventListener('change', function() {
            const socialGroup = document.getElementById('social_media_group');
            if (this.value === 'createur') {
              socialGroup.style.display = 'block';
            } else {
              socialGroup.style.display = 'none';
            }
          });
        </script>

        <button type="submit" class="btn btn-primary" style="width:100%; padding:13px; font-size:1rem;">
          💾 Enregistrer les modifications
        </button>

      </form>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
