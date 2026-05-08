<?php
$pageTitle   = 'Gérer les Rules';
$pageSubtitle = $contrat ? 'Contrat : ' . htmlspecialchars($contrat['titre']) : 'Toutes les rules';
?>

<?php if (!empty($success)): ?>
<div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
  <div>
    <strong>⚠ Erreurs :</strong>
    <ul style="margin:6px 0 0 16px;">
      <?php foreach ($errors as $e): ?>
        <?php if (is_array($e)): foreach ($e as $msg): ?>
        <li><?= htmlspecialchars($msg) ?></li>
        <?php endforeach; else: ?>
        <li><?= htmlspecialchars($e) ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
<?php endif; ?>

<div class="grid-2" style="align-items:start;gap:24px;">

  <!-- ══════════ PANNEAU GAUCHE : Sélection contrat + règles existantes ══════════ -->
  <div>

    <!-- Sélection du contrat -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><span class="icon">◧</span> Contrat cible</div>
      </div>
      <div class="form-group" style="margin:0;">
        <label class="form-label">Choisir un contrat</label>
        <select class="form-control" id="contrat-select" onchange="switchContrat(this.value)">
          <option value="">— Sélectionner —</option>
          <?php foreach ($contrats as $c): ?>
          <?php $sel = (($contrat['id'] ?? 0) == $c['id']) ? 'selected' : ''; ?>
          <option value="<?= $c['id'] ?>" <?= $sel ?>>
            [<?= $c['type'] ?>] <?= htmlspecialchars($c['titre']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Rules existantes du contrat sélectionné -->
    <?php if ($contrat): ?>
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <span class="icon">◆</span> Rules actuelles
          <span class="badge badge-purple"><?= count($rules) ?></span>
        </div>
        <a href="<?= BASE_URL ?>/contrats/show/<?= $contrat['id'] ?>" class="btn btn-outline btn-sm">
          Voir le contrat
        </a>
      </div>

      <?php if (empty($rules)): ?>
      <div class="empty-state" style="padding:24px;">
        <div class="empty-icon" style="font-size:32px;">◆</div>
        <h3>Aucune règle pour l'instant</h3>
      </div>
      <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:8px;">
        <?php foreach ($rules as $i => $r): ?>
        <div style="
          display:flex;align-items:center;gap:12px;
          padding:10px 14px;
          background:var(--bg-surface);
          border:1px solid var(--border);
          border-radius:var(--radius-xs);
        ">
          <span style="
            width:24px;height:24px;min-width:24px;
            background:var(--accent-dim);border-radius:4px;
            display:flex;align-items:center;justify-content:center;
            font-size:11px;font-weight:600;color:var(--accent-light);
          "><?= $r['position'] ?: $i + 1 ?></span>
          <div style="flex:1;font-size:13px;font-weight:500;"><?= htmlspecialchars($r['titre']) ?></div>
          <span class="badge <?= $r['source']==='import' ? 'badge-info' : 'badge-purple' ?>" style="font-size:10px;">
            <?= $r['source'] ?>
          </span>
          <form method="POST" action="<?= BASE_URL ?>/rules/delete/<?= $r['id'] ?>"
                onsubmit="return confirm('Supprimer ?')">
            <?= Csrf::field() ?>
            <button type="submit" class="btn btn-danger btn-sm btn-icon" style="padding:4px 8px;">✕</button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

  </div>

  <!-- ══════════ PANNEAU DROIT : AJOUTER + IMPORTER ══════════ -->
  <div>

    <!-- ── GÉNÉRATION IA (plusieurs règles / « tâches ») ── -->
    <div class="card" style="border-color:var(--accent-glow);margin-bottom:16px;">
      <div class="card-header">
        <div class="card-title"><span class="icon">🤖</span> Générer des règles avec l’IA (OpenAI)</div>
      </div>
      <p style="font-size:13px;color:var(--text-muted);margin:0 0 12px;">
        Décrivez le thème du contrat ; l’IA propose plusieurs règles que vous pourrez éditer avant d’enregistrer.
        Configurez une clé sur le serveur : <code style="font-size:12px;">OPENAI_API_KEY</code>, <code style="font-size:12px;">ANTHROPIC_API_KEY</code> (Claude) ou <code style="font-size:12px;">GEMINI_API_KEY</code> (Google), et éventuellement <code style="font-size:12px;">AI_PROVIDER</code> (<code>openai</code> | <code>anthropic</code> | <code>gemini</code>).
      </p>
      <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end;">
        <div class="form-group" style="flex:1;min-width:180px;margin:0;">
          <label class="form-label" for="ai_batch_topic">Thème / sujet</label>
          <input type="text" id="ai_batch_topic" class="form-control"
                 placeholder="Ex. : prestation de développement web, livrables, délais">
        </div>
        <div class="form-group" style="width:100px;margin:0;">
          <label class="form-label" for="ai_batch_count">Nombre</label>
          <select id="ai_batch_count" class="form-control">
            <?php for ($n = 1; $n <= 10; $n++): ?>
            <option value="<?= $n ?>"<?= $n === 5 ? ' selected' : '' ?>><?= $n ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <button type="button" id="ai_batch_btn" class="btn btn-primary">Générer</button>
      </div>
      <p id="ai_batch_status" style="font-size:12px;color:var(--text-dim);margin-top:10px;margin-bottom:0;"></p>
    </div>

    <!-- ── AJOUTER MANUELLEMENT ── -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><span class="icon">＋</span> Ajouter des Rules</div>
      </div>

      <form method="POST" action="<?= BASE_URL ?>/rules/save-batch" id="batch-form" novalidate>
        <?= Csrf::field() ?>
        <input type="hidden" name="contrat_id"
               value="<?= htmlspecialchars($contrat['id'] ?? '') ?>"
               id="hidden-contrat-id">

        <div id="rules-container">
          <!-- Ligne de règle initiale -->
          <div class="rule-row" style="
            background:var(--bg-surface);
            border:1px solid var(--border);
            border-radius:var(--radius-sm);
            padding:16px;
            margin-bottom:12px;
            position:relative;
          ">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
              <span style="font-size:12px;font-weight:600;color:var(--accent-light);">Règle 1</span>
              <button type="button" onclick="removeRow(this)"
                      class="btn btn-danger btn-sm btn-icon" style="padding:3px 8px;">✕</button>
            </div>
            <div class="form-group">
              <label class="form-label">Titre *</label>
              <input class="form-control" type="text" name="titres[]"
                     placeholder="Titre de la règle"
                     minlength="2" maxlength="200">
            </div>
            <div class="form-group">
              <label class="form-label">Description</label>
              <textarea class="form-control" name="descriptions[]" rows="2"
                        maxlength="5000"
                        placeholder="Description (optionnelle)..."></textarea>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label">Position (ordre)</label>
              <input class="form-control" type="number" min="1" max="9999" name="positions[]" placeholder="Ex: 1">
            </div>
          </div>
        </div>

        <!-- Bouton Ajouter une ligne -->
        <button type="button" onclick="addRow()"
                class="btn btn-outline" style="width:100%;margin-bottom:16px;">
          ＋ Ajouter une autre règle
        </button>

        <!-- ═══ BOUTON SAUVEGARDER ═══ -->
        <button type="submit" class="btn btn-primary" style="width:100%;font-size:16px;padding:14px;"
                onclick="return validateBatch()">
          ✓ Sauvegarder toutes les rules
        </button>

      </form>
    </div>

    <!-- ── IMPORTER DES RULES (JSON) ── -->
    <div class="card" style="border-color:var(--info-bg);">
      <div class="card-header">
        <div class="card-title" style="color:var(--info);">
          <span class="icon">⬇</span> Importer des Rules
        </div>
      </div>

      <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
        Collez un tableau JSON de règles. Format attendu :
      </p>
      <pre style="
        background:var(--bg-deep);
        border:1px solid var(--border);
        border-radius:var(--radius-xs);
        padding:12px;
        font-size:12px;
        color:var(--accent-light);
        margin-bottom:16px;
        overflow-x:auto;
      ">[
  {"titre": "Règle 1", "description": "..."},
  {"titre": "Règle 2", "description": "..."}
]</pre>

      <form method="POST" action="<?= BASE_URL ?>/rules/import" id="import-form" novalidate>
        <?= Csrf::field() ?>
        <input type="hidden" name="contrat_id"
               value="<?= htmlspecialchars($contrat['id'] ?? '') ?>"
               id="import-contrat-id">

        <div class="form-group">
          <label class="form-label">JSON des rules</label>
          <textarea class="form-control" name="import_json" rows="6"
                    id="json-input"
                    placeholder='[{"titre":"Ma règle","description":"..."}]'
                    style="font-family:monospace;font-size:13px;"></textarea>
        </div>

        <!-- ═══ BOUTON IMPORTER ═══ -->
        <button type="submit" class="btn btn-outline" style="width:100%;"
                onclick="return validateImport()">
          ⬇ Importer les Rules
        </button>
      </form>
    </div>

    <!-- Retour -->
    <div style="display:flex;gap:10px;">
      <a href="<?= BASE_URL ?>/rules" class="btn btn-outline" style="flex:1;">← Toutes les rules</a>
      <?php if ($contrat): ?>
      <a href="<?= BASE_URL ?>/contrats/show/<?= $contrat['id'] ?>"
         class="btn btn-outline" style="flex:1;">◧ Voir le contrat</a>
      <?php endif; ?>
    </div>

  </div>
