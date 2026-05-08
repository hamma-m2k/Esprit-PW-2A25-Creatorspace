<?php $pageTitle = 'Profils'; $pageSubtitle = 'Liste des utilisateurs'; ?>
<div class="card">
  <table class="data-table">
    <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach (($users ?? []) as $u): ?>
      <tr>
        <td><?= htmlspecialchars(($u['firstname'] ?? '') . ' ' . ($u['lastname'] ?? '')) ?></td>
        <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
        <td><?= htmlspecialchars($u['role_name'] ?? '—') ?></td>
        <td><a class="btn btn-outline" href="<?= BASE_URL ?>/profiles/view/<?= (int)$u['id'] ?>">Voir</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
