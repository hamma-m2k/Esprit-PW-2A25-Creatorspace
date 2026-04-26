<?php
// view/backoffice/demandes.php — admin only, demandes en attente
require_once __DIR__ . '/layout_back.php';
?>

<div class="back-section active" id="back-demandes">

  <div class="back-header">
    <div>
      <h2 style="font-size:1.6rem; color:var(--text);">📋 Demandes d'inscription</h2>
      <p style="color:var(--text3); font-size:0.9rem; margin-top:4px;">
        Demandes en attente de validation
        <span style="background:rgba(229,62,62,0.15); color:var(--danger);
                     border-radius:20px; padding:2px 10px; font-size:0.8rem;
                     font-weight:700; margin-left:8px;">
          <?= $totalEnAttente ?> en attente
        </span>
      </p>
    </div>
  </div>


  <?php if (!empty($successDemande)): ?>
  <div class="toast success" style="position:static;display:flex;margin-bottom:20px;animation:none;">
    <span class="toast-msg">✅ <?= htmlspecialchars($successDemande) ?></span>
  </div>
  <?php endif; ?>

  <div class="table-card">
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Type de compte</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($demandes)): ?>
          <tr>
            <td colspan="6" style="text-align:center; color:var(--text3); padding:28px;">
              Aucune demande en attente.
            </td>
          </tr>
          <?php else: ?>
          <?php
          $types = [
              'user'     => 'Utilisateur',
              'societe'  => 'Société',
              'createur' => 'Créateur',
          ];
          ?>
          <?php foreach ($demandes as $d): ?>
          <tr>
            <td style="font-weight:600; color:var(--text);"><?= htmlspecialchars($d->getNom()) ?></td>
            <td style="color:var(--text2);"><?= htmlspecialchars($d->getPrenom()) ?></td>
            <td style="color:var(--text2);"><?= htmlspecialchars($d->getMail()) ?></td>
            <td>
              <span class="badge badge-verified">
                <?= htmlspecialchars($types[$d->getTypeCompte()] ?? $d->getTypeCompte()) ?>
              </span>
            </td>
            <td style="color:var(--text3); font-size:0.82rem;">
              <?= htmlspecialchars(date('d/m/Y H:i', strtotime($d->getCreatedAt()))) ?>
            </td>
            <td>
              <div class="table-actions">
                <a href="index.php?ctrl=demande&action=accepter&id=<?= $d->getId() ?>"
                   onclick="return window.confirm('Accepter et créer le compte de <?= htmlspecialchars($d->getNom()) ?> ?')">
                  <button class="action-btn" title="Accepter"
                          style="color:var(--success); border-color:var(--success);">
                    ✅
                  </button>
                </a>
                <a href="index.php?ctrl=demande&action=refuser&id=<?= $d->getId() ?>"
                   onclick="return window.confirm('Refuser la demande de <?= htmlspecialchars($d->getNom()) ?> ?')">
                  <button class="action-btn del" title="Refuser">❌</button>
                </a>
              </div>
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
