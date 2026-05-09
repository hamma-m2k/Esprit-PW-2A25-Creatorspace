<?php require_once __DIR__ . '/../layout_back.php'; ?>
<?php
$isEdit      = isset($contrat) && !empty($contrat);
$pageTitle   = $isEdit ? 'Modifier le contrat' : 'Nouveau Contrat';
$pageSubtitle= $isEdit ? 'Modifier les informations' : 'Créer un contrat CDI / CDD / CDIV';

// Safe variables
$titreVal       = $old['titre']       ?? ($isEdit ? $contrat['titre'] : '');
$descVal        = $old['description'] ?? ($isEdit ? $contrat['description'] : '');
$typeVal        = $old['type']        ?? ($isEdit ? $contrat['type'] : 'CDI');
$sigVal         = $old['signature']   ?? ($isEdit ? $contrat['signature'] : '');
$signedByVal    = $old['signed_by']   ?? ($isEdit ? $contrat['signed_by'] : '');
$statutVal      = $old['statut']      ?? ($isEdit ? $contrat['statut'] : 'en_attente');
?>

<div style="max-width:760px;margin:0 auto;padding-bottom:50px;">

  <?php if (!empty($errors)): ?>
  <div class="alert alert-error" style="background:var(--danger-bg);color:var(--danger);border:1px solid var(--danger);padding:14px;border-radius:10px;margin-bottom:20px;">
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

  <div class="card" style="background:var(--bg-card);border:1px solid var(--border);border-radius:16px;padding:24px;box-shadow:0 8px 28px rgba(0,0,0,0.2);">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);padding-bottom:16px;margin-bottom:20px;">
      <div class="card-title" style="font-size:1.1rem;font-weight:600;color:var(--text-primary);">
        <span class="icon" style="color:var(--accent);">◧</span>
        <?= $isEdit ? 'Modifier le contrat #' . $contrat['id'] : 'Informations du contrat' ?>
      </div>
      <a href="index.php?ctrl=user&action=contrats" class="btn btn-outline btn-sm">← Retour</a>
    </div>

    <form method="POST"
          action="index.php?ctrl=user&action=<?= $isEdit ? 'updateContratAction&id=' . $contrat['id'] : 'storeContrat' ?>"
          novalidate>
      

      <!-- ── TITRE ── -->
      <div class="form-group" style="margin-bottom:20px;">
        <label class="form-label" for="titre" style="display:block;margin-bottom:8px;font-weight:500;">Titre du contrat *</label>
        <input
          id="titre"
          class="form-control"
          style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--border);background:rgba(255,255,255,0.03);color:white;"
          type="text"
          name="titre"
          placeholder="Ex : Contrat de développeur senior"
          value="<?= htmlspecialchars($titreVal) ?>"
          required minlength="3" maxlength="200"
        >
      </div>

      <!-- ── DESCRIPTION ── -->
      <div class="form-group" style="margin-bottom:20px;">
        <label class="form-label" for="description" style="display:block;margin-bottom:8px;font-weight:500;">Description</label>
        <textarea
          id="description"
          class="form-control"
          style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--border);background:rgba(255,255,255,0.03);color:white;min-height:100px;"
          name="description"
          rows="4"
          placeholder="Décrivez l'objet de ce contrat..."><?= htmlspecialchars($descVal) ?></textarea>
      </div>

      <!-- ── TYPE ── -->
      <div class="form-group" style="margin-bottom:20px;">
        <label class="form-label" style="display:block;margin-bottom:8px;font-weight:500;">Type de contrat *</label>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
          <?php foreach (['CDI' => 'Contrat à Durée Indéterminée', 'CDD' => 'Contrat à Durée Déterminée', 'CDIV' => 'Contrat D\'Intérim Variable'] as $val => $label): ?>
          <?php $checked = ($typeVal === $val) ? 'checked' : ''; ?>
          <label style="
            display:flex;align-items:center;gap:10px;
            padding:12px 18px;
            border:1.5px solid <?= $checked ? 'var(--accent, #7c6fef)' : 'var(--border, #333)' ?>;
            border-radius:10px;
            cursor:pointer;
            background:<?= $checked ? 'rgba(124, 111, 239, 0.1)' : 'transparent' ?>;
            color:<?= $checked ? '#a89cf5' : '#b8b4d9' ?>;
            transition:all 0.2s;
            flex:1;min-width:160px;
          " onclick="
            document.querySelectorAll('input[name=type]').forEach(el => {
              el.checked = false;
              el.parentElement.style.borderColor = 'var(--border, #333)';
              el.parentElement.style.background = 'transparent';
              el.parentElement.style.color = '#b8b4d9';
            });
            this.querySelector('input').checked = true;
            this.style.borderColor = 'var(--accent, #7c6fef)';
            this.style.background = 'rgba(124, 111, 239, 0.1)';
            this.style.color = '#a89cf5';
          ">
            <input type="radio" name="type" value="<?= $val ?>" <?= $checked ?> style="display:none;">
            <span style="font-weight:600;font-size:15px;pointer-events:none;"><?= $val ?></span>
            <span style="font-size:12px;opacity:0.8;pointer-events:none;"><?= $label ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- ── SIGNATURE ── -->
      <div class="form-group" style="margin-bottom:20px;">
        <label class="form-label" for="signature" style="display:block;margin-bottom:8px;font-weight:500;">Signature (nom du signataire)</label>
        <input
          id="signature"
          class="form-control"
          style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--border);background:rgba(255,255,255,0.03);color:white;"
          type="text"
          name="signature"
          placeholder="Ex : Jean Dupont"
          value="<?= htmlspecialchars($sigVal) ?>"
        >
      </div>



      <!-- ── STATUT ── -->
      <div class="form-group" style="margin-bottom:30px;">
        <label class="form-label" style="display:block;margin-bottom:8px;font-weight:500;">Statut</label>
        <select class="form-control" name="statut" style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--border);background:rgba(255,255,255,0.03);color:white;">
          <?php foreach (['en_attente' => 'En Attente', 'brouillon' => 'Brouillon', 'actif' => 'Actif', 'accepte' => 'Accepté', 'refuse' => 'Refusé', 'archive' => 'Archivé'] as $v => $l): ?>
          <option value="<?= $v ?>" <?= ($statutVal === $v) ? 'selected' : '' ?> style="background:#1a1a2e;">
            <?= $l ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- ── BOUTONS ── -->
      <div style="display:flex;gap:12px;margin-top:30px;padding-top:20px;border-top:1px solid var(--border);">
        <button type="submit" class="btn btn-primary" style="background:var(--accent,#7c6fef);color:white;padding:12px 24px;border-radius:10px;border:none;font-weight:600;cursor:pointer;">
          <?= $isEdit ? '✓ Enregistrer les modifications' : '✓ Créer le contrat' ?>
        </button>
        <a href="index.php?ctrl=user&action=contrats" class="btn btn-outline" style="padding:12px 24px;border-radius:10px;border:1px solid var(--border);color:white;text-decoration:none;">Annuler</a>
        
        <?php if ($isEdit): ?>
        <a href="index.php?ctrl=user&action=createRuleForm&contrat_id=<?= $contrat['id'] ?>"
           class="btn btn-outline" style="margin-left:auto;padding:12px 24px;border-radius:10px;border:1px solid var(--accent);color:var(--accent-light);text-decoration:none;">
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
    l.style.border = '1.5px solid var(--border, #333)';
    l.style.background = 'transparent';
    l.style.color = '#b8b4d9';
  });
  label.style.border = '1.5px solid var(--accent, #7c6fef)';
  label.style.background = 'rgba(124, 111, 239, 0.1)';
  label.style.color = '#a89cf5';
}
</script>
<?php require_once __DIR__ . '/../layout_back_end.php'; ?>