</div>

<script>
var rowCount = 1;

function addRow() {
  rowCount++;
  var container = document.getElementById('rules-container');
  var div = document.createElement('div');
  div.className = 'rule-row';
  div.style.cssText = 'background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;margin-bottom:12px;position:relative;';
  div.innerHTML = `
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
      <span style="font-size:12px;font-weight:600;color:var(--accent-light);">Règle ${rowCount}</span>
      <button type="button" onclick="removeRow(this)" class="btn btn-danger btn-sm btn-icon" style="padding:3px 8px;">✕</button>
    </div>
    <div class="form-group">
      <label class="form-label">Titre *</label>
      <input class="form-control" type="text" name="titres[]" placeholder="Titre de la règle">
    </div>
    <div class="form-group">
      <label class="form-label">Description</label>
      <textarea class="form-control" name="descriptions[]" rows="2" placeholder="Description..."></textarea>
    </div>
    <div class="form-group" style="margin-bottom:0;">
      <label class="form-label">Position</label>
      <input class="form-control" type="text" name="positions[]" placeholder="Ex: ${rowCount}">
    </div>
  `;
  container.appendChild(div);
}

function removeRow(btn) {
  var row = btn.closest('.rule-row');
  var rows = document.querySelectorAll('.rule-row');
  if (rows.length > 1) {
    row.remove();
  } else {
    alert('Vous devez garder au moins une règle.');
  }
}

