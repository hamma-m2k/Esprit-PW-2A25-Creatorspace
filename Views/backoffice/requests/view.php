<?php $pageTitle = 'Demande'; $pageSubtitle = htmlspecialchars($request['email'] ?? ''); ?>
<div class="card">
  <h3><?= htmlspecialchars(($request['firstname'] ?? '') . ' ' . ($request['lastname'] ?? '')) ?></h3>
  <ul>
    <li><strong>Email :</strong> <?= htmlspecialchars($request['email'] ?? '') ?></li>
    <li><strong>Statut :</strong> <?= htmlspecialchars($request['status'] ?? '') ?></li>
    <li><strong>Reçue le :</strong> <?= htmlspecialchars($request['created_at'] ?? '') ?></li>
    <li><strong>Message :</strong><br><?= nl2br(htmlspecialchars($request['message'] ?? '')) ?></li>
  </ul>
  <?php if (($request['status'] ?? '') === 'pending'): ?>
    <form method="post" action="<?= BASE_URL ?>/requests/approve/<?= (int)$request['id'] ?>" style="display:inline;">
      <input type="hidden" name="csrf" value="<?= Csrf::token() ?>">
      <button class="btn btn-primary">Approuver</button>
    </form>
    <form method="post" action="<?= BASE_URL ?>/requests/reject/<?= (int)$request['id'] ?>" style="display:inline;">
      <input type="hidden" name="csrf" value="<?= Csrf::token() ?>">
      <button class="btn btn-danger">Rejeter</button>
    </form>
  <?php endif; ?>
</div>
