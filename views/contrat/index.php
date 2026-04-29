<?php
/**
 * Vue Index (Liste des Contrats)
 * Affiche tous les contrats disponibles dans un tableau.
 */
?>

<h2>📋 Liste des Contrats</h2>

<?php if (empty($contrats)): ?>
    <div class="empty-state">
        <p>Aucun contrat trouvé. <a href="index.php?action=create">Créez le premier contrat →</a></p>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contrats as $c): ?>
                    <tr>
                        <td class="id-cell">#<?php echo htmlspecialchars($c['id']); ?></td>
                        <td class="title-cell">
                            <strong><?php echo htmlspecialchars($c['titre']); ?></strong>
                        </td>
                        <td class="author-cell">
                            <?php echo htmlspecialchars($c['auteur_nom'] ?? 'Non spécifié'); ?>
                        </td>
                        <td class="date-cell">
                            <?php echo date('d/m/Y', strtotime($c['date_creation'])); ?>
                        </td>
                        <td class="action-cell">
                            <a href="index.php?action=show&id=<?php echo $c['id']; ?>" 
                               class="btn btn-view" title="Voir le détail">
                                👁️ Voir
                            </a>
                            <a href="index.php?action=edit&id=<?php echo $c['id']; ?>" 
                               class="btn btn-edit" title="Modifier le contrat">
                                ✏️ Modifier
                            </a>
                            <a href="index.php?action=delete&id=<?php echo $c['id']; ?>" 
                               class="btn btn-delete" title="Supprimer le contrat"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce contrat ?');">
                                🗑️ Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div class="action_footer">
    <a href="index.php?action=create" class="btn btn-primary">
        ➕ Ajouter un nouveau contrat
    </a>
</div>
                <a class="btn btn-modifier" href="index.php?action=edit&id=<?= (int) $c['id'] ?>">Modifier</a>
                <a class="btn btn-suppr"    href="index.php?action=delete&id=<?= (int) $c['id'] ?>"
                   onclick="return confirm('Supprimer ce contrat et toutes ses règles ?')">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

</body>
</html>
