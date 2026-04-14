<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="office active" id="front-office">

  <!-- HERO -->
  <section id="hero-section">
    <div class="hero">
      <div class="hero-particles" id="hero-particles"></div>
      <div class="hero-content">
        <div class="hero-badge">✦ Plateforme #1 des créateurs francophones</div>
        <h1>Crée. Publie.<br/>Monétise.</h1>
        <p>CreatorSpace réunit créateurs de contenu et marques dans un espace unique. Gérez votre présence, développez votre audience et monétisez votre passion.</p>
        <div class="hero-ctas">
          <a href="index.php?ctrl=user&action=profile"><button class="btn btn-primary">👤 Voir un profil</button></a>
          <button class="btn btn-outline" onclick="window.location='index.php?ctrl=user&action=register'">🚀 Commencer gratuitement</button>
        </div>
        <div class="hero-stats">
          <div class="hero-stat"><strong>12K+</strong><span>Créateurs</span></div>
          <div class="hero-stat-sep"></div>
          <div class="hero-stat"><strong>4.2M</strong><span>Vues/mois</span></div>
          <div class="hero-stat-sep"></div>
          <div class="hero-stat"><strong>98%</strong><span>Satisfaction</span></div>
        </div>
      </div>
      <div class="hero-visual">
        <div class="hero-card-float card-1">
          <div class="hcf-avatar" style="background:linear-gradient(135deg,#6C3FC5,#9B5DE5)">SM</div>
          <div><div class="hcf-name">Sophie Martin</div><div class="hcf-sub">248K abonnés</div></div>
        </div>
        <div class="hero-card-float card-2">
          <span>📈</span><div><div class="hcf-name">+12.5%</div><div class="hcf-sub">Ce mois</div></div>
        </div>
        <div class="hero-card-float card-3">
          <span>🎯</span><div><div class="hcf-name">Nouveau contrat</div><div class="hcf-sub">Marque Nike</div></div>
        </div>
      </div>
    </div>

    <!-- FEATURES STRIP -->
    <div class="features-strip">
      <div class="feature-item"><span>🎨</span><div><strong>Profil créateur</strong><p>Personnalisez votre espace</p></div></div>
      <div class="feature-item"><span>📊</span><div><strong>Analytics avancés</strong><p>Suivez vos performances</p></div></div>
      <div class="feature-item"><span>💰</span><div><strong>Monétisation</strong><p>Gérez vos revenus</p></div></div>
      <div class="feature-item"><span>🤝</span><div><strong>Collaborations</strong><p>Connectez-vous aux marques</p></div></div>
    </div>
  </section>

  <!-- AUTH -->
  <section id="auth-section" style="padding:60px 0;">
    <div class="section-wrap auth-wrap">
      <div class="auth-left">
        <h2>Rejoignez la communauté</h2>
        <p>Des milliers de créateurs font confiance à CreatorSpace pour développer leur présence en ligne.</p>
        <div class="auth-testimonials">
          <div class="testimonial">
            <div class="t-avatar" style="background:linear-gradient(135deg,#6C3FC5,#9B5DE5)">LB</div>
            <div><p>"CreatorSpace a transformé ma façon de gérer mes collaborations."</p><strong>Lucas Bernard</strong></div>
          </div>
          <div class="testimonial">
            <div class="t-avatar" style="background:linear-gradient(135deg,#00C2CB,#00a8b0)">ED</div>
            <div><p>"Interface intuitive, analytics puissants. Je recommande !"</p><strong>Emma Dubois</strong></div>
          </div>
        </div>
      </div>
      <div class="auth-card">
        <div class="auth-tabs">
          <button class="auth-tab active" onclick="switchAuthTab('login')">Connexion</button>
          <button class="auth-tab" onclick="switchAuthTab('register')">Inscription</button>
        </div>

        <!-- LOGIN -->
        <div class="auth-form active" id="tab-login">
          <h2>Bon retour 👋</h2>
          <p class="auth-sub">Connectez-vous à votre espace créateur</p>
          <form method="POST" action="index.php?page=login">
            <div class="form-group">
              <label>Adresse email</label>
              <div class="input-icon-wrap">
                <span class="input-icon">✉️</span>
                <input type="email" name="email" placeholder="vous@exemple.com" value="sophie.martin@gmail.com" required />
              </div>
            </div>
            <div class="form-group">
              <label>Mot de passe</label>
              <div class="input-icon-wrap">
                <span class="input-icon">🔒</span>
                <input type="password" name="password" placeholder="••••••••" value="password123" required />
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-full">Se connecter →</button>
          </form>
          <p class="auth-switch">Pas encore de compte ? <a href="#" class="link-accent" onclick="switchAuthTab('register');return false;">S'inscrire gratuitement</a></p>
        </div>

        <!-- REGISTER -->
        <div class="auth-form" id="tab-register">
          <h2>Rejoignez-nous ✨</h2>
          <p class="auth-sub">Créez votre compte en quelques secondes</p>
          <form method="POST" action="index.php?page=register">
            <div class="form-row-2">
              <div class="form-group"><label>Prénom</label><input type="text" name="firstname" placeholder="Sophie" required /></div>
              <div class="form-group"><label>Nom</label><input type="text" name="lastname" placeholder="Martin" required /></div>
            </div>
            <div class="form-group"><label>Adresse email</label><input type="email" name="email" placeholder="vous@exemple.com" required /></div>
            <div class="form-group">
              <label>Mot de passe</label>
              <input type="password" name="password" placeholder="Minimum 8 caractères" oninput="updatePasswordStrength(this.value)" required />
              <div class="password-strength"><div class="pwd-bar" id="pwd-bar"></div></div>
              <span class="pwd-label" id="pwd-label"></span>
            </div>
            <div class="form-group">
              <label>Type de compte</label>
              <div class="account-type-selector">
                <label class="account-type active" id="type-creator">
                  <input type="radio" name="account_type" value="creator" checked style="display:none;" />
                  <span class="type-icon">🎨</span><span class="type-label">Créateur</span><span class="type-desc">Publiez du contenu</span>
                </label>
                <label class="account-type" id="type-brand">
                  <input type="radio" name="account_type" value="brand" style="display:none;" />
                  <span class="type-icon">🏢</span><span class="type-label">Marque</span><span class="type-desc">Trouvez des créateurs</span>
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" name="terms" required />
                J'accepte les <a href="#" class="link-accent">conditions d'utilisation</a>
              </label>
            </div>
            <button type="submit" class="btn btn-primary w-full">Créer mon compte →</button>
          </form>
          <p class="auth-switch">Déjà un compte ? <a href="#" class="link-accent" onclick="switchAuthTab('login');return false;">Se connecter</a></p>
        </div>
      </div>
    </div>
  </section>

</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
