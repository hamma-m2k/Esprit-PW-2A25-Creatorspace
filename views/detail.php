<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail - <?php echo htmlspecialchars($contrat['titre']); ?></title>
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
        <h2>📂 <?php echo htmlspecialchars($contrat['titre']); ?></h2>
        
        <p style="color: #a0aec0; margin-bottom: 20px;">
            Personne de contact : <strong style="color:#fff;"><?php echo htmlspecialchars($contrat['auteur_nom']); ?></strong><br>
            Date renseignée : <strong style="color:#fff;"><?php echo htmlspecialchars($contrat['date']); ?></strong>
        </p>
        
        <div class="content-box">
            <?php echo nl2br(htmlspecialchars($contrat['contenu'])); ?>
        </div>
        
        <a href="index.php?action=list" class="btn">Retour à la liste publique</a>
    </div>
</body>
</html>
