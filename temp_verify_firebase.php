<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$firebase = app('App\\Services\\FirebaseService');
$user = $firebase->getUserByUsername('admin');

if ($user && Illuminate\Support\Facades\Hash::check('password123', $user['password'])) {
    echo "PASSWORD_OK\n";
} else {
    echo "PASSWORD_FAIL\n";
}
