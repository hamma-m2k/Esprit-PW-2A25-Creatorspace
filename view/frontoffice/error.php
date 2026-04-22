<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CreatorSpace — Erreur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/variables.css" />
  <link rel="stylesheet" href="assets/css/base.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
</head>
<body style="display:flex; flex-direction:column; min-height:100vh;
             background:var(--bg); align-items:center; justify-content:center;">

  <nav class="topnav" id="topnav" style="position:fixed; top:0; left:0; right:0;">
    <div class="topnav-logo">✦ CreatorSpace</div>
    <div></div>
  </nav>

  <div style="margin-top:80px; width:100%; max-width:440px; padding:0 16px;">
    <div style="background:var(--card); border:1px solid var(--border);
                border-radius:var(--radius); padding:40px;
                box-shadow:var(--shadow-lg); text-align:center;">

      <h2 style="font-family:'Syne',sans-serif; font-size:1.6rem;
                 font-weight:800; color:var(--text); margin-bottom:12px;">Erreur</h2>
      <div style="background:rgba(229,62,62,0.12); border:1px solid var(--danger);
                  color:var(--danger); border-radius:var(--radius-sm);
                  padding:12px 14px; font-size:0.88rem; margin-bottom:24px;">
        <?= htmlspecialchars($message ?? 'Une erreur est survenue.') ?>
      </div>
      <a href="index.php?ctrl=auth&action=login" class="btn btn-primary w-full" style="display:inline-block; text-decoration:none;">
        Retour à la connexion
      </a>
    </div>
  </div>

</body>
</html>
