<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bienvenue — CreatorSpace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/Views/assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-bg"></div>

  <div class="auth-card" style="max-width: 560px;">
    <div class="auth-badge">FRONTOFFICE</div>

    <div class="auth-logo">
      <div class="logo-mark">
        <span class="star">✦</span>
        Creator<span>Space</span>
      </div>
    </div>

    <div class="auth-title">
      <h1>Bienvenue</h1>
      <p>Espace public avant authentification</p>
    </div>

    <p style="text-align:center; color:var(--text-muted); margin-bottom:24px;">
      Accédez à votre compte ou envoyez une demande d'inscription pour rejoindre la plateforme.
    </p>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
      <a href="<?= BASE_URL ?>/login" class="btn btn-primary" style="text-align:center; text-decoration:none;">
        Se connecter
      </a>
      <a href="<?= BASE_URL ?>/register" class="btn btn-google" style="text-align:center; text-decoration:none;">
        S'inscrire
      </a>
    </div>
  </div>
</div>
</body>
</html>
