<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Cart\CartService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($mainDomain, $apiKey);
$cartService = new CartService($request);

// Test selectWebSepet function
try {
    // Example parameters
    $dil = 'TR';           // Language code
    $paraBirimi = 'TL';    // Currency
    $sepetId = null;       // Optional cart ID
    $uyeId = null;         // Optional user ID

    // Call the function
    $result = $cartService->selectWebSepet($dil, $paraBirimi, $sepetId, $uyeId);

    // Print the result
    echo "SelectWebSepet Test Result:\n";
    echo "IsError: " . ($result['IsError'] ? 'true' : 'false') . "\n";
    echo "ErrorMessage: " . $result['ErrorMessage'] . "\n";
    
    if (!empty($result['Data'])) {
        echo "\nFound Carts:\n";
        foreach ($result['Data'] as $cart) {
            echo "Cart ID: " . ($cart->ID ?? 'N/A') . "\n";
            echo "User ID: " . ($cart->UyeID ?? 'N/A') . "\n";
            echo "User Name: " . ($cart->UyeAdi ?? 'N/A') . "\n";
            echo "Cart Date: " . ($cart->SepetTarihi ?? 'N/A') . "\n";
            
            if (isset($cart->Urunler) && !empty($cart->Urunler)) {
                echo "Products:\n";
                foreach ($cart->Urunler as $product) {
                    echo "  - Product ID: " . ($product->UrunID ?? 'N/A') . "\n";
                    echo "    Name: " . ($product->UrunAdi ?? 'N/A') . "\n";
                    echo "    Quantity: " . ($product->Adet ?? 'N/A') . "\n";
                    echo "    Price: " . ($product->Fiyati ?? 'N/A') . " " . ($product->ParaBirimi ?? '') . "\n";
                    echo "    -------------------------\n";
                }
            }
            echo "==============================\n";
        }
    } else {
        echo "\nNo carts found.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 