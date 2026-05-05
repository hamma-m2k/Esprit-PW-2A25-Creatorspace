<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Double Authentification - CreatorSpace</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --primary: #6c3fc5;
            --text: #f8fafc;
            --text2: #94a3b8;
        }
        body {
            margin: 0;
            padding: 0;
            background: var(--bg);
            color: var(--text);
            font-family: 'Outfit', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .container {
            background: var(--card);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
        h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 10px;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        p {
            color: var(--text2);
            font-size: 0.95rem;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            color: var(--text2);
        }
        input {
            width: 100%;
            padding: 14px;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 1.2rem;
            text-align: center;
            letter-spacing: 5px;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(108, 63, 197, 0.1);
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s;
            margin-top: 10px;
        }
        .btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }
        .error {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--text2);
            text-decoration: none;
            font-size: 0.85rem;
        }
        .back-link:hover {
            color: var(--text);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Vérification</h2>
    <p>Un code de 6 chiffres a été envoyé à votre adresse email.</p>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form action="index.php?ctrl=auth&action=verify2FA" method="POST">
        <div class="form-group">
            <label for="code">Entrez le code de sécurité</label>
            <input type="text" name="code" id="code" maxlength="6" placeholder="000000" required autofocus autocomplete="off">
        </div>
        <button type="submit" class="btn">Vérifier et se connecter</button>
    </form>

    <a href="index.php?ctrl=auth&action=logout" class="back-link">Annuler et se déconnecter</a>
</div>

</body>
</html>
