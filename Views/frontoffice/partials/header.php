<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'AntiGravity') ?> — CreatorSpace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/Views/assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="admin-layout">

<!-- ═══════════ SIDEBAR ═══════════ -->
<aside class="sidebar">

  <a href="<?= BASE_URL ?>/dashboard" class="sidebar-brand">
    <div class="brand-icon">✦</div>
    <div class="brand-text">Creator<span>Space</span></div>
  </a>

  <div class="sidebar-user">
    <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></div>
    <div class="user-info">
      <div class="name"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></div>
      <div class="role"><?= htmlspecialchars($_SESSION['user_role'] ?? '') ?></div>
    </div>
  </div>

  <nav class="sidebar-nav">

    <div class="nav-section-title">Principal</div>

    <a href="<?= BASE_URL ?>/dashboard" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : '' ?>">
      <span class="icon">⊞</span> Dashboard
    </a>

    <a href="<?= BASE_URL ?>/users" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/users') !== false) ? 'active' : '' ?>">
      <span class="icon">◉</span> Utilisateurs
    </a>

    <a href="<?= BASE_URL ?>/requests" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/requests') !== false) ? 'active' : '' ?>">
      <span class="icon">◈</span> Demandes
      <?php
        // Affiche le badge de demandes en attente si possible
        if (isset($_SESSION['pending_count']) && $_SESSION['pending_count'] > 0):
      ?>
      <span class="nav-badge"><?= $_SESSION['pending_count'] ?></span>
      <?php endif; ?>
    </a>

    <a href="<?= BASE_URL ?>/profiles" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/profiles') !== false) ? 'active' : '' ?>">
      <span class="icon">◎</span> Profils
    </a>

    <div class="nav-section-title" style="margin-top:8px;">Métier</div>

    <a href="<?= BASE_URL ?>/contrats" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/contrats') !== false) ? 'active' : '' ?>">
      <span class="icon">◧</span> Contrats
    </a>

    <a href="<?= BASE_URL ?>/rules" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/rules') !== false) ? 'active' : '' ?>">
      <span class="icon">◆</span> Rules
    </a>

    <div class="nav-section-title" style="margin-top:8px;">Configuration</div>

    <a href="<?= BASE_URL ?>/config/roles" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/config/roles') !== false) ? 'active' : '' ?>">
      <span class="icon">◬</span> Rôles &amp; Permissions
    </a>

    <a href="<?= BASE_URL ?>/config/settings" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/config/settings') !== false) ? 'active' : '' ?>">
      <span class="icon">◎</span> Paramètres
    </a>

    <a href="<?= BASE_URL ?>/config/history" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/config/history') !== false) ? 'active' : '' ?>">
      <span class="icon">◷</span> Historique
    </a>

  </nav>

  <div class="sidebar-footer">
    <a href="<?= BASE_URL ?>/logout" class="nav-item" style="color:var(--danger);">
      <span class="icon">⏏</span> Déconnexion
    </a>
  </div>

</aside>
<!-- ═══════════ CONTENU ═══════════ -->
<div class="main-content">

<header class="topbar">
  <div class="topbar-title">
    <h2><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h2>
    <p><?= htmlspecialchars($pageSubtitle ?? '') ?></p>
  </div>
  <div class="topbar-actions">
    <a href="<?= BASE_URL ?>/contrats/create" class="topbar-btn" title="Nouveau contrat">＋</a>
    <a href="<?= BASE_URL ?>/config/history" class="topbar-btn" title="Historique">◷</a>
    <a href="<?= BASE_URL ?>/profiles/view/<?= $_SESSION['user_id'] ?? 0 ?>" class="topbar-btn" title="Mon profil">◉</a>
  </div>
</header>

<main class="page-content">
