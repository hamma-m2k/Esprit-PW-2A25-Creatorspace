<?php $pageTitle = 'Demandes'; $pageSubtitle = 'Inscriptions en attente'; ?>
<div class="card">
  <?php if (!empty($success)): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
  <table class="data-table">
    <thead><tr><th>Nom</th><th>Email</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach (($requests ?? []) as $r): ?>
      <tr>
        <td><?= htmlspecialchars(($r['firstname'] ?? '') . ' ' . ($r['lastname'] ?? '')) ?></td>
        <td><?= htmlspecialchars($r['email'] ?? '') ?></td>
        <td><?= htmlspecialchars($r['status'] ?? '') ?></td>
        <td><?= htmlspecialchars($r['created_at'] ?? '') ?></td>
        <td><a class="btn btn-outline" href="<?= BASE_URL ?>/requests/view/<?= (int)$r['id'] ?>">Voir</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
