<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — CreatorSpace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/Views/assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-bg"></div>

  <div class="auth-card">
    <div class="auth-badge">ADMIN PANEL</div>

    <div class="auth-logo">
      <div class="logo-mark">
        <span class="star">✦</span>
        Creator<span>Space</span>
      </div>
    </div>

    <div class="auth-title">
      <h1>Connexion</h1>
      <p>Accédez à votre espace d'administration</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/login" novalidate>
      <?= Csrf::field() ?>

      <div class="form-group">
        <label class="form-label" for="email">Adresse Email</label>
        <input
          id="email"
          class="form-control"
          type="email"
          name="email"
          placeholder="votre@email.com"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          autocomplete="email"
          required
          maxlength="180"
        >
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Mot de Passe</label>
        <input
          id="password"
          class="form-control"
          type="password"
          name="password"
          placeholder="••••••••"
          autocomplete="current-password"
          required
          minlength="6"
          maxlength="100"
        >
      </div>

      <div class="check-row">
        <label class="form-check">
          <input type="checkbox" name="remember" value="1">
          Se souvenir de moi
        </label>
        <a href="#" class="auth-link">Mot de passe oublié ?</a>
      </div>

      <button type="submit" class="btn btn-primary btn-block">
        Se connecter ↗
      </button>

    </form>

    <div class="auth-divider"></div>

    <button type="button" class="btn btn-google" onclick="alert('Google OAuth non configuré')">
      <span class="g-icon">🔵</span> Continuer avec Google
    </button>

    <div class="auth-footer-text">
      Pas encore de compte ?
      <a href="<?= BASE_URL ?>/register" class="auth-link">Faire une demande d'inscription</a>
    </div>

  </div>
</div>
</body>
</html>
