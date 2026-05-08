<?php $pageTitle = 'Contrat #' . $contrat['id']; $pageSubtitle = htmlspecialchars($contrat['titre']); ?>

<?php if (!empty($_SESSION['success'])): ?>
<div class="alert alert-success">✓ <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (!empty($_SESSION['translated_rule'])): $tr = $_SESSION['translated_rule']; unset($_SESSION['translated_rule']); ?>
<div class="alert alert-info">
  <strong>Règle #<?= (int)$tr['id'] ?></strong> traduite (<?= htmlspecialchars($tr['source']) ?> → <?= htmlspecialchars($tr['target']) ?>)<br>
  <strong><?= htmlspecialchars($tr['titre']) ?></strong><br>
  <small><?= nl2br(htmlspecialchars($tr['description'])) ?></small>
</div>
<?php endif; ?>

<?php if (!empty($_SESSION['form_errors']['email'])): ?>
<div class="alert alert-danger">⚠ <?= htmlspecialchars($_SESSION['form_errors']['email']); unset($_SESSION['form_errors']); ?></div>
<?php endif; ?>

<div class="grid-2" style="align-items:start;">

  <!-- COLONNE GAUCHE : Infos contrat -->
  <div>
    <div class="card">
      <div class="card-header">
        <div class="card-title"><span class="icon">◧</span> Informations</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          <a href="<?= BASE_URL ?>/contrats/pdf/<?= $contrat['id'] ?>" target="_blank" class="btn btn-outline btn-sm">⬇ PDF</a>

          <details class="dropdown" style="position:relative;">
            <summary class="btn btn-outline btn-sm" style="list-style:none;cursor:pointer;">⌘ Traduire ▾</summary>
            <div style="position:absolute;top:calc(100% + 6px);right:0;background:var(--bg-elevated);border:1px solid var(--border);border-radius:var(--radius-sm);padding:6px;min-width:140px;z-index:10;display:flex;flex-direction:column;gap:4px;box-shadow:var(--shadow-card);">
              <?php foreach (['en'=>'English','es'=>'Español','de'=>'Deutsch','it'=>'Italiano','ar'=>'العربية','pt'=>'Português'] as $lang => $label): ?>
                <a href="<?= BASE_URL ?>/contrats/translate/<?= $contrat['id'] ?>?to=<?= $lang ?>"
                   class="btn btn-sm btn-outline"><?= $label ?></a>
              <?php endforeach; ?>
            </div>
          </details>

          <details class="dropdown" style="position:relative;">
            <summary class="btn btn-outline btn-sm" style="list-style:none;cursor:pointer;">✉ Envoyer ▾</summary>
            <form method="POST" action="<?= BASE_URL ?>/contrats/email/<?= $contrat['id'] ?>"
                  style="position:absolute;top:calc(100% + 6px);right:0;background:var(--bg-elevated);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;min-width:260px;z-index:10;display:flex;flex-direction:column;gap:6px;box-shadow:var(--shadow-card);">
              <?= Csrf::field() ?>
              <input type="email" name="to" class="form-control" required
                     placeholder="destinataire@email.com"
                     value="<?= htmlspecialchars($contrat['signataire_email'] ?? '') ?>">
              <button type="submit" class="btn btn-primary btn-sm">Envoyer le PDF</button>
            </form>
          </details>

          <a href="<?= BASE_URL ?>/contrats/edit/<?= $contrat['id'] ?>" class="btn btn-outline btn-sm">✎ Modifier</a>
          <a href="<?= BASE_URL ?>/contrats" class="btn btn-outline btn-sm">← Retour</a>
        </div>
      </div>

      <?php
        $typeBadge   = ['CDI'=>'badge-info','CDD'=>'badge-warning','CDIV'=>'badge-purple'][$contrat['type']] ?? 'badge-info';
        $statutBadge = ['actif'=>'badge-success','brouillon'=>'badge-warning','archive'=>'badge-danger'][$contrat['statut']] ?? 'badge-info';
      ?>

      <div style="display:grid;gap:14px;">
        <div>
          <div class="form-label">Titre</div>
          <div style="font-size:18px;font-weight:600;"><?= htmlspecialchars($contrat['titre']) ?></div>
        </div>

        <div style="display:flex;gap:10px;align-items:center;">
          <span class="badge <?= $typeBadge ?>" style="font-size:13px;padding:5px 14px;"><?= $contrat['type'] ?></span>
          <span class="badge <?= $statutBadge ?>" style="font-size:13px;padding:5px 14px;"><?= ucfirst($contrat['statut']) ?></span>
        </div>

        <?php if (!empty($contrat['description'])): ?>
        <div>
          <div class="form-label">Description</div>
          <p style="color:var(--text-muted);font-size:14px;line-height:1.7;">
            <?= nl2br(htmlspecialchars($contrat['description'])) ?>
          </p>
        </div>
        <?php endif; ?>

        <?php if (!empty($contrat['signature'])): ?>
        <div>
          <div class="form-label">Signature</div>
          <div style="font-size:15px;color:var(--accent-light);">✍ <?= htmlspecialchars($contrat['signature']) ?></div>
        </div>
        <?php endif; ?>

        <?php if (!empty($contrat['signataire_prenom'])): ?>
        <div>
          <div class="form-label">Signataire (compte)</div>
          <div class="user-cell">
            <div class="avatar"><?= strtoupper(substr($contrat['signataire_prenom'], 0, 1)) ?></div>
            <div>
              <div class="name"><?= htmlspecialchars($contrat['signataire_prenom'] . ' ' . $contrat['signataire_nom']) ?></div>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <div>
          <div class="form-label">Créé le</div>
          <div style="color:var(--text-muted);font-size:14px;">
            <?= date('d/m/Y à H:i', strtotime($contrat['created_at'])) ?>
          </div>
        </div>
      </div>

      <!-- Changer le statut -->
      <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
        <div class="form-label">Changer le statut</div>
        <form method="POST" action="<?= BASE_URL ?>/contrats/statut/<?= $contrat['id'] ?>"
              style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
          <?= Csrf::field() ?>
          <button name="statut" value="brouillon" class="btn btn-warning btn-sm">◈ Brouillon</button>
          <button name="statut" value="actif"     class="btn btn-success btn-sm">◆ Activer</button>
          <button name="statut" value="archive"   class="btn btn-danger  btn-sm">✕ Archiver</button>
        </form>
      </div>
    </div>

    <!-- Supprimer -->
    <div class="card" style="border-color:var(--danger-bg);">
      <div class="card-header">
        <div class="card-title" style="color:var(--danger);">⚠ Zone dangereuse</div>
      </div>
      <p style="font-size:14px;color:var(--text-muted);margin-bottom:16px;">
        La suppression d'un contrat entraîne la suppression de toutes ses règles associées.
      </p>
      <form method="POST" action="<?= BASE_URL ?>/contrats/delete/<?= $contrat['id'] ?>"
            onsubmit="return confirm('Confirmer la suppression du contrat et de ses ' + <?= count($rules) ?> + ' règle(s) ?')">
        <?= Csrf::field() ?>
        <button type="submit" class="btn btn-danger btn-sm">✕ Supprimer ce contrat</button>
      </form>
    </div>
  </div>

  <!-- COLONNE DROITE : Rules (jointure) -->
  <div>
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <span class="icon">◆</span> Rules associées
          <span class="badge badge-purple"><?= count($rules) ?></span>
        </div>
        <div style="display:flex;gap:8px;">
          <a href="<?= BASE_URL ?>/rules/create?contrat_id=<?= $contrat['id'] ?>" class="btn btn-outline btn-sm">＋ Ajouter</a>
          <a href="<?= BASE_URL ?>/rules/add?contrat_id=<?= $contrat['id'] ?>" class="btn btn-primary btn-sm">◆ Gérer tout</a>
        </div>
      </div>

      <?php if (empty($rules)): ?>
      <div class="empty-state">
        <div class="empty-icon">◆</div>
        <h3>Aucune règle ajoutée</h3>
        <p style="margin-top:12px;">
          <a href="<?= BASE_URL ?>/rules/add?contrat_id=<?= $contrat['id'] ?>"
             class="btn btn-primary btn-sm">Ajouter des rules</a>
        </p>
      </div>
      <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:10px;">
        <?php foreach ($rules as $i => $r): ?>
        <div style="
          display:flex;align-items:flex-start;gap:14px;
          padding:14px;
          background:var(--bg-surface);
          border:1px solid var(--border);
          border-radius:var(--radius-sm);
          transition:var(--transition);
        ">
          <div style="
            width:28px;height:28px;min-width:28px;
            background:var(--accent-dim);
            border-radius:6px;
            display:flex;align-items:center;justify-content:center;
            font-size:12px;font-weight:600;color:var(--accent-light);
          "><?= $r['position'] ?: ($i + 1) ?></div>
          <div style="flex:1;">
            <div style="font-weight:500;font-size:14px;"><?= htmlspecialchars($r['titre']) ?></div>
            <?php if (!empty($r['description'])): ?>
            <div style="font-size:12px;color:var(--text-muted);margin-top:3px;">
              <?= htmlspecialchars(mb_substr($r['description'], 0, 100)) ?><?= mb_strlen($r['description']) > 100 ? '...' : '' ?>
            </div>
            <?php endif; ?>
            <?php if ($r['source'] === 'import'): ?>
            <span class="badge badge-info" style="margin-top:6px;font-size:10px;">importé</span>
            <?php endif; ?>
          </div>
          <div class="actions">
            <a href="<?= BASE_URL ?>/rules/translate/<?= $r['id'] ?>?to=en"
               class="btn btn-outline btn-sm btn-icon" title="Traduire en anglais">⌘</a>
            <a href="<?= BASE_URL ?>/rules/edit/<?= $r['id'] ?>" class="btn btn-outline btn-sm btn-icon" title="Modifier">✎</a>
            <form method="POST" action="<?= BASE_URL ?>/rules/delete/<?= $r['id'] ?>"
                  onsubmit="return confirm('Supprimer cette règle ?')">
              <?= Csrf::field() ?>
              <button type="submit" class="btn btn-danger btn-sm btn-icon">✕</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
        <a href="<?= BASE_URL ?>/rules/add?contrat_id=<?= $contrat['id'] ?>"
           class="btn btn-primary" style="width:100%;">
          ＋ Ajouter / Importer des rules
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- ═══ Commentaires (bundle externe : Disqus) ═══ -->
<div class="card" style="margin-top:24px;">
  <h3>Commentaires</h3>
  <div id="disqus_thread"></div>
  <script>
    var disqus_config = function () {
      this.page.url = window.location.href;
      this.page.identifier = "contrat-<?= (int)($contrat['id'] ?? 0) ?>";
    };
    (function() {
      var d = document, s = d.createElement('script');
      s.src = 'https://<?= DISQUS_SHORTNAME ?>.disqus.com/embed.js';
      s.setAttribute('data-timestamp', +new Date());
      (d.head || d.body).appendChild(s);
    })();
  </script>
  <noscript>Activez JavaScript pour voir les commentaires.</noscript>
</div>
