<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Response\ApiResponse;

// Load configuration
$config = require __DIR__ . '/../config.php';

echo "=== FAVOURITE PRODUCT SERVICE TESTS ===\n\n";

try {
    // Initialize Ticimax API
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $favouriteService = $ticimax->favouriteProductService();
    
    // Test parameters
    $testUserId = 1055; // Test user ID
    $testProductId = 16; // Test product ID
    
    // Test counters
    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;
    
    echo "========================================\n";
    echo "      FAVOURITE PRODUCT TESTS\n";
    echo "========================================\n\n";
    
    // Test 1: Get favourite products with NULL user ID
    echo "🧪 Test 1: GET FAVOURITES WITH NULL USER ID\n";
    echo "---------------------------------------------\n";
    $testCount++;
    
    $response = $favouriteService->getFavouriteProducts(['UyeID' => null]);
    
    if ($response->isSuccess()) {
        $successCount++;
        $favourites = $response->getData();
        echo "✅ Request successful with NULL user ID\n";
        echo "📦 Number of favourite products: " . count($favourites) . "\n";
    } else {
        $errorCount++;
        echo "❌ Error: " . $response->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 2: Get favourite products with valid user ID
    echo "🧪 Test 2: GET FAVOURITES WITH VALID USER ID\n";
    echo "------------------------------------------------\n";
    $testCount++;
    
    $response = $favouriteService->getFavouriteProducts(['UyeID' => $testUserId]);
    
    if ($response->isSuccess()) {
        $successCount++;
        $favourites = $response->getData();
        echo "✅ Request successful with User ID: $testUserId\n";
        echo "📦 Number of favourite products: " . count($favourites) . "\n";
        
        if (!empty($favourites)) {
            $firstFav = $favourites[0];
            echo "\n📋 First Favourite Product Details:\n";
            echo "   🆔 Product ID: " . ($firstFav->UrunKartiID ?? 'N/A') . "\n";
            echo "   📝 Product Name: " . ($firstFav->UrunAdi ?? 'N/A') . "\n";
            echo "   💰 Price: " . ($firstFav->UrunFiyati ?? 'N/A') . " " . ($firstFav->ParaBirimi ?? 'TL') . "\n";
            echo "   📅 Added Date: " . ($firstFav->EklemeTarihi ?? 'N/A') . "\n";
            echo "   🏷️ Stock Code: " . ($firstFav->StokKodu ?? 'N/A') . "\n";
            echo "   📦 Stock Quantity: " . ($firstFav->ToplamStokAdedi ?? 'N/A') . "\n";
            echo "   🔄 Variation Count: " . ($firstFav->VaryasyonSayisi ?? 'N/A') . "\n";
            echo "   🖼️ Image URL: " . ($firstFav->ResimUrl ?? 'N/A') . "\n";
            echo "   🔗 Product URL: " . ($firstFav->UrunUrl ?? 'N/A') . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Error: " . $response->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 3: Get favourite products with date range
    echo "🧪 Test 3: GET FAVOURITES WITH DATE RANGE\n";
    echo "----------------------------------------\n";
    $testCount++;
    
    $startDate = date('Y-m-d', strtotime('-30 days'));
    $endDate = date('Y-m-d');
    
    $response = $favouriteService->getFavouriteProducts([
        'UyeID' => $testUserId,
        'BaslangicTarihi' => $startDate,
        'BitisTarihi' => $endDate
    ]);
    
    if ($response->isSuccess()) {
        $successCount++;
        $favourites = $response->getData();
        echo "✅ Request successful with date range\n";
        echo "📅 Start Date: $startDate\n";
        echo "📅 End Date: $endDate\n";
        echo "📦 Number of favourite products: " . count($favourites) . "\n";
    } else {
        $errorCount++;
        echo "❌ Error: " . $response->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 4: Get favourite products with pagination
    echo "🧪 Test 4: GET FAVOURITES WITH PAGINATION\n";
    echo "-------------------------------------\n";
    $testCount++;
    
    $pageSize = 10;
    $pageNumber = 1;
    
    $response = $favouriteService->getFavouriteProducts([
        'UyeID' => $testUserId,
        'KayitSayisi' => $pageSize,
        'SayfaNo' => $pageNumber
    ]);
    
    if ($response->isSuccess()) {
        $successCount++;
        $favourites = $response->getData();
        echo "✅ Request successful with pagination\n";
        echo "📑 Page Number: $pageNumber\n";
        echo "📊 Page Size: $pageSize\n";
        echo "📦 Number of favourite products: " . count($favourites) . "\n\n";
        
        if (!empty($favourites)) {
            foreach ($favourites as $index => $fav) {
                echo "📋 Favourite Product #" . ($index + 1) . " Details:\n";
                echo "   🆔 Product ID: " . ($fav->UrunKartiID ?? 'N/A') . "\n";
                echo "   📝 Product Name: " . ($fav->UrunAdi ?? 'N/A') . "\n";
                echo "   💰 Price: " . ($fav->UrunFiyati ?? 'N/A') . " " . ($fav->ParaBirimi ?? 'TL') . "\n";
                echo "   📅 Added Date: " . ($fav->EklemeTarihi ?? 'N/A') . "\n";
                echo "   🏷️ Stock Code: " . ($fav->StokKodu ?? 'N/A') . "\n";
                echo "   📦 Stock Quantity: " . ($fav->ToplamStokAdedi ?? 'N/A') . "\n";
                echo "   🔄 Variation Count: " . ($fav->VaryasyonSayisi ?? 'N/A') . "\n";
                echo "   🖼️ Image URL: " . ($fav->ResimUrl ?? 'N/A') . "\n";
                echo "   🔗 Product URL: " . ($fav->UrunUrl ?? 'N/A') . "\n\n";
            }
        }
    } else {
        $errorCount++;
        echo "❌ Error: " . $response->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 5: Add favourite product with invalid user
    echo "🧪 Test 5: ADD FAVOURITE WITH INVALID USER\n";
    echo "-----------------------------------------\n";
    $testCount++;
    
    $response = $favouriteService->addFavouriteProduct(0, $testProductId);
    
    if ($response->isSuccess()) {
        $successCount++;
        echo "✅ Add attempt successful with invalid user\n";
        echo "📝 Result: " . $response->getMessage() . "\n";
    } else {
        $errorCount++;
        echo "❌ Error: " . $response->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 6: Add favourite product with valid user
    echo "🧪 Test 6: ADD FAVOURITE WITH VALID USER\n";
    echo "--------------------------------------------\n";
    $testCount++;
    
    $response = $favouriteService->addFavouriteProduct($testUserId, $testProductId);
    
    if ($response->isSuccess()) {
        $successCount++;
        echo "✅ Add successful with valid user\n";
        echo "📝 Result: " . $response->getMessage() . "\n";
    } else {
        $errorCount++;
        echo "❌ Error: " . $response->getMessage() . "\n";
    }
    echo "\n";
    
    // Test results
    echo "========================================\n";
    echo "           TEST RESULTS\n";
    echo "========================================\n";
    echo "📊 Total Tests: $testCount\n";
    echo "✅ Successful: $successCount\n";
    echo "❌ Failed: $errorCount\n";
    echo "📈 Success Rate: " . round(($successCount / $testCount) * 100, 1) . "%\n\n";
    
    echo "Tested Functions:\n";
    echo "• getFavouriteProducts() - With different parameters\n";
    echo "• addFavouriteProduct() - With invalid and valid users\n";
    echo "\nTests completed!\n";
    
} catch (Exception $e) {
    echo "💥 ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
} 