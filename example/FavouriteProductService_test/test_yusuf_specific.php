<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== YUSUF KURNAZ (ID: 1055) FAVORİ ÜRÜN TESTİ ===\n\n";

// Initialize Ticimax
$ticimax = new Ticimax($mainDomain, $apiKey);

// Test 1: Önce mevcut favori ürünlerini kontrol et
echo "1. Yusuf'un mevcut favori ürünleri (ID: 1055):\n";
$existingFavourites = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => 1055,
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

echo "\n";

// Test 2: Yeni favori ürün ekle
echo "2. Yusuf için yeni favori ürün ekleme:\n";
$productIds = [1, 5, 10, 20, 25, 30];

foreach ($productIds as $productId) {
    echo "   Ürün ID $productId ekleniyor...\n";
    
    $addResult = $ticimax->favouriteProductService()->addFavouriteProduct(1055, $productId, 1);
    
    if ($addResult->isSuccess()) {
        echo "   ✓ Ürün ID $productId başarıyla eklendi\n";
        
        // Debug: Raw response'u göster
        if (method_exists($addResult, 'getRawResponse')) {
            echo "   Raw Response: " . print_r($addResult->getRawResponse(), true) . "\n";
        }
    } else {
        echo "   ✗ Ürün ID $productId eklenemedi: " . $addResult->getMessage() . "\n";
    }
    
    // Her ekleme sonrası kısa bekle
    usleep(500000); // 0.5 saniye bekle
}

echo "\n";

// Test 3: Ekleme sonrası tekrar kontrol et
echo "3. Ekleme sonrası favori ürünler:\n";
$updatedFavourites = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => 1055,
    'KayitSayisi' => 20,
    'SayfaNo' => 1
]);

if ($updatedFavourites->isSuccess()) {
    echo "✓ Güncel favori ürünler: " . count($updatedFavourites->getData()) . " adet\n";
    if (count($updatedFavourites->getData()) > 0) {
        foreach ($updatedFavourites->getData() as $index => $product) {
            echo "  - Ürün " . ($index + 1) . ": " . print_r($product, true) . "\n";
        }
    } else {
        echo "   ⚠ Hala favori ürün bulunamadı. API yapısını kontrol edelim...\n";
    }
} else {
    echo "✗ Güncel favori ürünler getirilemedi: " . $updatedFavourites->getMessage() . "\n";
}

echo "\n";

// Test 4: Farklı parametrelerle dene
echo "4. Farklı parametrelerle favori ürün getirme:\n";

// Parametresiz deneme
echo "   4a. Parametresiz deneme:\n";
$noParamFavourites = $ticimax->favouriteProductService()->getFavouriteProducts([]);
if ($noParamFavourites->isSuccess()) {
    echo "   ✓ Parametresiz: " . count($noParamFavourites->getData()) . " adet\n";
    if (count($noParamFavourites->getData()) > 0) {
        echo "   İlk ürün: " . print_r($noParamFavourites->getData()[0], true) . "\n";
    }
} else {
    echo "   ✗ Parametresiz getirilemedi: " . $noParamFavourites->getMessage() . "\n";
}

// Sadece UyeID ile deneme
echo "   4b. Sadece UyeID ile deneme:\n";
$userOnlyFavourites = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => 1055
]);
if ($userOnlyFavourites->isSuccess()) {
    echo "   ✓ Sadece UyeID: " . count($userOnlyFavourites->getData()) . " adet\n";
    if (count($userOnlyFavourites->getData()) > 0) {
        echo "   İlk ürün: " . print_r($userOnlyFavourites->getData()[0], true) . "\n";
    }
} else {
    echo "   ✗ Sadece UyeID getirilemedi: " . $userOnlyFavourites->getMessage() . "\n";
}

echo "\n";

// Test 5: SOAP response'u direkt kontrol et
echo "5. SOAP response direkt kontrolü:\n";
try {
    $soapClient = new SoapClient($mainDomain . '/services/TicimaxService.svc?wsdl');
    
    $soapParams = [
        'UyeKodu' => $apiKey,
        'request' => (object)[
            'UyeID' => 1055,
            'KayitSayisi' => 10,
            'SayfaNo' => 1,
            'BaslangicTarihi' => null,
            'BitisTarihi' => null
        ]
    ];
    
    $soapResponse = $soapClient->GetFavoriUrunler($soapParams);
    echo "   SOAP Response: " . print_r($soapResponse, true) . "\n";
    
    if (isset($soapResponse->GetFavoriUrunlerResult)) {
        $result = $soapResponse->GetFavoriUrunlerResult;
        echo "   IsError: " . ($result->IsError ? 'true' : 'false') . "\n";
        echo "   ErrorMessage: " . ($result->ErrorMessage ?? 'null') . "\n";
        
        if (isset($result->Urunler)) {
            echo "   Urunler field type: " . gettype($result->Urunler) . "\n";
            echo "   Urunler contents: " . print_r($result->Urunler, true) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ✗ SOAP hatası: " . $e->getMessage() . "\n";
}

echo "\n=== TEST TAMAMLANDI ===\n"; 