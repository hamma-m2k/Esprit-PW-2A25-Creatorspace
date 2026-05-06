<?php $pageTitle = 'Créer un utilisateur'; $pageSubtitle = ''; ?>
<div class="card">
  <form method="post" action="<?= BASE_URL ?>/users/store">
    <input type="hidden" name="csrf" value="<?= Csrf::token() ?>">
    <label>Prénom <input name="firstname" value="<?= htmlspecialchars($old['firstname'] ?? '') ?>" required></label>
    <label>Nom <input name="lastname" value="<?= htmlspecialchars($old['lastname'] ?? '') ?>" required></label>
    <label>Email <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required></label>
    <label>Mot de passe <input type="password" name="password" required></label>
    <label>Rôle
      <select name="role_id">
        <?php foreach (($roles ?? []) as $r): ?>
          <option value="<?= (int)$r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Statut
      <select name="status"><option value="active">Actif</option><option value="inactive">Inactif</option></select>
    </label>
    <?php foreach (($errors ?? []) as $e): ?><p class="error"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
    <button class="btn btn-primary" type="submit">Créer</button>
  </form>
</div>
