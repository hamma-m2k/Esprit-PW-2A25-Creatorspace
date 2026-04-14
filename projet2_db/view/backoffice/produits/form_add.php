<?php
// /view/backoffice/produits/form_add.php
// No business logic — display only.
include __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
  <div>
    <div class="page-title">Ajouter un produit</div>
    <div class="page-subtitle">Remplissez tous les champs ci-dessous</div>
  </div>
  <a href="index.php?controller=produit&action=index" class="btn btn-light">← Retour à la liste</a>
</div>

<div style="max-width:580px;">
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

    <!-- POST → controller?action=store — NO HTML5 validation -->
    <form method="POST" action="index.php?controller=produit&action=store">

      <label>Nom du produit</label>
      <input type="text" name="nom" placeholder="Ex: T-shirt blanc"
             value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
             style="width:100%;background:#f5f7fa;border:1px solid #e8e8e8;
                    border-radius:8px;padding:11px 14px;font-size:14px;
                    margin-bottom:16px;outline:none;">

      <label>Description</label>
      <textarea name="description" rows="3" placeholder="Description du produit…"
                style="width:100%;background:#f5f7fa;border:1px solid #e8e8e8;
                       border-radius:8px;padding:11px 14px;font-size:14px;
                       margin-bottom:16px;outline:none;resize:vertical;"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div>
          <label>Prix (€)</label>
          <input type="text" name="prix" placeholder="Ex: 29.99"
                 value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>"
                 style="width:100%;background:#f5f7fa;border:1px solid #e8e8e8;
                        border-radius:8px;padding:11px 14px;font-size:14px;
                        margin-bottom:16px;outline:none;">
        </div>
        <div>
          <label>Stock</label>
          <input type="text" name="stock" placeholder="Ex: 100"
                 value="<?= htmlspecialchars($_POST['stock'] ?? '') ?>"
                 style="width:100%;background:#f5f7fa;border:1px solid #e8e8e8;
                        border-radius:8px;padding:11px 14px;font-size:14px;
                        margin-bottom:16px;outline:none;">
        </div>
      </div>

      <button type="submit"
              style="width:100%;background:#e8394d;color:white;border:none;
                     border-radius:8px;padding:13px;font-size:15px;
                     font-weight:700;cursor:pointer;margin-top:8px;">
        ➕ Ajouter le produit
      </button>
      <a href="index.php?controller=produit&action=index"
         style="display:block;text-align:center;margin-top:14px;
                color:#888;font-size:13px;text-decoration:none;">Annuler</a>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
