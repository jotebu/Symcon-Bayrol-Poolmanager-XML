<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$paths = [
    $root . '/BayrolPoolmanagerXML/module.php',
    ...glob($root . '/BayrolPoolmanagerXML/lib/*.php')
];

$failed = false;
foreach ($paths as $path) {
    if (!is_file($path)) {
        continue;
    }

    $command = 'php -l ' . escapeshellarg($path);
    exec($command, $output, $code);
    if ($code !== 0) {
        $failed = true;
        echo "FAILED: {$path}\n";
        echo implode("\n", $output) . "\n";
    } else {
        echo "OK: {$path}\n";
    }
    $output = [];
}

exit($failed ? 1 : 0);
