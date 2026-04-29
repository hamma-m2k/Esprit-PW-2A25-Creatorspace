<?php
/**
 * Vue Show (Détail d'un Contrat)
 * Affiche les informations complètes d'un contrat spécifique.
 */
?>

<div class="detail-header">
    <h2><?php echo htmlspecialchars($contrat['titre']); ?></h2>
    <p class="detail-meta">
        Auteur: <strong><?php echo htmlspecialchars($contrat['auteur_nom'] ?? 'Non spécifié'); ?></strong> |
        Créé le: <strong><?php echo date('d/m/Y à H:i', strtotime($contrat['date_creation'])); ?></strong>
    </p>
</div>

<div class="detail-content">
    <section class="content-section">
        <h3>📝 Contenu</h3>
        <div class="text-content">
            <?php echo nl2br(htmlspecialchars($contrat['contenu'])); ?>
        </div>
    </section>

    <section class="content-section">
        <h3>ℹ️ Informations</h3>
        <div class="info-grid">
            <div class="info-item">
                <label>Identifiant:</label>
                <span><?php echo htmlspecialchars($contrat['id']); ?></span>
            </div>
            <div class="info-item">
                <label>Email de l'auteur:</label>
                <span><?php echo htmlspecialchars($contrat['auteur_email'] ?? 'Non disponible'); ?></span>
            </div>
            <div class="info-item">
                <label>Dernière modification:</label>
                <span><?php echo date('d/m/Y à H:i', strtotime($contrat['date_modification'])); ?></span>
            </div>
        </div>
    </section>
</div>

<div class="detail-actions">
    <a href="index.php?action=edit&id=<?php echo $contrat['id']; ?>" class="btn btn-primary">
        ✏️ Modifier ce contrat
    </a>
    <a href="index.php?action=index" class="btn btn-secondary">
        ← Retour à la liste
    </a>
    <a href="index.php?action=delete&id=<?php echo $contrat['id']; ?>" 
       class="btn btn-danger"
       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce contrat ?');">
        🗑️ Supprimer
    </a>
</div>
