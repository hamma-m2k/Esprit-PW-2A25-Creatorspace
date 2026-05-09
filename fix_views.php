<?php
function processView($file) {
    if (!file_exists($file)) return;
    $content = file_get_contents($file);

    // Fix query string
    $content = str_replace('?contrat_id=', '&contrat_id=', $content);
    // Fix action names
    $content = str_replace('action=createRule&', 'action=createRuleForm&', $content);
    $content = str_replace('action=createRule"', 'action=createRuleForm"', $content);
    $content = str_replace('action=rules/create', 'action=createRuleForm', $content);

    file_put_contents($file, $content);
    echo "Processed $file\n";
}

$files = [
    __DIR__ . '/View/backoffice/contrats/index.php',
    __DIR__ . '/View/backoffice/contrats/form.php',
    __DIR__ . '/View/backoffice/contrats/show.php',
    __DIR__ . '/View/backoffice/rules/index.php',
    __DIR__ . '/View/backoffice/rules/form.php',
];

foreach ($files as $f) {
    processView($f);
}
echo "Done fixing views!";
