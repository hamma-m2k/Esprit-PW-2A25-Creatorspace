<?php
// /view/backoffice/profile.php — any logged-in user (restriction handled by controller)
// No business logic — display only. Values pre-filled from $item.
include __DIR__ . '/layout/header.php';
?>

<div class="page-header">
  <div>
    <div class="page-title">Mon profil</div>
    <div class="page-subtitle">Gérez vos informations personnelles</div>
  </div>
  <?php if ($_SESSION['role'] === 'admin'): ?>
  <a href="index.php?ctrl=utilisateur&action=index" class="btn btn-dark">
    ← Retour à la liste
  </a>
  <?php endif; ?>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] === 'modif'): ?>
<div class="alert-success">✅ Profil mis à jour avec succès.</div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'admin_nodelete'): ?>
<div class="alert-error">⚠️ Un administrateur ne peut pas supprimer son propre compte.</div>
<?php endif; ?>

<div style="max-width:560px;">
  <div class="card">

    <!-- POST → action=updateProfile — NO HTML5 attributes anywhere -->
    <form method="POST" action="index.php?ctrl=utilisateur&action=updateProfile">

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

      <label>Nouveau mot de passe <span style="color:#aaa; font-size:11px;">(laisser vide pour ne pas changer)</span></label>
      <!-- password field empty by default for security -->
      <input type="password" name="password" placeholder="••••">
      <?php if (!empty($errors['password'])): ?>
        <p style="color:#e8394d; font-size:12px; margin-top:-12px; margin-bottom:12px;">
          <?= htmlspecialchars($errors['password']) ?>
        </p>
      <?php endif; ?>

      <button type="submit"
              style="width:100%; background:#e8394d; color:white; border:none;
                     border-radius:8px; padding:13px; font-size:15px;
                     font-weight:700; cursor:pointer; margin-top:8px;">
        💾 Mettre à jour mon profil
      </button>
    </form>

    <?php if ($_SESSION['role'] !== 'admin'): ?>
    <!-- Delete own account — hidden for admin -->
    <div style="margin-top:28px; padding-top:20px; border-top:1px solid #f0f0f0;">
      <p style="font-size:13px; color:#888; margin-bottom:12px;">
        Zone dangereuse — cette action est irréversible.
      </p>
      <a href="index.php?ctrl=utilisateur&action=deleteOwn"
         class="btn btn-danger"
         onclick="return window.confirm('Voulez-vous vraiment supprimer votre compte ?')">
        🗑️ Supprimer mon compte
      </a>
    </div>
    <?php endif; ?>

  </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
