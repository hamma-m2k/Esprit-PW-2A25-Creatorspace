<?php
// /view/backoffice/produits/list.php
// No business logic — display only.
include __DIR__ . '/../layout/header.php';
?>

<?php if (isset($_GET['success'])): ?>
<div class="alert-success">
  <?php
  if     ($_GET['success'] === 'ajout')       echo '✅ Produit ajouté avec succès.';
  elseif ($_GET['success'] === 'modif')       echo '✅ Produit modifié avec succès.';
  elseif ($_GET['success'] === 'suppression') echo '✅ Produit supprimé avec succès.';
  ?>
</div>
<?php endif; ?>

<div class="page-header">
  <div>
    <div class="page-title">Gestion des produits</div>
    <div class="page-subtitle"><?= count($data) ?> produit(s) enregistré(s)</div>
  </div>
  <a href="index.php?controller=produit&action=create" class="btn btn-primary">+ Ajouter un produit</a>
</div>

<div class="card" style="padding:0; overflow:hidden;">
  <table style="width:100%; border-collapse:collapse;">
    <thead>
      <tr style="background:#f8f9fa;">
        <th style="padding:14px 18px; text-align:left; color:#888; font-size:12px; text-transform:uppercase;">#</th>
        <th style="padding:14px 18px; text-align:left; color:#888; font-size:12px; text-transform:uppercase;">Nom</th>
        <th style="padding:14px 18px; text-align:left; color:#888; font-size:12px; text-transform:uppercase;">Description</th>
        <th style="padding:14px 18px; text-align:left; color:#888; font-size:12px; text-transform:uppercase;">Prix</th>
        <th style="padding:14px 18px; text-align:left; color:#888; font-size:12px; text-transform:uppercase;">Stock</th>
        <th style="padding:14px 18px; text-align:left; color:#888; font-size:12px; text-transform:uppercase;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($data)): ?>
      <tr>
        <td colspan="6" style="padding:28px; text-align:center; color:#aaa; font-size:14px;">
          Aucun produit enregistré.
        </td>
      </tr>
      <?php else: ?>
      <?php foreach ($data as $p): ?>
      <tr style="border-top:1px solid #f0f0f0;"
          onmouseover="this.style.background='#fafafa'"
          onmouseout="this.style.background='white'">
        <td style="padding:14px 18px; font-size:14px; color:#aaa;"><?= $p['id'] ?></td>
        <td style="padding:14px 18px; font-size:14px; font-weight:600; color:#1a1a2e;"><?= htmlspecialchars($p['nom']) ?></td>
        <td style="padding:14px 18px; font-size:13px; color:#555; max-width:220px;">
          <?= htmlspecialchars(mb_strimwidth($p['description'], 0, 60, '…')) ?>
        </td>
        <td style="padding:14px 18px; font-size:14px; color:#1a1a2e; font-weight:600;">
          <?= number_format((float)$p['prix'], 2, ',', ' ') ?> €
        </td>
        <td style="padding:14px 18px; font-size:14px;">
          <?php $s = (int)$p['stock']; ?>
          <span style="background:<?= $s > 0 ? '#e6f9ed' : '#fff0f2' ?>;
                       color:<?= $s > 0 ? '#007a32' : '#e8394d' ?>;
                       border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600;">
            <?= $s ?>
          </span>
        </td>
        <td style="padding:14px 18px;">
          <a href="index.php?controller=produit&action=show&id=<?= $p['id'] ?>"
             class="btn btn-light" style="font-size:12px; padding:6px 12px; margin-right:4px;">
            👁️ Voir
          </a>
          <a href="index.php?controller=produit&action=edit&id=<?= $p['id'] ?>"
             class="btn btn-light" style="font-size:12px; padding:6px 12px; margin-right:4px;">
            ✏️ Modifier
          </a>
          <a href="index.php?controller=produit&action=delete&id=<?= $p['id'] ?>"
             class="btn btn-danger" style="font-size:12px; padding:6px 12px;"
             onclick="return confirm('Supprimer ce produit ?')">
            🗑️ Supprimer
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
