<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CreatorSpace — Connexion</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/variables.css" />
  <link rel="stylesheet" href="assets/css/base.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
</head>
<body style="display:flex; flex-direction:column; min-height:100vh;
             background:var(--bg); align-items:center; justify-content:center;">

  <!-- NAVBAR -->
  <nav class="topnav" id="topnav" style="position:fixed; top:0; left:0; right:0;">
    <div class="topnav-logo">✦ CreatorSpace</div>
    <div></div>
  </nav>

  <!-- LOGIN CARD -->
  <div style="margin-top:80px; width:100%; max-width:440px; padding:0 16px;">
    <div style="background:var(--card); border:1px solid var(--border);
                border-radius:var(--radius); padding:40px;
                box-shadow:var(--shadow-lg);">

      <h2 style="font-family:'Syne',sans-serif; font-size:1.6rem;
                 font-weight:800; color:var(--text); text-align:center;
                 margin-bottom:6px;">Connexion</h2>
      <p style="text-align:center; color:var(--text3); font-size:0.88rem;
                margin-bottom:28px;">Accès réservé aux membres</p>

      <?php if (!empty($error)): ?>
      <div style="background:rgba(229,62,62,0.12); border:1px solid var(--danger);
                  color:var(--danger); border-radius:var(--radius-sm);
                  padding:12px 14px; font-size:0.88rem; margin-bottom:20px;
                  text-align:center;">
        <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($msgRegister)): ?>
      <div style="background:rgba(56,161,105,0.12); border:1px solid var(--success);
                  color:var(--success); border-radius:var(--radius-sm);
                  padding:12px 14px; font-size:0.88rem; margin-bottom:20px;
                  text-align:center;">
        <?= htmlspecialchars($msgRegister) ?>
      </div>
      <?php endif; ?>

      <?php if (isset($_GET['error']) && $_GET['error'] === 'access'): ?>
      <div style="background:rgba(229,62,62,0.12); border:1px solid var(--danger);
                  color:var(--danger); border-radius:var(--radius-sm);
                  padding:12px 14px; font-size:0.88rem; margin-bottom:20px;
                  text-align:center;">
        Accès refusé. Veuillez vous connecter.
      </div>
      <?php endif; ?>

      <!-- action pointe vers ctrl=auth&action=login -->
      <!-- name="mail" et name="password" — PAS type="email", PAS required -->
      <form method="POST" action="index.php?ctrl=auth&action=login">

        <div class="form-group">
          <label>Adresse mail</label>
          <div class="input-icon-wrap">
            <span class="input-icon">✉️</span>
            <!-- type="text" — NOT type="email", NO required, NO pattern -->
            <input type="text" id="mail" name="mail"
                   placeholder="exemple@gmail.com"
                   value="<?= htmlspecialchars($_POST['mail'] ?? '') ?>" />
          </div>
        </div>

        <div class="form-group">
          <label>Mot de passe</label>
          <div class="input-icon-wrap">
            <span class="input-icon">🔒</span>
            <input type="password" id="password" name="password"
                   placeholder="••••" />
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-full" style="margin-top:8px;">
          Se connecter →
        </button>

      </form>

      <?php if (!empty($success)): ?>
      <div style="background:rgba(56,161,105,0.12); border:1px solid var(--success);
                  color:var(--success); border-radius:var(--radius-sm);
                  padding:12px 14px; font-size:0.88rem; margin-top:16px; text-align:center;">
        <?= htmlspecialchars($success) ?>
      </div>
      <?php endif; ?>

      <p style="text-align:center; color:var(--text3); font-size:0.85rem; margin-top:20px;">
        Pas encore de compte ?
        <a href="index.php?ctrl=auth&action=register" class="link-accent">S'inscrire</a>
      </p>

      <p style="text-align:center; color:var(--text3); font-size:0.78rem; margin-top:12px;">
        Seul l'administrateur accède au back office.
      </p>
    </div>
  </div>

</body>
</html>
