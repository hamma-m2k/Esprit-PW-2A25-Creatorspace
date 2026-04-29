<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Creator Space - Accueil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <h1>Creator Space</h1>
        <div class="nav-links">
            <a href="index.php?action=list" class="active">FrontOffice</a>
            <a href="index.php?action=admin">BackOffice (Admin)</a>
        </div>
    </nav>
    <div class="container animate-fade-in">
        <h2>Contrats Disponibles M2</h2>
        <div class="card-grid">
            <?php foreach ($contrats as $c): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($c['titre']); ?></h3>
                <p class="meta">✍️ <?php echo htmlspecialchars($c['auteur_nom']); ?> &nbsp;|&nbsp; 🗓️ <?php echo htmlspecialchars($c['date']); ?></p>
                <div style="margin-top:auto;">
                    <a href="index.php?action=detail&id=<?php echo $c['id']; ?>" class="btn" style="width:100%; margin-top: 15px;">🔍 Voir les détails</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
