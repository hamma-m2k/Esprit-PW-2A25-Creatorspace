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

  <div class="table-card" style="padding: 40px; margin-top: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
      <h3 style="font-family: 'Syne', sans-serif; font-size: 1.2rem; color: var(--text); margin: 0;">
        Inscriptions par mois
      </h3>
      <button id="btnExportStats" class="btn btn-primary" style="padding: 10px 20px; background-color: #e74c3c; border-color: #c0392b;">
        📄 Export PDF (avec Graphique)
      </button>
    </div>

    <!-- GRAPHIQUE CHART.JS -->
    <div style="height: 400px; width: 100%;">
      <canvas id="inscriptionsChart"></canvas>
    </div>

    <!-- Hidden form to send chart image to server -->
    <form id="exportForm" method="POST" action="index.php?ctrl=user&action=exportStats" style="display:none;">
      <input type="hidden" name="chartImage" id="chartImageInput">
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const ctx = document.getElementById('inscriptionsChart').getContext('2d');
      
      const labels = <?= json_encode(array_values($moisFr)) ?>;
      const dataValues = <?= json_encode(array_values($inscriptionsStats)) ?>;

      const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Inscriptions',
            data: dataValues,
            backgroundColor: 'rgba(108, 63, 197, 0.6)',
            borderColor: 'rgba(108, 63, 197, 1)',
            borderWidth: 2,
            borderRadius: 8,
            hoverBackgroundColor: 'rgba(108, 63, 197, 0.9)'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: 'rgba(255,255,255,0.05)' },
              ticks: { color: '#94a3b8' }
            },
            x: {
              grid: { display: false },
              ticks: { color: '#94a3b8' }
            }
          },
          plugins: {
            legend: { display: false }
          },
          animation: {
            duration: 1000,
            easing: 'easeOutQuart'
          }
        }
      });

      // Export logic
      document.getElementById('btnExportStats').addEventListener('click', function() {
        const chartImage = myChart.toBase64Image();
        document.getElementById('chartImageInput').value = chartImage;
        document.getElementById('exportForm').submit();
      });
    });
  </script>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-top: 40px;">
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
