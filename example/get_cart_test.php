<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

use AlperRagib\Ticimax\Service\Cart\CartService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($mainDomain, $apiKey);
$cartService = new CartService($request);

echo "Testing with domain: $mainDomain\n\n";

// Test getSepet function
try {
    // John's information
    $userId = 1050;        // John's user ID
    $cartId = null;        // Optional cart ID
    $campaignId = 0;       // Optional campaign ID

    echo "Getting cart for User ID: $userId (John)\n\n";

    // Call the function
    $result = $cartService->getCart($userId, $cartId, $campaignId);

    if ($result !== null) {
        echo "Cart Details:\n";
        echo "Cart ID: " . ($result->ID ?? 'N/A') . "\n";
        echo "User ID: " . ($result->UyeID ?? 'N/A') . "\n";
        echo "Total Amount: " . ($result->GenelToplam ?? '0') . "\n";
        echo "Total VAT: " . ($result->ToplamKDV ?? '0') . "\n";
        echo "Total Products: " . ($result->ToplamUrunAdedi ?? '0') . "\n";
        echo "Currency: " . ($result->SepetParaBirimiDilKodu ?? 'N/A') . "\n";

        if (!empty($result->Urunler)) {
            echo "\nProducts in Cart:\n";
            foreach ($result->Urunler as $product) {
                echo "-------------------------\n";
                echo "Product ID: " . ($product->UrunID ?? 'N/A') . "\n";
                echo "Name: " . ($product->UrunAdi ?? 'N/A') . "\n";
                echo "Quantity: " . ($product->Adet ?? '0') . "\n";
                echo "Price: " . ($product->Fiyati ?? '0') . "\n";
                echo "VAT Rate: " . ($product->KDVOrani ?? '0') . "%\n";
                echo "VAT Amount: " . ($product->KDVTutari ?? '0') . "\n";
                echo "Shipping Cost: " . ($product->KargoUcreti ?? '0') . "\n";
                echo "Stock Code: " . ($product->StokKodu ?? 'N/A') . "\n";
                echo "Free Shipping: " . (($product->UcretsizKargo ?? false) ? 'Yes' : 'No') . "\n";

                // Raw data for debugging
                echo "\nRaw Product Data:\n";
                print_r($product);
                echo "-------------------------\n";
            }
        } else {
            echo "\nNo products in cart.\n";
        }

        // Raw data for debugging
        echo "\nRaw Cart Data:\n";
        print_r($result);
    } else {
        echo "No cart found or an error occurred.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
} 