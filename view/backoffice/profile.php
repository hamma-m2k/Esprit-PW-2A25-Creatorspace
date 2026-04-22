<?php require_once __DIR__ . '/layout_back.php'; ?>

<?php
$initiales  = strtoupper(substr($item['nom'] ?? 'U', 0, 1) . substr($item['prenom'] ?? 'U', 0, 1));
$fullName   = htmlspecialchars(($item['nom'] ?? '') . ' ' . ($item['prenom'] ?? ''));
$handle     = '@' . strtolower(str_replace(' ', '_', trim(($item['nom'] ?? '') . '_' . ($item['prenom'] ?? ''))));
$typeLabel  = match($item['type_compte'] ?? 'user') {
    'createur' => '🎬 Créateur de contenu',
    'societe'  => '🏢 Société',
    default    => '👤 Utilisateur',
};
$followers  = number_format((int)($item['followers'] ?? 0), 0, ',', ' ');
$following  = number_format((int)($item['following'] ?? 0), 0, ',', ' ');
$socialLink = $item['social_media_link'] ?? '';
$isCreateur = ($item['type_compte'] ?? '') === 'createur';
?>

<style>
/* ── Profile Instagram ───────────────── */
.ig-wrap        { max-width: 720px; margin: 0 auto; padding-bottom: 60px; }