function switchContrat(id) {
  if (id) {
    window.location.href = '<?= BASE_URL ?>/rules/add?contrat_id=' + id;
  }
}

function validateBatch() {
  var contratId = document.getElementById('hidden-contrat-id').value;
  if (!contratId) {
    alert('Veuillez sélectionner un contrat avant de sauvegarder.');
    document.getElementById('contrat-select').focus();
    return false;
  }
  var titres = document.querySelectorAll('[name="titres[]"]');
  var hasOne = false;
  for (var i = 0; i < titres.length; i++) {
    if (titres[i].value.trim().length >= 2) { hasOne = true; break; }
  }
  if (!hasOne) {
    alert('Au moins une règle doit avoir un titre (minimum 2 caractères).');
    return false;
  }
  return true;
}

function validateImport() {
  var contratId = document.getElementById('import-contrat-id').value;
  if (!contratId) {
    alert('Veuillez sélectionner un contrat avant d\'importer.');
    return false;
  }
  var json = document.getElementById('json-input').value.trim();
  if (!json) {
    alert('Veuillez coller un JSON valide.');
    return false;
  }
  try {
    var parsed = JSON.parse(json);
    if (!Array.isArray(parsed)) {
      alert('Le JSON doit être un tableau [ ... ].');
      return false;
    }
    if (parsed.length === 0) {
      alert('Le tableau JSON est vide.');
      return false;
    }
  } catch(e) {
    alert('JSON invalide : ' + e.message);
    return false;
  }
  return true;
}

