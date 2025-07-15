<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$favoriteProductService = $ticimax->favouriteProductService();


echo "=== Remove Favourite Product Example ===\n";

// Example: Remove favourite product
echo "Removing product from favourites:\n";

$userId = 1;
$favouriteProductId = 10;

$result = $favoriteProductService->removeFavouriteProduct($userId, $favouriteProductId);

if (!$result['IsError']) {
    echo "Product removed from favourites successfully!\n";
} else {
    echo "Failed to remove product from favourites: " . $result['ErrorMessage'] . "\n";
}

echo "\n=== List Favorite Products ===\n";

$parameters = [
    'BaslangicTarihi' => null,
    'BitisTarihi' => null,
    'KayitSayisi' => 20,
    'SayfaNo' => 1,
    'UyeID' => 1,
];

$products = $favoriteProductService->getFavouriteProducts($parameters);

foreach ($products as $product) {
    $uyeID = $product->UyeID ?? '[No UyeID]';
    $favoriUrunID = $product->FavoriUrunID ?? '[No FavoriUrunID]';
    $urunKartiID = $product->UrunKartiID ?? '[No UrunKartiID]';
    $urunAdi = $product->UrunAdi ?? '[No UrunAdi]';
    $urunFiyati = $product->UrunFiyati ?? '[No UrunFiyati]';
    $urunFiyatiKdv = $product->UrunFiyatiKdv ?? '[No UrunFiyatiKdv  ]';
    $paraBirimi = $product->ParaBirimi ?? '[No ParaBirimi]';
    $toplamStokAdedi = $product->ToplamStokAdedi ?? '[No ToplamStokAdedi]';
    $resimUrl = $product->ResimUrl ?? '[No ResimUrl]';

    echo "\nUyeID                : $uyeID";
    echo "\nFavoriUrunID         : $favoriUrunID";
    echo "\nUrunKartiID          : $urunKartiID";
    echo "\nUrunAdi              : $urunAdi";
    echo "\nUrunFiyati           : $urunFiyati";
    echo "\nUrunFiyatiKdv        : $urunFiyatiKdv";
    echo "\nParaBirimi           : $paraBirimi";
    echo "\nToplamStokAdedi      : $toplamStokAdedi";
    echo "\nResimUrl             : $resimUrl";

    echo "\n\n---------------------------------------------\n";
}
