<?php
/**
 * FIX: Active-state ternaries simplified — $page variable already set by controller.
 * No logic change, just cleaner readability. View only renders, never computes.
 */
require_once __DIR__ . '/../layout/header.php';
?>

<div class="office active" id="back-office">
  <div class="back-layout">

    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <div class="sidebar-logo">✦ CreatorSpace</div>
        <div class="sidebar-subtitle">Admin Panel</div>
      </div>
      <nav class="sidebar-nav">
        <div class="sidebar-section-label">Principal</div>
        <a href="index.php?ctrl=user&action=index">
          <button class="sidebar-item <?= $page === 'dashboard' ? 'active' : '' ?>">
            <span class="sidebar-icon">📊</span><span>Dashboard</span>
          </button>
        </a>
        <a href="index.php?page=users">
          <button class="sidebar-item <?= $page === 'users' ? 'active' : '' ?>">
            <span class="sidebar-icon">👥</span><span>Utilisateurs</span>
            <span class="sidebar-badge">12K</span>
          </button>
        </a>
        <a href="index.php?ctrl=demande&action=liste">
          <button class="sidebar-item <?= $page === 'demandes' ? 'active' : '' ?>">
            <span class="sidebar-icon">📋</span><span>Demandes</span>
            <?php if (!empty($demandesEnAttente) && (int)$demandesEnAttente > 0): ?>
                <span class="sidebar-badge" style="background:rgba(229,62,62,0.2); color:var(--danger);">
                  <?= (int)$demandesEnAttente ?>
                </span>
            <?php endif; ?>
          </button>
        </a>
        <a href="index.php?page=profiles">
          <button class="sidebar-item <?= $page === 'profiles' ? 'active' : '' ?>">
            <span class="sidebar-icon">🎨</span><span>Profils</span>
          </button>
        </a>
        <div class="sidebar-section-label">Configuration</div>
        <a href="index.php?page=roles">
          <button class="sidebar-item <?= $page === 'roles' ? 'active' : '' ?>">
            <span class="sidebar-icon">🔐</span><span>Rôles & Permissions</span>
          </button>
        </a>
        <a href="index.php?page=settings">
          <button class="sidebar-item <?= $page === 'settings' ? 'active' : '' ?>">
            <span class="sidebar-icon">⚙️</span><span>Paramètres</span>
          </button>
        </a>
      </nav>
      <div class="sidebar-footer">
        <div class="sidebar-user">
          <div class="sidebar-avatar">
            <?= htmlspecialchars($currentUser['initials'] ?? 'AD') ?>
          </div>
          <div class="sidebar-user-info">
            <div class="sidebar-uname"><?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?></div>
            <div class="sidebar-urole"><?= htmlspecialchars($currentUser['role'] ?? 'Admin') ?></div>
          </div>
          <a href="index.php?ctrl=auth&action=logout">
            <button class="sidebar-logout" title="Déconnexion">↩</button>
          </a>
        </div>
      </div>
    </aside>

    <main class="back-main">
