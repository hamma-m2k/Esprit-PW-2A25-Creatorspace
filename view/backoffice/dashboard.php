<?php require_once __DIR__ . '/layout_back.php'; ?>

<div class="back-section active" id="back-dashboard">

  <!-- PAGE HEADER -->
  <div class="back-header">
    <div>
      <h2 style="font-size:1.6rem; color:var(--text);">📊 Dashboard</h2>
      <p style="color:var(--text3); font-size:0.9rem; margin-top:4px;">
        Bienvenue, <?= htmlspecialchars($nomAdmin ?? 'Admin') ?> 👋
      </p>
    </div>
  </div>

  <!-- 4 STAT CARDS — ligne 1 -->
  <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1rem;">

    <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3);
                border-radius:16px; padding:1.5rem; text-align:center;">
      <div class="animate-number" data-target="<?= $stats['total'] ?>" style="font-size:2.5rem; font-weight:700; color:#a855f7; font-family:'Syne',sans-serif;">
        0
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-top:0.5rem;">👥 Total utilisateurs</div>
    </div>

    <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3);
                border-radius:16px; padding:1.5rem; text-align:center;">
      <div class="animate-number" data-target="<?= $stats['users'] ?>" style="font-size:2.5rem; font-weight:700; color:#00C2CB; font-family:'Syne',sans-serif;">
        0
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-top:0.5rem;">👤 Utilisateurs</div>
    </div>

    <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3);
                border-radius:16px; padding:1.5rem; text-align:center;">
      <div class="animate-number" data-target="<?= $stats['admins'] ?>" style="font-size:2.5rem; font-weight:700; color:#9B5DE5; font-family:'Syne',sans-serif;">
        0
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-top:0.5rem;">🔐 Administrateurs</div>
    </div>

    <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3);
                border-radius:16px; padding:1.5rem; text-align:center;">
      <div class="animate-number" data-target="<?= $stats['new_month'] ?>" style="font-size:2.5rem; font-weight:700; color:#38a169; font-family:'Syne',sans-serif;">
        0
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-top:0.5rem;">🆕 Nouveaux ce mois</div>
    </div>

  </div>

  <!-- STAT CARDS — ligne 2 : par type de compte -->
  <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:32px;">

    <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(14,116,144,0.4);
                border-radius:16px; padding:1.5rem; text-align:center;">
      <div class="animate-number" data-target="<?= $stats['createurs'] ?>" style="font-size:2.5rem; font-weight:700; color:#0e7490; font-family:'Syne',sans-serif;">
        0
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-top:0.5rem;">🎨 Créateurs de contenu</div>
    </div>

    <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(124,58,237,0.4);
                border-radius:16px; padding:1.5rem; text-align:center;">
      <div class="animate-number" data-target="<?= $stats['societes'] ?>" style="font-size:2.5rem; font-weight:700; color:#7c3aed; font-family:'Syne',sans-serif;">
        0
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-top:0.5rem;">🏢 Sociétés</div>
    </div>

    <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(16,185,129,0.4);
                border-radius:16px; padding:1.5rem; text-align:center;">
      <div class="animate-number" data-target="<?= $stats['normaux'] ?>" style="font-size:2.5rem; font-weight:700; color:#10b981; font-family:'Syne',sans-serif;">
        0
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-top:0.5rem;">👤 Utilisateurs normaux</div>
    </div>

    <a href="index.php?ctrl=demande&action=liste" style="text-decoration:none;">
      <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(229,62,62,0.4);
                  border-radius:16px; padding:1.5rem; text-align:center;
                  transition:transform 0.2s; cursor:pointer;"
           onmouseover="this.style.transform='translateY(-3px)'"
           onmouseout="this.style.transform='translateY(0)'">
        <div class="animate-number" data-target="<?= $stats['demandes_attente'] ?>" style="font-size:2.5rem; font-weight:700; color:var(--danger); font-family:'Syne',sans-serif;">
          0
        </div>
        <div style="font-size:0.9rem; color:#94a3b8; margin-top:0.5rem;">📋 Demandes en attente</div>
        <div style="font-size:0.75rem; color:var(--danger); margin-top:6px; font-weight:600;">
          Voir les demandes →
        </div>
      </div>
    </a>

  </div>

  <!-- QUICK ACTIONS -->
  <div style="background:rgba(255,255,255,0.04); border:1px solid var(--border);
              border-radius:var(--radius); padding:24px; margin-bottom:28px;">
    <h3 style="color:var(--text); font-size:1rem; margin-bottom:16px;">Accès rapides</h3>
    <div style="display:flex; gap:12px; flex-wrap:wrap;">
      <a href="index.php?ctrl=user&action=index">
        <button class="btn btn-primary">👥 Gérer les utilisateurs</button>
      </a>
      <a href="index.php?ctrl=demande&action=liste">
        <button class="btn btn-outline btn-sm">📋 Voir les demandes →</button>
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
            <th>Type</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($lastUsers)): ?>
          <tr>
            <td colspan="5" style="text-align:center; color:var(--text3); padding:20px;">
              Aucun utilisateur.
            </td>
          </tr>
          <?php else: ?>
          <?php
          $labels = [
              'societe'  => ['label' => 'Société',             'color' => '#7c3aed'],
              'createur' => ['label' => 'Créateur de contenu', 'color' => '#0e7490'],
              'user'     => ['label' => 'Utilisateur',         'color' => '#10b981'],
              'admin'    => ['label' => 'Admin',               'color' => '#dc2626'],
          ];
          ?>
          <?php foreach ($lastUsers as $u): ?>
          <?php $t = $labels[$u->getTypeCompte() ?: 'user'] ?? $labels['user']; ?>
          <tr>
            <td style="font-weight:600; color:var(--text);"><?= htmlspecialchars($u->getNom()) ?></td>
            <td style="color:var(--text2);"><?= htmlspecialchars($u->getPrenom()) ?></td>
            <td style="color:var(--text2);"><?= htmlspecialchars($u->getMail()) ?></td>
            <td>
              <?php if ($u->getRole() === 'admin'): ?>
                <span class="badge badge-pro">Admin</span>
              <?php else: ?>
                <span class="badge badge-verified">User</span>
              <?php endif; ?>
            </td>
            <td>
              <span style="background:rgba(255,255,255,0.08); color:<?= $t['color'] ?>;
                           border:1px solid <?= $t['color'] ?>40;
                           border-radius:20px; padding:3px 10px; font-size:0.75rem; font-weight:600;">
                <?= htmlspecialchars($t['label']) ?>
              </span>
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
