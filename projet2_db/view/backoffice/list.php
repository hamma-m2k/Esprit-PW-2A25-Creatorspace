<?php
// /view/backoffice/list.php — admin only (restriction handled by controller)
// No business logic — display only.

// Fix sidebar active state for new ctrl= routing
$_GET['action'] = $_GET['action'] ?? 'index';
include __DIR__ . '/layout/header.php';
?>

<?php if (isset($_GET['success'])): ?>
<div class="alert-success">
  <?php
  if     ($_GET['success'] === 'ajout')       echo '✅ Utilisateur ajouté avec succès.';
  elseif ($_GET['success'] === 'modif')       echo '✅ Utilisateur modifié avec succès.';
  elseif ($_GET['success'] === 'suppression') echo '✅ Utilisateur supprimé avec succès.';
  ?>
</div>
<?php endif; ?>

<div class="page-header">
  <div>
    <div class="page-title">Gestion des utilisateurs</div>
    <div class="page-subtitle"><?= count($data) ?> utilisateur(s) enregistré(s)</div>
  </div>
  <a href="index.php?ctrl=utilisateur&action=create" class="btn btn-primary">
    + Ajouter un utilisateur
  </a>
</div>

<div class="card" style="padding:0; overflow:hidden;">
  <table style="width:100%; border-collapse:collapse;">
    <thead>
      <tr style="background:#f8f9fa;">
        <th style="padding:14px 18px; text-align:left; color:#888; font-size:12px; text-transform:uppercase;">#</th>
        <th style="padding:14px 18px; text-align:left; color:#888; font-size:12px; text-transform:uppercase;">Nom</th>
        <th style="padding:14px 18px; text-align:left; color:#888; font-size:12px; text-transform:uppercase;">Prénom</th>
        <th style="padding:14px 18px; text-align:left; color:#888; font-size:12px; text-transform:uppercase;">Mail</th>
        <th style="padding:14px 18px; text-align:left; color:#888; font-size:12px; text-transform:uppercase;">Rôle</th>
        <th style="padding:14px 18px; text-align:left; color:#888; font-size:12px; text-transform:uppercase;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($data)): ?>
      <tr>
        <td colspan="6" style="padding:28px; text-align:center; color:#aaa; font-size:14px;">
          Aucun utilisateur enregistré.
        </td>
      </tr>
      <?php else: ?>
      <?php foreach ($data as $u): ?>
      <tr style="border-top:1px solid #f0f0f0;"
          onmouseover="this.style.background='#fafafa'"
          onmouseout="this.style.background='white'">
        <td style="padding:14px 18px; font-size:14px; color:#aaa;"><?= $u['id'] ?></td>
        <td style="padding:14px 18px; font-size:14px; font-weight:600; color:#1a1a2e;"><?= htmlspecialchars($u['nom']) ?></td>
        <td style="padding:14px 18px; font-size:14px; color:#333;"><?= htmlspecialchars($u['prenom']) ?></td>
        <td style="padding:14px 18px; font-size:14px; color:#333;"><?= htmlspecialchars($u['mail']) ?></td>
        <td style="padding:14px 18px;">
          <?php if ($u['role'] === 'admin'): ?>
            <span style="background:#fff0f2; color:#e8394d; border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600;">Admin</span>
          <?php else: ?>
            <span style="background:#f0f4ff; color:#3d5afe; border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600;">User</span>
          <?php endif; ?>
        </td>
        <td style="padding:14px 18px;">
          <a href="index.php?ctrl=utilisateur&action=edit&id=<?= $u['id'] ?>"
             class="btn btn-light" style="font-size:12px; padding:6px 14px; margin-right:6px;">
            ✏️ Modifier
          </a>
          <a href="index.php?ctrl=utilisateur&action=delete&id=<?= $u['id'] ?>"
             class="btn btn-danger" style="font-size:12px; padding:6px 14px;"
             onclick="return window.confirm('Confirmer la suppression ?')">
            🗑️ Supprimer
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
