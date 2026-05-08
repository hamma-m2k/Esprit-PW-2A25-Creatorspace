<?php $pageTitle = 'Historique'; $pageSubtitle = 'Journal des actions'; ?>
<div class="card">
  <table class="data-table">
    <thead><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Détails</th></tr></thead>
    <tbody>
    <?php foreach (($logs ?? []) as $l): ?>
      <tr>
        <td><?= htmlspecialchars($l['created_at'] ?? '') ?></td>
        <td><?= htmlspecialchars(($l['firstname'] ?? '') . ' ' . ($l['lastname'] ?? '')) ?></td>
        <td><?= htmlspecialchars($l['action'] ?? '') ?></td>
        <td><?= htmlspecialchars($l['details'] ?? '') ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
