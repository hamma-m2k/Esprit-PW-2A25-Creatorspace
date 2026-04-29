<?php
/**
 * Vue Create (Créer un Contrat)
 * Affiche le formulaire de création d'un nouveau contrat.
 */
?>

<h2>➕ Ajouter un nouveau contrat</h2>

<?php
// Afficher les erreurs de validation s'il y en a
if (!empty($erreurs)):
?>
    <div class="error-box">
        <h3>⚠️ Erreurs de validation :</h3>
        <ul class="error-list">
            <?php foreach ($erreurs as $champ => $message): ?>
                <li><?php echo htmlspecialchars($message); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" class="form form-large">
    <!-- Titre -->
    <div class="form-group">
        <label for="titre">
            Titre du contrat <span class="required">*</span>
        </label>
        <input 
            type="text" 
            id="titre" 
            name="titre" 
            class="form-control <?php echo isset($erreurs['Titre']) ? 'is-invalid' : ''; ?>"
            placeholder="Ex: Contrat de collaboration avec Jean Dupont"
            value="<?php echo htmlspecialchars($contrat['titre'] ?? ''); ?>"
            minlength="3"
            maxlength="255"
            required
        >
        <?php if (isset($erreurs['Titre'])): ?>
            <small class="error-text"><?php echo htmlspecialchars($erreurs['Titre']); ?></small>
        <?php endif; ?>
    </div>

    <!-- Auteur -->
    <div class="form-group">
        <label for="auteur_id">
            Auteur <span class="required">*</span>
        </label>
        <select 
            id="auteur_id" 
            name="auteur_id" 
            class="form-control <?php echo isset($erreurs['auteur_id']) ? 'is-invalid' : ''; ?>"
            required
        >
            <option value="">-- Sélectionnez un auteur --</option>
            <?php foreach ($auteurs as $auteur): ?>
                <option 
                    value="<?php echo $auteur['id']; ?>"
                    <?php echo isset($contrat['auteur_id']) && $contrat['auteur_id'] == $auteur['id'] ? 'selected' : ''; ?>
                >
                    <?php echo htmlspecialchars($auteur['nom'] . ' (' . $auteur['email'] . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($erreurs['auteur_id'])): ?>
            <small class="error-text"><?php echo htmlspecialchars($erreurs['auteur_id']); ?></small>
        <?php endif; ?>
    </div>

    <!-- Contenu -->
    <div class="form-group">
        <label for="contenu">
            Contenu du contrat <span class="required">*</span>
        </label>
        <textarea 
            id="contenu" 
            name="contenu" 
            class="form-control <?php echo isset($erreurs['Contenu']) ? 'is-invalid' : ''; ?>"
            rows="8"
            placeholder="Décrivez les termes et conditions du contrat..."
            minlength="10"
            required
        ><?php echo htmlspecialchars($contrat['contenu'] ?? ''); ?></textarea>
        <small class="form-hint">Minimum 10 caractères</small>
        <?php if (isset($erreurs['Contenu'])): ?>
            <small class="error-text"><?php echo htmlspecialchars($erreurs['Contenu']); ?></small>
        <?php endif; ?>
    </div>

    <!-- Boutons d'action -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-large">
            ✅ Créer le contrat
        </button>
        <a href="index.php?action=index" class="btn btn-secondary">
            ❌ Annuler
        </a>
    </div>
</form>

<p class="form-info">
    <strong>Note:</strong> Tous les champs marqués avec un <span class="required">*</span> sont obligatoires.
</p>

    <label for="id_collaborateur">ID Collaborateur *</label>
    <input type="text" id="id_collaborateur" name="id_collaborateur" required
           value="<?= htmlspecialchars($_POST['id_collaborateur'] ?? '') ?>">

    <!-- Section règles dynamiques -->
    <label>Règles du contrat</label>
    <div id="rules-container">
        <div class="rule-item">
            <input type="text" name="rules[]" placeholder="Description de la règle">
            <button type="button" class="btn btn-remove" onclick="removeRule(this)">−</button>
        </div>
    </div>
    <button type="button" class="btn btn-add" onclick="addRule()" style="margin-top:8px">+ Ajouter une règle</button>

    <br>
    <button type="submit" class="btn btn-primary">Créer le contrat</button>
    <a href="index.php?action=index" class="btn-back" style="margin-left:10px">Annuler</a>
</form>

<script>
    function addRule() {
        const container = document.getElementById('rules-container');
        const div = document.createElement('div');
        div.className = 'rule-item';
        div.innerHTML = `<input type="text" name="rules[]" placeholder="Description de la règle">
                         <button type="button" class="btn btn-remove" onclick="removeRule(this)">−</button>`;
        container.appendChild(div);
    }

    function removeRule(btn) {
        const container = document.getElementById('rules-container');
        // Garder au moins une ligne
        if (container.children.length > 1) {
            btn.parentElement.remove();
        }
    }
</script>

</body>
</html>
