<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Product\ProductService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$productService = new ProductService($request);

echo "=== INSTALLMENT OPTIONS TEST ===\n";
echo "Testing ProductService::GetInstallmentOptions()\n";
echo "API URL: {$config['mainDomain']}\n\n";

try {
    // First, let's get some product variations to get their IDs
    echo "0. Getting product variations to test installment options...\n";
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

    // Test installment options for each variation
    foreach ($testVariationIds as $index => $variationId) {
        echo ($index + 1) . ". Testing installment options for variation ID: $variationId\n";
        
        $installmentOptions = $productService->GetInstallmentOptions($variationId);
        
        if (!empty($installmentOptions)) {
            echo "✅ Found " . count($installmentOptions) . " installment option(s)\n";
            
            foreach ($installmentOptions as $bankIndex => $bankOption) {
                echo "  Bank #" . ($bankIndex + 1) . ":\n";
                echo "    - Bank Name: " . ($bankOption['bankaAdi'] ?? 'N/A') . "\n";
                echo "    - Bank ID: " . ($bankOption['bankaId'] ?? 'N/A') . "\n";
                echo "    - Bank Image: " . ($bankOption['bankaResmi'] ?? 'N/A') . "\n";
                echo "    - Logo Path: " . ($bankOption['logoPath'] ?? 'N/A') . "\n";
                echo "    - Installments Available: " . count($bankOption['taksitler'] ?? []) . "\n";
                
                if (!empty($bankOption['taksitler'])) {
                    echo "    - Installment Details:\n";
                    foreach ($bankOption['taksitler'] as $taksitIndex => $taksit) {
                        echo "      * " . ($taksit['taksitSayisi'] ?? 0) . " installments: " . 
                             ($taksit['taksitTutari'] ?? 'N/A') . " each\n";
                        echo "        Total: " . ($taksit['toplamTutar'] ?? 'N/A') . "\n";
                        echo "        Rate: " . ($taksit['oran'] ?? 'N/A') . "%\n";
                        echo "        Fee: " . ($taksit['komisyon'] ?? 'N/A') . "\n";
                    }
                }
                echo "  ---\n";
            }
        } else {
            echo "ℹ️ No installment options found for variation $variationId\n";
        }
        echo "\n";
    }

    // Test with invalid variation ID
    echo "4. Testing with invalid variation ID (-1)...\n";
    $invalidOptions = $productService->GetInstallmentOptions(-1);
    
    if (empty($invalidOptions)) {
        echo "✅ Correctly returned empty result for invalid variation ID\n";
    } else {
        echo "⚠️ Unexpected result for invalid variation ID\n";
        print_r($invalidOptions);
    }

    echo "\n";

    // Test with large variation ID
    echo "5. Testing with large variation ID (999999)...\n";
    $largeIdOptions = $productService->GetInstallmentOptions(999999);
    
    if (empty($largeIdOptions)) {
        echo "✅ Correctly returned empty result for non-existent variation ID\n";
    } else {
        echo "⚠️ Unexpected result for large variation ID\n";
        print_r($largeIdOptions);
    }

    echo "\n";

    // Summary
    $totalBanks = 0;
    $totalInstallments = 0;
    
    foreach ($testVariationIds as $variationId) {
        $options = $productService->GetInstallmentOptions($variationId);
        $totalBanks += count($options);
        
        foreach ($options as $bankOption) {
            $totalInstallments += count($bankOption['taksitler'] ?? []);
        }
    }

    echo "=== SUMMARY ===\n";
    echo "Variations tested: " . count($testVariationIds) . "\n";
    echo "Total banks found: $totalBanks\n";
    echo "Total installment options: $totalInstallments\n";
    echo "Average banks per variation: " . ($totalBanks > 0 ? round($totalBanks / count($testVariationIds), 2) : 0) . "\n";

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== INSTALLMENT OPTIONS TEST COMPLETED ===\n"; 