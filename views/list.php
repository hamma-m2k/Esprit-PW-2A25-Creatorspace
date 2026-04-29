<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Creator Space - Les Contrats</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        a { text-decoration: none; color: #0066cc; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>FrontOffice - Liste des contrats (Utilisateur)</h1>
    <ul>
        <?php foreach ($contrats as $c): ?>
            <li>
                <a href="index.php?action=detail&id=<?php echo $c['id']; ?>">
                    <strong><?php echo htmlspecialchars($c['titre']); ?></strong>
                </a>
                - par <?php echo htmlspecialchars($c['auteur_nom']); ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <hr>

    <h2>BackOffice - Gestion des contrats (Administrateur)</h2>
    <a href="index.php?action=add">+ Ajouter un nouveau contrat</a>
    <br><br>
    <table>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($contrats as $c): ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><?php echo htmlspecialchars($c['titre']); ?></td>
                <td><?php echo htmlspecialchars($c['auteur_nom']); ?></td>
                <td><?php echo htmlspecialchars($c['date']); ?></td>
                <td>
                    <a href="index.php?action=edit&id=<?php echo $c['id']; ?>">Modifier</a> |
                    <a href="index.php?action=delete&id=<?php echo $c['id']; ?>" style="color:red;">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
