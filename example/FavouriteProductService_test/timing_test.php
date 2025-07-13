<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== TIMING & API BEHAVIOR TEST ===\n\n";

// Initialize Ticimax
$ticimax = new Ticimax($mainDomain, $apiKey);

$yusufUserId = 1055;
$productId = 16;

echo "1. Başlangıç durumu:\n";
$initialCheck = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 50
]);
echo "Başlangıç: " . count($initialCheck->getData()) . " favori ürün\n\n";

echo "2. 5 farklı ürün ekleyelim:\n";
$productIds = [16, 14, 12, 11, 10];
$addedCount = 0;

foreach ($productIds as $pid) {
    $addResult = $ticimax->favouriteProductService()->addFavouriteProduct($yusufUserId, $pid, 1);
    if ($addResult->isSuccess()) {
        $addedCount++;
        echo "✓ Ürün $pid eklendi\n";
    } else {
        echo "✗ Ürün $pid eklenemedi: " . $addResult->getMessage() . "\n";
    }
    
    // Kısa bekle
    usleep(200000); // 0.2 saniye
}

echo "\nToplam $addedCount ürün eklendi\n\n";

echo "3. Farklı bekleme süreleri ile test:\n";
$waitTimes = [0, 1, 3, 5, 10]; // saniye

foreach ($waitTimes as $waitTime) {
    if ($waitTime > 0) {
        echo "   $waitTime saniye bekleniyor...\n";
        sleep($waitTime);
    }
    
    $checkResult = $ticimax->favouriteProductService()->getFavouriteProducts([
        'UyeID' => $yusufUserId,
        'KayitSayisi' => 50
    ]);
    
    $count = count($checkResult->getData());
    echo "   ${waitTime}s sonra: $count favori ürün\n";
    
    if ($count > 0) {
        echo "   ✓ BULUNDU! Breaking loop.\n";
        break;
    }
}

echo "\n4. Farklı yaklaşımlar:\n";

// Test different approaches
echo "4a. Çok geniş tarih aralığı:\n";
$wideRange = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 100,
    'BaslangicTarihi' => date('Y-m-d', strtotime('-30 days')),
    'BitisTarihi' => date('Y-m-d', strtotime('+1 day'))
]);
echo "   Geniş tarih aralığı: " . count($wideRange->getData()) . " favori ürün\n";

echo "4b. Çok büyük sayfa boyutu:\n";
$bigPage = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 999,
    'SayfaNo' => 1
]);
echo "   Büyük sayfa: " . count($bigPage->getData()) . " favori ürün\n";

echo "4c. Farklı sayfalar:\n";
for ($page = 1; $page <= 5; $page++) {
    $pageResult = $ticimax->favouriteProductService()->getFavouriteProducts([
        'UyeID' => $yusufUserId,
        'KayitSayisi' => 20,
        'SayfaNo' => $page
    ]);
    echo "   Sayfa $page: " . count($pageResult->getData()) . " favori ürün\n";
}

echo "\n5. API davranışı testi:\n";

// Test if API is working correctly with other users
echo "5a. Admin kullanıcı ile test (ID: 1):\n";
$adminAdd = $ticimax->favouriteProductService()->addFavouriteProduct(1, 16, 1);
echo "   Admin add: " . ($adminAdd->isSuccess() ? "✓" : "✗") . "\n";

$adminGet = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => 1,
    'KayitSayisi' => 50
]);
echo "   Admin get: " . count($adminGet->getData()) . " favori ürün\n";

echo "5b. Birden fazla kullanıcı ile test:\n";
$testUsers = [2, 3, 1055];
foreach ($testUsers as $userId) {
    // Add favourite
    $userAdd = $ticimax->favouriteProductService()->addFavouriteProduct($userId, 14, 1);
    echo "   User $userId add: " . ($userAdd->isSuccess() ? "✓" : "✗") . "\n";
    
    // Get favourites
    $userGet = $ticimax->favouriteProductService()->getFavouriteProducts([
        'UyeID' => $userId,
        'KayitSayisi' => 20
    ]);
    echo "   User $userId get: " . count($userGet->getData()) . " favori ürün\n";
}

echo "\n6. Son kontrol:\n";
$finalCheck = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 100
]);
echo "Final check: " . count($finalCheck->getData()) . " favori ürün\n";

if (count($finalCheck->getData()) > 0) {
    echo "✓ Favori ürünler bulundu!\n";
    foreach ($finalCheck->getData() as $index => $fav) {
        echo "   " . ($index + 1) . ". " . print_r($fav, true) . "\n";
    }
} else {
    echo "❌ Hala favori ürün bulunamadı. Bu API'nin tasarım sorunu olabilir.\n";
}

echo "\n=== TEST TAMAMLANDI ===\n"; 