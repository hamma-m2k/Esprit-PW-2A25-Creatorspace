<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Demande d'inscription — CreatorSpace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/Views/assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-bg"></div>

  <div class="auth-card" style="max-width:520px;">
    <div class="auth-badge">INSCRIPTION</div>

    <div class="auth-logo">
      <div class="logo-mark"><span class="star">✦</span> Creator<span>Space</span></div>
    </div>

    <div class="auth-title">
      <h1>Demande d'accès</h1>
      <p>Un administrateur examinera votre demande</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
    <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/register" novalidate>
      <?= Csrf::field() ?>

      <div class="grid-2" style="gap:16px;">
        <div class="form-group">
          <label class="form-label" for="firstname">Prénom *</label>
          <input id="firstname" class="form-control" type="text" name="firstname"
                 placeholder="Votre prénom"
                 value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>"
                 required minlength="2" maxlength="80"
                 pattern="[\p{L} \-']{2,80}">
        </div>
        <div class="form-group">
          <label class="form-label" for="lastname">Nom *</label>
          <input id="lastname" class="form-control" type="text" name="lastname"
                 placeholder="Votre nom"
                 value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>"
                 required minlength="2" maxlength="80"
                 pattern="[\p{L} \-']{2,80}">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="email">Adresse Email *</label>
        <input id="email" class="form-control" type="email" name="email"
               placeholder="votre@email.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               required maxlength="180">
      </div>

      <div class="form-group">
        <label class="form-label" for="account_type">Type de compte</label>
        <select id="account_type" class="form-control" name="account_type" required>
          <option value="standard">Standard</option>
          <option value="creator">Créateur</option>
          <option value="entreprise">Entreprise</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" for="message">Message (optionnel)</label>
        <textarea id="message" class="form-control" name="message" rows="3"
                  maxlength="1000"
                  placeholder="Décrivez votre besoin..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary btn-block">
        Envoyer la demande ↗
      </button>

    </form>

    <div class="auth-footer-text">
      Déjà un compte ? <a href="<?= BASE_URL ?>/login" class="auth-link">Se connecter</a>
    </div>
  </div>
</div>
</body>
</html>
