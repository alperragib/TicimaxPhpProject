<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Configuration
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey); 
$userService = $ticimax->userService();

echo "=== User Login ===\n";
$loginResult = $userService->login('john.doe@example.com', 'password123');

if ($loginResult) {
    if ($loginResult->Basarili) {
        echo "Login successful!\n";
        echo "User ID: " . $loginResult->KullaniciID . "\n";
        echo "User Name: " . $loginResult->Isim . "\n";
    } else {
        echo "Login failed: " . $loginResult->Mesaj . "\n";

        if ($loginResult->OtpZorunlu) {
            echo "OTP is required for login.\n";
        }

        if ($loginResult->SifreSifirla) {
            echo "Password reset is required.\n";
        }
    }
} else {
    echo "Login request failed.\n";
}
