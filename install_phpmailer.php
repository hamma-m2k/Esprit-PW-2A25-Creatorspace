<?php
/**
 * Script temporaire pour télécharger PHPMailer.
 * Ouvrez http://localhost/projetwebhamma/install_phpmailer.php dans votre navigateur.
 * Supprimez ce fichier après utilisation.
 */

$dir = __DIR__ . '/View/lib/phpmailer/';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$files = [
    'PHPMailer.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/PHPMailer.php',
    'SMTP.php'      => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/SMTP.php',
    'Exception.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/Exception.php',
];

echo "<h2>Installation de PHPMailer</h2>";

$allOk = true;
foreach ($files as $name => $url) {
    $content = @file_get_contents($url);
    if ($content === false) {
        // Fallback avec cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $content = curl_exec($ch);
        curl_close($ch);
    }

    if ($content && strlen($content) > 100) {
        file_put_contents($dir . $name, $content);
        echo "<p style='color:green;'>✅ $name téléchargé (" . strlen($content) . " octets)</p>";
    } else {
        echo "<p style='color:red;'>❌ Échec du téléchargement de $name</p>";
        $allOk = false;
    }
}

if ($allOk) {
    echo "<h3 style='color:green;'>✅ PHPMailer installé avec succès !</h3>";
    echo "<p>Vous pouvez maintenant <strong>supprimer ce fichier</strong> (install_phpmailer.php) et retourner à l'application.</p>";
    echo "<p><a href='index.php?ctrl=auth&action=login'>← Retour au login</a></p>";
} else {
    echo "<h3 style='color:red;'>⚠️ Certains fichiers n'ont pas pu être téléchargés.</h3>";
}
