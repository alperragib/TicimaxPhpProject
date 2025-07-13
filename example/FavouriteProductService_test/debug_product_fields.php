<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== PRODUCT SERVICE FIELD DEBUG ===\n\n";

// Initialize Ticimax
$ticimax = new Ticimax($mainDomain, $apiKey);

// Get products from the system
echo "1. Sistemdeki ürünleri getiriyoruz:\n";
$productsResponse = $ticimax->productService()->getProducts([
    'KayitSayisi' => 3,
    'SayfaNo' => 1
]);

if ($productsResponse->isSuccess()) {
    $products = $productsResponse->getData();
    echo "✓ " . count($products) . " ürün bulundu\n\n";
    
    if (count($products) > 0) {
        echo "İlk ürünün RAW yapısı:\n";
        echo str_repeat("=", 60) . "\n";
        
        $firstProduct = $products[0];
        echo "Raw product object:\n";
        print_r($firstProduct);
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "Mevcut field'lar:\n";
        
        foreach ($firstProduct as $field => $value) {
            echo sprintf("%-20s | %s\n", $field, 
                is_string($value) ? (strlen($value) > 50 ? substr($value, 0, 50) . "..." : $value) : 
                (is_numeric($value) ? $value : gettype($value))
            );
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "Muhtemel ID field'ları:\n";
        
        $idFields = ['ID', 'UrunID', 'UrunKartiID', 'ProductID', 'KartID', 'UrunNo', 'ProductCardID'];
        foreach ($idFields as $field) {
            if (isset($firstProduct->$field)) {
                echo "✓ $field: " . $firstProduct->$field . "\n";
            } else {
                echo "✗ $field: Bulunamadı\n";
            }
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "Tüm ürünlerin muhtemel ID'leri:\n";
        
        foreach ($products as $index => $product) {
            echo "\nÜrün " . ($index + 1) . ":\n";
            foreach ($idFields as $field) {
                if (isset($product->$field) && $product->$field) {
                    echo "  $field: " . $product->$field . "\n";
                }
            }
        }
        
    } else {
        echo "⚠ Ürün bulunamadı\n";
    }
} else {
    echo "✗ Ürünler getirilemedi: " . $productsResponse->getMessage() . "\n";
}

echo "\n=== SOAP RAW RESPONSE DEBUG ===\n";

// Direct SOAP call to see raw response
try {
    $soapClient = new SoapClient($mainDomain . '/services/TicimaxService.svc?wsdl');
    
    $soapParams = [
        'UyeKodu' => $apiKey,
        'request' => (object)[
            'KayitSayisi' => 2,
            'SayfaNo' => 1
        ]
    ];
    
    $soapResponse = $soapClient->GetUrunler($soapParams);
    echo "Raw SOAP Response:\n";
    print_r($soapResponse);
    
} catch (Exception $e) {
    echo "SOAP Error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG TAMAMLANDI ===\n"; 