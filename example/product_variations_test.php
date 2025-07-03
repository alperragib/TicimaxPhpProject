<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Product\ProductService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$productService = new ProductService($request);

echo "=== PRODUCT VARIATIONS TEST ===\n";
echo "Testing ProductService::GetProductVariations()\n";
echo "API URL: {$config['mainDomain']}\n\n";

try {
    // Test 1: Get all variations (first 10)
    echo "1. Getting first 10 product variations...\n";
    $allVariations = $productService->GetProductVariations([], ['KayitSayisi' => 10]);
    
    if (!empty($allVariations)) {
        echo "✅ Found " . count($allVariations) . " variation(s)\n";
        
        foreach ($allVariations as $index => $variation) {
            echo "Variation #" . ($index + 1) . ":\n";
            echo "  - ID: " . ($variation->ID ?? 'N/A') . "\n";
            echo "  - Product ID: " . ($variation->UrunID ?? 'N/A') . "\n";
            echo "  - Product Card ID: " . ($variation->UrunKartiID ?? 'N/A') . "\n";
            echo "  - Barcode: " . ($variation->Barkod ?? 'N/A') . "\n";
            echo "  - Stock Code: " . ($variation->StokKodu ?? 'N/A') . "\n";
            echo "  - Stock Quantity: " . ($variation->StokAdedi ?? 'N/A') . "\n";
            echo "  - Price: " . ($variation->Fiyat ?? 'N/A') . "\n";
            echo "  - Active: " . (($variation->Aktif ?? false) ? 'Yes' : 'No') . "\n";
            echo "  ---\n";
        }
    } else {
        echo "❌ No variations found\n";
    }

    echo "\n";

    // Test 2: Get active variations only
    echo "2. Getting active variations only...\n";
    $activeVariations = $productService->GetProductVariations(['Aktif' => 1], ['KayitSayisi' => 5]);
    
    if (!empty($activeVariations)) {
        echo "✅ Found " . count($activeVariations) . " active variation(s)\n";
    } else {
        echo "ℹ️ No active variations found\n";
    }

    echo "\n";

    // Test 3: Get variations by barcode
    if (!empty($allVariations) && isset($allVariations[0]->Barkod) && !empty($allVariations[0]->Barkod)) {
        $testBarcode = $allVariations[0]->Barkod;
        echo "3. Getting variations by barcode '$testBarcode'...\n";
        $barcodeVariations = $productService->GetProductVariations(['Barkod' => $testBarcode], ['KayitSayisi' => 5]);
        
        if (!empty($barcodeVariations)) {
            echo "✅ Found " . count($barcodeVariations) . " variation(s) with barcode '$testBarcode'\n";
        } else {
            echo "ℹ️ No variations found with barcode '$testBarcode'\n";
        }
    } else {
        echo "3. Skipping barcode test (no barcode found in first variation)\n";
    }

    echo "\n";

    // Test 4: Get variations by stock code
    if (!empty($allVariations) && isset($allVariations[0]->StokKodu) && !empty($allVariations[0]->StokKodu)) {
        $testStockCode = $allVariations[0]->StokKodu;
        echo "4. Getting variations by stock code '$testStockCode'...\n";
        $stockCodeVariations = $productService->GetProductVariations(['StokKodu' => $testStockCode], ['KayitSayisi' => 5]);
        
        if (!empty($stockCodeVariations)) {
            echo "✅ Found " . count($stockCodeVariations) . " variation(s) with stock code '$testStockCode'\n";
        } else {
            echo "ℹ️ No variations found with stock code '$testStockCode'\n";
        }
    } else {
        echo "4. Skipping stock code test (no stock code found in first variation)\n";
    }

    echo "\n";

    // Test 5: Get variations by product ID
    if (!empty($allVariations) && isset($allVariations[0]->UrunID)) {
        $testProductId = $allVariations[0]->UrunID;
        echo "5. Getting variations by product ID '$testProductId'...\n";
        $productIdVariations = $productService->GetProductVariations(['UrunID' => $testProductId], ['KayitSayisi' => 10]);
        
        if (!empty($productIdVariations)) {
            echo "✅ Found " . count($productIdVariations) . " variation(s) for product ID '$testProductId'\n";
        } else {
            echo "ℹ️ No variations found for product ID '$testProductId'\n";
        }
    } else {
        echo "5. Skipping product ID test (no product ID found in first variation)\n";
    }

    echo "\n";

    // Test 6: Get variations by product card ID
    if (!empty($allVariations) && isset($allVariations[0]->UrunKartiID)) {
        $testProductCardId = $allVariations[0]->UrunKartiID;
        echo "6. Getting variations by product card ID '$testProductCardId'...\n";
        $productCardIdVariations = $productService->GetProductVariations(['UrunKartiID' => $testProductCardId], ['KayitSayisi' => 10]);
        
        if (!empty($productCardIdVariations)) {
            echo "✅ Found " . count($productCardIdVariations) . " variation(s) for product card ID '$testProductCardId'\n";
        } else {
            echo "ℹ️ No variations found for product card ID '$testProductCardId'\n";
        }
    } else {
        echo "6. Skipping product card ID test (no product card ID found in first variation)\n";
    }

    echo "\n";

    // Test 7: Test pagination
    echo "7. Testing pagination (Page 1 vs Page 2)...\n";
    $page1 = $productService->GetProductVariations([], ['BaslangicIndex' => 0, 'KayitSayisi' => 3]);
    $page2 = $productService->GetProductVariations([], ['BaslangicIndex' => 3, 'KayitSayisi' => 3]);
    
    echo "Page 1: " . count($page1) . " variations\n";
    echo "Page 2: " . count($page2) . " variations\n";
    
    if (!empty($page1) && !empty($page2)) {
        $firstId1 = $page1[0]->ID ?? null;
        $firstId2 = $page2[0]->ID ?? null;
        if ($firstId1 !== $firstId2) {
            echo "✅ Pagination working correctly (different results)\n";
        } else {
            echo "⚠️ Pagination might not be working (same first ID)\n";
        }
    }

    echo "\n";

    // Test 8: Test sorting
    echo "8. Testing sorting (ASC vs DESC)...\n";
    $ascResults = $productService->GetProductVariations([], ['SiralamaYonu' => 'ASC', 'KayitSayisi' => 5]);
    $descResults = $productService->GetProductVariations([], ['SiralamaYonu' => 'DESC', 'KayitSayisi' => 5]);
    
    if (!empty($ascResults) && !empty($descResults)) {
        $firstAsc = $ascResults[0]->ID ?? 0;
        $firstDesc = $descResults[0]->ID ?? 0;
        echo "First ASC ID: $firstAsc\n";
        echo "First DESC ID: $firstDesc\n";
        
        if ($firstAsc !== $firstDesc) {
            echo "✅ Sorting working correctly\n";
        } else {
            echo "⚠️ Sorting might not be working\n";
        }
    }

    echo "\n";

    // Summary
    echo "=== SUMMARY ===\n";
    echo "Total variations found: " . count($allVariations) . "\n";
    if (!empty($allVariations)) {
        $activeCount = count(array_filter($allVariations, function($v) { return $v->Aktif ?? false; }));
        $withStock = count(array_filter($allVariations, function($v) { return ($v->StokAdedi ?? 0) > 0; }));
        echo "Active variations: $activeCount\n";
        echo "Variations with stock: $withStock\n";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== PRODUCT VARIATIONS TEST COMPLETED ===\n"; 