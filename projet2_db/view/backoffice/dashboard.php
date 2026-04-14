<?php include __DIR__ . '/layout/header.php'; ?>

<div class="page-header">
  <div>
    <div class="page-title">Tableau de bord</div>
    <div class="page-subtitle">Bienvenue, <?= htmlspecialchars($_SESSION['nom'] . ' ' . $_SESSION['prenom']) ?></div>
  </div>
</div>

<!-- STAT CARDS -->
<div style="display:flex; gap:20px; margin-bottom:28px;">

  <div class="card" style="flex:1; border-top:4px solid #e8394d; text-align:center;">
    <div style="font-size:36px; font-weight:800; color:#1a1a2e;"><?= $totalUsers ?></div>
    <div style="color:#888; font-size:13px; margin-top:6px;">Total Utilisateurs</div>
  </div>

  <div class="card" style="flex:1; border-top:4px solid #3d5afe; text-align:center;">
    <div style="font-size:36px; font-weight:800; color:#1a1a2e;"><?= $totalAdmins ?></div>
    <div style="color:#888; font-size:13px; margin-top:6px;">Administrateurs</div>
  </div>

  <div class="card" style="flex:1; border-top:4px solid #00c853; text-align:center;">
    <div style="font-size:36px; font-weight:800; color:#1a1a2e;"><?= $totalSimple ?></div>
    <div style="color:#888; font-size:13px; margin-top:6px;">Utilisateurs simples</div>
  </div>

</div>

<!-- ACTION BUTTONS -->
<div style="display:flex; gap:12px;">
  <a href="index.php?action=list"   class="btn btn-dark">👥 Gérer les utilisateurs</a>
  <a href="index.php?action=create" class="btn btn-primary">➕ Ajouter un utilisateur</a>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