/* Banner */
.ig-banner      { height: 160px; border-radius: 18px 18px 0 0;
                  background: linear-gradient(135deg, #6C3FC5 0%, #00C2CB 60%, #9B5DE5 100%);
                  position: relative; }

/* Card */
.ig-card        { background: rgba(255,255,255,0.05);
                  border: 1px solid rgba(108,63,197,0.35);
                  border-radius: 0 0 18px 18px;
                  padding: 0 32px 32px;
                  box-shadow: 0 12px 40px rgba(108,63,197,0.18); }

/* Avatar ring */
.ig-avatar      { width: 110px; height: 110px; border-radius: 50%;
                  background: linear-gradient(135deg,#6C3FC5,#00C2CB);
                  display: flex; align-items: center; justify-content: center;
                  font-family:'Syne',sans-serif; font-size: 2.6rem; font-weight: 800;
                  color: #fff; border: 4px solid var(--bg);
                  box-shadow: 0 0 0 3px #6C3FC5, 0 8px 28px rgba(108,63,197,0.45);
                  margin-top: -55px; flex-shrink: 0; }

/* Header row */
.ig-head        { display: flex; align-items: flex-end; gap: 24px;
                  flex-wrap: wrap; margin-bottom: 20px; }
.ig-head-info   { flex: 1; min-width: 200px; padding-top: 16px; }
.ig-name        { font-family:'Syne',sans-serif; font-size: 1.5rem; font-weight: 800;
                  color: var(--text); margin: 0 0 2px; }
.ig-handle      { font-size: 0.85rem; color: var(--text3); margin: 0 0 8px; }
.ig-badge       { display: inline-block; font-size: 0.78rem; font-weight: 600;
                  padding: 4px 12px; border-radius: 20px;
                  background: rgba(108,63,197,0.22); color: #9B5DE5;
                  border: 1px solid rgba(108,63,197,0.4); }

/* Stats */
.ig-stats       { display: flex; gap: 0; margin: 24px 0 20px; }
.ig-stat        { flex: 1; text-align: center; padding: 16px 8px;
                  border-right: 1px solid rgba(255,255,255,0.07); }
.ig-stat:last-child { border-right: none; }
.ig-stat-num    { font-family:'Syne',sans-serif; font-size: 1.6rem; font-weight: 800;
                  color: var(--text); line-height: 1; }
.ig-stat-label  { font-size: 0.78rem; color: var(--text3); margin-top: 4px; letter-spacing:.5px; }

/* Social link */
.ig-social      { display: inline-flex; align-items: center; gap: 8px;
                  background: rgba(0,194,203,0.1); border: 1px solid rgba(0,194,203,0.35);
                  border-radius: 10px; padding: 10px 18px; color: #00C2CB;
                  font-size: 0.88rem; text-decoration: none; transition: all .2s; }
.ig-social:hover{ background: rgba(0,194,203,0.2); transform: translateY(-1px); }

/* Edit btn */
.btn-edit-profile { display: inline-flex; align-items: center; gap: 8px;
                    background: rgba(108,63,197,0.15); border: 1.5px solid #6C3FC5;
                    color: #9B5DE5; border-radius: 10px; padding: 10px 22px;
                    font-size: 0.9rem; font-weight: 600; cursor: pointer;
                    transition: all .2s; }
.btn-edit-profile:hover { background: rgba(108,63,197,0.3); transform: translateY(-1px); }

/* Form panel */
.ig-form-panel  { overflow: hidden; transition: max-height .4s ease, opacity .3s ease;
                  max-height: 0; opacity: 0; }
.ig-form-panel.open { max-height: 900px; opacity: 1; }

.ig-input       { width: 100%; background: rgba(255,255,255,0.07);
                  border: 1px solid rgba(108,63,197,0.4); color: var(--text);
                  border-radius: 10px; padding: 12px 14px; font-size: 0.95rem;
                  outline: none; transition: border .2s; box-sizing: border-box; }
.ig-input:focus { border-color: #6C3FC5; }
.ig-label       { display: block; color: var(--text2); font-size: 0.82rem;
                  margin-bottom: 7px; font-weight: 500; }
.ig-fg          { margin-bottom: 18px; }

/* Danger zone */
.danger-zone    { margin-top: 28px; padding-top: 20px;
                  border-top: 1px solid rgba(229,62,62,0.25); }

/* Success toast */
.ig-toast       { display: flex; align-items: center; gap: 10px;
                  background: rgba(56,161,105,0.15); border: 1px solid rgba(56,161,105,0.4);
                  border-radius: 10px; padding: 12px 18px; margin-bottom: 20px;
                  color: #68D391; font-size: 0.9rem; }
</style>

<div class="back-section active" id="back-profile">
<div class="ig-wrap">

  <?php if (!empty($successProfile)): ?>
  <div class="ig-toast">✅ <?= htmlspecialchars($successProfile) ?></div>
  <?php endif; ?>

  <!-- ── BANNER ── -->
  <div class="ig-banner"></div>

  <!-- ── CARD ── -->
  <div class="ig-card">

    <!-- Header : avatar + nom + badge + bouton édition -->
    <div class="ig-head">
      <div class="ig-avatar"><?= $initiales ?></div>
      <div class="ig-head-info">
        <h1 class="ig-name"><?= $fullName ?></h1>
        <p class="ig-handle"><?= htmlspecialchars($handle) ?></p>
        <span class="ig-badge"><?= $typeLabel ?></span>
      </div>
      <button class="btn-edit-profile" onclick="toggleEditForm()" id="btn-edit">
        ✏️ Modifier le profil
      </button>
    </div>

    <!-- ── STATS ── -->
    <div style="background:rgba(255,255,255,0.04); border-radius:14px; border:1px solid rgba(255,255,255,0.07); overflow:hidden; margin-bottom:24px;">
      <div class="ig-stats">
        <div class="ig-stat">
          <div class="ig-stat-num"><?= $followers ?></div>
          <div class="ig-stat-label">Followers</div>
        </div>
        <div class="ig-stat">
          <div class="ig-stat-num"><?= $following ?></div>
          <div class="ig-stat-label">Following</div>
        </div>
        <div class="ig-stat">
          <div class="ig-stat-num">
            <?= match($item['type_compte'] ?? 'user') {
              'createur' => '🎬', 'societe' => '🏢', default => '👤'
            } ?>
          </div>
          <div class="ig-stat-label">Type</div>
        </div>
      </div>
    </div>

    <!-- ── LIEN SOCIAL (si créateur) ── -->
    <?php if ($isCreateur && $socialLink !== ''): ?>
    <div style="margin-bottom: 24px;">
      <a href="<?= htmlspecialchars($socialLink) ?>" target="_blank" rel="noopener" class="ig-social">
        🔗 <?= htmlspecialchars($socialLink) ?>
      </a>
    </div>
    <?php endif; ?>

    <!-- ── FORMULAIRE D'ÉDITION (collapsible) ── -->
    <div class="ig-form-panel" id="ig-form-panel">
      <div style="background:rgba(255,255,255,0.04); border-radius:14px;
                  border:1px solid rgba(108,63,197,0.3); padding:24px; margin-top:4px;">
        <p style="color:var(--text2); font-size:0.88rem; margin-bottom:20px;">
          ✏️ Modifier vos informations
        </p>
        <form method="POST" action="index.php?ctrl=user&action=updateProfile">

          <div class="ig-fg">
            <label class="ig-label">Nom</label>
            <input type="text" name="nom" class="ig-input"
                   value="<?= htmlspecialchars($item['nom'] ?? '') ?>">
            <?php if (!empty($errors['nom'])): ?>
              <span style="color:#ff6b6b;font-size:0.8rem;margin-top:4px;display:block;">
                <?= htmlspecialchars($errors['nom']) ?></span>
            <?php endif; ?>
          </div>

          <div class="ig-fg">
            <label class="ig-label">Prénom</label>
            <input type="text" name="prenom" class="ig-input"
                   value="<?= htmlspecialchars($item['prenom'] ?? '') ?>">
            <?php if (!empty($errors['prenom'])): ?>
              <span style="color:#ff6b6b;font-size:0.8rem;margin-top:4px;display:block;">
                <?= htmlspecialchars($errors['prenom']) ?></span>
            <?php endif; ?>
          </div>

          <div class="ig-fg">
            <label class="ig-label">Mail</label>
            <input type="text" name="mail" class="ig-input"
                   value="<?= htmlspecialchars($item['mail'] ?? '') ?>">
            <?php if (!empty($errors['mail'])): ?>
              <span style="color:#ff6b6b;font-size:0.8rem;margin-top:4px;display:block;">
                <?= htmlspecialchars($errors['mail']) ?></span>
            <?php endif; ?>
          </div>

          <div class="ig-fg">
            <label class="ig-label">Type de compte</label>
            <select name="type_compte" id="prof_type_compte" class="ig-input">
              <option value="user"     <?= ($item['type_compte'] ?? 'user') === 'user'     ? 'selected' : '' ?>>Utilisateur normal</option>
              <option value="societe"  <?= ($item['type_compte'] ?? '')     === 'societe'  ? 'selected' : '' ?>>Société</option>
              <option value="createur" <?= ($item['type_compte'] ?? '')     === 'createur' ? 'selected' : '' ?>>Créateur de contenu</option>
            </select>
          </div>

          <!-- Lien social : visible seulement si créateur -->
          <div class="ig-fg" id="prof_bloc_social"
               style="display:<?= $isCreateur ? 'block' : 'none' ?>;">
            <label class="ig-label">Lien réseau social</label>
            <input type="text" name="social_media_link" class="ig-input"
                   placeholder="https://www.instagram.com/moncompte"
                   value="<?= htmlspecialchars($socialLink) ?>">
          </div>

          <div class="ig-fg">
            <label class="ig-label">
              Nouveau mot de passe
              <span style="color:var(--text3);font-size:0.75rem;">(laisser vide pour ne pas changer)</span>
            </label>
            <input type="password" name="password" class="ig-input"
                   placeholder="••••••••">
          </div>

          <button type="submit" class="btn btn-primary" style="width:100%; padding:13px; font-size:1rem;">
            💾 Sauvegarder les modifications
          </button>

        </form>

        <?php if (($currentUser['role'] ?? '') !== 'admin'): ?>
        <div class="danger-zone">
          <p style="color:var(--text3);font-size:0.82rem;margin-bottom:12px;">
            Zone dangereuse — cette action est irréversible.
          </p>
          <a href="index.php?ctrl=user&action=deleteOwn"
             onclick="return window.confirm('Supprimer définitivement votre compte ?')">
            <button class="btn btn-sm"
                    style="background:rgba(229,62,62,0.15);color:var(--danger);border:1px solid var(--danger);">
              🗑️ Supprimer mon compte
            </button>
          </a>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <!-- /form panel -->

  </div><!-- /ig-card -->
</div><!-- /ig-wrap -->
</div><!-- /back-section -->

<script>
(function() {
  // Toggle form panel
  function toggleEditForm() {
    var panel = document.getElementById('ig-form-panel');
    var btn   = document.getElementById('btn-edit');
    panel.classList.toggle('open');
    btn.textContent = panel.classList.contains('open') ? '✖ Fermer' : '✏️ Modifier le profil';
  }
  window.toggleEditForm = toggleEditForm;

  // Ouvrir automatiquement si des erreurs existent
  <?php if (!empty($errors)): ?>
  toggleEditForm();
  <?php endif; ?>

  // Show/hide social link field in the edit form
  var sel  = document.getElementById('prof_type_compte');
  var bloc = document.getElementById('prof_bloc_social');
  if (sel && bloc) {
    sel.addEventListener('change', function() {
      bloc.style.display = (sel.value === 'createur') ? 'block' : 'none';
    });
  }
})();
</script>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
