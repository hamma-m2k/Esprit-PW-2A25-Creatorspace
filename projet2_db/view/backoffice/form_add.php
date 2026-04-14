<?php
// /view/backoffice/form_add.php — admin only (restriction handled by controller)
// No business logic — display only. Values pre-filled from $old on error.
include __DIR__ . '/layout/header.php';
?>

<div class="page-header">
  <div>
    <div class="page-title">Ajouter un utilisateur</div>
    <div class="page-subtitle">Remplissez tous les champs ci-dessous</div>
  </div>
  <a href="index.php?ctrl=utilisateur&action=index" class="btn btn-light">← Retour à la liste</a>
</div>

<div style="max-width:560px;">
  <div class="card">

    <!-- POST → action=store — NO HTML5 attributes anywhere -->
    <form method="POST" action="index.php?ctrl=utilisateur&action=store">

      <label>Nom</label>
      <!-- type="text" — NO required, NO pattern -->
      <input type="text" name="nom"
             placeholder="Ex: Marzougui"
             value="<?= htmlspecialchars($old['nom'] ?? '') ?>">
      <?php if (!empty($errors['nom'])): ?>
        <p style="color:#e8394d; font-size:12px; margin-top:-12px; margin-bottom:12px;">
          <?= htmlspecialchars($errors['nom']) ?>
        </p>
      <?php endif; ?>

      <label>Prénom</label>
      <input type="text" name="prenom"
             placeholder="Ex: Mohamed"
             value="<?= htmlspecialchars($old['prenom'] ?? '') ?>">
      <?php if (!empty($errors['prenom'])): ?>
        <p style="color:#e8394d; font-size:12px; margin-top:-12px; margin-bottom:12px;">
          <?= htmlspecialchars($errors['prenom']) ?>
        </p>
      <?php endif; ?>

      <label>Adresse mail</label>
      <!-- type="text" — NOT type="email" -->
      <input type="text" name="mail"
             placeholder="exemple@gmail.com"
             value="<?= htmlspecialchars($old['mail'] ?? '') ?>">
      <?php if (!empty($errors['mail'])): ?>
        <p style="color:#e8394d; font-size:12px; margin-top:-12px; margin-bottom:12px;">
          <?= htmlspecialchars($errors['mail']) ?>
        </p>
      <?php endif; ?>

      <label>Mot de passe</label>
      <input type="password" name="password" placeholder="••••">
      <?php if (!empty($errors['password'])): ?>
        <p style="color:#e8394d; font-size:12px; margin-top:-12px; margin-bottom:12px;">
          <?= htmlspecialchars($errors['password']) ?>
        </p>
      <?php endif; ?>

      <label>Rôle</label>
      <select name="role">
        <option value="user"  <?= ($old['role'] ?? 'user') === 'user'  ? 'selected' : '' ?>>Utilisateur</option>
        <option value="admin" <?= ($old['role'] ?? '')     === 'admin' ? 'selected' : '' ?>>Administrateur</option>
      </select>

      <button type="submit"
              style="width:100%; background:#e8394d; color:white; border:none;
                     border-radius:8px; padding:13px; font-size:15px;
                     font-weight:700; cursor:pointer; margin-top:8px;">
        ➕ Ajouter l'utilisateur
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
