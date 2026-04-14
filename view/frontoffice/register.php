<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CreatorSpace — S'inscrire</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/variables.css" />
  <link rel="stylesheet" href="assets/css/base.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
</head>
<body style="display:flex; flex-direction:column; min-height:100vh;
             background:var(--bg); align-items:center; justify-content:center;">

  <nav class="topnav" style="position:fixed; top:0; left:0; right:0;">
    <div class="topnav-logo" onclick="window.location='index.php'">✦ CreatorSpace</div>
    <div></div>
  </nav>

  <div style="margin-top:80px; width:100%; max-width:480px; padding:0 16px 40px;">
    <div style="background:var(--card); border:1px solid var(--border);
                border-radius:var(--radius); padding:40px;
                box-shadow:var(--shadow-lg);">

      <h2 style="font-family:'Syne',sans-serif; font-size:1.6rem; font-weight:800;
                 color:var(--text); text-align:center; margin-bottom:6px;">
        Créer un compte
      </h2>
      <p style="text-align:center; color:var(--text3); font-size:0.88rem; margin-bottom:28px;">
        Rejoignez la communauté CreatorSpace
      </p>

      <!-- POST → ctrl=user&action=register — NO HTML5 attributes, rôle forcé 'user' -->
      <form method="POST" action="index.php?ctrl=user&action=register">

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

        <button type="submit" class="btn btn-primary w-full" style="margin-top:8px;">
          Créer mon compte →
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
