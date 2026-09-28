<?php

// The PHP community runtime serves this router from the repository root, not
// Laravel's public directory. Serve public assets directly before booting the
// application so Vite CSS/JS, images, and fonts retain their normal URLs.
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$publicPath = realpath(__DIR__.'/../public');
$assetPath = realpath($publicPath.$requestPath);

if (
    $publicPath !== false
    && $assetPath !== false
    && str_starts_with($assetPath, $publicPath.DIRECTORY_SEPARATOR)
    && is_file($assetPath)
) {
    $extension = strtolower(pathinfo($assetPath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'text/javascript; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    ];

    header('Content-Type: '.($mimeTypes[$extension] ?? 'application/octet-stream'));
    header('Cache-Control: public, max-age=31536000, immutable');
    readfile($assetPath);
    exit;
}

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
