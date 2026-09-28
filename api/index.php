<?php

// Vercel functions have a read-only deployment filesystem. Point Laravel's
// runtime files (compiled views, sessions, cache, and logs) at its writable
// temporary directory instead.
$storagePath = '/tmp/laravel';

putenv("LARAVEL_STORAGE_PATH={$storagePath}");
$_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
$_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;

$tmpDirectories = [
    "{$storagePath}/app",
    "{$storagePath}/framework/cache/data",
    "{$storagePath}/framework/sessions",
    "{$storagePath}/framework/testing",
    "{$storagePath}/framework/views",
    "{$storagePath}/logs",
];

foreach ($tmpDirectories as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

require __DIR__.'/../public/index.php';
