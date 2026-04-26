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
    <h3 style="margin-bottom: 40px; font-family: 'Syne', sans-serif; font-size: 1.2rem; color: var(--text);">
      Inscriptions par mois
    </h3>

    <!-- GRAPHIQUE CONTENANT -->
    <div style="height: 350px; border-left: 2px solid rgba(255,255,255,0.1); border-bottom: 2px solid rgba(255,255,255,0.1); position: relative; display: flex; align-items: flex-end; justify-content: space-between; padding: 0 20px 0 60px;">
      
      <!-- ÉCHELLE Y -->
      <div style="position: absolute; left: 0; bottom: 0; height: 100%; width: 50px; display: flex; flex-direction: column-reverse; justify-content: space-between; padding-bottom: 2px; color: var(--text3); font-size: 0.8rem; text-align: right; padding-right: 10px;">
        <span>0</span>
        <span><?= round($maxVal * 0.25) ?></span>
        <span><?= round($maxVal * 0.5) ?></span>
        <span><?= round($maxVal * 0.75) ?></span>
        <span><?= $maxVal ?></span>
      </div>

      <!-- LIGNES DE GRILLE -->
      <div style="position: absolute; left: 60px; right: 20px; top: 0; bottom: 0; pointer-events: none;">
        <div style="position: absolute; width: 100%; border-top: 1px dashed rgba(255,255,255,0.05); top: 0%;"></div>
        <div style="position: absolute; width: 100%; border-top: 1px dashed rgba(255,255,255,0.05); top: 25%;"></div>
        <div style="position: absolute; width: 100%; border-top: 1px dashed rgba(255,255,255,0.05); top: 50%;"></div>
        <div style="position: absolute; width: 100%; border-top: 1px dashed rgba(255,255,255,0.05); top: 75%;"></div>
      </div>

      <!-- BARRES -->
      <?php foreach ($inscriptionsStats as $mois => $count): ?>
        <?php 
          $heightPercent = ($count / $maxVal) * 100;
          $isCurrentMonth = ((int)date('n') === $mois);
        ?>
        <div style="flex: 1; margin: 0 8px; display: flex; flex-direction: column; align-items: center; position: relative; max-width: 60px;">
          
          <!-- Tooltip (au survol) -->
          <div class="chart-tooltip" style="position: absolute; bottom: calc(<?= $heightPercent ?>% + 10px); background: #6C3FC5; color: white; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; opacity: 0; transition: opacity 0.2s; pointer-events: none; white-space: nowrap;">
            <?= $count ?> inscrit(s)
          </div>

          <!-- Barre -->
          <div style="width: 100%; height: <?= $heightPercent ?>%; background: <?= $isCurrentMonth ? 'linear-gradient(to top, #6C3FC5, #00C2CB)' : 'rgba(108, 63, 197, 0.4)' ?>; border-radius: 6px 6px 0 0; transition: all 0.3s ease; cursor: pointer; border: 1px solid rgba(108, 63, 197, 0.6);"
               onmouseover="this.style.background='#6C3FC5'; this.previousElementSibling.style.opacity='1'; this.style.transform='scaleX(1.05)';"
               onmouseout="this.style.background='<?= $isCurrentMonth ? 'linear-gradient(to top, #6C3FC5, #00C2CB)' : 'rgba(108, 63, 197, 0.4)' ?>'; this.previousElementSibling.style.opacity='0'; this.style.transform='none';">
          </div>

          <!-- Étiquette Mois -->
          <div style="position: absolute; top: 100%; padding-top: 10px; color: var(--text3); font-size: 0.8rem; font-weight: 600;">
            <?= $moisFr[$mois] ?>
          </div>
        </div>
      <?php endforeach; ?>

    </div>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-top: 40px;">
    <div class="table-card" style="padding: 24px; text-align: center;">
      <div style="font-size: 0.9rem; color: var(--text3); margin-bottom: 8px;">Moyenne mensuelle</div>
      <div style="font-size: 2rem; font-family: 'Syne', sans-serif; font-weight: 800; color: #00C2CB;">
        <?= round(array_sum($inscriptionsStats) / 12, 1) ?>
      </div>
    </div>
    <div class="table-card" style="padding: 24px; text-align: center;">
      <div style="font-size: 0.9rem; color: var(--text3); margin-bottom: 8px;">Total annuel</div>
      <div style="font-size: 2rem; font-family: 'Syne', sans-serif; font-weight: 800; color: #6C3FC5;">
        <?= array_sum($inscriptionsStats) ?>
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
