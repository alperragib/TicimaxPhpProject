<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Product\ProductService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$productService = new ProductService($request);

echo "=== STORE STOCK TEST ===\n";
echo "Testing ProductService::GetStoreStock()\n";
echo "API URL: {$config['mainDomain']}\n\n";

try {
    // First, let's get some product variations to get their barcodes/stock codes
    echo "0. Getting product variations to test store stock...\n";
    $variations = $productService->GetProductVariations([], ['KayitSayisi' => 10]);
    
    $testBarcodes = [];
    $testStockCodes = [];
    
    if (empty($variations)) {
        echo "⚠️ No product variations found. Using test values.\n";
        $testBarcodes = ['1234567890123', '9876543210987', '5555555555555'];
        $testStockCodes = ['STOCK001', 'STOCK002', 'STOCK003'];
    } else {
        echo "✅ Found " . count($variations) . " variations. Extracting barcodes and stock codes.\n";
        
        foreach ($variations as $variation) {
            if (!empty($variation->Barkod) && count($testBarcodes) < 3) {
                $testBarcodes[] = $variation->Barkod;
            }
            if (!empty($variation->StokKodu) && count($testStockCodes) < 3) {
                $testStockCodes[] = $variation->StokKodu;
            }
            
            if (count($testBarcodes) >= 3 && count($testStockCodes) >= 3) {
                break;
            }
        }
        
        // Fill with test values if not enough found
        while (count($testBarcodes) < 3) {
            $testBarcodes[] = 'TEST_BARCODE_' . count($testBarcodes);
        }
        while (count($testStockCodes) < 3) {
            $testStockCodes[] = 'TEST_STOCK_' . count($testStockCodes);
        }
    }

    echo "\n";

    // Test 1: Get stock by barcodes
    echo "1. Testing stock lookup by barcodes...\n";
    foreach ($testBarcodes as $index => $barcode) {
        echo "  " . ($index + 1) . ".1. Testing barcode: '$barcode'\n";
        
        $stockResult = $productService->GetStoreStock($barcode);
        
        if (!empty($stockResult)) {
            echo "    ✅ Found stock information:\n";
            foreach ($stockResult as $stockIndex => $stock) {
                echo "      Stock #" . ($stockIndex + 1) . ":\n";
                echo "        - Barcode: " . ($stock['barkod'] ?? 'N/A') . "\n";
                echo "        - Stock Code: " . ($stock['stokKodu'] ?? 'N/A') . "\n";
                echo "        - Product Name: " . ($stock['urunAdi'] ?? 'N/A') . "\n";
                echo "        - Store Name: " . ($stock['magazaAdi'] ?? 'N/A') . "\n";
                echo "        - Stock Quantity: " . ($stock['stokAdedi'] ?? 'N/A') . "\n";
                echo "        - Reserved: " . ($stock['rezerve'] ?? 'N/A') . "\n";
                echo "        - Available: " . ($stock['musait'] ?? 'N/A') . "\n";
                echo "      ---\n";
            }
        } else {
            echo "    ℹ️ No stock found for barcode '$barcode'\n";
        }
        echo "\n";
    }

    // Test 2: Get stock by stock codes
    echo "2. Testing stock lookup by stock codes...\n";
    foreach ($testStockCodes as $index => $stockCode) {
        echo "  " . ($index + 1) . ".2. Testing stock code: '$stockCode'\n";
        
        $stockResult = $productService->GetStoreStock($stockCode);
        
        if (!empty($stockResult)) {
            echo "    ✅ Found stock information:\n";
            foreach ($stockResult as $stockIndex => $stock) {
                echo "      Stock #" . ($stockIndex + 1) . ":\n";
                echo "        - Barcode: " . ($stock['barkod'] ?? 'N/A') . "\n";
                echo "        - Stock Code: " . ($stock['stokKodu'] ?? 'N/A') . "\n";
                echo "        - Product Name: " . ($stock['urunAdi'] ?? 'N/A') . "\n";
                echo "        - Store Name: " . ($stock['magazaAdi'] ?? 'N/A') . "\n";
                echo "        - Stock Quantity: " . ($stock['stokAdedi'] ?? 'N/A') . "\n";
                echo "        - Reserved: " . ($stock['rezerve'] ?? 'N/A') . "\n";
                echo "        - Available: " . ($stock['musait'] ?? 'N/A') . "\n";
                echo "      ---\n";
            }
        } else {
            echo "    ℹ️ No stock found for stock code '$stockCode'\n";
        }
        echo "\n";
    }

    // Test 3: Test with empty/invalid values
    echo "3. Testing with invalid values...\n";
    
    echo "  3.1. Testing with empty string...\n";
    $emptyResult = $productService->GetStoreStock('');
    if (empty($emptyResult)) {
        echo "    ✅ Correctly returned empty result for empty string\n";
    } else {
        echo "    ⚠️ Unexpected result for empty string\n";
    }

    echo "  3.2. Testing with null...\n";
    $nullResult = $productService->GetStoreStock(null);
    if (empty($nullResult)) {
        echo "    ✅ Correctly returned empty result for null\n";
    } else {
        echo "    ⚠️ Unexpected result for null\n";
    }

    echo "  3.3. Testing with invalid barcode...\n";
    $invalidResult = $productService->GetStoreStock('INVALID_BARCODE_12345');
    if (empty($invalidResult)) {
        echo "    ✅ Correctly returned empty result for invalid barcode\n";
    } else {
        echo "    ⚠️ Unexpected result for invalid barcode\n";
    }

    echo "\n";

    // Summary
    $totalStockItems = 0;
    $totalStores = [];
    
    foreach (array_merge($testBarcodes, $testStockCodes) as $identifier) {
        $stockResult = $productService->GetStoreStock($identifier);
        $totalStockItems += count($stockResult);
        
        foreach ($stockResult as $stock) {
            if (!empty($stock['magazaAdi'])) {
                $totalStores[$stock['magazaAdi']] = true;
            }
        }
    }

    echo "=== SUMMARY ===\n";
    echo "Identifiers tested: " . (count($testBarcodes) + count($testStockCodes)) . "\n";
    echo "Total stock items found: $totalStockItems\n";
    echo "Unique stores found: " . count($totalStores) . "\n";
    if (!empty($totalStores)) {
        echo "Store names: " . implode(', ', array_keys($totalStores)) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== STORE STOCK TEST COMPLETED ===\n"; 