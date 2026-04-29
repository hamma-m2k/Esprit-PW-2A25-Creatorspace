<?php
/**
 * Vue Edit (Modifier un Contrat)
 * Affiche le formulaire de modification d'un contrat existant.
 */
?>

<h2>✏️ Modifier le contrat</h2>

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
            placeholder="Ex: Contrat de collaboration..."
            value="<?php echo htmlspecialchars($contrat['titre']); ?>"
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
                    <?php echo $contrat['auteur_id'] == $auteur['id'] ? 'selected' : ''; ?>
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
            rows="10"
            placeholder="Décrivez les termes et conditions du contrat..."
            minlength="10"
            required
        ><?php echo htmlspecialchars($contrat['contenu']); ?></textarea>
        <small class="form-hint">Minimum 10 caractères</small>
        <?php if (isset($erreurs['Contenu'])): ?>
            <small class="error-text"><?php echo htmlspecialchars($erreurs['Contenu']); ?></small>
        <?php endif; ?>
    </div>

    <!-- Informations additionnelles (lecture seule) -->
    <div class="info-box">
        <h4>📋 Informations du contrat</h4>
        <p><strong>Créé le:</strong> <?php echo date('d/m/Y à H:i', strtotime($contrat['date_creation'])); ?></p>
        <p><strong>Dernière modification:</strong> <?php echo date('d/m/Y à H:i', strtotime($contrat['date_modification'])); ?></p>
    </div>

    <!-- Boutons d'action -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-large">
            ✅ Enregistrer les modifications
        </button>
        <a href="index.php?action=show&id=<?php echo $contrat['id']; ?>" class="btn btn-secondary">
            ❌ Annuler
        </a>
    </div>
</form>

<p class="form-info">
    <strong>Note:</strong> Tous les champs marqués avec un <span class="required">*</span> sont obligatoires.
</p>
