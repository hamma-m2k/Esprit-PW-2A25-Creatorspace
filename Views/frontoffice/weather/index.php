<div class="card">
  <h3>Météo — <?= htmlspecialchars($weather['location'] ?? '—') ?></h3>
  <?php if (!empty($weather['error'])): ?>
    <p class="error"><?= htmlspecialchars($weather['error']) ?></p>
  <?php else: ?>
    <ul class="weather-list">
      <li><strong>Température :</strong> <?= htmlspecialchars((string)($weather['temperature'] ?? '—')) ?> °C</li>
      <li><strong>Humidité :</strong>    <?= htmlspecialchars((string)($weather['humidity'] ?? '—')) ?> %</li>
      <li><strong>Vent :</strong>        <?= htmlspecialchars((string)($weather['wind'] ?? '—')) ?> km/h</li>
      <li><strong>Mesure :</strong>      <?= htmlspecialchars((string)($weather['time'] ?? '—')) ?></li>
    </ul>
  <?php endif; ?>
  <p><small>Source : Open-Meteo (API externe gratuite)</small></p>
</div>
