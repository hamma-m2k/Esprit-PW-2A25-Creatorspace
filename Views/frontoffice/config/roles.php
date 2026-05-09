<?php $pageTitle = 'Rôles & Permissions'; $pageSubtitle = ''; ?>
<div class="card">
  <?php if (!empty($success)): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
  <h3>Créer un rôle</h3>
  <form method="post" action="<?= BASE_URL ?>/config/roles/create">
    <input type="hidden" name="csrf" value="<?= Csrf::token() ?>">
    <input name="name" placeholder="Nom du rôle" required>
    <input name="description" placeholder="Description">
    <button class="btn btn-primary">Ajouter</button>
  </form>
</div>

<div class="card" style="margin-top:24px;">
  <h3>Rôles existants</h3>
  <table class="data-table">
    <thead><tr><th>ID</th><th>Nom</th><th>Description</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach (($roles ?? []) as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><?= htmlspecialchars($r['description'] ?? '') ?></td>
        <td>
          <form method="post" action="<?= BASE_URL ?>/config/roles/delete/<?= (int)$r['id'] ?>" onsubmit="return confirm('Supprimer ?');">
            <input type="hidden" name="csrf" value="<?= Csrf::token() ?>">
            <button class="btn btn-danger">Supprimer</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
