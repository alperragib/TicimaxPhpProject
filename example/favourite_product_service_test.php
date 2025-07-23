<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Response\ApiResponse;

// Load configuration
$config = require __DIR__ . '/../config.php';

echo "=== FAVOURITE PRODUCT SERVICE TESTS ===\n\n";

try {
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $favouriteService = $ticimax->favouriteProductService();

    $testUserId = 1055;
    $testProductId = 16;

    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;

    echo "----------------------------------------\n";
    echo "         FAVOURITE PRODUCT TESTS\n";
    echo "----------------------------------------\n\n";

    // Test 1
    echo "[Test 1] Get favourites with NULL user ID\n";
    echo "----------------------------------------\n";
    $testCount++;
    $response = $favouriteService->getFavouriteProducts(['UyeID' => null]);

    if ($response->isSuccess()) {
        $successCount++;
        $favs = $response->getData();
        echo "Status     : SUCCESS\n";
        echo "Count      : " . count($favs) . "\n";
    } else {
        $errorCount++;
        echo "Status     : FAILED\n";
        echo "Message    : " . $response->getMessage() . "\n";
    }
    echo "\n";

    // Test 2
    echo "[Test 2] Get favourites with valid user ID ($testUserId)\n";
    echo "----------------------------------------\n";
    $testCount++;
    $response = $favouriteService->getFavouriteProducts(['UyeID' => $testUserId]);

    if ($response->isSuccess()) {
        $successCount++;
        $favs = $response->getData();
        echo "Status     : SUCCESS\n";
        echo "Count      : " . count($favs) . "\n";

        if (!empty($favs)) {
            $p = $favs[0];
            echo "\nSample Product:\n";
            echo "- Product ID     : " . ($p->UrunKartiID ?? 'N/A') . "\n";
            echo "- Name           : " . ($p->UrunAdi ?? 'N/A') . "\n";
            echo "- Price          : " . ($p->UrunFiyati ?? 'N/A') . " " . ($p->ParaBirimi ?? 'TL') . "\n";
            echo "- Added Date     : " . ($p->EklemeTarihi ?? 'N/A') . "\n";
            echo "- Stock Code     : " . ($p->StokKodu ?? 'N/A') . "\n";
            echo "- Stock Quantity : " . ($p->ToplamStokAdedi ?? 'N/A') . "\n";
            echo "- Variation Count: " . ($p->VaryasyonSayisi ?? 'N/A') . "\n";
            echo "- Image URL      : " . ($p->ResimUrl ?? 'N/A') . "\n";
            echo "- Product URL    : " . ($p->UrunUrl ?? 'N/A') . "\n";
        }
    } else {
        $errorCount++;
        echo "Status     : FAILED\n";
        echo "Message    : " . $response->getMessage() . "\n";
    }
    echo "\n";

    // Test 3
    echo "[Test 3] Get favourites with date range\n";
    echo "----------------------------------------\n";
    $testCount++;
    $start = date('Y-m-d', strtotime('-30 days'));
    $end = date('Y-m-d');
    $response = $favouriteService->getFavouriteProducts([
        'UyeID' => $testUserId,
        'BaslangicTarihi' => $start,
        'BitisTarihi' => $end
    ]);

    if ($response->isSuccess()) {
        $successCount++;
        $favs = $response->getData();
        echo "Status     : SUCCESS\n";
        echo "Start Date : $start\n";
        echo "End Date   : $end\n";
        echo "Count      : " . count($favs) . "\n";
    } else {
        $errorCount++;
        echo "Status     : FAILED\n";
        echo "Message    : " . $response->getMessage() . "\n";
    }
    echo "\n";

    // Test 4
    echo "[Test 4] Get favourites with pagination\n";
    echo "----------------------------------------\n";
    $testCount++;
    $page = 1;
    $size = 10;

    $response = $favouriteService->getFavouriteProducts([
        'UyeID' => $testUserId,
        'KayitSayisi' => $size,
        'SayfaNo' => $page
    ]);

    if ($response->isSuccess()) {
        $successCount++;
        $favs = $response->getData();
        echo "Status     : SUCCESS\n";
        echo "Page       : $page\n";
        echo "Page Size  : $size\n";
        echo "Count      : " . count($favs) . "\n";

        if (!empty($favs)) {
            echo "\nSample Product:\n";
            $p = $favs[0];
            echo "- Product ID     : " . ($p->UrunKartiID ?? 'N/A') . "\n";
            echo "- Name           : " . ($p->UrunAdi ?? 'N/A') . "\n";
            echo "- Price          : " . ($p->UrunFiyati ?? 'N/A') . " " . ($p->ParaBirimi ?? 'TL') . "\n";
            echo "- Added Date     : " . ($p->EklemeTarihi ?? 'N/A') . "\n";
            echo "- Stock Code     : " . ($p->StokKodu ?? 'N/A') . "\n";
            echo "- Stock Quantity : " . ($p->ToplamStokAdedi ?? 'N/A') . "\n";
            echo "- Variation Count: " . ($p->VaryasyonSayisi ?? 'N/A') . "\n";
            echo "- Image URL      : " . ($p->ResimUrl ?? 'N/A') . "\n";
            echo "- Product URL    : " . ($p->UrunUrl ?? 'N/A') . "\n";
        }
    } else {
        $errorCount++;
        echo "Status     : FAILED\n";
        echo "Message    : " . $response->getMessage() . "\n";
    }
    echo "\n";

    // Test 5
    echo "[Test 5] Add favourite with invalid user\n";
    echo "----------------------------------------\n";
    $testCount++;
    $response = $favouriteService->addFavouriteProduct(1055, $testProductId);

    if ($response->isSuccess()) {
        $successCount++;
        echo "Status     : SUCCESS\n";
        echo "Message    : " . $response->getMessage() . "\n";
    } else {
        $errorCount++;
        echo "Status     : FAILED\n";
        echo "Message    : " . $response->getMessage() . "\n";
    }
    echo "\n";

    // Test 6
    echo "[Test 6] Add favourite with valid user\n";
    echo "----------------------------------------\n";
    $testCount++;
    $response = $favouriteService->addFavouriteProduct($testUserId, $testProductId);

    if ($response->isSuccess()) {
        $successCount++;
        echo "Status     : SUCCESS\n";
        echo "Message    : " . $response->getMessage() . "\n";
    } else {
        $errorCount++;
        echo "Status     : FAILED\n";
        echo "Message    : " . $response->getMessage() . "\n";
    }
    echo "\n";

    // Summary
    echo "----------------------------------------\n";
    echo "             TEST SUMMARY\n";
    echo "----------------------------------------\n";
    echo "Total Tests : $testCount\n";
    echo "Successful  : $successCount\n";
    echo "Failed      : $errorCount\n";
    echo "Success Rate: " . round(($successCount / $testCount) * 100, 1) . "%\n\n";

    echo "Functions Tested:\n";
    echo "- getFavouriteProducts()\n";
    echo "- addFavouriteProduct()\n";

} catch (Exception $e) {
    echo "ERROR       : " . $e->getMessage() . "\n";
    echo "File        : " . $e->getFile() . "\n";
    echo "Line        : " . $e->getLine() . "\n";
}
