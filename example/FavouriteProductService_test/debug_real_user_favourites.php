<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== GERÇEK KULLANICI FAVORİLERİ DEBUG ===\n\n";

// Initialize Ticimax
$ticimax = new Ticimax($mainDomain, $apiKey);

// Test different user IDs
$testUserIds = [1, 2, 3, 1055];

foreach ($testUserIds as $userId) {
    echo "Kullanıcı ID $userId için favori ürün kontrolü:\n";
    echo str_repeat("-", 40) . "\n";
    
    $favouritesResponse = $ticimax->favouriteProductService()->getFavouriteProducts([
        'UyeID' => $userId,
        'KayitSayisi' => 10,
        'SayfaNo' => 1
    ]);
    
    if ($favouritesResponse->isSuccess()) {
        $favourites = $favouritesResponse->getData();
        echo "✓ Başarılı - " . count($favourites) . " favori ürün\n";
        
        if (count($favourites) > 0) {
            echo "İlk favori ürün:\n";
            print_r($favourites[0]);
        }
    } else {
        echo "✗ Hata: " . $favouritesResponse->getMessage() . "\n";
    }
    
    echo "\n";
}

echo str_repeat("=", 50) . "\n";
echo "Tüm kullanıcılar için favori ürünler (UyeID parametresi olmadan):\n";

$allFavouritesResponse = $ticimax->favouriteProductService()->getFavouriteProducts([
    'KayitSayisi' => 50,
    'SayfaNo' => 1
]);

if ($allFavouritesResponse->isSuccess()) {
    $allFavourites = $allFavouritesResponse->getData();
    echo "✓ Başarılı - " . count($allFavourites) . " favori ürün\n";
    
    if (count($allFavourites) > 0) {
        echo "İlk favori ürün:\n";
        print_r($allFavourites[0]);
        
        echo "\nTüm favori ürünlerin kullanıcı ID'leri:\n";
        foreach ($allFavourites as $index => $favourite) {
            echo "- Favori " . ($index + 1) . ": ";
            if (isset($favourite->UyeID)) {
                echo "UyeID: " . $favourite->UyeID;
            } else {
                echo "UyeID bulunamadı";
            }
            
            if (isset($favourite->UrunAdi)) {
                echo " - " . $favourite->UrunAdi;
            }
            echo "\n";
        }
    } else {
        echo "Hiç favori ürün bulunamadı\n";
    }
} else {
    echo "✗ Hata: " . $allFavouritesResponse->getMessage() . "\n";
}

echo "\n=== DEBUG TAMAMLANDI ===\n"; 