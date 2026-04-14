<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion — CreatorSpace</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
  font-family: 'Segoe UI', Arial, sans-serif;
  background: #f0f2f5;
  min-height: 100vh;
}
.navbar {
  background: #1a1a2e;
  height: 58px;
  display: flex;
  align-items: center;
  padding: 0 30px;
}
.navbar .logo { color: #fff; font-size: 18px; font-weight: 700; }
.navbar .logo span { color: #e8394d; }
.login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 58px);
}
.login-card {
  background: #ffffff;
  border-radius: 14px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.09);
  padding: 44px 40px;
  width: 420px;
}
.login-card h2 {
  text-align: center; font-size: 24px;
  font-weight: 800; color: #1a1a2e; margin-bottom: 6px;
}
.login-card .subtitle {
  text-align: center; color: #888;
  font-size: 13px; margin-bottom: 28px;
}
label { display: block; color: #555; font-size: 13px; margin-bottom: 6px; }
input {
  width: 100%; background: #eef2f7; border: none;
  border-radius: 8px; padding: 13px 14px;
  font-size: 14px; margin-bottom: 18px; outline: none;
}
input:focus { border: 1.5px solid #e8394d; background: #fff; }
.btn-login {
  width: 100%; background: #e8394d; color: white;
  border: none; border-radius: 8px; padding: 14px;
  font-size: 15px; font-weight: 700; cursor: pointer; margin-top: 4px;
}
.btn-login:hover { background: #d02f42; }
.login-note { text-align: center; color: #aaa; font-size: 12px; margin-top: 14px; }
.alert-error {
  background: #fff0f2; border: 1px solid #e8394d;
  color: #e8394d; border-radius: 8px;
  padding: 11px 14px; font-size: 13px;
  margin-bottom: 18px; text-align: center;
}
</style>
</head>
<body>

<nav class="navbar">
  <div class="logo"><span>✦</span> CreatorSpace</div>
</nav>

<div class="login-wrapper">
  <div class="login-card">
    <h2>Connexion</h2>
    <p class="subtitle">Réservé à l'administrateur</p>

    <?php if (!empty($error)): ?>
    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'access'): ?>
    <div class="alert-error">Accès refusé. Connectez-vous en tant qu'administrateur.</div>
    <?php endif; ?>

    <!-- onsubmit → validateLoginForm() inline — NO HTML5 attributes -->
    <form id="user-form" method="POST" action="index.php?action=login"
          onsubmit="return validateLoginForm();">

      <label>Adresse mail</label>
      <!-- type="text" — NOT type="email", NO required -->
      <input type="text" id="mail" name="mail"
             placeholder="exemple@gmail.com"
             value="<?= htmlspecialchars($_POST['mail'] ?? '') ?>">

      <label>Mot de passe</label>
      <input type="password" id="password" name="password" placeholder="••••">

      <button type="submit" class="btn-login">Se connecter</button>
      <p class="login-note">Seul l'administrateur peut accéder au tableau de bord.</p>
    </form>
  </div>
</div>

<script src="view/js/validate.js"></script>
<script>
function validateLoginForm() {
  var mail     = document.getElementById('mail').value.trim();
  var password = document.getElementById('password').value.trim();
  var errors   = [];

  if (mail === '')     errors.push("L'adresse mail est obligatoire.");
  if (password === '') errors.push("Le mot de passe est obligatoire.");
  if (mail !== '' && !/^[a-zA-Z0-9._%+\-]+@gmail\.com$/.test(mail))
    errors.push("Le mail doit être au format exemple@gmail.com.");
  if (password !== '' && !/^\d+$/.test(password))
    errors.push("Le mot de passe doit contenir uniquement des chiffres.");

  if (errors.length > 0) {
    var existing = document.getElementById('js-error');
    if (existing) existing.remove();
    var box = document.createElement('div');
    box.id = 'js-error';
    box.className = 'alert-error';
    box.innerHTML = errors.join('<br>');
    var form = document.getElementById('user-form');
    form.parentNode.insertBefore(box, form);
    return false;
  }
  return true;
}
</script>
</body>
</html>
