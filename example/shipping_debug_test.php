<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Cart\CartService;
use AlperRagib\Ticimax\Service\Shipping\ShippingService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$cartService = new CartService($request);
$shippingService = new ShippingService($request);

echo "=== SHIPPING DEBUG TEST ===\n";
echo "Domain: {$config['mainDomain']}\n\n";

$userId = 1053;
$testCities = [
    34 => 'İstanbul',
    6 => 'Ankara', 
    35 => 'İzmir',
    72 => 'Batman',
    1 => 'Adana'
];

try {
    // First, let's check if user has a cart
    echo "1. Checking user cart for ID: $userId\n";
    $cart = $cartService->getSepet($userId);
    
    if (!$cart || empty($cart->ID)) {
        echo "❌ No cart found for user $userId\n";
        echo "Let's try with a different user or create a test cart...\n\n";
        
        // Try with different user IDs
        $testUserIds = [1050, 1051, 1052, 1053, 1054, 1055];
        foreach ($testUserIds as $testUserId) {
            echo "Trying user ID: $testUserId\n";
            $testCart = $cartService->getSepet($testUserId);
            if ($testCart && !empty($testCart->ID)) {
                echo "✅ Found cart for user $testUserId\n";
                echo "Cart ID: {$testCart->ID}\n";
                $userId = $testUserId;
                $cart = $testCart;
                break;
            }
        }
        
        if (!$cart || empty($cart->ID)) {
            echo "❌ No carts found for any test users\n";
            echo "Creating a minimal cart object for testing...\n";
            // Create a minimal cart-like object
            $cart = (object)[
                'ID' => 999,
                'UyeID' => $userId,
                'ToplamTutar' => 100.00
            ];
        }
    } else {
        echo "✅ Cart found for user $userId\n";
        echo "Cart ID: {$cart->ID}\n";
    }
    
    echo "\n2. Testing shipping options for different cities:\n\n";
    
    // Test shipping options for different cities
    foreach ($testCities as $cityId => $cityName) {
        echo "--- Testing $cityName (ID: $cityId) ---\n";
        
        try {
            $result = $shippingService->getShippingOptions($cityId, 'TL', $cart);
            
            echo "IsError: " . ($result['IsError'] ? 'true' : 'false') . "\n";
            echo "ErrorMessage: " . ($result['ErrorMessage'] ?? 'None') . "\n";
            
            if (!empty($result['Data'])) {
                echo "✅ Found " . count($result['Data']) . " shipping companies:\n";
                foreach ($result['Data'] as $company) {
                    echo "  - {$company->Tanim} (ID: {$company->ID}) - {$company->Fiyat} TL\n";
                }
            } else {
                echo "❌ No shipping companies found\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Exception: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    // Test 3: Try to get all shipping companies directly
    echo "3. Testing getShippingCompanies() method:\n";
    try {
        $companies = $shippingService->getShippingCompanies();
        
        if (!empty($companies)) {
            echo "✅ Found " . count($companies) . " shipping companies:\n";
            foreach ($companies as $company) {
                echo "  - Company: " . ($company->Tanim ?? $company->FirmaAdi ?? 'Unknown') . "\n";
                echo "    ID: " . ($company->ID ?? 'N/A') . "\n";
                echo "    Active: " . (($company->Aktif ?? true) ? 'Yes' : 'No') . "\n";
                echo "  ---\n";
            }
        } else {
            echo "❌ No shipping companies found\n";
        }
    } catch (Exception $e) {
        echo "❌ Exception in getShippingCompanies: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Main Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG TEST COMPLETED ===\n"; 