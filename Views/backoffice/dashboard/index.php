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

<!-- Charger Chart.js depuis CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- ══════ LIGNE 1 : KPI AVANCÉS ══════ -->
<div style="
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 28px;
">

  <!-- Taux de complétion -->
  <div style="
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 24px;
    display: flex; align-items: center; gap: 16px;
  ">
    <div style="
      width: 56px; height: 56px;
      border-radius: 50%;
      background: conic-gradient(
        #7c6fef <?= $tauxCompletion ?>%, 
        rgba(124,111,239,0.1) <?= $tauxCompletion ?>%
      );
      display: flex; align-items: center; justify-content: center;
      position: relative;
    ">
      <div style="
        width: 40px; height: 40px;
        background: var(--bg-card);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700;
        color: var(--accent-light);
      "><?= $tauxCompletion ?>%</div>
    </div>
    <div>
      <div style="font-size:22px;font-weight:700;"> <?= $tauxCompletion ?>% </div>
      <div style="font-size:12px;color:var(--text-muted);">Taux de complétion</div>
      <div style="font-size:11px;color:var(--text-dim);margin-top:2px;">
        <?= $contratsAvecRules ?>/<?= $totalContrats ?> contrats avec rules
      </div>
    </div>
  </div>

  <!-- CDI -->
  <div style="
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-left: 3px solid #60a5fa;
    border-radius: 14px; padding: 24px;
  ">
    <?php $cdi = array_sum(array_column(
      array_filter($repartitionType, fn($r) => $r['type'] === 'CDI'), 
      'total'
    )); ?>
    <div style="font-size:32px;font-weight:700;color:#60a5fa;"><?= $cdi ?></div>
    <div style="font-size:13px;color:var(--text-muted);margin-top:4px;">CDI</div>
    <div style="
      margin-top:10px; height:4px;
      background:rgba(96,165,250,0.15);
      border-radius:2px; overflow:hidden;
    ">
      <div style="
        height:100%; width:<?= $totalContrats > 0 ? round($cdi / $totalContrats * 100) : 0 ?>%;
        background:#60a5fa; border-radius:2px;
      "></div>
    </div>
  </div>

  <!-- CDD -->
  <div style="
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-left: 3px solid #f5a623;
    border-radius: 14px; padding: 24px;
  ">
    <?php $cdd = array_sum(array_column(
      array_filter($repartitionType, fn($r) => $r['type'] === 'CDD'), 
      'total'
    )); ?>
    <div style="font-size:32px;font-weight:700;color:#f5a623;"> <?= $cdd ?> </div>
    <div style="font-size:13px;color:var(--text-muted);margin-top:4px;">CDD</div>
    <div style="
      margin-top:10px; height:4px;
      background:rgba(245,166,35,0.15);
      border-radius:2px; overflow:hidden;
    ">
      <div style="
        height:100%; width:<?= $totalContrats > 0 ? round($cdd / $totalContrats * 100) : 0 ?>%;
        background:#f5a623; border-radius:2px;
      "></div>
    </div>
  </div>

  <!-- CDIV -->
  <div style="
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-left: 3px solid #a99df5;
    border-radius: 14px; padding: 24px;
  ">
    <?php $cdiv = array_sum(array_column(
      array_filter($repartitionType, fn($r) => $r['type'] === 'CDIV'), 
      'total'
    )); ?>
    <div style="font-size:32px;font-weight:700;color:#a99df5;"> <?= $cdiv ?> </div>
    <div style="font-size:13px;color:var(--text-muted);margin-top:4px;">CDIV</div>
    <div style="
      margin-top:10px; height:4px;
      background:rgba(124,111,239,0.15);
      border-radius:2px; overflow:hidden;
    ">
      <div style="
        height:100%; width:<?= $totalContrats > 0 ? round($cdiv / $totalContrats * 100) : 0 ?>%;
        background:#a99df5; border-radius:2px;
      "></div>
    </div>
  </div>

</div>

