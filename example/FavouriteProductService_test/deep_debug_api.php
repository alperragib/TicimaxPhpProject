<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== DEEP API DEBUG - FAVORİ ÜRÜN RESPONSE ANALİZİ ===\n\n";

// Initialize Ticimax
$ticimax = new Ticimax($mainDomain, $apiKey);

$yusufUserId = 1055;
$productId = 16;

echo "1. Favori ürün ekle:\n";
$addResult = $ticimax->favouriteProductService()->addFavouriteProduct($yusufUserId, $productId, 1);
echo "Ekleme sonucu: " . ($addResult->isSuccess() ? "✓ Başarılı" : "✗ Başarısız") . "\n\n";

echo "2. SOAP Response Derinlemesine Analiz:\n";
echo str_repeat("=", 60) . "\n";

try {
    // Get the SOAP client using reflection
    $reflection = new ReflectionClass($ticimax);
    $requestProperty = $reflection->getProperty('request');
    $requestProperty->setAccessible(true);
    $request = $requestProperty->getValue($ticimax);
    
    $client = $request->soap_client('/Servis/CustomServis.svc?singleWsdl');
    
    $soapParams = [
        'UyeKodu' => $apiKey,
        'request' => (object)[
            'UyeID' => $yusufUserId,
            'KayitSayisi' => 50,
            'SayfaNo' => 1,
            'BaslangicTarihi' => null,
            'BitisTarihi' => null
        ]
    ];
    
    $soapResponse = $client->__soapCall("GetFavoriUrunler", [$soapParams]);
    
    echo "FULL SOAP Response:\n";
    print_r($soapResponse);
    
    echo "\n" . str_repeat("-", 60) . "\n";
    echo "Response Structure Analysis:\n";
    
    if (isset($soapResponse->GetFavoriUrunlerResult)) {
        $result = $soapResponse->GetFavoriUrunlerResult;
        
        echo "✓ GetFavoriUrunlerResult found\n";
        echo "  - IsError: " . ($result->IsError ? 'true' : 'false') . "\n";
        echo "  - ErrorMessage: " . ($result->ErrorMessage ?? 'null') . "\n";
        
        echo "\n  All fields in GetFavoriUrunlerResult:\n";
        foreach ($result as $fieldName => $fieldValue) {
            echo "    [$fieldName]: " . gettype($fieldValue) . "\n";
            
            if (is_object($fieldValue)) {
                echo "      Object contents:\n";
                foreach ($fieldValue as $subField => $subValue) {
                    echo "        [$subField]: " . gettype($subValue) . " = ";
                    if (is_string($subValue) || is_numeric($subValue)) {
                        echo "$subValue\n";
                    } else {
                        echo "(" . gettype($subValue) . ")\n";
                    }
                }
            } elseif (is_array($fieldValue)) {
                echo "      Array with " . count($fieldValue) . " elements\n";
                foreach ($fieldValue as $index => $item) {
                    echo "        [$index]: " . gettype($item) . "\n";
                }
            } else {
                echo "      Value: $fieldValue\n";
            }
        }
        
        // Check Urunler field specifically
        if (isset($result->Urunler)) {
            echo "\n  Urunler field detailed analysis:\n";
            $urunler = $result->Urunler;
            
            echo "    Type: " . gettype($urunler) . "\n";
            echo "    Is empty: " . (empty($urunler) ? 'true' : 'false') . "\n";
            
            if (is_object($urunler)) {
                $urunlerArray = (array)$urunler;
                echo "    Properties count: " . count($urunlerArray) . "\n";
                
                if (count($urunlerArray) > 0) {
                    echo "    Properties:\n";
                    foreach ($urunlerArray as $prop => $value) {
                        echo "      [$prop]: " . gettype($value) . "\n";
                        if (is_object($value) || is_array($value)) {
                            echo "        Content: " . print_r($value, true) . "\n";
                        }
                    }
                }
            }
        }
    }
    
    echo "\n" . str_repeat("-", 60) . "\n";
    echo "3. Farklı kullanıcı ID'leri ile test:\n";
    
    $testUserIds = [1, 2, 3, 1055];
    
    foreach ($testUserIds as $testUserId) {
        echo "\nKullanıcı ID: $testUserId\n";
        
        $testParams = [
            'UyeKodu' => $apiKey,
            'request' => (object)[
                'UyeID' => $testUserId,
                'KayitSayisi' => 10,
                'SayfaNo' => 1,
                'BaslangicTarihi' => null,
                'BitisTarihi' => null
            ]
        ];
        
        $testResponse = $client->__soapCall("GetFavoriUrunler", [$testParams]);
        
        if (isset($testResponse->GetFavoriUrunlerResult->Urunler)) {
            $testUrunler = $testResponse->GetFavoriUrunlerResult->Urunler;
            $testArray = (array)$testUrunler;
            
            echo "  - Urunler count: " . count($testArray) . "\n";
            
            if (count($testArray) > 0) {
                echo "  - HAS DATA! First item:\n";
                foreach ($testArray as $key => $value) {
                    echo "    [$key]: " . print_r($value, true) . "\n";
                }
            }
        }
    }
    
    echo "\n" . str_repeat("-", 60) . "\n";
    echo "4. Parametresiz deneme:\n";
    
    $noUserParams = [
        'UyeKodu' => $apiKey,
        'request' => (object)[
            'KayitSayisi' => 100,
            'SayfaNo' => 1,
            'BaslangicTarihi' => null,
            'BitisTarihi' => null
        ]
    ];
    
    $noUserResponse = $client->__soapCall("GetFavoriUrunler", [$noUserParams]);
    
    if (isset($noUserResponse->GetFavoriUrunlerResult->Urunler)) {
        $noUserUrunler = $noUserResponse->GetFavoriUrunlerResult->Urunler;
        $noUserArray = (array)$noUserUrunler;
        
        echo "Parametresiz Urunler count: " . count($noUserArray) . "\n";
        
        if (count($noUserArray) > 0) {
            echo "PARAMETRESIZ DATA VAR! First item:\n";
            foreach ($noUserArray as $key => $value) {
                echo "  [$key]: " . print_r($value, true) . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "SOAP Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "5. Mevcut FavouriteProductService kodunu kontrol:\n";

echo "SDK ile test:\n";
$sdkResult = $ticimax->favouriteProductService()->getFavouriteProducts([
    'UyeID' => $yusufUserId,
    'KayitSayisi' => 50
]);

echo "SDK Success: " . ($sdkResult->isSuccess() ? 'true' : 'false') . "\n";
echo "SDK Data count: " . count($sdkResult->getData()) . "\n";

echo "\n=== DEBUG TAMAMLANDI ===\n"; 