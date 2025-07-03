<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\User\UserService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$userService = new UserService($request);

// Test getUsers function
try {
    // Filters for active users
    $filters = [
        'Aktif' => 1,  // Active users only
        'Onay' => 1,   // Approved users only
    ];

    // Pagination settings
    $pagination = [
        'KayitSayisi' => 10,  // Number of records per page
        'SayfaNo' => 0,       // First page
        'SiralamaYonu' => 'DESC'  // Latest users first
    ];

    echo "Fetching users from: {$config['mainDomain']}\n";
    echo "API Key: " . substr($config['apiKey'], 0, 5) . "...\n\n";

    // Get users
    $users = $userService->getUsers($filters, $pagination);

    if (!empty($users)) {
        echo "Found " . count($users) . " users:\n\n";
        foreach ($users as $user) {
            echo "-------------------------\n";
            echo "User ID: " . ($user->ID ?? 'N/A') . "\n";
            echo "Name: " . ($user->Isim ?? 'N/A') . "\n";
            echo "Email: " . ($user->Mail ?? 'N/A') . "\n";
            echo "Phone: " . ($user->Telefon ?? 'N/A') . "\n";
            echo "Registration Date: " . ($user->UyelikTarihi ?? 'N/A') . "\n";
            echo "Last Login: " . ($user->SonGirisTarihi ?? 'N/A') . "\n";
            echo "Active: " . ($user->Aktif ? 'Yes' : 'No') . "\n";
            
            // Raw data for debugging
            echo "\nRaw Data:\n";
            print_r($user->toArray());
            echo "-------------------------\n";
        }
    } else {
        echo "No users found.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
} 