// Synchronise le contrat_id caché avec le select
document.getElementById('contrat-select').addEventListener('change', function() {
  var v = this.value;
  document.getElementById('hidden-contrat-id').value  = v;
  document.getElementById('import-contrat-id').value  = v;
});

function appendRuleRow(title, description) {
  rowCount++;
  var container = document.getElementById('rules-container');
  var div = document.createElement('div');
  div.className = 'rule-row';
  div.style.cssText = 'background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;margin-bottom:12px;position:relative;';
  var head = document.createElement('div');
  head.style.cssText = 'display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;';
  var sp = document.createElement('span');
  sp.style.cssText = 'font-size:12px;font-weight:600;color:var(--accent-light);';
  sp.textContent = 'Règle ' + rowCount;
  var rm = document.createElement('button');
  rm.type = 'button';
  rm.className = 'btn btn-danger btn-sm btn-icon';
  rm.style.padding = '3px 8px';
  rm.textContent = '✕';
  rm.addEventListener('click', function() { removeRow(rm); });
  head.appendChild(sp);
  head.appendChild(rm);
  div.appendChild(head);
  var fg1 = document.createElement('div');
  fg1.className = 'form-group';
  fg1.innerHTML = '<label class="form-label">Titre *</label>';
  var inp = document.createElement('input');
  inp.className = 'form-control';
  inp.type = 'text';
  inp.name = 'titres[]';
  inp.placeholder = 'Titre de la règle';
  inp.minLength = 2;
  inp.maxLength = 200;
  inp.value = title || '';
  fg1.appendChild(inp);
  div.appendChild(fg1);
  var fg2 = document.createElement('div');
  fg2.className = 'form-group';
  fg2.innerHTML = '<label class="form-label">Description</label>';
  var ta = document.createElement('textarea');
  ta.className = 'form-control';
  ta.name = 'descriptions[]';
  ta.rows = 2;
  ta.maxLength = 5000;
  ta.placeholder = 'Description...';
  ta.value = description || '';
  fg2.appendChild(ta);
  div.appendChild(fg2);
  var fg3 = document.createElement('div');
  fg3.className = 'form-group';
  fg3.style.marginBottom = '0';
  fg3.innerHTML = '<label class="form-label">Position</label>';
  var pos = document.createElement('input');
  pos.className = 'form-control';
  pos.type = 'number';
  pos.min = 1;
  pos.max = 9999;
  pos.name = 'positions[]';
  pos.placeholder = String(rowCount);
  fg3.appendChild(pos);
  div.appendChild(fg3);
  container.appendChild(div);
}

document.getElementById('ai_batch_btn').addEventListener('click', async function() {
  var topic = document.getElementById('ai_batch_topic').value.trim();
  var status = document.getElementById('ai_batch_status');
  var count = document.getElementById('ai_batch_count').value;
  if (!topic) { status.textContent = 'Saisis un thème ou un sujet.'; return; }
  status.textContent = '⏳ Génération en cours...';
  this.disabled = true;
  try {
    var res = await fetch('<?= BASE_URL ?>/ai/rules-batch', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'topic=' + encodeURIComponent(topic) + '&count=' + encodeURIComponent(count)
    });
    var data = await res.json();
    if (data.error) {
      status.textContent = '⚠ ' + data.error + (data.raw ? ' (voir console)' : '');
      if (data.raw) console.warn('IA raw:', data.raw);
      return;
    }
    if (!data.rules || !data.rules.length) {
      status.textContent = '⚠ Aucune règle reçue.';
      return;
    }
    data.rules.forEach(function(r) {
      appendRuleRow(r.titre || '', r.description || '');
    });
    status.textContent = '✓ ' + data.rules.length + ' règle(s) ajoutée(s) au formulaire. Vérifiez puis enregistrez.';
  } catch (e) {
    status.textContent = '⚠ Erreur réseau.';
  } finally {
    document.getElementById('ai_batch_btn').disabled = false;
  }
});
</script>
