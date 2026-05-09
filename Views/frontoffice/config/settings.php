<?php $pageTitle = 'Paramètres'; $pageSubtitle = ''; ?>
<div class="card">
  <?php if (!empty($success)): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
  <form method="post" action="<?= BASE_URL ?>/config/settings/update">
    <input type="hidden" name="csrf" value="<?= Csrf::token() ?>">
    <label>Nom de l'application <input name="app_name" value="<?= htmlspecialchars(APP_NAME) ?>"></label>
    <label>Ville météo <input name="weather_city" value="<?= htmlspecialchars(WEATHER_CITY) ?>"></label>
    <button class="btn btn-primary">Enregistrer</button>
  </form>
  <p><small>Note : ces paramètres sont actuellement définis dans <code>index.php</code>.</small></p>
</div>
