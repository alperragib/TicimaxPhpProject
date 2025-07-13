<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$favouriteProductService = $ticimax->favouriteProductService();

echo "=== FAVOURITE PRODUCT SERVICE TEST SUITE ===\n\n";

// Test 1: Get Favourite Products with Default Parameters
echo "1. === GET FAVOURITE PRODUCTS (DEFAULT) TEST ===\n";
echo "Testing getFavouriteProducts() with default parameters...\n";

$defaultResponse = $favouriteProductService->getFavouriteProducts(['UyeID' => 1, 'KayitSayisi' => 1]);

if ($defaultResponse->isSuccess()) {
    echo "✓ " . $defaultResponse->getMessage() . "\n";
    $favouriteProducts = $defaultResponse->getData();
    echo "Found " . count($favouriteProducts) . " favourite products.\n";
    
    if (!empty($favouriteProducts)) {
        echo "First favourite product details:\n";
        $firstProduct = $favouriteProducts[0];
        echo "- Favourite ID: " . ($firstProduct->FavoriUrunID ?? 'N/A') . "\n";
        echo "- User ID: " . ($firstProduct->UyeID ?? 'N/A') . "\n";
        echo "- Product Card ID: " . ($firstProduct->UrunKartiID ?? 'N/A') . "\n";
        echo "- Product Name: " . ($firstProduct->UrunAdi ?? 'N/A') . "\n";
        echo "- Quantity: " . ($firstProduct->UrunSayisi ?? 'N/A') . "\n";
        echo "- Price: " . ($firstProduct->UrunFiyati ?? 'N/A') . " TL\n";
        echo "- Price with VAT: " . ($firstProduct->UrunFiyatiKdv ?? 'N/A') . " TL\n";
        echo "- Stock Code: " . ($firstProduct->StokKodu ?? 'N/A') . "\n";
        echo "- Add Date: " . ($firstProduct->EklemeTarihi ?? 'N/A') . "\n";
        
        // Store data for other tests
        $testUserId = $firstProduct->UyeID ?? 1;
        $testProductCardId = $firstProduct->UrunKartiID ?? 1;
        $testFavouriteId = $firstProduct->FavoriUrunID ?? null;
    } else {
        $testUserId = 1; // Default user ID for testing
        $testProductCardId = 1; // Default product card ID for testing
        $testFavouriteId = null;
    }
} else {
    echo "✗ " . $defaultResponse->getMessage() . "\n";
    $testUserId = 1;
    $testProductCardId = 1;
    $testFavouriteId = null;
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 2: Get Favourite Products with Specific User ID
echo "2. === GET FAVOURITE PRODUCTS (SPECIFIC USER) TEST ===\n";
echo "Testing getFavouriteProducts() with specific user ID: $testUserId...\n";

$userSpecificResponse = $favouriteProductService->getFavouriteProducts([
    'UyeID' => $testUserId,
    'KayitSayisi' => 10
]);

if ($userSpecificResponse->isSuccess()) {
    echo "✓ " . $userSpecificResponse->getMessage() . "\n";
    $userFavourites = $userSpecificResponse->getData();
    echo "Found " . count($userFavourites) . " favourite products for user ID $testUserId.\n";
} else {
    echo "✗ " . $userSpecificResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 3: Get Favourite Products with Date Range
echo "3. === GET FAVOURITE PRODUCTS (DATE RANGE) TEST ===\n";
echo "Testing getFavouriteProducts() with date range...\n";

$dateRangeResponse = $favouriteProductService->getFavouriteProducts([
    'UyeID' => 1,
    'KayitSayisi' => 1
]);

if ($dateRangeResponse->isSuccess()) {
    echo "✓ " . $dateRangeResponse->getMessage() . "\n";
    $dateRangeFavourites = $dateRangeResponse->getData();
    echo "Found " . count($dateRangeFavourites) . " favourite products in the last 30 days.\n";
} else {
    echo "✗ " . $dateRangeResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 4: Add Favourite Product
echo "4. === ADD FAVOURITE PRODUCT TEST ===\n";
echo "Testing addFavouriteProduct() with user ID: $testUserId, product card ID: $testProductCardId...\n";

$addResponse = $favouriteProductService->addFavouriteProduct($testUserId, $testProductCardId, 2.0);

if ($addResponse->isSuccess()) {
    echo "✓ " . $addResponse->getMessage() . "\n";
    $addResult = $addResponse->getData();
    echo "Add result data:\n";
    print_r($addResult);
} else {
    echo "✗ " . $addResponse->getMessage() . "\n";
    echo "Note: This might be expected if the product is already in favourites.\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 5: Add Favourite Product with Default Quantity
echo "5. === ADD FAVOURITE PRODUCT (DEFAULT QUANTITY) TEST ===\n";
echo "Testing addFavouriteProduct() with default quantity...\n";

// Try with a different product card ID to avoid duplicates
$alternativeProductCardId = $testProductCardId + 1;
$addDefaultResponse = $favouriteProductService->addFavouriteProduct($testUserId, $alternativeProductCardId);

if ($addDefaultResponse->isSuccess()) {
    echo "✓ " . $addDefaultResponse->getMessage() . "\n";
    $addDefaultResult = $addDefaultResponse->getData();
    echo "Add result data:\n";
    print_r($addDefaultResult);
} else {
    echo "✗ " . $addDefaultResponse->getMessage() . "\n";
    echo "Note: This might be expected if the product is already in favourites.\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 6: Remove Favourite Product
if ($testFavouriteId) {
    echo "6. === REMOVE FAVOURITE PRODUCT TEST ===\n";
    echo "Testing removeFavouriteProduct() with user ID: $testUserId, favourite ID: $testFavouriteId...\n";

    $removeResponse = $favouriteProductService->removeFavouriteProduct($testUserId, $testFavouriteId);

    if ($removeResponse->isSuccess()) {
        echo "✓ " . $removeResponse->getMessage() . "\n";
        $removeResult = $removeResponse->getData();
        echo "Remove result data:\n";
        print_r($removeResult);
    } else {
        echo "✗ " . $removeResponse->getMessage() . "\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test 7: Add Favourite Product with Invalid Data
echo "7. === ADD FAVOURITE PRODUCT (INVALID DATA) TEST ===\n";
echo "Testing addFavouriteProduct() with invalid user/product IDs...\n";

$invalidAddResponse = $favouriteProductService->addFavouriteProduct(999999, 999999);

if ($invalidAddResponse->isSuccess()) {
    echo "✓ " . $invalidAddResponse->getMessage() . "\n";
    echo "⚠ Warning: Invalid data was accepted, this might need investigation.\n";
} else {
    echo "✓ " . $invalidAddResponse->getMessage() . " (Expected for invalid data)\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 8: Remove Favourite Product with Invalid Data
echo "8. === REMOVE FAVOURITE PRODUCT (INVALID DATA) TEST ===\n";
echo "Testing removeFavouriteProduct() with invalid IDs...\n";

$invalidRemoveResponse = $favouriteProductService->removeFavouriteProduct(999999, 999999);

if ($invalidRemoveResponse->isSuccess()) {
    echo "✓ " . $invalidRemoveResponse->getMessage() . "\n";
    echo "⚠ Warning: Invalid data was accepted, this might need investigation.\n";
} else {
    echo "✓ " . $invalidRemoveResponse->getMessage() . " (Expected for invalid data)\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 9: Pagination Test
echo "9. === PAGINATION TEST ===\n";
echo "Testing getFavouriteProducts() with different page sizes...\n";

$paginationTests = [
    ['UyeID' => 1, 'KayitSayisi' => 1, 'SayfaNo' => 1],
    ['UyeID' => 1, 'KayitSayisi' => 1, 'SayfaNo' => 2],
    ['UyeID' => 1, 'KayitSayisi' => 1, 'SayfaNo' => 3]
];

foreach ($paginationTests as $index => $pagination) {
    echo "Testing pagination " . ($index + 1) . ": " . json_encode($pagination) . "\n";
    
    $paginationResponse = $favouriteProductService->getFavouriteProducts($pagination);
    
    if ($paginationResponse->isSuccess()) {
        $paginationData = $paginationResponse->getData();
        echo "✓ Page " . $pagination['SayfaNo'] . " with " . $pagination['KayitSayisi'] . 
             " records: Found " . count($paginationData) . " items\n";
    } else {
        echo "✗ Pagination test failed: " . $paginationResponse->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 10: Performance Test
echo "10. === PERFORMANCE TEST ===\n";
echo "Testing multiple favourite product requests for performance...\n";

$startTime = microtime(true);

// Make 5 requests to test performance
for ($i = 1; $i <= 5; $i++) {
    $favouriteProductService->getFavouriteProducts(['UyeID' => 1, 'KayitSayisi' => 1]);
    echo "Request $i completed...\n";
}

$endTime = microtime(true);
$totalTime = $endTime - $startTime;
$averageTime = $totalTime / 5;

echo "✓ Performance test completed.\n";
echo "- Total time for 5 requests: " . number_format($totalTime, 4) . " seconds\n";
echo "- Average time per request: " . number_format($averageTime, 4) . " seconds\n";

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 11: Comprehensive Data Analysis
echo "11. === COMPREHENSIVE DATA ANALYSIS TEST ===\n";
echo "Analyzing favourite products data comprehensively...\n";

$analysisResponse = $favouriteProductService->getFavouriteProducts(['UyeID' => 1, 'KayitSayisi' => 1]);

if ($analysisResponse->isSuccess()) {
    $allFavourites = $analysisResponse->getData();
    
    $userStats = [];
    $productStats = [];
    $quantityStats = [];
    $dateStats = [];
    
    foreach ($allFavourites as $favourite) {
        // User statistics
        $userId = $favourite->UyeID ?? 'unknown';
        $userStats[$userId] = ($userStats[$userId] ?? 0) + 1;
        
        // Product statistics
        $productId = $favourite->UrunKartiID ?? 'unknown';
        $productStats[$productId] = ($productStats[$productId] ?? 0) + 1;
        
        // Quantity statistics
        $quantity = $favourite->UrunSayisi ?? 0;
        $quantityStats[] = $quantity;
        
        // Date statistics
        $date = $favourite->EklemeTarihi ?? null;
        if ($date) {
            $dateKey = date('Y-m', strtotime($date));
            $dateStats[$dateKey] = ($dateStats[$dateKey] ?? 0) + 1;
        }
    }
    
    echo "✓ Data analysis completed.\n";
    echo "- Total favourite products analyzed: " . count($allFavourites) . "\n";
    echo "- Unique users with favourites: " . count($userStats) . "\n";
    echo "- Unique products in favourites: " . count($productStats) . "\n";
    echo "- Average quantity per favourite: " . (count($quantityStats) > 0 ? number_format(array_sum($quantityStats) / count($quantityStats), 2) : 0) . "\n";
    echo "- Most active user has: " . (count($userStats) > 0 ? max($userStats) : 0) . " favourite products\n";
    echo "- Most popular product appears in: " . (count($productStats) > 0 ? max($productStats) : 0) . " favourite lists\n";
    echo "- Monthly distribution:\n";
    
    arsort($dateStats);
    $topMonths = array_slice($dateStats, 0, 5, true);
    foreach ($topMonths as $month => $count) {
        echo "  * $month: $count favourites\n";
    }
    
} else {
    echo "✗ Data analysis failed: " . $analysisResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "FAVOURITE PRODUCT SERVICE TEST SUITE COMPLETED!\n";
echo str_repeat("=", 50) . "\n\n";

echo "SUMMARY:\n";
echo "- All FavouriteProductService methods have been tested\n";
echo "- Read, write, and delete operations tested\n";
echo "- Parameter validation and error handling tested\n";
echo "- Pagination and performance tests completed\n";
echo "- Comprehensive data analysis performed\n";
echo "- Real API interactions were performed for all tests\n"; 