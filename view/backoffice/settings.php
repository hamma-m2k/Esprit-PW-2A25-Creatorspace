<?php require_once __DIR__ . '/layout_back.php'; ?>

      <div class="back-section active" id="back-settings">
        <div class="back-header">
            <h2>Paramètres du compte</h2>
            <p>Gérez votre sécurité et vos préférences</p>
        </div>

        <?php if (!empty($successMsg)): ?>
            <div style="background: rgba(104, 211, 145, 0.1); border: 1px solid #68D391; color: #68D391; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                ✅ <?= $successMsg ?>
            </div>
        <?php endif; ?>

        <div class="settings-grid">
          <!-- SECTION SECURITE -->
          <div class="settings-card">
            <h4 style="color: var(--primary); margin-bottom: 20px; font-family: 'Syne', sans-serif;">🛡️ Contrôle et Confidentialité</h4>
            <div class="permissions-list">
              <div class="perm-row" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div>
                    <span style="display: block; font-weight: 600;">Double Authentification (2FA)</span>
                    <small style="color: var(--text2); font-size: 0.8rem;">Recevoir un code par email lors de la connexion</small>
                </div>
                <label class="toggle" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                    <input type="checkbox" onchange="window.location.href='index.php?ctrl=user&action=toggle2FA'" <?= $user->getTwoFactorEnabled() ? 'checked' : '' ?> style="opacity: 0; width: 0; height: 0;">
                    <span class="slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #334155; transition: .4s; border-radius: 24px;"></span>
                </label>
              </div>
            </div>
          </div>

          <!-- SECTION GENERALE (Admin only or shared) -->
          <div class="settings-card">
            <h4>🌐 Préférences</h4>
            <div class="form-group"><label>Langue de l'interface</label>
              <select style="width: 100%; padding: 10px; background: var(--bg); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white;">
                <option>Français</option>
                <option>English</option>
              </select>
            </div>
            <button class="btn btn-primary btn-sm" style="margin-top: 20px;" onclick="showToast('Paramètres sauvegardés !','success')">Sauvegarder</button>
          </div>
        </div>
      </div>

<style>
/* Slider styling if not in index.css */
.toggle input:checked + .slider { background-color: var(--primary); }
.slider:before {
  position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px;
  background-color: white; transition: .4s; border-radius: 50%;
}
.toggle input:checked + .slider:before { transform: translateX(26px); }
</style>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
