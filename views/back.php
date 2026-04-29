<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Creator Space - BackOffice</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <h1>Creator Space</h1>
        <div class="nav-links">
            <a href="index.php?action=list">FrontOffice</a>
            <a href="index.php?action=admin" class="active">BackOffice (Admin)</a>
        </div>
    </nav>
    <div class="container animate-fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Système de Gestion des Contrats (Admin)</h2>
            <a href="index.php?action=add" class="btn">Créer un nouveau contrat</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Ref</th>
                    <th>Titre</th>
                    <th>Auteur / Participant</th>
                    <th>Date d'édition</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contrats as $c): ?>
                <tr>
                    <td>#<?php echo $c['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($c['titre']); ?></strong></td>
                    <td><?php echo htmlspecialchars($c['auteur_nom']); ?></td>
                    <td><?php echo htmlspecialchars($c['date']); ?></td>
                    <td>
                        <a href="index.php?action=edit&id=<?php echo $c['id']; ?>" class="btn" style="padding: 6px 12px; font-size: 0.8rem; margin-right: 5px;">Editer</a>
                        <a href="index.php?action=delete&id=<?php echo $c['id']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.8rem;">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
