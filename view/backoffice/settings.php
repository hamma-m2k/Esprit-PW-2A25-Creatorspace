<?php require_once __DIR__ . '/layout_back.php'; ?>

      <div class="back-section active" id="back-settings">
        <div class="back-header"><h2>Paramètres système</h2><p>Configuration générale de la plateforme</p></div>
        <div class="settings-grid">
          <div class="settings-card">
            <h4>🌐 Général</h4>
            <div class="form-group"><label>Nom de la plateforme</label><input type="text" value="CreatorSpace" /></div>
            <div class="form-group"><label>Email de contact</label><input type="text" value="admin@creatorspace.fr" /></div>
            <div class="form-group"><label>Langue par défaut</label>
              <select><option>Français</option><option>English</option></select>
            </div>
            <button class="btn btn-primary btn-sm" onclick="showToast('Paramètres sauvegardés !','success')">Sauvegarder</button>
          </div>
          <div class="settings-card">
            <h4>🔔 Notifications</h4>
            <div class="permissions-list">
              <div class="perm-row"><span>Nouvelles inscriptions</span><label class="toggle"><input type="checkbox" checked /><span class="slider"></span></label></div>
              <div class="perm-row"><span>Alertes sécurité</span><label class="toggle"><input type="checkbox" checked /><span class="slider"></span></label></div>
              <div class="perm-row"><span>Rapports hebdomadaires</span><label class="toggle"><input type="checkbox" checked /><span class="slider"></span></label></div>
              <div class="perm-row"><span>Mises à jour système</span><label class="toggle"><input type="checkbox" /><span class="slider"></span></label></div>
            </div>
          </div>
        </div>
      </div>

<?php require_once __DIR__ . '/layout_back_end.php'; ?>
