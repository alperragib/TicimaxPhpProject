<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== FAVORİ ÜRÜN SOAP RESPONSE DEBUG ===\n\n";

// Test user ID - Yusuf
$yusufUserId = 1055;

// Test product IDs
$testProductIds = [16, 14, 12];

echo "Debug için kullanılacak:\n";
echo "- Kullanıcı ID: $yusufUserId\n";
echo "- Test ürün ID'leri: " . implode(', ', $testProductIds) . "\n\n";

// Try direct SOAP call first
try {
    $soapClient = new SoapClient($mainDomain . '/services/TicimaxService.svc?wsdl');
    
    echo "1. SOAP ile favori ürün ekleme:\n";
    echo str_repeat("-", 40) . "\n";
    
    // Add a favourite product
    $addParams = [
        'UyeKodu' => $apiKey,
        'UyeID' => $yusufUserId,
        'UrunKartiID' => $testProductIds[0],
        'UrunSayisi' => 1.0
    ];
    
    echo "AddFavoriUrun parametreleri:\n";
    print_r($addParams);
    
    $addResponse = $soapClient->AddFavoriUrun($addParams);
    echo "\nAddFavoriUrun RAW response:\n";
    print_r($addResponse);
    
    echo "\n" . str_repeat("-", 40) . "\n";
    echo "2. SOAP ile favori ürün getirme:\n";
    
    // Get favourite products
    $getParams = [
        'UyeKodu' => $apiKey,
        'request' => (object)[
            'UyeID' => $yusufUserId,
            'KayitSayisi' => 20,
            'SayfaNo' => 1,
            'BaslangicTarihi' => null,
            'BitisTarihi' => null
        ]
    ];
    
    echo "GetFavoriUrunler parametreleri:\n";
    print_r($getParams);
    
    $getResponse = $soapClient->GetFavoriUrunler($getParams);
    echo "\nGetFavoriUrunler RAW response:\n";
    print_r($getResponse);
    
    echo "\n" . str_repeat("-", 40) . "\n";
    echo "3. Response analizi:\n";
    
    if (isset($getResponse->GetFavoriUrunlerResult)) {
        $result = $getResponse->GetFavoriUrunlerResult;
        
        echo "IsError: " . ($result->IsError ? 'true' : 'false') . "\n";
        echo "ErrorMessage: " . ($result->ErrorMessage ?? 'null') . "\n";
        
        if (isset($result->Urunler)) {
            echo "Urunler field type: " . gettype($result->Urunler) . "\n";
            echo "Urunler content:\n";
            print_r($result->Urunler);
            
            // Check if it's an object with properties
            if (is_object($result->Urunler)) {
                echo "\nUrunler object properties:\n";
                foreach ($result->Urunler as $key => $value) {
                    echo "  $key: " . gettype($value) . "\n";
                    if (is_object($value) || is_array($value)) {
                        echo "    Content: " . print_r($value, true) . "\n";
                    }
                }
            }
        } else {
            echo "Urunler field not found in response\n";
        }
    } else {
        echo "GetFavoriUrunlerResult not found in response\n";
    }
    
    echo "\n" . str_repeat("-", 40) . "\n";
    echo "4. Farklı parametrelerle deneme:\n";
    
    // Try without user ID
    $getParamsNoUser = [
        'UyeKodu' => $apiKey,
        'request' => (object)[
            'KayitSayisi' => 10,
            'SayfaNo' => 1,
            'BaslangicTarihi' => null,
            'BitisTarihi' => null
        ]
    ];
    
    echo "Parametresiz GetFavoriUrunler:\n";
    $getResponseNoUser = $soapClient->GetFavoriUrunler($getParamsNoUser);
    print_r($getResponseNoUser);
    
    // Try with different user ID
    $getParamsDifferentUser = [
        'UyeKodu' => $apiKey,
        'request' => (object)[
            'UyeID' => 1, // Different user
            'KayitSayisi' => 10,
            'SayfaNo' => 1,
            'BaslangicTarihi' => null,
            'BitisTarihi' => null
        ]
    ];
    
    echo "\nFarklı kullanıcı ID (1) ile GetFavoriUrunler:\n";
    $getResponseDifferentUser = $soapClient->GetFavoriUrunler($getParamsDifferentUser);
    print_r($getResponseDifferentUser);
    
} catch (Exception $e) {
    echo "SOAP Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "5. Ticimax SDK ile karşılaştırma:\n";

// Initialize Ticimax
$ticimax = new Ticimax($mainDomain, $apiKey);

// Add favourite
echo "SDK ile favori ürün ekleme:\n";
$addResult = $ticimax->favouriteProductService()->addFavouriteProduct($yusufUserId, $testProductIds[0], 1);
echo "SDK add result: " . ($addResult->isSuccess() ? 'Success' : 'Failed') . "\n";
echo "SDK add message: " . $addResult->getMessage() . "\n";

// Get favourites
echo "\nSDK ile favori ürün getirme:\n";
$getResult = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 20,
    'SayfaNo' => 1
]);

echo "SDK get result: " . ($getResult->isSuccess() ? 'Success' : 'Failed') . "\n";
echo "SDK get message: " . $getResult->getMessage() . "\n";
echo "SDK get data type: " . gettype($getResult->getData()) . "\n";
echo "SDK get data content:\n";
print_r($getResult->getData());

echo "\n=== DEBUG TAMAMLANDI ===\n"; 