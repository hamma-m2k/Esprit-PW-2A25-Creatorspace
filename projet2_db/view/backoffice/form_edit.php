<?php
// /view/backoffice/form_edit.php — admin only (restriction handled by controller)
// No business logic — display only. Values pre-filled from $item.
include __DIR__ . '/layout/header.php';
?>

<div class="page-header">
  <div>
    <div class="page-title">Modifier l'utilisateur</div>
    <div class="page-subtitle">Modifiez les informations ci-dessous</div>
  </div>
  <a href="index.php?ctrl=utilisateur&action=index" class="btn btn-light">← Retour à la liste</a>
</div>

<div style="max-width:560px;">
  <div class="card">

    <!-- POST → action=update&id= — NO HTML5 attributes anywhere -->
    <form method="POST" action="index.php?ctrl=utilisateur&action=update&id=<?= (int)$item['id'] ?>">

      <label>Nom</label>
      <!-- type="text" — NO required, NO pattern -->
      <input type="text" name="nom"
             placeholder="Ex: Marzougui"
             value="<?= htmlspecialchars($item['nom'] ?? '') ?>">
      <?php if (!empty($errors['nom'])): ?>
        <p style="color:#e8394d; font-size:12px; margin-top:-12px; margin-bottom:12px;">
          <?= htmlspecialchars($errors['nom']) ?>
        </p>
      <?php endif; ?>

      <label>Prénom</label>
      <input type="text" name="prenom"
             placeholder="Ex: Mohamed"
             value="<?= htmlspecialchars($item['prenom'] ?? '') ?>">
      <?php if (!empty($errors['prenom'])): ?>
        <p style="color:#e8394d; font-size:12px; margin-top:-12px; margin-bottom:12px;">
          <?= htmlspecialchars($errors['prenom']) ?>
        </p>
      <?php endif; ?>

      <label>Adresse mail</label>
      <!-- type="text" — NOT type="email" -->
      <input type="text" name="mail"
             placeholder="exemple@gmail.com"
             value="<?= htmlspecialchars($item['mail'] ?? '') ?>">
      <?php if (!empty($errors['mail'])): ?>
        <p style="color:#e8394d; font-size:12px; margin-top:-12px; margin-bottom:12px;">
          <?= htmlspecialchars($errors['mail']) ?>
        </p>
      <?php endif; ?>

      <label>Mot de passe <span style="color:#aaa; font-size:11px;">(laisser vide pour ne pas changer)</span></label>
      <input type="password" name="password" placeholder="••••"
             value="<?= htmlspecialchars($item['password'] ?? '') ?>">
      <?php if (!empty($errors['password'])): ?>
        <p style="color:#e8394d; font-size:12px; margin-top:-12px; margin-bottom:12px;">
          <?= htmlspecialchars($errors['password']) ?>
        </p>
      <?php endif; ?>

      <label>Rôle</label>
      <select name="role">
        <option value="user"  <?= ($item['role'] ?? '') === 'user'  ? 'selected' : '' ?>>Utilisateur</option>
        <option value="admin" <?= ($item['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
      </select>

      <button type="submit"
              style="width:100%; background:#e8394d; color:white; border:none;
                     border-radius:8px; padding:13px; font-size:15px;
                     font-weight:700; cursor:pointer; margin-top:8px;">
        💾 Enregistrer les modifications
      </button>

      <a href="index.php?ctrl=utilisateur&action=index"
         style="display:block; text-align:center; margin-top:14px;
                color:#888; font-size:13px; text-decoration:none;">
        Annuler
      </a>
    </form>
  </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
