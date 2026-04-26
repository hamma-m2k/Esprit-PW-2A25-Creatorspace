<?php require_once __DIR__ . '/layout_back.php'; ?>

<?php
$initiales  = strtoupper(substr($item->getNom() ?: 'U', 0, 1) . substr($item->getPrenom() ?: 'U', 0, 1));
$fullName   = htmlspecialchars(($item->getNom() ?: '') . ' ' . ($item->getPrenom() ?: ''));
$handle     = '@' . strtolower(str_replace(' ', '_', trim(($item->getNom() ?: '') . '_' . ($item->getPrenom() ?: ''))));
$typeLabel  = match($item->getTypeCompte() ?: 'user') {
    'createur' => '🎬 Créateur de contenu',
    'societe'  => '🏢 Société',
    default    => '👤 Utilisateur',
};
$followers  = number_format((int)($item->getFollowers() ?: 0), 0, ',', ' ');
$following  = number_format((int)($item->getFollowing() ?: 0), 0, ',', ' ');
$socialLink = $item->getSocialMediaLink() ?: '';
$isCreateur = ($item->getTypeCompte() ?: '') === 'createur';
?>

<style>
/* ── Profile Instagram ───────────────── */
.ig-wrap        { max-width: 720px; margin: 0 auto; padding-bottom: 60px; }

/* Banner */
.ig-banner      { height: 160px; border-radius: 18px 18px 0 0;
                  background: linear-gradient(135deg, #00C2CB 0%, #6C3FC5 60%, #9B5DE5 100%);
                  position: relative; }

/* Card */
.ig-card        { background: rgba(255,255,255,0.05);
                  border: 1px solid rgba(108,63,197,0.35);
                  border-radius: 0 0 18px 18px;
                  padding: 0 32px 32px;
                  box-shadow: 0 12px 40px rgba(108,63,197,0.18); }

/* Avatar ring */
.ig-avatar      { width: 110px; height: 110px; border-radius: 50%;
                  background: linear-gradient(135deg,#00C2CB,#6C3FC5);
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
.btn-follow { display: inline-flex; align-items: center; gap: 8px;
                    background: #6C3FC5; border: none;
                    color: white; border-radius: 10px; padding: 10px 22px;
                    font-size: 0.9rem; font-weight: 600; cursor: pointer;
                    transition: all .2s; }
.btn-follow:hover { background: #55319e; transform: translateY(-1px); }

</style>

<div class="back-section active" id="back-profile">
  <div class="back-header" style="max-width: 720px; margin: 0 auto 20px;">
    <a href="index.php?ctrl=user&action=searchUsers">
      <button class="btn btn-outline btn-sm">← Retour à la recherche</button>
    </a>
  </div>

  <div class="ig-wrap">
    <!-- ── BANNER ── -->
    <div class="ig-banner"></div>

    <!-- ── CARD ── -->
    <div class="ig-card">

      <!-- Header : avatar + nom + badge + bouton follow -->
      <div class="ig-head">
        <div class="ig-avatar"><?= $initiales ?></div>
        <div class="ig-head-info">
          <h1 class="ig-name"><?= $fullName ?></h1>
          <p class="ig-handle"><?= htmlspecialchars($handle) ?></p>
          <span class="ig-badge"><?= $typeLabel ?></span>
        </div>
        <?php if ($item->getId() !== (int)($_SESSION['user_id'] ?? 0)): ?>
          <button class="btn-follow" onclick="alert('Fonctionnalité de suivi à venir !')">
            + Suivre
          </button>
        <?php endif; ?>
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
              <?= match($item->getTypeCompte() ?: 'user') {
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
      
      <!-- ── INSCRIPTION ── -->
      <div style="text-align:center; margin-top:30px; color:var(--text3); font-size:0.85rem;">
        Membre depuis le <?= htmlspecialchars(date('d/m/Y', strtotime($item->getCreatedAt() ?: 'now'))) ?>
      </div>

    </div><!-- /ig-card -->
  </div><!-- /ig-wrap -->
</div><!-- /back-section -->

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
