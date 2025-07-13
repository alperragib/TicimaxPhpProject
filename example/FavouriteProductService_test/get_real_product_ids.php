<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== GERÇEK ÜRÜN ID'LERİNİ BULMA ===\n\n";

// Initialize Ticimax
$ticimax = new Ticimax($mainDomain, $apiKey);

// Get products from the system
echo "1. Sistemdeki ürünleri getiriyoruz:\n";
$productsResponse = $ticimax->productService()->getProducts([
    'KayitSayisi' => 20,
    'SayfaNo' => 1
]);

if ($productsResponse->isSuccess()) {
    $products = $productsResponse->getData();
    echo "✓ " . count($products) . " ürün bulundu\n\n";
    
    echo "Mevcut ürünler:\n";
    echo str_repeat("-", 80) . "\n";
    
    $realProductIds = [];
    
    foreach ($products as $index => $product) {
        echo sprintf("%-3d | ID: %-8s | %s\n", 
            $index + 1,
            $product->UrunKartiID ?? 'N/A',
            $product->UrunAdi ?? 'İsimsiz Ürün'
        );
        
        if (isset($product->UrunKartiID) && $product->UrunKartiID) {
            $realProductIds[] = $product->UrunKartiID;
        }
    }
    
    echo str_repeat("-", 80) . "\n";
    echo "Toplam " . count($realProductIds) . " geçerli ürün ID'si bulundu\n\n";
    
    // İlk 10 ürün ID'sini göster
    echo "İlk 10 geçerli ürün ID'si:\n";
    $firstTenIds = array_slice($realProductIds, 0, 10);
    foreach ($firstTenIds as $id) {
        echo "- $id\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Bu gerçek ID'lerle Yusuf'a favori ürün ekleyelim:\n\n";
    
    // Yusuf'a favori ürün ekle
    $yusufUserId = 1055;
    $addedCount = 0;
    
    foreach ($firstTenIds as $productId) {
        echo "Ürün ID $productId ekleniyor...\n";
        
        $addResult = $ticimax->favouriteProductService()->addFavouriteProduct($yusufUserId, $productId, 1);
        
        if ($addResult->isSuccess()) {
            echo "✓ Ürün ID $productId başarıyla eklendi\n";
            $addedCount++;
        } else {
            echo "✗ Ürün ID $productId eklenemedi: " . $addResult->getMessage() . "\n";
        }
        
        // Kısa bekle
        usleep(300000); // 0.3 saniye
    }
    
    echo "\n" . str_repeat("-", 50) . "\n";
    echo "Toplam $addedCount ürün eklendi\n\n";
    
    // Şimdi favori ürünleri kontrol et
    echo "Yusuf'un favori ürünlerini kontrol ediyoruz:\n";
    $favouritesResponse = $ticimax->favouriteProductService()->getFavouriteProducts([
        'UyeID' => $yusufUserId,
        'KayitSayisi' => 20,
        'SayfaNo' => 1
    ]);
    
    if ($favouritesResponse->isSuccess()) {
        $favourites = $favouritesResponse->getData();
        echo "✓ " . count($favourites) . " favori ürün bulundu\n";
        
        if (count($favourites) > 0) {
            echo "\nFavori ürünler:\n";
            foreach ($favourites as $index => $favourite) {
                echo sprintf("%-3d | Ürün ID: %-8s | %s\n", 
                    $index + 1,
                    $favourite->UrunKartiID ?? 'N/A',
                    $favourite->UrunAdi ?? 'İsimsiz Ürün'
                );
            }
        } else {
            echo "⚠ Hala favori ürün bulunamadı!\n";
            echo "Bu durumda API'nin farklı bir davranışı olabilir.\n";
        }
    } else {
        echo "✗ Favori ürünler getirilemedi: " . $favouritesResponse->getMessage() . "\n";
    }
    
} else {
    echo "✗ Ürünler getirilemedi: " . $productsResponse->getMessage() . "\n";
}

echo "\n=== TEST TAMAMLANDI ===\n"; 