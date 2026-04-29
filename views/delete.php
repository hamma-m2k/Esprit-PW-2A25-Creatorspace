<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Alerte - Suppression</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .alerte-box {
            background: rgba(255, 71, 87, 0.1);
            border: 2px solid #ff4757;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>Creator Space</h1>
        <div class="nav-links">
            <a href="index.php?action=list">FrontOffice</a>
            <a href="index.php?action=admin" class="active">BackOffice (Admin)</a>
        </div>
    </nav>
    <div class="container" style="max-width: 600px;">
        <div class="alerte-box">
            <h2 style="color: #ff4757; margin-bottom: 20px;">⚠️ Zone de Danger</h2>
            <p style="color: #fff; margin-bottom: 30px; font-size: 1.1rem;">
                Êtes-vous certain de vouloir supprimer ce contrat ?<br>
                <strong style="font-size: 1.3rem; display: block; margin-top: 10px;">"<?php echo htmlspecialchars($contrat['titre']); ?>"</strong>
            </p>
            
            <form method="POST" action="index.php?action=delete&id=<?php echo $contrat['id']; ?>" style="display: inline-block;">
                <input type="hidden" name="confirm" value="1">
                <input type="submit" class="btn btn-danger" value="Oui, je supprime">
            </form>
            <a href="index.php?action=admin" class="btn" style="margin-left: 15px; background: rgba(255,255,255,0.1); box-shadow: none;">Non, annuler</a>
        </div>
    </div>
</body>
</html>
