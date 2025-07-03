<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Product\ProductService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$productService = new ProductService($request);

echo "=== PRODUCT PAYMENT OPTIONS TEST ===\n";
echo "Testing ProductService::SelectUrunOdemeSecenek()\n";
echo "API URL: {$config['mainDomain']}\n\n";

try {
    // First, let's get some products to get variation IDs
    echo "0. Getting product variations to test payment options...\n";
    $variations = $productService->GetProductVariations([], ['KayitSayisi' => 5]);
    
    if (empty($variations)) {
        echo "⚠️ No product variations found. Using test variation ID: 1\n";
        $testVariationIds = [1, 2, 3];
    } else {
        echo "✅ Found " . count($variations) . " variations. Testing with real IDs.\n";
        $testVariationIds = [];
        foreach ($variations as $variation) {
            if (isset($variation->ID)) {
                $testVariationIds[] = $variation->ID;
                if (count($testVariationIds) >= 3) break;
            }
        }
        if (empty($testVariationIds)) {
            $testVariationIds = [1, 2, 3]; // Fallback
        }
    }

    echo "\n";

    // Test payment options for each variation
    foreach ($testVariationIds as $index => $variationId) {
        echo ($index + 1) . ". Testing payment options for variation ID: $variationId\n";
        
        $paymentOptions = $productService->SelectUrunOdemeSecenek($variationId);
        
        if (!empty($paymentOptions)) {
            echo "✅ Found " . count($paymentOptions) . " payment option(s)\n";
            
            foreach ($paymentOptions as $bankIndex => $option) {
                echo "  Bank #" . ($bankIndex + 1) . ":\n";
                echo "    - Bank Name: " . ($option['bankaAdi'] ?? 'N/A') . "\n";
                echo "    - Bank ID: " . ($option['bankaId'] ?? 'N/A') . "\n";
                echo "    - Installment Options: " . count($option['taksitler'] ?? []) . "\n";
                
                if (!empty($option['taksitler'])) {
                    echo "    - Installment Details:\n";
                    foreach ($option['taksitler'] as $taksitIndex => $taksit) {
                        echo "      * " . ($taksit['taksitSayisi'] ?? 0) . " installments: " . 
                             ($taksit['taksitTutariStr'] ?? 'N/A') . " each, " .
                             "Total: " . ($taksit['toplamTutarStr'] ?? 'N/A') . "\n";
                    }
                }
                echo "  ---\n";
            }
        } else {
            echo "❌ No payment options found for variation $variationId\n";
        }
        echo "\n";
    }

    // Test with invalid variation ID
    echo "4. Testing with invalid variation ID (-1)...\n";
    $invalidOptions = $productService->SelectUrunOdemeSecenek(-1);
    
    if (empty($invalidOptions)) {
        echo "✅ Correctly returned empty result for invalid variation ID\n";
    } else {
        echo "⚠️ Unexpected result for invalid variation ID\n";
        print_r($invalidOptions);
    }

    echo "\n";

    // Test with large variation ID
    echo "5. Testing with large variation ID (999999)...\n";
    $largeIdOptions = $productService->SelectUrunOdemeSecenek(999999);
    
    if (empty($largeIdOptions)) {
        echo "✅ Correctly returned empty result for non-existent variation ID\n";
    } else {
        echo "⚠️ Unexpected result for large variation ID\n";
        print_r($largeIdOptions);
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== PRODUCT PAYMENT OPTIONS TEST COMPLETED ===\n"; 