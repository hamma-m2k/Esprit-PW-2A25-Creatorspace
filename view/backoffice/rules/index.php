<?php require_once __DIR__ . '/../layout_back.php'; ?>
<?php require_once __DIR__ . '/../layout_back.php'; ?>
<?php if (!empty($success)): ?>
<div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <div class="card-title"><span class="icon">◆</span> Liste des Rules</div>
    <div style="display:flex;gap:10px;">
      <a href="index.php?ctrl=user&action=createRuleForm"   class="btn btn-outline btn-sm">＋ Ajouter une rule</a>
      <a href="index.php?ctrl=user&action=createRuleForm"      class="btn btn-primary btn-sm">◆ Ajout / Import</a>
    </div>
  </div>

  <!-- Filtre par contrat -->
  <div class="filters">
    <span style="font-size:12px;color:var(--text-muted);margin-right:4px;">Filtrer par contrat :</span>
    <a href="index.php?ctrl=user&action=rules" class="filter-btn active">Tous</a>
    <?php foreach ($contrats as $c): ?>
    <a href="&contrat_id=<?= $c['id'] ?>" class="filter-btn"><?= htmlspecialchars($c['titre']) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($rules)): ?>
  <div class="empty-state">
    <div class="empty-icon">◆</div>
    <h3>Aucune règle trouvée</h3>
    <p style="margin-top:12px;">
      <a href="index.php?ctrl=user&action=createRuleForm" class="btn btn-primary btn-sm">Ajouter des rules</a>
    </p>
  </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Titre</th>
          <th>Description</th>
          <th>Contrat (JOIN)</th>
          <th>Type</th>
          <th>Position</th>
          <th>Source</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rules as $r): ?>
        <tr>
          <td style="color:var(--text-dim);font-size:13px;">#<?= $r['id'] ?></td>
          <td style="font-weight:500;"><?= htmlspecialchars($r['titre']) ?></td>
          <td style="color:var(--text-muted);font-size:13px;max-width:200px;">
            <?= htmlspecialchars(mb_substr($r['description'] ?? '', 0, 60)) ?>
            <?= mb_strlen($r['description'] ?? '') > 60 ? '...' : '' ?>
          </td>
          <td>
            <a href="index.php?ctrl=user&action=showContrat&id=<?= $r['contrat_id'] ?>"
               style="color:var(--accent-light);text-decoration:none;font-size:13px;">
              ◧ <?= htmlspecialchars($r['contrat_titre']) ?>
            </a>
          </td>
          <td>
            <?php $tb = ['CDI'=>'badge-info','CDD'=>'badge-warning','CDIV'=>'badge-purple'][$r['contrat_type']] ?? 'badge-info'; ?>
            <span class="badge <?= $tb ?>"><?= $r['contrat_type'] ?></span>
          </td>
          <td style="text-align:center;font-size:13px;color:var(--text-muted);"><?= $r['position'] ?></td>
          <td>
            <span class="badge <?= $r['source'] === 'import' ? 'badge-info' : 'badge-purple' ?>">
              <?= $r['source'] ?>
            </span>
          </td>
          <td>
            <div class="actions">
              <a href="index.php?ctrl=user&action=editRule&id=<?= $r['id'] ?>"
                 class="btn btn-outline btn-sm btn-icon">✎</a>
              <form method="POST" action="index.php?ctrl=user&action=deleteRuleAction&id=<?= $r['id'] ?>"
                    onsubmit="return confirm('Supprimer cette règle ?')">
                
                <button type="submit" class="btn btn-danger btn-sm btn-icon">✕</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layout_back_end.php'; ?>
<?php require_once __DIR__ . '/../layout_back_end.php'; ?>
