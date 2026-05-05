<?php
$srcDir = __DIR__ . '/Controller/phpmailer';
$destDir = __DIR__ . '/lib/phpmailer';

if (!is_dir($destDir)) {
    mkdir($destDir, 0777, true);
}

$files = ['Exception.php', 'PHPMailer.php', 'SMTP.php'];
foreach ($files as $file) {
    if (file_exists("$srcDir/$file")) {
        rename("$srcDir/$file", "$destDir/$file");
    }
}
if (is_dir($srcDir)) {
    rmdir($srcDir);
}
echo "Files moved successfully.";
