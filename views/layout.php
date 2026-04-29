<?php
/**
 * Layout/Template Principal
 * 
 * Ce fichier contient la structure HTML commune à toutes les pages.
 * Les vues individuelles sont incluses dans le contenu principal.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CreatorSpace - Gestion des Contrats</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars('/css/styles.css'); ?>">
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="navbar-logo">
                <a href="index.php?action=index">CreatorSpace</a>
            </h1>
            <ul class="nav-menu">
                <li>
                    <a href="index.php?action=index" class="nav-link <?php echo ($action === 'index') ? 'active' : ''; ?>">
                        Accueil
                    </a>
                </li>
                <li>
                    <a href="index.php?action=create" class="nav-link <?php echo ($action === 'create') ? 'active' : ''; ?>">
                        ➕ Nouveau Contrat
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Conteneur principal -->
    <main class="main-container">
        <!-- Zone des messages flash/alertes -->
        <?php if (isset($_SESSION['succes'])): ?>
            <div class="alert alert-success">
                <strong>✓ Succès :</strong> <?php echo htmlspecialchars($_SESSION['succes']); ?>
            </div>
            <?php unset($_SESSION['succes']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['erreur'])): ?>
            <div class="alert alert-error">
                <strong>✗ Erreur :</strong> <?php echo htmlspecialchars($_SESSION['erreur']); ?>
            </div>
            <?php unset($_SESSION['erreur']); ?>
        <?php endif; ?>

        <!-- Contenu principal (changé selon l'action) -->
        <section class="content">
            <?php
            // Les vues appelées par le contrôleur remplacent cette section
            // Voir les fichiers views/contrat/*.php
            ?>
        </section>
    </main>

    <!-- Pied de page -->
    <footer class="footer">
        <p>&copy; 2024 CreatorSpace. Gestion simple et efficace des contrats.</p>
    </footer>
</body>
</html>
