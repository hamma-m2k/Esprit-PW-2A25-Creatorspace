<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CreatorSpace — Inscription</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/variables.css" />
  <link rel="stylesheet" href="assets/css/base.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
</head>
<body style="display:flex; flex-direction:column; min-height:100vh;
             background:var(--bg); align-items:center; justify-content:center;">

  <!-- NAVBAR — même que login.php -->
  <nav class="topnav" id="topnav" style="position:fixed; top:0; left:0; right:0;">
    <div class="topnav-logo">✦ CreatorSpace</div>
    <div></div>
  </nav>

  <!-- REGISTER CARD — même style que login.php -->
  <div style="margin-top:80px; width:100%; max-width:440px; padding:0 16px 40px;">
    <div style="background:var(--card); border:1px solid var(--border);
                border-radius:var(--radius); padding:40px;
                box-shadow:var(--shadow-lg);">

      <h2 style="font-family:'Syne',sans-serif; font-size:1.6rem;
                 font-weight:800; color:var(--text); text-align:center;
                 margin-bottom:6px;">Inscription</h2>
      <p style="text-align:center; color:var(--text3); font-size:0.88rem;
                margin-bottom:28px;">Rejoins la communauté CreatorSpace</p>

      <!-- POST → ctrl=auth&action=register — NO HTML5 attributes -->
      <form method="POST" action="index.php?ctrl=auth&action=register">

        <div class="form-group">
          <label>Nom</label>
          <!-- type="text" — NO required, NO pattern -->
          <input type="text" name="nom"
                 placeholder="Ex: Marzougui"
                 value="<?= htmlspecialchars($old['nom'] ?? '') ?>" />
          <?php if (!empty($errors['nom'])): ?>
            <span style="color:var(--danger); font-size:0.8rem; margin-top:4px; display:block;">
              <?= htmlspecialchars($errors['nom']) ?>
            </span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Prénom</label>
          <input type="text" name="prenom"
                 placeholder="Ex: Mohamed"
                 value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" />
          <?php if (!empty($errors['prenom'])): ?>
            <span style="color:var(--danger); font-size:0.8rem; margin-top:4px; display:block;">
              <?= htmlspecialchars($errors['prenom']) ?>
            </span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Adresse mail</label>
          <!-- type="text" — NOT type="email" -->
          <input type="text" name="mail"
                 placeholder="exemple@gmail.com"
                 value="<?= htmlspecialchars($old['mail'] ?? '') ?>" />
          <?php if (!empty($errors['mail'])): ?>
            <span style="color:var(--danger); font-size:0.8rem; margin-top:4px; display:block;">
              <?= htmlspecialchars($errors['mail']) ?>
            </span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Mot de passe</label>
          <input type="password" name="password" placeholder="••••••••" />
          <?php if (!empty($errors['password'])): ?>
            <span style="color:var(--danger); font-size:0.8rem; margin-top:4px; display:block;">
              <?= htmlspecialchars($errors['password']) ?>
            </span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Type de compte</label>
          <select name="type_compte" id="reg_type_compte">
            <option value="" disabled <?= empty($old['type_compte']) ? 'selected' : '' ?>>-- Choisissez votre type --</option>
            <option value="user"     <?= ($old['type_compte'] ?? '') === 'user'     ? 'selected' : '' ?>>Utilisateur normal</option>
            <option value="societe"  <?= ($old['type_compte'] ?? '') === 'societe'  ? 'selected' : '' ?>>Société</option>
            <option value="createur" <?= ($old['type_compte'] ?? '') === 'createur' ? 'selected' : '' ?>>Créateur de contenu</option>
          </select>
          <?php if (!empty($errors['type_compte'])): ?>
            <span style="color:var(--danger); font-size:0.8rem; margin-top:4px; display:block;">
              <?= htmlspecialchars($errors['type_compte']) ?>
            </span>
          <?php endif; ?>
        </div>

        <!-- Bloc social media : visible SEULEMENT si type = "createur" -->
        <div id="bloc_social_register"
             style="display:<?= ($old['type_compte'] ?? '') === 'createur' ? 'block' : 'none' ?>;">

          <div class="form-group">
            <label>Plateforme sociale</label>
            <select name="social_media_platform" id="reg_platform">
              <option value="" disabled <?= empty($old['social_media_platform']) ? 'selected' : '' ?>>-- Choisissez une plateforme --</option>
              <option value="instagram" <?= ($old['social_media_platform'] ?? '') === 'instagram' ? 'selected' : '' ?>>Instagram</option>
              <option value="facebook"  <?= ($old['social_media_platform'] ?? '') === 'facebook'  ? 'selected' : '' ?>>Facebook</option>
              <option value="tiktok"    <?= ($old['social_media_platform'] ?? '') === 'tiktok'    ? 'selected' : '' ?>>TikTok</option>
              <option value="youtube"   <?= ($old['social_media_platform'] ?? '') === 'youtube'   ? 'selected' : '' ?>>YouTube</option>
            </select>
          </div>

          <div class="form-group">
            <label>Lien du réseau social</label>
            <input type="text" name="social_media_link" id="reg_social_link"
                   placeholder="https://www.instagram.com/moncompte"
                   value="<?= htmlspecialchars($old['social_media_link'] ?? '') ?>" />
            <?php if (!empty($errors['social_media_link'])): ?>
              <span style="color:var(--danger); font-size:0.8rem; margin-top:4px; display:block;">
                <?= htmlspecialchars($errors['social_media_link']) ?>
              </span>
            <?php endif; ?>
          </div>

        </div>

        <script>
          (function() {
            var sel   = document.getElementById('reg_type_compte');
            var bloc  = document.getElementById('bloc_social_register');
            function toggle() {
              bloc.style.display = (sel.value === 'createur') ? 'block' : 'none';
            }
            sel.onchange = toggle;
          })();
        </script>

        <button type="submit" class="btn btn-primary w-full" style="margin-top:8px;">
          S'inscrire →
        </button>

      </form>

      <p style="text-align:center; color:var(--text3); font-size:0.85rem; margin-top:20px;">
        Déjà un compte ?
        <a href="index.php?ctrl=auth&action=login" class="link-accent">Se connecter</a>
      </p>
    </div>
  </div>

</body>
</html>
