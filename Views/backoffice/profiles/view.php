<?php $pageTitle = 'Profil'; $pageSubtitle = htmlspecialchars(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')); ?>
<div class="card">
  <h3><?= htmlspecialchars(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')) ?></h3>
  <ul>
    <li><strong>Email :</strong> <?= htmlspecialchars($user['email'] ?? '') ?></li>
    <li><strong>Rôle :</strong> <?= htmlspecialchars($user['role_name'] ?? '—') ?></li>
    <li><strong>Statut :</strong> <?= htmlspecialchars($user['status'] ?? '—') ?></li>
    <li><strong>Inscrit le :</strong> <?= htmlspecialchars($user['created_at'] ?? '—') ?></li>
  </ul>
</div>

<div class="card" style="margin-top:24px;">
  <h3>Historique récent</h3>
  <ul>
    <?php foreach (($logs ?? []) as $l): ?>
      <li><?= htmlspecialchars($l['created_at'] ?? '') ?> — <?= htmlspecialchars($l['action'] ?? '') ?></li>
    <?php endforeach; ?>
  </ul>
</div>
