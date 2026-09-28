<?php

// Vercel functions have a read-only deployment filesystem. Point Laravel's
// runtime files (compiled views, sessions, cache, and logs) at its writable
// temporary directory instead.
$storagePath = '/tmp/laravel';

putenv("LARAVEL_STORAGE_PATH={$storagePath}");
$_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
$_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;

// Do not load configuration/provider caches generated during a deployment.
// A cached config omits Laravel's default providers in this project, including
// the view provider required to render Blade templates.
$cachePath = "{$storagePath}/bootstrap/cache";
foreach ([
    'APP_CONFIG_CACHE' => "{$cachePath}/config.php",
    'APP_PACKAGES_CACHE' => "{$cachePath}/packages.php",
    'APP_SERVICES_CACHE' => "{$cachePath}/services.php",
] as $name => $value) {
    putenv("{$name}={$value}");
    $_ENV[$name] = $value;
    $_SERVER[$name] = $value;
}

$tmpDirectories = [
    "{$storagePath}/app",
    $cachePath,
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
