<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== ANINDA FAVORİ ÜRÜN TEST ===\n\n";

// Initialize Ticimax
$ticimax = new Ticimax($mainDomain, $apiKey);

$yusufUserId = 1055;
$productId = 16; // Bildiğimiz gerçek ürün ID'si

echo "1. Yusuf'un mevcut favori ürünleri:\n";
$beforeFavourites = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 20,
    'SayfaNo' => 1
]);
echo "Önce: " . count($beforeFavourites->getData()) . " favori ürün\n\n";

echo "2. Favori ürün ekleme:\n";
$addResult = $ticimax->favouriteProductService()->addFavouriteProduct($yusufUserId, $productId, 1);
if ($addResult->isSuccess()) {
    echo "✓ Ürün başarıyla eklendi\n";
} else {
    echo "✗ Ürün eklenemedi: " . $addResult->getMessage() . "\n";
}

echo "\n3. Hemen sonra favori ürünler:\n";
$afterFavourites = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 20,
    'SayfaNo' => 1
]);
echo "Sonra: " . count($afterFavourites->getData()) . " favori ürün\n\n";

echo "4. Farklı parametrelerle denemeler:\n";

// Date range ile
$withDateRange = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 20,
    'SayfaNo' => 1,
    'BaslangicTarihi' => date('Y-m-d', strtotime('-1 day')),
    'BitisTarihi' => date('Y-m-d', strtotime('+1 day'))
]);
echo "Tarih aralığı ile: " . count($withDateRange->getData()) . " favori ürün\n";

// Bigger page size
$biggerPage = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 100,
    'SayfaNo' => 1
]);
echo "Büyük sayfa ile: " . count($biggerPage->getData()) . " favori ürün\n";

// Page 2
$page2 = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 20,
    'SayfaNo' => 2
]);
echo "Sayfa 2: " . count($page2->getData()) . " favori ürün\n";

echo "\n5. Başka bir ürün daha ekleyelim:\n";
$productId2 = 14;
$addResult2 = $ticimax->favouriteProductService()->addFavouriteProduct($yusufUserId, $productId2, 1);
if ($addResult2->isSuccess()) {
    echo "✓ İkinci ürün başarıyla eklendi\n";
} else {
    echo "✗ İkinci ürün eklenemedi: " . $addResult2->getMessage() . "\n";
}

echo "\n6. İki ürün sonrası kontrol:\n";
$afterTwoFavourites = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 20,
    'SayfaNo' => 1
]);
echo "İki ürün sonra: " . count($afterTwoFavourites->getData()) . " favori ürün\n";

echo "\n7. Direkt SOAP çağrısı ile kontrol:\n";
try {
    // Using TicimaxRequest to get the SOAP client
    $reflection = new ReflectionClass($ticimax);
    $requestProperty = $reflection->getProperty('request');
    $requestProperty->setAccessible(true);
    $request = $requestProperty->getValue($ticimax);
    
    $client = $request->soap_client('/Servis/CustomServis.svc?singleWsdl');
    
    $soapParams = [
        'UyeKodu' => $config['apiKey'],
        'request' => (object)[
            'UyeID' => $yusufUserId,
            'KayitSayisi' => 20,
            'SayfaNo' => 1,
            'BaslangicTarihi' => null,
            'BitisTarihi' => null
        ]
    ];
    
    $soapResponse = $client->__soapCall("GetFavoriUrunler", [$soapParams]);
    
    echo "SOAP Response:\n";
    print_r($soapResponse);
    
} catch (Exception $e) {
    echo "SOAP Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST TAMAMLANDI ===\n"; 