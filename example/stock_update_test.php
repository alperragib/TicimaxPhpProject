<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Product\ProductService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$productService = new ProductService($request);

echo "=== STOCK UPDATE TEST ===\n";
echo "Testing ProductService::UpdateStockQuantity()\n";
echo "API URL: {$config['mainDomain']}\n\n";

echo "⚠️  WARNING: This test will attempt to modify actual stock quantities!\n";
echo "⚠️  Make sure you are testing on a development/test environment!\n";
echo "⚠️  Press Ctrl+C to cancel if this is a production environment.\n\n";

// Wait 3 seconds to allow cancellation
echo "Starting in 3 seconds...\n";
sleep(1);
echo "Starting in 2 seconds...\n";
sleep(1);
echo "Starting in 1 second...\n";
sleep(1);
echo "Starting tests...\n\n";

try {
    // First, let's get some product variations to test stock updates
    echo "0. Getting product variations to test stock updates...\n";
    $variations = $productService->GetProductVariations([], ['KayitSayisi' => 5]);
    
    $testVariationIds = [];
    $originalStocks = [];
    
    if (empty($variations)) {
        echo "⚠️ No product variations found. Using test variation IDs.\n";
        $testVariationIds = [1, 2, 3];
        $originalStocks = [1 => 100, 2 => 50, 3 => 25]; // Mock original stocks
    } else {
        echo "✅ Found " . count($variations) . " variations. Testing with real IDs.\n";
        
        foreach ($variations as $variation) {
            if (isset($variation->ID) && count($testVariationIds) < 3) {
                $testVariationIds[] = $variation->ID;
                $originalStocks[$variation->ID] = $variation->StokAdedi ?? 0;
            }
        }
        
        if (empty($testVariationIds)) {
            $testVariationIds = [1, 2, 3];
            $originalStocks = [1 => 100, 2 => 50, 3 => 25];
        }
    }

    echo "Test variations: " . implode(', ', $testVariationIds) . "\n";
    echo "Original stocks: " . json_encode($originalStocks) . "\n\n";

    // Test 1: Update stock quantity (increase)
    echo "1. Testing stock quantity increase...\n";
    foreach ($testVariationIds as $index => $variationId) {
        $originalStock = $originalStocks[$variationId] ?? 0;
        $newStock = $originalStock + 10; // Increase by 10
        
        echo "  " . ($index + 1) . ".1. Updating variation $variationId: $originalStock → $newStock\n";
        
        $updateResult = $productService->UpdateStockQuantity($variationId, $newStock);
        
        if ($updateResult) {
            echo "    ✅ Stock update successful\n";
            
            // Verify the update by getting the variation again
            $updatedVariations = $productService->GetProductVariations(['ID' => $variationId]);
            if (!empty($updatedVariations)) {
                $currentStock = $updatedVariations[0]->StokAdedi ?? 'N/A';
                echo "    ✅ Verification: Current stock is $currentStock\n";
                if ($currentStock == $newStock) {
                    echo "    ✅ Stock update verified successfully\n";
                } else {
                    echo "    ⚠️ Stock update verification failed: expected $newStock, got $currentStock\n";
                }
            } else {
                echo "    ⚠️ Could not verify stock update (variation not found)\n";
            }
        } else {
            echo "    ❌ Stock update failed\n";
        }
        echo "\n";
    }

    // Test 2: Update stock quantity (decrease)
    echo "2. Testing stock quantity decrease...\n";
    foreach ($testVariationIds as $index => $variationId) {
        // Get current stock first
        $currentVariations = $productService->GetProductVariations(['ID' => $variationId]);
        $currentStock = 0;
        if (!empty($currentVariations)) {
            $currentStock = $currentVariations[0]->StokAdedi ?? 0;
        }
        
        $newStock = max(0, $currentStock - 5); // Decrease by 5, but not below 0
        
        echo "  " . ($index + 1) . ".2. Updating variation $variationId: $currentStock → $newStock\n";
        
        $updateResult = $productService->UpdateStockQuantity($variationId, $newStock);
        
        if ($updateResult) {
            echo "    ✅ Stock update successful\n";
            
            // Verify the update
            $updatedVariations = $productService->GetProductVariations(['ID' => $variationId]);
            if (!empty($updatedVariations)) {
                $verifyStock = $updatedVariations[0]->StokAdedi ?? 'N/A';
                echo "    ✅ Verification: Current stock is $verifyStock\n";
                if ($verifyStock == $newStock) {
                    echo "    ✅ Stock update verified successfully\n";
                } else {
                    echo "    ⚠️ Stock update verification failed: expected $newStock, got $verifyStock\n";
                }
            }
        } else {
            echo "    ❌ Stock update failed\n";
        }
        echo "\n";
    }

    // Test 3: Test edge cases
    echo "3. Testing edge cases...\n";
    
    if (!empty($testVariationIds)) {
        $testVariationId = $testVariationIds[0];
        
        echo "  3.1. Testing zero stock...\n";
        $zeroResult = $productService->UpdateStockQuantity($testVariationId, 0);
        echo "    " . ($zeroResult ? "✅ Zero stock update successful" : "❌ Zero stock update failed") . "\n";
        
        echo "  3.2. Testing negative stock (should fail or be handled)...\n";
        $negativeResult = $productService->UpdateStockQuantity($testVariationId, -10);
        echo "    " . ($negativeResult ? "⚠️ Negative stock update allowed" : "✅ Negative stock update rejected") . "\n";
        
        echo "  3.3. Testing large stock number...\n";
        $largeResult = $productService->UpdateStockQuantity($testVariationId, 999999);
        echo "    " . ($largeResult ? "✅ Large stock update successful" : "❌ Large stock update failed") . "\n";
    }

    echo "\n";

    // Test 4: Test invalid variation ID
    echo "4. Testing with invalid variation ID...\n";
    $invalidResult = $productService->UpdateStockQuantity(-1, 100);
    echo "  " . ($invalidResult ? "⚠️ Invalid variation ID update allowed" : "✅ Invalid variation ID update rejected") . "\n";

    echo "\n";

    // Test 5: Restore original stock quantities
    echo "5. Restoring original stock quantities...\n";
    foreach ($testVariationIds as $index => $variationId) {
        $originalStock = $originalStocks[$variationId] ?? 0;
        
        echo "  " . ($index + 1) . ".5. Restoring variation $variationId to original stock: $originalStock\n";
        
        $restoreResult = $productService->UpdateStockQuantity($variationId, $originalStock);
        
        if ($restoreResult) {
            echo "    ✅ Stock restored successfully\n";
            
            // Verify restoration
            $restoredVariations = $productService->GetProductVariations(['ID' => $variationId]);
            if (!empty($restoredVariations)) {
                $finalStock = $restoredVariations[0]->StokAdedi ?? 'N/A';
                echo "    ✅ Final verification: Stock is $finalStock\n";
                if ($finalStock == $originalStock) {
                    echo "    ✅ Stock restoration verified successfully\n";
                } else {
                    echo "    ⚠️ Stock restoration verification failed: expected $originalStock, got $finalStock\n";
                }
            }
        } else {
            echo "    ❌ Stock restoration failed\n";
        }
        echo "\n";
    }

    // Summary
    echo "=== SUMMARY ===\n";
    echo "Variations tested: " . count($testVariationIds) . "\n";
    echo "Original stocks: " . json_encode($originalStocks) . "\n";
    echo "✅ Stock update testing completed\n";
    echo "⚠️  Please verify that all stock quantities have been restored to their original values.\n";

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
    
    // Attempt to restore original stocks if an exception occurred
    echo "\n🔄 Attempting to restore original stock quantities after exception...\n";
    foreach ($testVariationIds as $variationId) {
        if (isset($originalStocks[$variationId])) {
            try {
                $productService->UpdateStockQuantity($variationId, $originalStocks[$variationId]);
                echo "  ✅ Restored variation $variationId to " . $originalStocks[$variationId] . "\n";
            } catch (Exception $restoreException) {
                echo "  ❌ Failed to restore variation $variationId: " . $restoreException->getMessage() . "\n";
            }
        }
    }
}

echo "\n=== STOCK UPDATE TEST COMPLETED ===\n"; 