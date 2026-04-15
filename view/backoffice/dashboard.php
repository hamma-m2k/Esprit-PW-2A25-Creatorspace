<?php require_once __DIR__ . '/layout_back.php'; ?>

<div class="back-section active" id="back-dashboard">

  <!-- PAGE HEADER -->
  <div class="back-header">
    <div>
      <h2 style="font-size:1.6rem; color:var(--text);">📊 Dashboard</h2>
      <p style="color:var(--text3); font-size:0.9rem; margin-top:4px;">
        Bienvenue, <?= htmlspecialchars($_SESSION['nom'] ?? 'Admin') ?> 👋
      </p>
    </div>
  </div>

  <!-- 4 STAT CARDS -->
  <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:32px;">

    <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3);
                border-radius:16px; padding:1.5rem; text-align:center;">
      <div style="font-size:2.5rem; font-weight:700; color:#a855f7; font-family:'Syne',sans-serif;">
        <?= $stats['total'] ?>
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-top:0.5rem;">👥 Total utilisateurs</div>
    </div>

    <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3);
                border-radius:16px; padding:1.5rem; text-align:center;">
      <div style="font-size:2.5rem; font-weight:700; color:#00C2CB; font-family:'Syne',sans-serif;">
        <?= $stats['users'] ?>
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-top:0.5rem;">👤 Utilisateurs</div>
    </div>

    <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3);
                border-radius:16px; padding:1.5rem; text-align:center;">
      <div style="font-size:2.5rem; font-weight:700; color:#9B5DE5; font-family:'Syne',sans-serif;">
        <?= $stats['admins'] ?>
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-top:0.5rem;">🔐 Administrateurs</div>
    </div>

    <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3);
                border-radius:16px; padding:1.5rem; text-align:center;">
      <div style="font-size:2.5rem; font-weight:700; color:#38a169; font-family:'Syne',sans-serif;">
        <?= $stats['new_month'] ?>
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-top:0.5rem;">🆕 Nouveaux ce mois</div>
    </div>

  </div>

  <!-- QUICK ACTIONS -->
  <div style="background:rgba(255,255,255,0.04); border:1px solid var(--border);
              border-radius:var(--radius); padding:24px; margin-bottom:28px;">
    <h3 style="color:var(--text); font-size:1rem; margin-bottom:16px;">Accès rapides</h3>
    <div style="display:flex; gap:12px; flex-wrap:wrap;">
      <a href="index.php?ctrl=user&action=index">
        <button class="btn btn-primary">👥 Gérer les utilisateurs</button>
      </a>
      <a href="index.php?ctrl=user&action=profile">
        <button class="btn btn-outline btn-sm">👤 Mon profil</button>
      </a>
    </div>
  </div>

  <!-- LAST 5 USERS TABLE -->
  <div class="table-card">
    <div style="padding:20px 24px 12px; border-bottom:1px solid var(--border);">
      <h3 style="color:var(--text); font-size:1rem;">🕐 Dernières inscriptions</h3>
    </div>
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Rôle</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($lastUsers)): ?>
          <tr>
            <td colspan="4" style="text-align:center; color:var(--text3); padding:20px;">
              Aucun utilisateur.
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($lastUsers as $u): ?>
          <tr>
            <td style="font-weight:600; color:var(--text);"><?= htmlspecialchars($u['nom']) ?></td>
            <td style="color:var(--text2);"><?= htmlspecialchars($u['prenom']) ?></td>
            <td style="color:var(--text2);"><?= htmlspecialchars($u['mail']) ?></td>
            <td>
              <?php if ($u['role'] === 'admin'): ?>
                <span class="badge badge-pro">Admin</span>
              <?php else: ?>
                <span class="badge badge-verified">User</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
