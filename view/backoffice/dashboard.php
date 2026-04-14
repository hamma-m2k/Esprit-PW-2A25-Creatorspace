<?php require_once __DIR__ . '/layout_back.php'; ?>

<div class="back-section active" id="back-dashboard">

  <!-- PAGE HEADER -->
  <div class="back-header">
    <div>
      <h2 style="font-size:1.6rem; color:var(--text);">⚙️ Tableau de bord</h2>
      <p style="color:var(--text3); font-size:0.9rem; margin-top:4px;">
        Bienvenue, <?= htmlspecialchars($_SESSION['nom'] ?? 'Admin') ?> — Vue d'ensemble
      </p>
    </div>
  </div>

  <!-- STAT CARDS -->
  <div style="display:flex; gap:24px; margin-bottom:32px; flex-wrap:wrap;">

    <div style="flex:1; min-width:220px;
                background:rgba(255,255,255,0.06);
                border:1px solid rgba(108,63,197,0.4);
                border-radius:16px; padding:2rem;
                box-shadow:0 8px 32px rgba(108,63,197,0.2);">
      <div style="font-size:3rem; font-weight:800; color:#00C2CB; font-family:'Syne',sans-serif;">
        <?= $totalUsers ?>
      </div>
      <div style="color:var(--text2); font-size:0.95rem; margin-top:8px;">
        👥 Total Utilisateurs
      </div>
    </div>

    <div style="flex:1; min-width:220px;
                background:rgba(255,255,255,0.06);
                border:1px solid rgba(108,63,197,0.4);
                border-radius:16px; padding:2rem;
                box-shadow:0 8px 32px rgba(108,63,197,0.2);">
      <div style="font-size:3rem; font-weight:800; color:#9B5DE5; font-family:'Syne',sans-serif;">
        <?= $totalAdmins ?>
      </div>
      <div style="color:var(--text2); font-size:0.95rem; margin-top:8px;">
        🔐 Administrateurs
      </div>
    </div>

  </div>

  <!-- QUICK ACTIONS -->
  <div style="background:rgba(255,255,255,0.04);
              border:1px solid var(--border);
              border-radius:var(--radius); padding:28px;">
    <h3 style="color:var(--text); font-size:1rem; margin-bottom:20px;">Accès rapides</h3>
    <div style="display:flex; gap:12px; flex-wrap:wrap;">
      <a href="index.php?ctrl=user&action=index">
        <button class="btn btn-primary">👥 Gérer les utilisateurs</button>
      </a>
      <a href="index.php?ctrl=user&action=create">
        <button class="btn btn-accent">➕ Ajouter un utilisateur</button>
      </a>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
