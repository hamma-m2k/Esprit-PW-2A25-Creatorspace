<?php include __DIR__ . '/layout/header.php'; ?>

<div class="page-header">
  <div>
    <div class="page-title">Modifier l'utilisateur</div>
    <div class="page-subtitle">Modifiez les informations ci-dessous</div>
  </div>
  <a href="index.php?action=list" class="btn btn-light">← Retour à la liste</a>
</div>

<div style="max-width:560px;">
  <div class="card">

    <?php if (!empty($errors)): ?>
    <div class="alert-error">
      <ul>
        <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <!-- onsubmit → validate.js first, PHP second — NO HTML5 attributes -->
    <form id="user-form" method="POST"
          action="index.php?action=edit&id=<?= (int)$user['id'] ?>"
          onsubmit="return validateForm();">

      <label>Nom</label>
      <!-- type="text" — NO required, NO pattern -->
      <input type="text" id="nom" name="nom"
             placeholder="Ex: Marzougui"
             value="<?= htmlspecialchars($user['nom'] ?? '') ?>">

      <label>Prénom</label>
      <input type="text" id="prenom" name="prenom"
             placeholder="Ex: Mohamed"
             value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">

      <label>Adresse mail</label>
      <!-- type="text" — NOT type="email" -->
      <input type="text" id="mail" name="mail"
             placeholder="exemple@gmail.com"
             value="<?= htmlspecialchars($user['mail'] ?? '') ?>">

      <label>Mot de passe <span style="color:#aaa; font-size:11px;">(chiffres uniquement)</span></label>
      <input type="password" id="password" name="password"
             value="<?= htmlspecialchars($user['password'] ?? '') ?>">

      <label>Rôle</label>
      <select name="role">
        <option value="user"  <?= ($user['role'] ?? '') === 'user'  ? 'selected' : '' ?>>Utilisateur</option>
        <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
      </select>

      <button type="submit"
              style="width:100%; background:#e8394d; color:white; border:none;
                     border-radius:8px; padding:13px; font-size:15px;
                     font-weight:700; cursor:pointer; margin-top:8px;">
        💾 Enregistrer les modifications
      </button>

      <a href="index.php?action=list"
         style="display:block; text-align:center; margin-top:14px;
                color:#888; font-size:13px; text-decoration:none;">
        Annuler
      </a>
    </form>
  </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
<script src="view/js/validate.js"></script>
