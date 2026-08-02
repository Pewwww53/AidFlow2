<?php

$tmpDirectories = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
];

foreach ($tmpDirectories as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

require __DIR__.'/../public/index.php';