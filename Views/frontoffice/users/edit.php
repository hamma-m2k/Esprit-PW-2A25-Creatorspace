<?php $pageTitle = 'Modifier utilisateur'; $pageSubtitle = htmlspecialchars($user['email'] ?? ''); ?>
<div class="card">
  <form method="post" action="<?= BASE_URL ?>/users/update/<?= (int)$user['id'] ?>">
    <input type="hidden" name="csrf" value="<?= Csrf::token() ?>">
    <label>Prénom <input name="firstname" value="<?= htmlspecialchars($user['firstname'] ?? '') ?>" required></label>
    <label>Nom <input name="lastname" value="<?= htmlspecialchars($user['lastname'] ?? '') ?>" required></label>
    <label>Email <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required></label>
    <label>Rôle
      <select name="role_id">
        <?php foreach (($roles ?? []) as $r): ?>
          <option value="<?= (int)$r['id'] ?>" <?= ($user['role_id'] ?? 0) == $r['id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Statut
      <select name="status">
        <option value="active"   <?= ($user['status'] ?? '') === 'active'   ? 'selected' : '' ?>>Actif</option>
        <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactif</option>
      </select>
    </label>
    <?php foreach (($errors ?? []) as $e): ?><p class="error"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
    <button class="btn btn-primary" type="submit">Enregistrer</button>
  </form>
</div>