<!-- ══════ LIGNE 2 : GRAPHIQUES PRINCIPAUX ══════ -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:28px;">

  <!-- Graphique barres - Contrats par mois -->
  <div style="
    background:var(--bg-card);
    border:1px solid var(--border);
    border-radius:14px; padding:24px;
  ">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
      <div>
        <div style="font-size:16px;font-weight:600;">Contrats par mois</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
          12 derniers mois — CDI / CDD / CDIV
        </div>
      </div>
      <button onclick="exportChart('barChart','contrats-mois')" style="
        padding:6px 14px;
        background:transparent;
        border:1px solid var(--border);
        border-radius:8px; color:var(--text-muted);
        font-size:12px; cursor:pointer;
        transition:0.2s;
      " onmouseover="this.style.borderColor='#7c6fef';this.style.color='#a99df5'"
         onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)'">
        ⬇ PNG
      </button>
    </div>
    <canvas id="barChart" height="120"></canvas>
  </div>

  <!-- Graphique donut - Répartition -->
  <div style="
    background:var(--bg-card);
    border:1px solid var(--border);
    border-radius:14px; padding:24px;
  ">
    <div style="font-size:16px;font-weight:600;margin-bottom:4px;">Répartition</div>
    <div style="font-size:12px;color:var(--text-muted);margin-bottom:20px;">
      Par type de contrat
    </div>
    <canvas id="donutChart" height="160"></canvas>
    <!-- Légende -->
    <div style="display:flex;flex-direction:column;gap:8px;margin-top:20px;">
      <?php foreach($repartitionType as $r): ?>
      <?php $colors = ['CDI'=>'#60a5fa','CDD'=>'#f5a623','CDIV'=>'#a99df5']; ?>
      <div style="display:flex;align-items:center;gap:10px;font-size:13px;">
        <div style="
          width:10px;height:10px;border-radius:50%;
          background:<?= $colors[$r['type']] ?? '#7c6fef' ?>;
          flex-shrink:0;
        "></div>
        <span style="flex:1;color:var(--text-muted);"><?= htmlspecialchars($r['type']) ?></span>
        <span style="font-weight:600;"><?= $r['total'] ?></span>
        <span style="color:var(--text-dim);font-size:11px;">
          (<?= $totalContrats > 0 ? round($r['total'] / $totalContrats * 100) : 0 ?>%)
        </span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<!-- ══════ LIGNE 3 : STATUT + TOP CONTRATS ══════ -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px;">

  <!-- Graphique statuts -->
  <div style="
    background:var(--bg-card);
    border:1px solid var(--border);
    border-radius:14px; padding:24px;
  ">
    <div style="font-size:16px;font-weight:600;margin-bottom:4px;">Statuts des contrats</div>
    <div style="font-size:12px;color:var(--text-muted);margin-bottom:20px;">
      Brouillon / Actif / Archivé
    </div>
    <?php
    $statutColors = [
      'brouillon' => ['bg'=>'rgba(245,166,35,0.12)','color'=>'#f5a623','pct'=>0],
      'actif'     => ['bg'=>'rgba(34,211,160,0.12)', 'color'=>'#22d3a0','pct'=>0],
      'archive'   => ['bg'=>'rgba(239,69,101,0.12)', 'color'=>'#ef4565','pct'=>0],
    ];
    foreach($repartitionStatut as $s) {
      $pct = $totalContrats > 0 ? round($s['total'] / $totalContrats * 100) : 0;
      $statutColors[$s['statut']]['pct'] = $pct;
      $statutColors[$s['statut']]['total'] = $s['total'];
    }
    ?>
    <div style="display:flex;flex-direction:column;gap:14px;">
      <?php foreach($statutColors as $label => $sc): ?>
      <div>
        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
          <span style="font-size:13px;color:var(--text-muted);text-transform:capitalize;">
            <?= htmlspecialchars($label) ?>
          </span>
          <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:13px;font-weight:600;">
              <?= $sc['total'] ?? 0 ?>
            </span>
            <span style="
              font-size:11px;
              background:<?= $sc['bg'] ?>;
              color:<?= $sc['color'] ?>;
              padding:2px 8px;border-radius:10px;
            "><?= $sc['pct'] ?>%</span>
          </div>
        </div>
        <div style="
          height:8px;background:rgba(255,255,255,0.05);
          border-radius:4px;overflow:hidden;
        ">
          <div style="
            height:100%;
            width:<?= $sc['pct'] ?>%;
            background:<?= $sc['color'] ?>;
            border-radius:4px;
            transition:width 1s ease;
          "></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Top 5 contrats avec le plus de rules -->
  <div style="
    background:var(--bg-card);
    border:1px solid var(--border);
    border-radius:14px; padding:24px;
  ">
    <div style="font-size:16px;font-weight:600;margin-bottom:4px;">
      Top 5 — Rules par contrat
    </div>
    <div style="font-size:12px;color:var(--text-muted);margin-bottom:20px;">
      Contrats les plus complets
    </div>
    <?php if(empty($topContrats)): ?>
    <div style="text-align:center;color:var(--text-dim);padding:24px;">
      Aucune donnée
    </div>
    <?php else: ?>
    <?php $maxRules = max(array_column($topContrats,'nb_rules')) ?: 1; ?>
    <div style="display:flex;flex-direction:column;gap:14px;">
      <?php foreach($topContrats as $i => $tc): ?>
      <?php $pct = round($tc['nb_rules'] / $maxRules * 100); ?>
      <div>
        <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
          <div style="display:flex;align-items:center;gap:8px;">
            <span style="
              width:20px;height:20px;
              background:var(--accent-dim);
              border-radius:4px;
              display:flex;align-items:center;justify-content:center;
              font-size:10px;font-weight:700;
              color:var(--accent-light);
              flex-shrink:0;
            "><?= $i + 1 ?></span>
            <span style="font-size:13px;font-weight:500;">
              <?= htmlspecialchars(mb_substr($tc['titre'], 0, 28)) ?>
              <?= mb_strlen($tc['titre']) > 28 ? '...' : '' ?>
            </span>
          </div>
          <span style="
            font-size:12px;font-weight:700;
            color:var(--accent-light);
          "><?= $tc['nb_rules'] ?> rules</span>
        </div>
        <div style="
          height:6px;
          background:rgba(124,111,239,0.1);
          border-radius:3px;overflow:hidden;
        ">
          <div style="
            height:100%;width:<?= $pct ?>%;
            background:linear-gradient(90deg,#7c6fef,#a99df5);
            border-radius:3px;
          "></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

</div>

<!-- ══════ JAVASCRIPT CHART.JS ══════ -->
<script>
// Données PHP → JavaScript
const moisLabels = <?= json_encode(array_column($contratParMois, 'mois')) ?>;
const dataCdi    = <?= json_encode(array_column($contratParMois, 'cdi')) ?>;
const dataCdd    = <?= json_encode(array_column($contratParMois, 'cdd')) ?>;
const dataCdiv   = <?= json_encode(array_column($contratParMois, 'cdiv')) ?>;
const dataDonut  = <?= json_encode(array_column($repartitionType, 'total')) ?>;
const donutLabels= <?= json_encode(array_column($repartitionType, 'type')) ?>;

// Config commune
Chart.defaults.color = '#8b89a8';
Chart.defaults.font.family = 'Outfit, sans-serif';

// ── Graphique barres groupées ──
const barCtx = document.getElementById('barChart').getContext('2d');
const barChart = new Chart(barCtx, {
  type: 'bar',
  data: {
    labels: moisLabels.length ? moisLabels : ['Aucune donnée'],
    datasets: [
      {
        label: 'CDI',
        data: dataCdi,
        backgroundColor: 'rgba(96,165,250,0.75)',
        borderColor: '#60a5fa',
        borderWidth: 1.5,
        borderRadius: 6,
      },
      {
        label: 'CDD',
        data: dataCdd,
        backgroundColor: 'rgba(245,166,35,0.75)',
        borderColor: '#f5a623',
        borderWidth: 1.5,
        borderRadius: 6,
      },
      {
        label: 'CDIV',
        data: dataCdiv,
        backgroundColor: 'rgba(169,157,245,0.75)',
        borderColor: '#a99df5',
        borderWidth: 1.5,
        borderRadius: 6,
      }
    ]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'top',
        labels: {
          padding: 16,
          usePointStyle: true,
          pointStyle: 'circle',
        }
      },
      tooltip: {
        backgroundColor: '#13112b',
        borderColor: 'rgba(124,111,239,0.3)',
        borderWidth: 1,
        padding: 12,
        callbacks: {
          label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y} contrat(s)`
        }
      }
    },
    scales: {
      x: {
        grid: { color: 'rgba(255,255,255,0.04)' },
        ticks: { color: '#8b89a8' }
      },
      y: {
        grid: { color: 'rgba(255,255,255,0.04)' },
        ticks: {
          color: '#8b89a8',
          stepSize: 1,
          precision: 0
        },
        beginAtZero: true
      }
    }
  }
});

// ── Graphique donut ──
const donutCtx = document.getElementById('donutChart').getContext('2d');
new Chart(donutCtx, {
  type: 'doughnut',
  data: {
    labels: donutLabels.length ? donutLabels : ['Aucun'],
    datasets: [{
      data: dataDonut.length ? dataDonut : [1],
      backgroundColor: ['#60a5fa','#f5a623','#a99df5'],
      borderColor: '#13112b',
      borderWidth: 3,
      hoverOffset: 8,
    }]
  },
  options: {
    responsive: true,
    cutout: '68%',
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: '#13112b',
        borderColor: 'rgba(124,111,239,0.3)',
        borderWidth: 1,
        padding: 12,
        callbacks: {
          label: ctx => ` ${ctx.label}: ${ctx.parsed} contrat(s)`
        }
      }
    }
  }
});

// ── Export PNG ──
function exportChart(chartId, filename) {
  const canvas = document.getElementById(chartId);
  const link = document.createElement('a');
  link.download = filename + '.png';
  link.href = canvas.toDataURL('image/png');
  link.click();
}
</script>
