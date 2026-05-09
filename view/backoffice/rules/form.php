<?php require_once __DIR__ . '/../layout_back.php'; ?>
<?php
$isEdit      = !empty($rule);
$pageTitle   = $isEdit ? 'Modifier la règle' : 'Nouvelle Rule';
$pageSubtitle= $isEdit ? '#' . $rule['id'] . ' — ' . htmlspecialchars($rule['titre']) : 'Ajouter une règle à un contrat';
?>

<div style="max-width:680px;margin:0 auto;">

  <?php if (!empty($errors)): ?>
  <div class="alert alert-error">
    <div>
      <strong>⚠ Erreurs :</strong>
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
        <span class="icon">◆</span>
        <?= $isEdit ? 'Modifier la règle' : 'Ajouter une Rule' ?>
      </div>
      <a href="index.php?ctrl=user&action=rules" class="btn btn-outline btn-sm">← Retour</a>
    </div>

    <form method="POST"
          action="index.php?ctrl=user&action=<?= $isEdit ? 'updateRuleAction&id=' . $rule['id'] : 'storeRule' ?>"
          novalidate>
      

      <!-- Contrat (JOIN) -->
      <div class="form-group">
        <label class="form-label" for="contrat_id">Contrat associé *</label>
        <select id="contrat_id" class="form-control <?= isset($errors['contrat_id']) ? 'is-invalid' : '' ?>"
                name="contrat_id" required>
          <option value="">— Sélectionner un contrat —</option>
          <?php foreach ($contrats as $c): ?>
          <?php $sel = (($old['contrat_id'] ?? $rule['contrat_id'] ?? $contrat['id'] ?? 0) == $c['id']) ? 'selected' : ''; ?>
          <option value="<?= $c['id'] ?>" <?= $sel ?>>
            [<?= $c['type'] ?>] <?= htmlspecialchars($c['titre']) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($errors['contrat_id'])): ?>
        <div class="form-error">⚠ <?= htmlspecialchars($errors['contrat_id']) ?></div>
        <?php endif; ?>
      </div>

      <?php if (!$isEdit && !empty($templates)): ?>
      <!-- ═══ Modèles Prédéfinis ═══ -->
      <div class="form-group" style="background:rgba(124, 111, 239, 0.05);padding:15px;border-radius:10px;border:1px solid rgba(124, 111, 239, 0.2);margin-bottom:24px;">
        <label class="form-label" style="display:block;margin-bottom:12px;font-weight:600;color:var(--accent-light);">Règles Prédéfinies (Cochez pour ajouter)</label>
        <div style="display:flex;flex-direction:column;gap:10px;">
          <?php foreach ($templates as $key => $t): ?>
          <label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;padding:10px;border-radius:8px;background:rgba(255,255,255,0.02);border:1px solid var(--border);transition:0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'">
            <input type="checkbox" name="templates[]" value="<?= htmlspecialchars($key) ?>" style="margin-top:4px;accent-color:var(--accent);width:16px;height:16px;">
            <div>
              <div style="font-weight:600;font-size:14px;margin-bottom:4px;"><?= htmlspecialchars($t['titre']) ?></div>
              <div style="font-size:12px;color:var(--text-muted);line-height:1.4;"><?= htmlspecialchars($t['desc']) ?></div>
            </div>
          </label>
          <?php endforeach; ?>
        </div>
        <p style="font-size:12px;color:var(--text-dim);margin-top:10px;font-style:italic;">Vous pouvez cocher plusieurs modèles et/ou créer une règle personnalisée ci-dessous.</p>
      </div>
      <?php endif; ?>

      <!-- ═══ Génération IA ═══ -->
      <div class="form-group" style="background:#f7f5ff;padding:12px;border-radius:8px;border:1px dashed #7c5cff;">
        <label class="form-label" for="ai_topic">🤖 Générer avec l'IA</label>
        <div style="display:flex;gap:8px;">
          <input type="text" id="ai_topic" class="form-control"
                 placeholder="Sujet : ex. Confidentialité des données client"
                 style="flex:1;">
          <button type="button" id="ai_generate_btn" class="btn btn-primary">Générer</button>
        </div>
        <p id="ai_status" style="font-size:12px;color:var(--text-dim);margin-top:6px;"></p>
      </div>

      <script>
      document.getElementById('ai_generate_btn').addEventListener('click', async () => {
        const topic = document.getElementById('ai_topic').value.trim();
        const status = document.getElementById('ai_status');
        if (!topic) { status.textContent = 'Saisis un sujet.'; return; }
        status.textContent = '⏳ Génération en cours...';
        try {
          // AI generation endpoint not yet available in the integrated structure
          status.textContent = '⚠ L\'IA n\'est pas encore configurée sur ce nouveau dashboard.';
        } catch (e) {
          status.textContent = '⚠ Erreur réseau.';
        }
      });
      </script>

      <!-- Titre -->
      <div class="form-group">
        <label class="form-label" for="rule_titre">Titre de la règle *</label>
        <input
          id="rule_titre"
          class="form-control <?= isset($errors['titre']) ? 'is-invalid' : '' ?>"
          type="text"
          name="titre"
          placeholder="Ex : Confidentialité des données"
          value="<?= htmlspecialchars($old['titre'] ?? $rule['titre'] ?? '') ?>"
          minlength="2" maxlength="200"
        >
        <?php if (isset($errors['titre'])): ?>
        <div class="form-error">⚠ <?= htmlspecialchars($errors['titre']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Description -->
      <div class="form-group">
        <label class="form-label" for="rule_description">Description</label>
        <textarea
          id="rule_description"
          class="form-control"
          name="description"
          rows="4"
          maxlength="5000"
          placeholder="Détaillez cette règle..."><?= htmlspecialchars($old['description'] ?? $rule['description'] ?? '') ?></textarea>
      </div>

      <!-- Position -->
      <div class="form-group">
        <label class="form-label" for="rule_position">Position (ordre d'affichage)</label>
        <input
          id="rule_position"
          class="form-control <?= isset($errors['position']) ? 'is-invalid' : '' ?>"
          type="number"
          min="1" max="9999"
          name="position"
          placeholder="Ex : 1, 2, 3..."
          value="<?= htmlspecialchars($old['position'] ?? $rule['position'] ?? '') ?>"
        >
        <?php if (isset($errors['position'])): ?>
        <div class="form-error">⚠ <?= htmlspecialchars($errors['position']) ?></div>
        <?php endif; ?>
        <p style="font-size:12px;color:var(--text-dim);margin-top:4px;">
          Laissez vide pour ajouter automatiquement à la fin.
        </p>
      </div>

      <!-- Boutons -->
      <div style="display:flex;gap:12px;margin-top:24px;flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary">
          <?= $isEdit ? '✓ Enregistrer' : '＋ Ajouter la règle' ?>
        </button>
        <a href="index.php?ctrl=user&action=createRuleForm<?= !empty($contrat['id']) ? '&contrat_id='.$contrat['id'] : '' ?>"
           class="btn btn-outline">
          ◆ Page ajout multiple
        </a>
        <a href="index.php?ctrl=user&action=rules" class="btn btn-outline">Annuler</a>
      </div>

    </form>
  </div>
</div>
<?php require_once __DIR__ . '/../layout_back_end.php'; ?>
