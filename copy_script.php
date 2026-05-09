<?php
function copyDir($src, $dst) {
    if (!is_dir($dst)) mkdir($dst, 0777, true);
    $dir = opendir($src);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copyDir($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

copyDir(__DIR__ . '/Esprit-PW-2A25-Creatorspace/Views/backoffice/contrats', __DIR__ . '/View/backoffice/contrats');
copyDir(__DIR__ . '/Esprit-PW-2A25-Creatorspace/Views/backoffice/rules', __DIR__ . '/View/backoffice/rules');
echo "Copied views successfully!";
