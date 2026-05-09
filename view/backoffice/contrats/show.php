<?php require_once __DIR__ . '/../layout_back.php'; ?>
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
          <a href="index.php?ctrl=user&action=contrats/pdf/<?= $contrat['id'] ?>" target="_blank" class="btn btn-outline btn-sm">⬇ PDF</a>

          <details class="dropdown" style="position:relative;">
            <summary class="btn btn-outline btn-sm" style="list-style:none;cursor:pointer;">⌘ Traduire ▾</summary>
            <div style="position:absolute;top:calc(100% + 6px);right:0;background:var(--bg-elevated);border:1px solid var(--border);border-radius:var(--radius-sm);padding:6px;min-width:140px;z-index:10;display:flex;flex-direction:column;gap:4px;box-shadow:var(--shadow-card);">
              <?php foreach (['en'=>'English','es'=>'Español','de'=>'Deutsch','it'=>'Italiano','ar'=>'العربية','pt'=>'Português'] as $lang => $label): ?>
                <a href="index.php?ctrl=user&action=contrats/translate/<?= $contrat['id'] ?>?to=<?= $lang ?>"
                   class="btn btn-sm btn-outline"><?= $label ?></a>
              <?php endforeach; ?>
            </div>
          </details>

          <details class="dropdown" style="position:relative;">
            <summary class="btn btn-outline btn-sm" style="list-style:none;cursor:pointer;">✉ Envoyer ▾</summary>
            <form method="POST" action="index.php?ctrl=user&action=contrats/email/<?= $contrat['id'] ?>"
                  style="position:absolute;top:calc(100% + 6px);right:0;background:var(--bg-elevated);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;min-width:260px;z-index:10;display:flex;flex-direction:column;gap:6px;box-shadow:var(--shadow-card);">
              
              <input type="email" name="to" class="form-control" required
                     placeholder="destinataire@email.com"
                     value="<?= htmlspecialchars($contrat['signataire_email'] ?? '') ?>">
              <button type="submit" class="btn btn-primary btn-sm">Envoyer le PDF</button>
            </form>
          </details>

          <?php if ($contrat['statut'] !== 'accepte' && ($_SESSION['type_compte'] === 'societe' || $_SESSION['role'] === 'admin')): ?>
          <a href="index.php?ctrl=user&action=editContrat&id=<?= $contrat['id'] ?>" class="btn btn-outline btn-sm">✎ Modifier</a>
          <?php endif; ?>
          <a href="index.php?ctrl=user&action=contrats" class="btn btn-outline btn-sm">← Retour</a>
        </div>
      </div>

      <?php
        $typeBadge   = ['CDI'=>'badge-info','CDD'=>'badge-warning','CDIV'=>'badge-purple'][$contrat['type']] ?? 'badge-info';
        $statutBadge = [
          'actif'      => 'badge-success',
          'brouillon'  => 'badge-warning',
          'archive'    => 'badge-danger',
          'accepte'    => 'badge-success',
          'refuse'     => 'badge-danger',
          'en_attente' => 'badge-warning',
        ][$contrat['statut']] ?? 'badge-info';
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

      <!-- Changer le statut (Societe uniquement) -->
      <?php if ($_SESSION['type_compte'] === 'societe'): ?>
      <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
        <div class="form-label">Changer le statut</div>
        <form method="POST" action="index.php?ctrl=user&action=statutContratAction&id=<?= $contrat['id'] ?>"
              style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
          
          <button name="statut" value="en_attente" class="btn btn-warning btn-sm">En Attente</button>
          <button name="statut" value="actif"     class="btn btn-success btn-sm">Activer</button>
          <button name="statut" value="archive"   class="btn btn-danger  btn-sm">Archiver</button>
        </form>
      </div>
      <?php endif; ?>

      <!-- Actions (Admin / Createur) -->
      <?php 
      $isSignataire = ((int)$_SESSION['user_id'] === (int)$contrat['signed_by']);
      $isAdmin = ($_SESSION['role'] === 'admin');
      
      // Step 1: Createur accepte le contrat
      if ($contrat['statut'] === 'en_attente' && $isSignataire): 
      ?>
      <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
        <div class="form-label" style="color:var(--accent);">Action requise (Créateur)</div>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:10px;">Veuillez lire le contrat et l'accepter pour l'envoyer à l'administrateur.</p>
        <div style="display:flex;gap:10px;">
          <form method="POST" action="index.php?ctrl=user&action=acceptContratAction&id=<?= $contrat['id'] ?>">
            <button type="submit" class="btn btn-success btn-sm" style="background:#7c6fef;border:none;color:white;">Envoyer une acceptation de contrat</button>
          </form>
          <form method="POST" action="index.php?ctrl=user&action=refuseContratAction&id=<?= $contrat['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm">Refuser</button>
          </form>
        </div>
      </div>
      <?php endif; ?>

      <?php 
      // Step 2: Admin approuve comme témoin
      if ($contrat['statut'] === 'approuve_createur' && $isAdmin): 
      ?>
      <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
        <div class="form-label" style="color:var(--success);">Approbation requise (Admin Témoin)</div>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:10px;">Le créateur a accepté ce contrat. Vous devez l'approuver en tant que témoin pour le finaliser.</p>
        <div style="display:flex;gap:10px;">
          <form method="POST" action="index.php?ctrl=user&action=acceptContratAction&id=<?= $contrat['id'] ?>">
            <button type="submit" class="btn btn-success btn-sm">Approuver (Témoin)</button>
          </form>
          <form method="POST" action="index.php?ctrl=user&action=refuseContratAction&id=<?= $contrat['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm">Refuser</button>
          </form>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($contrat['statut'] === 'accepte'): ?>
      <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
        <div class="alert alert-success" style="margin:0;background:rgba(34, 211, 160, 0.1);color:#22d3a0;border:1px solid #22d3a0;">
          Ce contrat a été définitivement approuvé et verrouillé. Aucune modification ne peut y être apportée.
        </div>
      </div>
      <?php endif; ?>

    </div>

    <!-- Supprimer -->
    <?php if ($_SESSION['type_compte'] === 'societe' || $_SESSION['role'] === 'admin'): ?>
    <div class="card" style="border-color:var(--danger-bg);">
      <div class="card-header">
        <div class="card-title" style="color:var(--danger);">⚠ Zone dangereuse</div>
      </div>
      <p style="font-size:14px;color:var(--text-muted);margin-bottom:16px;">
        La suppression d'un contrat entraîne la suppression de toutes ses règles associées.
      </p>
      <form method="POST" action="index.php?ctrl=user&action=deleteContratAction&id=<?= $contrat['id'] ?>"
            onsubmit="return confirm('Confirmer la suppression du contrat et de ses ' + <?= count($rules) ?> + ' règle(s) ?')">
        
        <button type="submit" class="btn btn-danger btn-sm">✕ Supprimer ce contrat</button>
      </form>
    </div>
    <?php endif; ?>
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
          <a href="index.php?ctrl=user&action=createRuleForm&contrat_id=<?= $contrat['id'] ?>" class="btn btn-outline btn-sm">＋ Ajouter</a>
          <a href="index.php?ctrl=user&action=createRuleForm&contrat_id=<?= $contrat['id'] ?>" class="btn btn-primary btn-sm">◆ Gérer tout</a>
        </div>
      </div>

      <?php if (empty($rules)): ?>
      <div class="empty-state">
        <div class="empty-icon">◆</div>
        <h3>Aucune règle ajoutée</h3>
        <p style="margin-top:12px;">
          <a href="index.php?ctrl=user&action=createRuleForm&contrat_id=<?= $contrat['id'] ?>"
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
            <a href="index.php?ctrl=user&action=editRule&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm btn-icon" title="Modifier">✎</a>
            <form method="POST" action="index.php?ctrl=user&action=deleteRuleAction&id=<?= $r['id'] ?>"
                  onsubmit="return confirm('Supprimer cette règle ?')">
              
              <button type="submit" class="btn btn-danger btn-sm btn-icon">✕</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
        <a href="index.php?ctrl=user&action=createRuleForm&contrat_id=<?= $contrat['id'] ?>"
           class="btn btn-primary" style="width:100%;">
          ＋ Ajouter / Importer des rules
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>
<?php require_once __DIR__ . '/../layout_back_end.php'; ?>
