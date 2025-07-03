<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

use AlperRagib\Ticimax\Service\Cart\CartService;
use AlperRagib\Ticimax\Service\Shipping\ShippingService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($mainDomain, $apiKey);
$cartService = new CartService($request);
$shippingService = new ShippingService($request);

echo "Testing with domain: $mainDomain\n\n";

// Test getShippingOptions function
try {
    // User information
    $userId = 1053;      // Batman'lı kullanıcı ID
    $cityId = 72;        // Batman şehir kodu
    $currency = 'TL';    // Turkish Lira (using TL instead of TRY)

    // First get user's cart
    echo "Getting cart for User ID: $userId\n";
    $cart = $cartService->getSepet($userId);
    
    if (!$cart) {
        throw new Exception("Could not get cart for user $userId");
    }

    echo "Cart ID: " . $cart->ID . "\n";
    echo "Testing shipping options for User ID: $userId\n";
    echo "City ID: $cityId (Batman)\n";
    echo "Currency: $currency\n\n";

    // Call the function with cart object
    $result = $shippingService->getShippingOptions($cityId, $currency, $cart);

    // Print the result
    echo "Shipping Options Test Result:\n";
    echo "IsError: " . ($result['IsError'] ? 'true' : 'false') . "\n";
    echo "ErrorMessage: " . $result['ErrorMessage'] . "\n";

    if (!empty($result['Data'])) {
        echo "\nAvailable Shipping Companies:\n";
        /** @var \AlperRagib\Ticimax\Model\Shipping\ShippingCompanyModel $company */
        foreach ($result['Data'] as $company) {
            echo "-------------------------\n";
            // Using original API field names
            echo "Company ID: " . $company->ID . "\n";
            echo "Name: " . $company->Tanim . "\n";
            echo "Base Price: " . $company->Fiyat . " " . $currency . "\n";
            
            // Using helper methods
            if ($company->hasCashOnDelivery()) {
                echo "Cash on Delivery: Available\n";
                echo "Cash on Delivery Price: " . $company->KapidaOdemeFiyati . " " . $currency . "\n";
            }
            
            if ($company->hasCreditCardOnDelivery()) {
                echo "Credit Card on Delivery: Available\n";
                echo "Credit Card on Delivery Price: " . $company->KapidaOdemeKKFiyati . " " . $currency . "\n";
            }

            // Raw data for debugging
            echo "\nRaw Data:\n";
            print_r($company->toArray());
            echo "-------------------------\n";
        }
    } else {
        echo "\nNo shipping companies found for this city.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
} 