<?php

declare(strict_types=1);

require_once __DIR__ . '/support/helpers.php';
require_once __DIR__ . '/presentation/gui/view_helpers/formatters.php';

$loadFiles = null;
$loadFiles = static function (string $dir) use (&$loadFiles): void {
    $entries = scandir($dir);
    if ($entries === false) {
        return;
    }
    sort($entries);

    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        $path = $dir . DIRECTORY_SEPARATOR . $entry;

        if (is_dir($path)) {
            $normalizedPath = str_replace('\\', '/', $path);
            $guiBase = str_replace('\\', '/', __DIR__ . '/presentation/gui');

            // Compatible PHP 7+ (évite str_starts_with)
            if (strpos($normalizedPath, $guiBase) === 0) {
                continue;
            }

            $loadFiles($path);
            continue;
        }

        if (substr($entry, -4) === '.php') {
            require_once $path;
        }
    }
};

$loadFiles(__DIR__ . '/domain');
$loadFiles(__DIR__ . '/infrastructure');
$loadFiles(__DIR__ . '/usecases');
$loadFiles(__DIR__ . '/presentation');
$loadFiles(__DIR__ . '/controllers');
