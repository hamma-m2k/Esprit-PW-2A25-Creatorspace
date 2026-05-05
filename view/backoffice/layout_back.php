<?php
/**
 * FIX: Active-state ternaries simplified — $page variable already set by controller.
 * No logic change, just cleaner readability. View only renders, never computes.
 */
require_once __DIR__ . '/../layout/header.php';
?>
<style>
/* ── Health AI Dashboard ───────────────── */
.health-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
.health-card      { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 20px; }
.health-full      { grid-column: 1 / -1; }
.health-title     { font-size: 0.9rem; font-weight: 700; color: var(--text3); text-transform: uppercase; margin-bottom: 15px; letter-spacing: 1px; }

.diag-box         { background: rgba(108,63,197,0.1); border: 1px solid rgba(108,63,197,0.3); border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
.diag-title       { font-size: 1.8rem; font-weight: 800; color: var(--text); margin-bottom: 5px; }
.diag-badge       { padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; }
.badge-low        { background: rgba(56,161,105,0.2); color: #68D391; }
.badge-high       { background: rgba(229,62,62,0.2); color: #FC8181; }

.prob-row         { display: flex; align-items: center; gap: 15px; margin-bottom: 12px; }
.prob-label       { width: 140px; font-size: 0.85rem; color: var(--text2); }
.prob-bar-bg      { flex: 1; height: 8px; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden; }
.prob-bar-fill    { height: 100%; transition: width 1s ease; }
.prob-val         { width: 40px; text-align: right; font-weight: 700; color: var(--text); font-size: 0.85rem; }

.feat-row         { display: flex; align-items: center; gap: 15px; margin-bottom: 10px; }
.feat-label       { width: 120px; font-size: 0.82rem; color: var(--text3); }
.feat-dot         { width: 8px; height: 8px; border-radius: 50%; background: #9B5DE5; }
.feat-bar         { flex: 1; height: 4px; background: rgba(108,63,197,0.3); border-radius: 2px; position: relative; }
.feat-bar-fill    { height: 100%; background: #9B5DE5; border-radius: 2px; }

.health-input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.health-slider-group { margin-bottom: 15px; }
.health-slider-head  { display: flex; justify-content: space-between; margin-bottom: 8px; }
.health-slider-val   { font-weight: 700; color: #00C2CB; }
input[type=range]   { width: 100%; height: 6px; border-radius: 3px; background: rgba(255,255,255,0.1); outline: none; -webkit-appearance: none; }
input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; width: 18px; height: 18px; border-radius: 50%; background: #00C2CB; cursor: pointer; border: 3px solid var(--bg); }
</style>

<div class="office active" id="back-office">
  <div class="back-layout">

    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <div class="sidebar-logo">✦ CreatorSpace</div>
        <div class="sidebar-subtitle">Admin Panel</div>
      </div>
      <nav class="sidebar-nav">
        <div class="sidebar-section-label">Principal</div>
        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
        <a href="index.php?ctrl=user&action=dashboard">
          <button class="sidebar-item <?= $page === 'dashboard' ? 'active' : '' ?>">
            <span class="sidebar-icon">📊</span><span>Dashboard</span>
          </button>
        </a>
        <a href="index.php?ctrl=user&action=statistics">
          <button class="sidebar-item <?= $page === 'stats' ? 'active' : '' ?>">
            <span class="sidebar-icon">📈</span><span>Statistiques</span>
          </button>
        </a>
        <a href="index.php?ctrl=user&action=index">
          <button class="sidebar-item <?= $page === 'users' ? 'active' : '' ?>">
            <span class="sidebar-icon">👥</span><span>Utilisateurs</span>
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
        <?php endif; ?>

        <a href="index.php?ctrl=user&action=profile">
          <button class="sidebar-item <?= $page === 'profile' ? 'active' : '' ?>">
            <span class="sidebar-icon">👤</span><span>Mon Profil</span>
          </button>
        </a>
        <button class="sidebar-item" onclick="openModal('health-modal')">
          <span class="sidebar-icon">🩺</span><span>Santé IA</span>
        </button>
        <a href="index.php?ctrl=user&action=searchUsers">
          <button class="sidebar-item <?= $page === 'search' ? 'active' : '' ?>">
            <span class="sidebar-icon">🔍</span><span>Rechercher</span>
          </button>
        </a>
        <div class="sidebar-section-label">Configuration</div>
        <a href="index.php?ctrl=user&action=settings">
          <button class="sidebar-item <?= $page === 'settings' ? 'active' : '' ?>">
            <span class="sidebar-icon">⚙️</span><span>Paramètres</span>
          </button>
        </a>
        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
        <a href="index.php?page=roles">
          <button class="sidebar-item <?= $page === 'roles' ? 'active' : '' ?>">
            <span class="sidebar-icon">🔐</span><span>Rôles & Permissions</span>
          </button>
        </a>
        <?php endif; ?>
      </nav>
      <div class="sidebar-footer">
        <div class="sidebar-user">
          <div class="sidebar-avatar" style="<?php if(!empty($currentUser['profile_picture'])): ?>background: url('<?= htmlspecialchars($currentUser['profile_picture']) ?>') center/cover; color: transparent;<?php endif; ?>">
            <?php if(empty($currentUser['profile_picture'])): ?><?= htmlspecialchars($currentUser['initials'] ?? 'AD') ?><?php endif; ?>
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
