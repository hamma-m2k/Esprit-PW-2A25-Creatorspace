<?php require_once __DIR__ . '/layout_back.php'; ?>

<div class="back-section active" id="back-stats">
  <div class="back-header">
    <div>
      <h2 style="font-size:1.6rem; color:var(--text);">📈 Statistiques de la plateforme</h2>
      <p style="color:var(--text3); font-size:0.9rem; margin-top:4px;">
        Évolution des inscriptions pour l'année <?= date('Y') ?>
      </p>
    </div>
  </div>

  <?php
  $moisFr = [
    1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr', 5 => 'Mai', 6 => 'Juin',
    7 => 'Juil', 8 => 'Août', 9 => 'Sept', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'
  ];
  $maxVal = max($inscriptionsStats) ?: 1;
  $maxVal = ceil($maxVal / 5) * 5; // Arrondir au multiple de 5 supérieur pour l'échelle
  ?>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-top: 20px;">
    <!-- GRAPHIQUE 1: COURBE DES INSCRIPTIONS -->
    <div class="table-card" style="padding: 30px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-family: 'Syne', sans-serif; font-size: 1.1rem; color: var(--text); margin: 0;">
          📈 Courbe des inscriptions
        </h3>
        <button id="btnExportStats" class="btn btn-primary" style="padding: 8px 15px; background-color: #e74c3c; border: none; font-size: 0.8rem;">
          📄 PDF
        </button>
      </div>
      <div style="height: 300px;">
        <canvas id="inscriptionsChart"></canvas>
      </div>
    </div>

    <!-- GRAPHIQUE 2: CERCLE DES TYPES -->
    <div class="table-card" style="padding: 30px;">
      <h3 style="font-family: 'Syne', sans-serif; font-size: 1.1rem; color: var(--text); margin-bottom: 20px;">
        ⭕ Distribution des comptes
      </h3>
      <div style="height: 300px;">
        <canvas id="distributionChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Hidden form to send chart image to server -->
  <form id="exportForm" method="POST" action="index.php?ctrl=user&action=exportStats" style="display:none;">
    <input type="hidden" name="chartImage" id="chartImageInput">
  </form>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // 1. CHART INSCRIPTIONS (LINE / COURBE)
      const ctx1 = document.getElementById('inscriptionsChart').getContext('2d');
      const moisLabels = <?= json_encode(array_values($moisFr)) ?>;
      const dataInscriptions = <?= json_encode(array_values($inscriptionsStats)) ?>;

      const chart1 = new Chart(ctx1, {
        type: 'line',
        data: {
          labels: moisLabels,
          datasets: [{
            label: 'Inscriptions',
            data: dataInscriptions,
            borderColor: '#6C3FC5',
            backgroundColor: 'rgba(108, 63, 197, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#6C3FC5',
            pointBorderWidth: 2,
            pointRadius: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
            x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
          },
          plugins: { legend: { display: false } }
        }
      });

      // 2. CHART DISTRIBUTION (DOUGHNUT / CERCLE)
      const ctx2 = document.getElementById('distributionChart').getContext('2d');
      const distData = <?= json_encode(array_values($distributionStats)) ?>;
      const distLabels = <?= json_encode(array_keys($distributionStats)) ?>;

      new Chart(ctx2, {
        type: 'doughnut',
        data: {
          labels: distLabels,
          datasets: [{
            data: distData,
            backgroundColor: ['#00C2CB', '#FF6B6B', '#6C3FC5'],
            borderColor: 'rgba(255,255,255,0.1)',
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: { color: '#94a3b8', padding: 20, font: { size: 12 } }
            }
          },
          cutout: '70%'
        }
      });

      // Export logic
      document.getElementById('btnExportStats').addEventListener('click', function() {
        const chartImage = chart1.toBase64Image();
        document.getElementById('chartImageInput').value = chartImage;
        document.getElementById('exportForm').submit();
      });
    });
  </script>

  <!-- STATS NUMÉRIQUES -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-top: 20px;">
    <div class="table-card" style="padding: 24px; text-align: center;">
      <div style="font-size: 0.9rem; color: var(--text3); margin-bottom: 8px;">Moyenne mensuelle</div>
      <div class="animate-number" data-target="<?= round(array_sum($inscriptionsStats) / 12, 1) ?>" style="font-size: 2rem; font-family: 'Syne', sans-serif; font-weight: 800; color: #00C2CB;">
        0
      </div>
    </div>
    <div class="table-card" style="padding: 24px; text-align: center;">
      <div style="font-size: 0.9rem; color: var(--text3); margin-bottom: 8px;">Total annuel</div>
      <div class="animate-number" data-target="<?= array_sum($inscriptionsStats) ?>" style="font-size: 2rem; font-family: 'Syne', sans-serif; font-weight: 800; color: #6C3FC5;">
        0
      </div>
    </div>
    <div class="table-card" style="padding: 24px; text-align: center;">
      <div style="font-size: 0.9rem; color: var(--text3); margin-bottom: 8px;">Mois le plus actif</div>
      <div style="font-size: 2rem; font-family: 'Syne', sans-serif; font-weight: 800; color: var(--text);">
        <?= $moisFr[array_search(max($inscriptionsStats), $inscriptionsStats)] ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
