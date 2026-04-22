<?php
// view/backoffice/demandes_historique.php — historique de toutes les demandes
require_once __DIR__ . '/layout_back.php';
?>

<div class="back-section active" id="back-demandes-historique">

  <div class="back-header">
    <div>
      <h2 style="font-size:1.6rem; color:var(--text);">📜 Historique des demandes</h2>
      <p style="color:var(--text3); font-size:0.9rem; margin-top:4px;">
        Toutes les demandes d'inscription
      </p>
    </div>
    <a href="index.php?ctrl=demande&action=liste">
      <button class="btn btn-outline btn-sm">← Retour aux demandes</button>
    </a>
  </div>

  <div class="table-card">
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Type</th>
            <th>Statut</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($demandes)): ?>
          <tr>
            <td colspan="6" style="text-align:center; color:var(--text3); padding:28px;">
              Aucune demande enregistrée.
            </td>
          </tr>
          <?php else: ?>
          <?php
          $types = ['user'=>'Utilisateur','societe'=>'Société','createur'=>'Créateur'];
          $statuts = [
              'en_attente' => ['label'=>'En attente', 'color'=>'var(--warning)'],
              'accepte'    => ['label'=>'Accepté',    'color'=>'var(--success)'],
              'refuse'     => ['label'=>'Refusé',     'color'=>'var(--danger)'],
          ];
          ?>
          <?php foreach ($demandes as $d): ?>
          <?php $s = $statuts[$d['statut']] ?? $statuts['en_attente']; ?>
          <tr>
            <td style="font-weight:600; color:var(--text);"><?= htmlspecialchars($d['nom']) ?></td>
            <td style="color:var(--text2);"><?= htmlspecialchars($d['prenom']) ?></td>
            <td style="color:var(--text2);"><?= htmlspecialchars($d['mail']) ?></td>
            <td>
              <span class="badge badge-verified">
                <?= htmlspecialchars($types[$d['type_compte']] ?? $d['type_compte']) ?>
              </span>
            </td>
            <td>
              <span style="color:<?= $s['color'] ?>; font-weight:600; font-size:0.82rem;">
                <?= $s['label'] ?>
              </span>
            </td>
            <td style="color:var(--text3); font-size:0.82rem;">
              <?= htmlspecialchars(date('d/m/Y H:i', strtotime($d['created_at']))) ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
