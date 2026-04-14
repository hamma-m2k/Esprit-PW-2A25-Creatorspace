<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>CreatorSpace — Backoffice</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
  font-family: 'Segoe UI', Arial, sans-serif;
  background: #f0f2f5; color: #333;
  display: flex; flex-direction: column; min-height: 100vh;
}
.navbar {
  background: #1a1a2e; height: 58px;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 30px; position: fixed; top: 0; left: 0; right: 0; z-index: 100;
}
.navbar .logo { color: #ffffff; font-size: 18px; font-weight: 700; letter-spacing: 1px; }
.navbar .logo span { color: #e8394d; }
.navbar .nav-right { display: flex; align-items: center; gap: 16px; }
.navbar .nav-right .admin-name { color: #aaaaaa; font-size: 13px; }
.navbar .nav-right .btn-logout {
  background: #e8394d; color: white; border: none;
  border-radius: 6px; padding: 7px 16px; font-size: 13px;
  cursor: pointer; text-decoration: none;
}
.sidebar {
  position: fixed; top: 58px; left: 0;
  width: 220px; height: calc(100vh - 58px);
  background: #ffffff; border-right: 1px solid #e8e8e8; padding-top: 20px;
}
.sidebar a {
  display: block; padding: 13px 24px; color: #555;
  font-size: 14px; text-decoration: none;
  border-left: 3px solid transparent; transition: all 0.2s;
}
.sidebar a:hover, .sidebar a.active {
  color: #e8394d; font-weight: 600;
  border-left: 3px solid #e8394d; background: #fff5f6;
}
.sidebar .sidebar-title {
  color: #aaa; font-size: 11px; text-transform: uppercase;
  padding: 8px 24px 4px; letter-spacing: 1px;
}
.main-content {
  margin-left: 220px; margin-top: 58px;
  padding: 32px; min-height: calc(100vh - 58px);
}
.card {
  background: #ffffff; border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.06);
  padding: 28px; margin-bottom: 24px;
}
.btn {
  display: inline-block; padding: 10px 20px;
  border-radius: 8px; font-size: 14px; font-weight: 600;
  cursor: pointer; border: none; text-decoration: none;
}
.btn-primary  { background: #e8394d; color: white; }
.btn-dark     { background: #1a1a2e; color: white; }
.btn-light    { background: #f0f2f5; color: #333; }
.btn-danger   { background: #fff0f2; color: #e8394d; }
.btn:hover    { opacity: 0.88; }
.alert-success {
  background: #e6f9ed; border: 1px solid #00c853;
  color: #007a32; border-radius: 8px;
  padding: 12px 16px; margin-bottom: 20px; font-size: 13px;
}
.alert-error {
  background: #fff0f2; border: 1px solid #e8394d;
  color: #e8394d; border-radius: 8px;
  padding: 12px 16px; margin-bottom: 20px; font-size: 13px;
}
.alert-error ul { margin: 0; padding-left: 18px; }
.page-header {
  display: flex; justify-content: space-between;
  align-items: center; margin-bottom: 24px;
}
.page-title   { font-size: 22px; font-weight: 700; color: #1a1a2e; }
.page-subtitle{ color: #888; font-size: 13px; margin-top: 4px; }
label { display: block; color: #555; font-size: 13px; margin-bottom: 6px; }
input[type="text"], input[type="password"], select {
  width: 100%; background: #f5f7fa;
  border: 1px solid #e8e8e8; border-radius: 8px;
  padding: 11px 14px; font-size: 14px; margin-bottom: 16px; outline: none;
}
input[type="text"]:focus, input[type="password"]:focus, select:focus {
  border-color: #e8394d; background: #fff;
}
</style>
</head>
<body>

<nav class="navbar">
  <div class="logo"><span>✦</span> CreatorSpace</div>
  <div class="nav-right">
    <span class="admin-name">
      Bonjour, <?= htmlspecialchars(($_SESSION['nom'] ?? '') . ' ' . ($_SESSION['prenom'] ?? '')) ?>
    </span>
    <a href="index.php?ctrl=auth&action=logout" class="btn-logout">Se déconnecter</a>
  </div>
</nav>

<div class="sidebar">
  <div class="sidebar-title">Menu</div>
  <a href="index.php?ctrl=utilisateur&action=index"
     class="<?= in_array($_GET['action'] ?? '', ['index','edit','create','store','update','delete']) ? 'active' : '' ?>">
    👥 Utilisateurs
  </a>
  <a href="index.php?ctrl=utilisateur&action=create"
     class="<?= ($_GET['action'] ?? '') === 'create' ? 'active' : '' ?>">
    ➕ Ajouter
  </a>
  <a href="index.php?ctrl=utilisateur&action=profile"
     class="<?= in_array($_GET['action'] ?? '', ['profile','updateProfile']) ? 'active' : '' ?>">
    👤 Mon profil
  </a>

  <div class="sidebar-title" style="margin-top:16px;">Site</div>
  <!-- Lien vers le front office — toujours visible depuis le backoffice -->
  <a href="index.php?ctrl=produit&action=front">
    🌐 Voir le front office
  </a>
</div>

<div class="main-content">
