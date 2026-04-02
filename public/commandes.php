<?php

$config = require __DIR__ . '/../src/config.php';
require __DIR__ . '/../src/http.php';

$baseUrl = $config['services']['commandes'];
$timeout = $config['http']['timeout'];

$url = $baseUrl . '/commandes';

$result = http_get($url, $timeout);

header("Content-Type: text/plain; charset=utf-8");

if (!$result['ok']) {
    echo "Erreur cURL: " . $result['error'] . "\n";
    exit;
}

echo "HTTP " . $result['http_code'] . "\n\n";
echo $result['body'];