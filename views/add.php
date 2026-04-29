<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Création contrat</title>
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
        <h2>Créer un nouveau contrat</h2>
        
        <?php if (isset($erreur)): ?>
            <div class="alert"><?php echo $erreur; ?></div>
        <?php endif; ?>

        <!-- NO HTML5 Validation allowed (No 'required', strictly 'text' input types) -->
        <form method="POST" action="index.php?action=add">
            <div class="form-row">
                <div class="form-group">
                    <label>Titre de contrat (Libellé)</label>
                    <input type="text" name="titre" value="">
                </div>
                <div class="form-group">
                    <label>Date de signature (JJ/MM/AAAA)</label>
                    <input type="text" name="date" value="">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label>Veuillez lier un Auteur (clé étrangère PHP)</label>
                <select name="auteur_id">
                    <option value="">Sélectionnez un auteur (jointure...)</option>
                    <?php foreach ($auteurs as $a): ?>
                        <option value="<?php echo $a['id']; ?>"><?php echo htmlspecialchars($a['nom']); ?> (<?php echo htmlspecialchars($a['email']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Contenu et réglement du contrat</label>
                <textarea name="contenu" rows="6"></textarea>
            </div>

            <div style="margin-top: 30px;">
                <input type="submit" class="btn" value="Enregistrer le contrat sécurisé">
                <a href="index.php?action=admin" style="color: #a0aec0; margin-left: 20px; text-decoration: none;">Retour au panel</a>
            </div>
        </form>
    </div>
</body>
</html>
