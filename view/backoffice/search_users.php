<?php require_once __DIR__ . '/layout_back.php'; ?>

<div class="back-section active" id="back-search">
  <div class="back-header">
    <div>
      <h2 style="font-size:1.6rem; color:var(--text);">🔍 Rechercher des utilisateurs</h2>
      <p style="color:var(--text3); font-size:0.9rem; margin-top:4px;">
        Trouvez d'autres créateurs et utilisateurs de CreatorSpace
      </p>
    </div>
  </div>

  <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(108,63,197,0.3); border-radius:12px; padding:16px; margin-bottom:30px;">
    <form method="GET" action="index.php" style="display:flex; gap:12px;">
      <input type="hidden" name="ctrl" value="user">
      <input type="hidden" name="action" value="searchUsers">
      <input type="text" name="q" placeholder="Rechercher un nom ou prénom..." 
             value="<?= htmlspecialchars($term ?? '') ?>"
             style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid rgba(108,63,197,0.4); background:rgba(0,0,0,0.2); color:white; outline:none; font-size:1rem;">
      <button type="submit" class="btn btn-primary" style="padding:0 24px; font-size:1rem;">Rechercher</button>
    </form>
  </div>

  <?php if (!empty($term)): ?>
    <h3 style="margin-bottom: 20px; font-size:1.2rem; color:var(--text2);">
      Résultats pour "<?= htmlspecialchars($term) ?>" (<?= count($users) ?> trouvés)
    </h3>
  <?php endif; ?>

  <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
    <?php if (empty($users) && !empty($term)): ?>
      <div style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text3); background:rgba(255,255,255,0.02); border-radius:12px;">
        Aucun utilisateur ne correspond à votre recherche.
      </div>
    <?php endif; ?>

    <?php foreach ($users as $u): ?>
      <?php
      $initiales = strtoupper(substr($u->getNom() ?: 'U', 0, 1) . substr($u->getPrenom() ?: 'U', 0, 1));
      $typeLabel = match($u->getTypeCompte() ?: 'user') {
          'createur' => '🎬 Créateur',
          'societe'  => '🏢 Société',
          default    => '👤 Utilisateur',
      };
      ?>
      <div style="background:rgba(255,255,255,0.04); border:1px solid rgba(108,63,197,0.2); border-radius:16px; padding:24px; text-align:center; transition:transform 0.2s, box-shadow 0.2s;"
           onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 20px rgba(108,63,197,0.15)';"
           onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
        
        <div style="width:80px; height:80px; border-radius:50%; background:linear-gradient(135deg, #6C3FC5, #00C2CB); display:flex; align-items:center; justify-content:center; font-family:'Syne',sans-serif; font-size:1.8rem; font-weight:800; color:#fff; margin:0 auto 16px;">
          <?= $initiales ?>
        </div>
        
        <h3 style="font-size:1.2rem; margin:0 0 4px; color:var(--text);"><?= htmlspecialchars($u->getNom() . ' ' . $u->getPrenom()) ?></h3>
        <p style="font-size:0.85rem; color:var(--text3); margin:0 0 12px;"><?= $typeLabel ?></p>
        
        <div style="display:flex; justify-content:center; gap:16px; margin-bottom:20px; border-top:1px solid rgba(255,255,255,0.05); border-bottom:1px solid rgba(255,255,255,0.05); padding:12px 0;">
          <div>
            <div style="font-weight:700; color:var(--text);"><?= number_format($u->getFollowers(), 0, ',', ' ') ?></div>
            <div style="font-size:0.75rem; color:var(--text3);">Abonnés</div>
          </div>
          <div>
            <div style="font-weight:700; color:var(--text);"><?= number_format($u->getFollowing(), 0, ',', ' ') ?></div>
            <div style="font-size:0.75rem; color:var(--text3);">Abonnements</div>
          </div>
        </div>
        
        <a href="index.php?ctrl=user&action=publicProfile&id=<?= $u->getId() ?>" style="display:block;">
          <button class="btn btn-outline" style="width:100%; border-radius:8px;">Voir le profil</button>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
