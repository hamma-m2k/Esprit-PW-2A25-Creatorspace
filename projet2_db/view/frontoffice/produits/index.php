<!DOCTYPE html>
<?php
// /view/frontoffice/produits/index.php — public product list, no logic
// Backoffice button visible ONLY when connected as admin
if (session_status() === PHP_SESSION_NONE) session_start();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Nos Produits — CreatorSpace</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Segoe UI',Arial,sans-serif; background:#f0f2f5; color:#333; }
.navbar {
  background:#1a1a2e; height:58px;
  display:flex; align-items:center;
  justify-content:space-between; padding:0 30px;
}
.navbar .logo { color:#fff; font-size:18px; font-weight:700; }
.navbar .logo span { color:#e8394d; }
.navbar-right { display:flex; align-items:center; gap:16px; }
.navbar a { color:#aaa; font-size:13px; text-decoration:none; }
.navbar a:hover { color:#fff; }
.btn-backoffice {
  background:#e8394d; color:#fff !important;
  padding:7px 16px; border-radius:6px;
  font-size:13px; font-weight:600; text-decoration:none;
}
.btn-backoffice:hover { opacity:0.88; }
.btn-logout {
  background:transparent; color:#aaa !important;
  padding:7px 16px; border-radius:6px;
  font-size:13px; border:1px solid #444; text-decoration:none;
}
.btn-logout:hover { color:#fff !important; border-color:#fff; }
.admin-name { color:#aaa; font-size:13px; }
.container { max-width:1100px; margin:40px auto; padding:0 24px; }
.page-title { font-size:26px; font-weight:800; color:#1a1a2e; margin-bottom:6px; }
.page-sub   { color:#888; font-size:14px; margin-bottom:32px; }
.grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:24px; }
.product-card {
  background:#fff; border-radius:12px;
  box-shadow:0 2px 10px rgba(0,0,0,0.06);
  padding:24px; transition:transform 0.2s;
}
.product-card:hover { transform:translateY(-3px); box-shadow:0 6px 20px rgba(0,0,0,0.1); }
.product-name  { font-size:16px; font-weight:700; color:#1a1a2e; margin-bottom:8px; }
.product-desc  { font-size:13px; color:#666; margin-bottom:16px; line-height:1.5; }
.product-price { font-size:20px; font-weight:800; color:#e8394d; margin-bottom:8px; }
.product-stock { font-size:12px; color:#888; margin-bottom:16px; }
.btn-detail {
  display:inline-block; background:#1a1a2e; color:#fff;
  padding:9px 20px; border-radius:8px; font-size:13px;
  font-weight:600; text-decoration:none;
}
.btn-detail:hover { background:#e8394d; }
.empty { text-align:center; color:#aaa; padding:60px; font-size:15px; }
.login-hint {
  text-align:center; padding:12px;
  background:#fff; border-radius:8px;
  font-size:13px; color:#888; margin-bottom:24px;
  box-shadow:0 1px 4px rgba(0,0,0,0.05);
}
.login-hint a { color:#e8394d; text-decoration:none; font-weight:600; }
</style>
</head>
<body>

<nav class="navbar">
  <div class="logo"><span>✦</span> CreatorSpace</div>
  <div class="navbar-right">

    <?php if ($isAdmin): ?>
      <!-- Admin connecté : affiche son nom + bouton backoffice + déconnexion -->
      <span class="admin-name">
        👤 <?= htmlspecialchars($_SESSION['nom']) ?>
      </span>
      <a href="index.php?ctrl=utilisateur&action=index" class="btn-backoffice">
        ⚙️ Back Office
      </a>
      <a href="index.php?ctrl=auth&action=logout" class="btn-logout">
        Déconnexion
      </a>
    <?php else: ?>
      <!-- Non connecté ou user simple : bouton connexion discret -->
      <a href="index.php?ctrl=auth&action=login" class="navbar a">
        🔐 Connexion admin
      </a>
    <?php endif; ?>

  </div>
</nav>

<div class="container">
  <div class="page-title">Nos Produits</div>
  <div class="page-sub"><?= count($data) ?> produit(s) disponible(s)</div>

  <?php if (empty($data)): ?>
  <div class="empty">Aucun produit disponible pour le moment.</div>
  <?php else: ?>
  <div class="grid">
    <?php foreach ($data as $p): ?>
    <div class="product-card">
      <div class="product-name"><?= htmlspecialchars($p['nom']) ?></div>
      <div class="product-desc"><?= htmlspecialchars(mb_strimwidth($p['description'], 0, 80, '…')) ?></div>
      <div class="product-price"><?= number_format((float)$p['prix'], 2, ',', ' ') ?> €</div>
      <div class="product-stock">
        Stock :
        <strong style="color:<?= (int)$p['stock'] > 0 ? '#007a32' : '#e8394d' ?>">
          <?= (int)$p['stock'] > 0 ? $p['stock'] . ' disponible(s)' : 'Rupture de stock' ?>
        </strong>
      </div>
      <a href="index.php?controller=produit&action=show&id=<?= $p['id'] ?>" class="btn-detail">
        Voir le détail →
      </a>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

</body>
</html>
