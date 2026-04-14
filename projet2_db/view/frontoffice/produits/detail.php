<!DOCTYPE html>
<?php // /view/frontoffice/produits/detail.php — public product detail, no logic ?>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($item['nom']) ?> — CreatorSpace</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Segoe UI',Arial,sans-serif; background:#f0f2f5; color:#333; }
.navbar { background:#1a1a2e; height:58px; display:flex; align-items:center; padding:0 30px; }
.navbar .logo { color:#fff; font-size:18px; font-weight:700; }
.navbar .logo span { color:#e8394d; }
.navbar a { color:#aaa; font-size:13px; text-decoration:none; margin-left:24px; }
.navbar a:hover { color:#fff; }
.container { max-width:700px; margin:40px auto; padding:0 24px; }
.card {
  background:#fff; border-radius:12px;
  box-shadow:0 2px 10px rgba(0,0,0,0.06); padding:36px;
}
.product-name  { font-size:26px; font-weight:800; color:#1a1a2e; margin-bottom:12px; }
.product-desc  { font-size:15px; color:#555; line-height:1.7; margin-bottom:24px; }
.product-price { font-size:28px; font-weight:800; color:#e8394d; margin-bottom:8px; }
.product-stock { font-size:14px; color:#888; margin-bottom:28px; }
.badge-stock {
  display:inline-block; padding:5px 14px; border-radius:20px;
  font-size:13px; font-weight:600;
}
.btn-back {
  display:inline-block; background:#1a1a2e; color:#fff;
  padding:11px 24px; border-radius:8px; font-size:14px;
  font-weight:600; text-decoration:none;
}
.btn-back:hover { background:#e8394d; }
.divider { border:none; border-top:1px solid #f0f0f0; margin:24px 0; }
</style>
</head>
<body>

<nav class="navbar">
  <div class="logo"><span>✦</span> CreatorSpace</div>
  <a href="index.php?controller=produit&action=front">← Tous les produits</a>
  <a href="index.php?controller=produit&action=index">⚙️ Backoffice</a>
</nav>

<div class="container">
  <div class="card">
    <div class="product-name"><?= htmlspecialchars($item['nom']) ?></div>
    <div class="product-desc"><?= nl2br(htmlspecialchars($item['description'])) ?></div>
    <hr class="divider">
    <div class="product-price"><?= number_format((float)$item['prix'], 2, ',', ' ') ?> €</div>
    <div class="product-stock">
      <?php $s = (int)$item['stock']; ?>
      <span class="badge-stock"
            style="background:<?= $s > 0 ? '#e6f9ed' : '#fff0f2' ?>;
                   color:<?= $s > 0 ? '#007a32' : '#e8394d' ?>;">
        <?= $s > 0 ? "✅ En stock ($s disponible(s))" : '❌ Rupture de stock' ?>
      </span>
    </div>
    <a href="index.php?controller=produit&action=front" class="btn-back">← Retour aux produits</a>
  </div>
</div>

</body>
</html>
