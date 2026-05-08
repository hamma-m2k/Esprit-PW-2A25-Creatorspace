<?php $pageTitle = 'Utilisateurs'; $pageSubtitle = 'Gestion des comptes'; ?>
<div class="card">
  <a class="btn btn-primary" href="<?= BASE_URL ?>/users/create">＋ Nouvel utilisateur</a>
  <table class="data-table" style="margin-top:16px;">
    <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach (($users ?? []) as $u): ?>
      <tr>
        <td><?= htmlspecialchars(($u['firstname'] ?? '') . ' ' . ($u['lastname'] ?? '')) ?></td>
        <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
        <td><?= htmlspecialchars($u['role_name'] ?? '—') ?></td>
        <td><?= htmlspecialchars($u['status'] ?? '') ?></td>
        <td>
          <a class="btn btn-outline" href="<?= BASE_URL ?>/users/edit/<?= (int)$u['id'] ?>">Éditer</a>
          <form method="post" action="<?= BASE_URL ?>/users/delete/<?= (int)$u['id'] ?>" style="display:inline;" onsubmit="return confirm('Supprimer ?');">
            <input type="hidden" name="csrf" value="<?= Csrf::token() ?>">
            <button class="btn btn-danger">Supprimer</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
