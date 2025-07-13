<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== YUSUF'A GERÇEK ÜRÜN ID'LERİYLE FAVORİ EKLEME ===\n\n";

// Initialize Ticimax
$ticimax = new Ticimax($mainDomain, $apiKey);

// Real product IDs from the system
$realProductIds = [16, 14, 12, 11, 10, 9, 8, 7, 6, 5];
$yusufUserId = 1055;

echo "Gerçek ürün ID'leri: " . implode(', ', $realProductIds) . "\n";
echo "Yusuf'un kullanıcı ID'si: $yusufUserId\n\n";

// Test 1: Önce mevcut favori ürünlerini kontrol et
echo "1. Yusuf'un mevcut favori ürünleri:\n";
$existingFavourites = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 20,
    'SayfaNo' => 1
]);

if ($existingFavourites->isSuccess()) {
    echo "✓ Mevcut favori ürünler: " . count($existingFavourites->getData()) . " adet\n";
    if (count($existingFavourites->getData()) > 0) {
        foreach ($existingFavourites->getData() as $index => $product) {
            echo "  - Ürün " . ($index + 1) . ": " . print_r($product, true) . "\n";
        }
    }
} else {
    echo "✗ Mevcut favori ürünler getirilemedi: " . $existingFavourites->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n";

// Test 2: Gerçek ID'lerle favori ürün ekle
echo "2. Gerçek ürün ID'leriyle favori ürün ekleme:\n";
$addedCount = 0;
$failedCount = 0;

foreach ($realProductIds as $productId) {
    echo "   Ürün ID $productId ekleniyor...\n";
    
    $addResult = $ticimax->favouriteProductService()->addFavouriteProduct($yusufUserId, $productId, 1);
    
    if ($addResult->isSuccess()) {
        echo "   ✓ Ürün ID $productId başarıyla eklendi\n";
        $addedCount++;
    } else {
        echo "   ✗ Ürün ID $productId eklenemedi: " . $addResult->getMessage() . "\n";
        $failedCount++;
    }
    
    // Kısa bekle
    usleep(300000); // 0.3 saniye
}

echo "\n   Özet: $addedCount ürün eklendi, $failedCount ürün eklenemedi\n";

echo "\n" . str_repeat("-", 50) . "\n";

// Test 3: Ekleme sonrası favori ürünleri kontrol et
echo "3. Ekleme sonrası favori ürünler:\n";
$updatedFavourites = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 20,
    'SayfaNo' => 1
]);

if ($updatedFavourites->isSuccess()) {
    echo "✓ Güncel favori ürünler: " . count($updatedFavourites->getData()) . " adet\n";
    if (count($updatedFavourites->getData()) > 0) {
        foreach ($updatedFavourites->getData() as $index => $product) {
            echo sprintf("  %d. Ürün ID: %s | %s\n", 
                $index + 1,
                $product->UrunKartiID ?? 'N/A',
                $product->UrunAdi ?? 'İsimsiz Ürün'
            );
        }
    } else {
        echo "   ⚠ Hala favori ürün bulunamadı!\n";
        echo "   Bu durumda API'nin özel bir davranışı olabilir.\n";
        echo "   Farklı parametrelerle tekrar deneyelim...\n\n";
        
        // Farklı parametrelerle deneme
        echo "   Farklı parametrelerle deneme:\n";
        
        // Parametresiz
        $noParamResult = $ticimax->favouriteProductService()->getFavouriteProducts([]);
        $noParamData = $noParamResult->getData();
        echo "   - Parametresiz: " . (is_array($noParamData) ? count($noParamData) : 0) . " adet\n";
        
        // Sadece UyeID
        $userOnlyResult = $ticimax->favouriteProductService()->getFavouriteProducts(['UyeID' => $yusufUserId]);
        $userOnlyData = $userOnlyResult->getData();
        echo "   - Sadece UyeID: " . (is_array($userOnlyData) ? count($userOnlyData) : 0) . " adet\n";
        
        // Daha fazla kayıt
        $moreRecordsResult = $ticimax->favouriteProductService()->getFavouriteProducts([
            'UyeID' => $yusufUserId,
            'KayitSayisi' => 100,
            'SayfaNo' => 1
        ]);
        $moreRecordsData = $moreRecordsResult->getData();
        echo "   - Daha fazla kayıt: " . (is_array($moreRecordsData) ? count($moreRecordsData) : 0) . " adet\n";
    }
} else {
    echo "✗ Güncel favori ürünler getirilemedi: " . $updatedFavourites->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Test 4: Direct SOAP call to understand the issue
echo "4. Direct SOAP call ile favori ürün ekleme test:\n";
try {
    $soapClient = new SoapClient($mainDomain . '/services/TicimaxService.svc?wsdl');
    
    // Favori ürün ekleme
    $addParams = [
        'UyeKodu' => $apiKey,
        'UyeID' => $yusufUserId,
        'UrunKartiID' => $realProductIds[0], // İlk ürün
        'UrunSayisi' => 1.0
    ];
    
    echo "   SOAP AddFavoriUrun parametreleri:\n";
    print_r($addParams);
    
    $addResponse = $soapClient->AddFavoriUrun($addParams);
    echo "   SOAP AddFavoriUrun response:\n";
    print_r($addResponse);
    
    // Favori ürün getirme
    $getParams = [
        'UyeKodu' => $apiKey,
        'request' => (object)[
            'UyeID' => $yusufUserId,
            'KayitSayisi' => 10,
            'SayfaNo' => 1
        ]
    ];
    
    echo "\n   SOAP GetFavoriUrunler parametreleri:\n";
    print_r($getParams);
    
    $getResponse = $soapClient->GetFavoriUrunler($getParams);
    echo "   SOAP GetFavoriUrunler response:\n";
    print_r($getResponse);
    
} catch (Exception $e) {
    echo "   SOAP Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST TAMAMLANDI ===\n"; 