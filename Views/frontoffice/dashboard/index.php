<?php $pageTitle = 'Dashboard'; $pageSubtitle = 'Vue d\'ensemble'; ?>
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon purple">◧</div>
    <div>
      <div class="stat-value"><?= $stats['total_users'] ?></div>
      <div class="stat-label">Utilisateurs</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">◆</div>
    <div>
      <div class="stat-value"><?= $stats['active_users'] ?></div>
      <div class="stat-label">Actifs</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon amber">◈</div>
    <div>
      <div class="stat-value"><?= $stats['pending_requests'] ?></div>
      <div class="stat-label">Demandes en attente</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon blue">◷</div>
    <div>
      <div class="stat-value"><?= $stats['total_requests'] ?></div>
      <div class="stat-label">Total demandes</div>
    </div>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <div class="card-header">
      <div class="card-title"><span class="icon">◷</span> Activité récente</div>
    </div>
    <div class="timeline">
      <?php if (empty($recent_logs)): ?>
        <div class="empty-state"><div class="empty-icon">◷</div><h3>Aucune activité</h3></div>
      <?php else: ?>
        <?php foreach ($recent_logs as $log): ?>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <div class="timeline-action"><?= htmlspecialchars($log['action']) ?></div>
            <div class="timeline-meta">
              <?= htmlspecialchars($log['firstname'] ?? 'Système') ?>
              · <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div class="card-title"><span class="icon">◈</span> Dernières demandes</div>
      <a href="<?= BASE_URL ?>/requests" class="btn btn-outline btn-sm">Voir tout</a>
    </div>
    <?php if (empty($recent_requests)): ?>
      <div class="empty-state"><div class="empty-icon">◈</div><h3>Aucune demande</h3></div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Nom</th><th>Email</th><th>Statut</th></tr></thead>
        <tbody>
          <?php foreach ($recent_requests as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['firstname'] . ' ' . $r['lastname']) ?></td>
            <td style="color:var(--text-muted);font-size:13px;"><?= htmlspecialchars($r['email']) ?></td>
            <td>
              <?php $sc = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'][$r['status']] ?? 'info'; ?>
              <span class="badge badge-<?= $sc ?>"><?= ucfirst($r['status']) ?></span>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<div class="grid-2">
  <div class="card" style="border-color:var(--accent-glow);">
    <div class="card-header">
      <div class="card-title"><span class="icon">◧</span> Raccourcis Contrats</div>
    </div>
    <div style="display:flex;flex-direction:column;gap:10px;">
      <a href="<?= BASE_URL ?>/contrats" class="btn btn-outline">◧ Voir tous les contrats</a>
      <a href="<?= BASE_URL ?>/contrats/create" class="btn btn-primary">＋ Nouveau contrat</a>
    </div>
  </div>
  <div class="card" style="border-color:var(--accent-glow);">
    <div class="card-header">
      <div class="card-title"><span class="icon">◆</span> Raccourcis Rules</div>
    </div>
    <div style="display:flex;flex-direction:column;gap:10px;">
      <a href="<?= BASE_URL ?>/rules" class="btn btn-outline">◆ Voir toutes les rules</a>
      <a href="<?= BASE_URL ?>/rules/add" class="btn btn-primary">＋ Ajouter des rules</a>
    </div>
  </div>
</div>

<!-- ═══ Météo (API externe Open-Meteo) ═══ -->
<?php if (!empty($weather) && empty($weather['error'])): ?>
<div class="card" style="margin-top:24px;">
  <h3>Météo · <?= htmlspecialchars($weather['location']) ?></h3>
  <p>
    <strong><?= htmlspecialchars((string)$weather['temperature']) ?> °C</strong>
    · Humidité <?= htmlspecialchars((string)$weather['humidity']) ?>%
    · Vent <?= htmlspecialchars((string)$weather['wind']) ?> km/h
  </p>
</div>
<?php endif; ?>

<!-- ═══ Graphique Chart.js ═══ -->
<div class="card" style="margin-top:24px;">
  <h3>Contrats par mois</h3>
  <canvas id="contratsChart" height="100"></canvas>
  <button id="exportChart" class="btn btn-outline" style="margin-top:12px;">Exporter PNG</button>
</div>
<script>
fetch('<?= BASE_URL ?>/dashboard/chart')
  .then(r => r.json())
  .then(d => {
    const ctx = document.getElementById('contratsChart');
    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: d.labels,
        datasets: [{ label: 'Contrats', data: d.data, backgroundColor: '#7c5cff' }]
      },
      options: { responsive: true, plugins: { legend: { display: false } } }
    });
    document.getElementById('exportChart').onclick = () => {
      const a = document.createElement('a');
      a.href = chart.toBase64Image();
      a.download = 'contrats.png';
      a.click();
    };
  });
</script>
