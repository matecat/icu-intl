<?php

$jsonFile = 'src/Locales/supported_langs.json';
$data = json_decode(file_get_contents($jsonFile), true);

if (!isset($data['langs'])) {
    die("Key 'langs' not found in JSON.\n");
}

usort($data['langs'], function($a, $b) {
    $nameA = $a['localized'][0]['en'] ?? '';
    $nameB = $b['localized'][0]['en'] ?? '';
    return strcasecmp($nameA, $nameB);
});

file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");

echo "Sorted successfully.\n";
