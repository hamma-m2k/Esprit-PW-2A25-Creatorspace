<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le contrat</title>
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
        <h2>Modifier le contrat #<?php echo $contrat['id']; ?></h2>
        
        <?php if (isset($erreur)): ?>
            <div class="alert"><?php echo $erreur; ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=edit&id=<?php echo $contrat['id']; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Titre de contrat (Libellé)</label>
                    <input type="text" name="titre" value="<?php echo htmlspecialchars($contrat['titre']); ?>">
                </div>
                <div class="form-group">
                    <label>Date de signature (JJ/MM/AAAA)</label>
                    <input type="text" name="date" value="<?php echo htmlspecialchars($contrat['date']); ?>">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label>Veuillez lier un Auteur (clé étrangère PHP)</label>
                <select name="auteur_id">
                    <?php foreach ($auteurs as $a): ?>
                        <option value="<?php echo $a['id']; ?>" <?php if($a['id'] == $contrat['auteur_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($a['nom']); ?> (<?php echo htmlspecialchars($a['email']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Contenu et réglement du contrat</label>
                <textarea name="contenu" rows="6"><?php echo htmlspecialchars($contrat['contenu']); ?></textarea>
            </div>

            <div style="margin-top: 30px;">
                <input type="submit" class="btn" value="Mise à jour du contrat">
                <a href="index.php?action=admin" style="color: #a0aec0; margin-left: 20px; text-decoration: none;">Annuler et retourner au panel</a>
            </div>
        </form>
    </div>
</body>
</html>
