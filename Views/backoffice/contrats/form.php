<?php
$isEdit      = !empty($contrat);
$pageTitle   = $isEdit ? 'Modifier le contrat' : 'Nouveau Contrat';
$pageSubtitle= $isEdit ? 'Modifier les informations' : 'Créer un contrat CDI / CDD / CDIV';
?>

<div style="max-width:760px;margin:0 auto;">

  <?php if (!empty($errors)): ?>
  <div class="alert alert-error">
    <div>
      <strong>⚠ Erreurs de validation :</strong>
      <ul style="margin:8px 0 0 16px;">
        <?php foreach ($errors as $err): ?>
        <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <span class="icon">◧</span>
        <?= $isEdit ? 'Modifier le contrat #' . $contrat['id'] : 'Informations du contrat' ?>
      </div>
      <a href="<?= BASE_URL ?>/contrats" class="btn btn-outline btn-sm">← Retour</a>
    </div>

    <form method="POST"
          action="<?= BASE_URL . ($isEdit ? '/contrats/update/' . $contrat['id'] : '/contrats/store') ?>"
          novalidate>
      <?= Csrf::field() ?>

      <!-- ── TITRE ── -->
      <div class="form-group">
        <label class="form-label" for="titre">Titre du contrat *</label>
        <input
          id="titre"
          class="form-control <?= isset($errors['titre']) ? 'is-invalid' : '' ?>"
          type="text"
          name="titre"
          placeholder="Ex : Contrat de développeur senior"
          value="<?= htmlspecialchars($old['titre'] ?? $contrat['titre'] ?? '') ?>"
          required minlength="3" maxlength="200"
        >
        <?php if (isset($errors['titre'])): ?>
        <div class="form-error">⚠ <?= htmlspecialchars($errors['titre']) ?></div>
        <?php endif; ?>
      </div>

      <!-- ── DESCRIPTION ── -->
      <div class="form-group">
        <label class="form-label" for="description">Description</label>
        <textarea
          id="description"
          class="form-control"
          name="description"
          rows="4"
          maxlength="5000"
          placeholder="Décrivez l'objet de ce contrat..."><?= htmlspecialchars($old['description'] ?? $contrat['description'] ?? '') ?></textarea>
      </div>

      <!-- ── TYPE ── -->
      <div class="form-group">
        <label class="form-label">Type de contrat *</label>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
          <?php foreach (['CDI' => 'Contrat à Durée Indéterminée', 'CDD' => 'Contrat à Durée Déterminée', 'CDIV' => 'Contrat D\'Intérim Variable'] as $val => $label): ?>
          <?php $checked = (($old['type'] ?? $contrat['type'] ?? 'CDI') === $val) ? 'checked' : ''; ?>
          <label style="
            display:flex;align-items:center;gap:10px;
            padding:12px 18px;
            border:1.5px solid <?= $checked ? 'var(--accent)' : 'var(--border)' ?>;
            border-radius:var(--radius-sm);
            cursor:pointer;
            background:<?= $checked ? 'var(--accent-dim)' : 'transparent' ?>;
            color:<?= $checked ? 'var(--accent-light)' : 'var(--text-muted)' ?>;
            transition:var(--transition);
            flex:1;min-width:160px;
          " onclick="selectType(this, '<?= $val ?>')">
            <input type="radio" name="type" value="<?= $val ?>" <?= $checked ?> style="display:none;">
            <span style="font-weight:600;font-size:15px;"><?= $val ?></span>
            <span style="font-size:12px;"><?= $label ?></span>
          </label>
          <?php endforeach; ?>
        </div>
        <?php if (isset($errors['type'])): ?>
        <div class="form-error">⚠ <?= htmlspecialchars($errors['type']) ?></div>
        <?php endif; ?>
      </div>

      <!-- ── SIGNATURE ── -->
      <div class="form-group">
        <label class="form-label" for="signature">Signature (nom du signataire)</label>
        <input
          id="signature"
          class="form-control"
          type="text"
          name="signature"
          placeholder="Ex : Jean Dupont"
          maxlength="255"
          value="<?= htmlspecialchars($old['signature'] ?? $contrat['signature'] ?? '') ?>"
        >
      </div>

      <!-- ── SIGNATAIRE (FK users) ── -->
      <div class="form-group">
        <label class="form-label">Signataire (compte utilisateur)</label>
        <select class="form-control" name="signed_by">
          <option value="">— Aucun —</option>
          <?php foreach ($users as $u): ?>
          <?php $sel = (($old['signed_by'] ?? $contrat['signed_by'] ?? '') == $u['id']) ? 'selected' : ''; ?>
          <option value="<?= $u['id'] ?>" <?= $sel ?>>
            <?= htmlspecialchars($u['firstname'] . ' ' . $u['lastname'] . ' (' . $u['email'] . ')') ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- ── STATUT ── -->
      <div class="form-group">
        <label class="form-label">Statut</label>
        <select class="form-control" name="statut">
          <?php foreach (['brouillon' => 'Brouillon', 'actif' => 'Actif', 'archive' => 'Archivé'] as $v => $l): ?>
          <option value="<?= $v ?>" <?= (($old['statut'] ?? $contrat['statut'] ?? 'brouillon') === $v) ? 'selected' : '' ?>>
            <?= $l ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- ── BOUTONS ── -->
      <div style="display:flex;gap:12px;margin-top:24px;">
        <button type="submit" class="btn btn-primary">
          <?= $isEdit ? '✓ Enregistrer les modifications' : '✓ Créer le contrat' ?>
        </button>
        <a href="<?= BASE_URL ?>/contrats" class="btn btn-outline">Annuler</a>
        <?php if ($isEdit): ?>
        <a href="<?= BASE_URL ?>/rules/add?contrat_id=<?= $contrat['id'] ?>"
           class="btn btn-outline" style="margin-left:auto;">
          ◆ Gérer les rules
        </a>
        <?php endif; ?>
      </div>

    </form>
  </div>
</div>

<script>
function selectType(label, val) {
  document.querySelectorAll('[name="type"]').forEach(r => r.checked = false);
  label.querySelector('input').checked = true;
  document.querySelectorAll('.form-group label[onclick]').forEach(l => {
    l.style.border = '1.5px solid var(--border)';
    l.style.background = 'transparent';
    l.style.color = 'var(--text-muted)';
  });
  label.style.border = '1.5px solid var(--accent)';
  label.style.background = 'var(--accent-dim)';
  label.style.color = 'var(--accent-light)';
}
</script>
