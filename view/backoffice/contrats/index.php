<?php require_once __DIR__ . '/../layout_back.php'; ?>
<?php if (!empty($success)): ?>
<div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon purple">◧</div>
    <div><div class="stat-value"><?= $stats['total'] ?></div><div class="stat-label">Total contrats</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">◆</div>
    <div><div class="stat-value"><?= $stats['actif'] ?></div><div class="stat-label">Actifs</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon blue">◧</div>
    <div><div class="stat-value"><?= $stats['cdi'] ?></div><div class="stat-label">CDI</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon amber">◧</div>
    <div><div class="stat-value"><?= $stats['cdd'] ?></div><div class="stat-label">CDD</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon red">◧</div>
    <div><div class="stat-value"><?= $stats['cdiv'] ?></div><div class="stat-label">CDIV</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon purple">◈</div>
    <div><div class="stat-value"><?= $stats['brouillon'] ?></div><div class="stat-label">Brouillons</div></div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title"><span class="icon">◧</span> Liste des Contrats</div>
    <?php if ($_SESSION['type_compte'] === 'societe'): ?>
    <a href="index.php?ctrl=user&action=createContratForm" class="btn btn-primary btn-sm">＋ Nouveau contrat</a>
    <?php endif; ?>
  </div>

  <!-- Filtres -->
  <div class="filters">
    <a href="index.php?ctrl=user&action=contrats" class="filter-btn active">Tous</a>
    <a href="?type=CDI"  class="filter-btn">CDI</a>
    <a href="?type=CDD"  class="filter-btn">CDD</a>
    <a href="?type=CDIV" class="filter-btn">CDIV</a>
    <a href="?statut=actif"     class="filter-btn">Actifs</a>
    <a href="?statut=brouillon" class="filter-btn">Brouillons</a>
    <a href="?statut=archive"   class="filter-btn">Archivés</a>
  </div>

  <?php if (empty($contrats)): ?>
  <div class="empty-state">
    <div class="empty-icon">◧</div>
    <h3>Aucun contrat trouvé</h3>
    <?php if ($_SESSION['type_compte'] === 'societe'): ?>
    <p style="margin-top:12px;">
      <a href="index.php?ctrl=user&action=createContratForm" class="btn btn-primary btn-sm">Créer le premier contrat</a>
    </p>
    <?php endif; ?>
  </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Titre</th>
          <th>Type</th>
          <th>Signataire</th>
          <th>Rules</th>
          <th>Statut</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contrats as $c): ?>
        <?php
          $typeBadge = [
            'CDI'  => 'badge-info',
            'CDD'  => 'badge-warning',
            'CDIV' => 'badge-purple',
          ][$c['type']] ?? 'badge-info';
          $statutBadge = [
            'actif'      => 'badge-success',
            'brouillon'  => 'badge-warning',
            'archive'    => 'badge-danger',
            'accepte'    => 'badge-success',
            'refuse'     => 'badge-danger',
            'en_attente' => 'badge-warning',
          ][$c['statut']] ?? 'badge-info';
        ?>
        <tr>
          <td style="color:var(--text-dim);font-size:13px;">#<?= $c['id'] ?></td>
          <td>
            <a href="index.php?ctrl=user&action=showContrat&id=<?= $c['id'] ?>"
               style="color:var(--text-primary);font-weight:500;text-decoration:none;">
              <?= htmlspecialchars($c['titre']) ?>
            </a>
          </td>
          <td><span class="badge <?= $typeBadge ?>"><?= $c['type'] ?></span></td>
          <td>
            <?php if ($c['signataire_prenom']): ?>
            <div class="user-cell">
              <div class="avatar" style="width:28px;height:28px;font-size:11px;">
                <?= strtoupper(substr($c['signataire_prenom'], 0, 1)) ?>
              </div>
              <?= htmlspecialchars($c['signataire_prenom'] . ' ' . $c['signataire_nom']) ?>
            </div>
            <?php else: ?>
            <span style="color:var(--text-dim);font-size:13px;">—</span>
            <?php endif; ?>
          </td>
          <td>
            <span class="badge badge-purple"><?= $c['nb_rules'] ?> règle(s)</span>
          </td>
          <td><span class="badge <?= $statutBadge ?>"><?= ucfirst($c['statut']) ?></span></td>
          <td style="color:var(--text-muted);font-size:13px;">
            <?= date('d/m/Y', strtotime($c['created_at'])) ?>
          </td>
          <td>
            <div class="actions">
              <a href="index.php?ctrl=user&action=showContrat&id=<?= $c['id'] ?>"
                 class="btn btn-outline btn-sm btn-icon" title="Voir">◉</a>
              
              <?php if ($c['statut'] === 'accepte'): ?>
              <span class="btn btn-sm btn-icon" title="Verrouillé (Accepté)" style="cursor:not-allowed;background:rgba(255,255,255,0.05);color:var(--text-muted);border:1px solid transparent;">🔒</span>
              <?php else: ?>
                  <?php if ($_SESSION['type_compte'] === 'societe' || $_SESSION['role'] === 'admin'): ?>
                  <a href="index.php?ctrl=user&action=editContrat&id=<?= $c['id'] ?>"
                     class="btn btn-outline btn-sm btn-icon" title="Modifier">✎</a>
                  <?php endif; ?>
                  
                  <?php if ($_SESSION['type_compte'] === 'societe'): ?>
                  <a href="index.php?ctrl=user&action=createRuleForm&contrat_id=<?= $c['id'] ?>"
                     class="btn btn-primary btn-sm btn-icon" title="Ajouter rules">◆</a>
                  <?php endif; ?>
                  
                  <?php if ($_SESSION['type_compte'] === 'societe' || $_SESSION['role'] === 'admin'): ?>
                  <form method="POST" action="index.php?ctrl=user&action=deleteContratAction&id=<?= $c['id'] ?>"
                        onsubmit="return confirm('Supprimer ce contrat et toutes ses règles ?')"
                        style="display:inline;">
                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Supprimer">✕</button>
                  </form>
                  <?php endif; ?>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php if (isset($pagination)) echo $pagination->render('index.php?ctrl=user&action=contrats'); ?>
<?php require_once __DIR__ . '/../layout_back_end.php'; ?>
