<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;

// Config dosyasını yükle
$config = require __DIR__ . '/../config.php';

echo "=== GetKargoSecenek Debug Test ===\n\n";

try {
    // Ticimax servisini başlat
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $shippingService = $ticimax->shippingService();

    // Test için dolu sepet objesi oluştur
    $cart = (object)[
        'SepetID' => 1,
        'UyeID' => 1055,
        'GenelToplam' => 1250.0,
        'ToplamKDV' => 208.33,
        'ToplamUrunAdedi' => 1,
        'SepetParaBirimiDilKodu' => 'tr-TR',
        'Urunler' => [
            (object)[
                'UrunID' => 16,
                'UrunAdi' => 'Koku Deneyimleme Kiti',
                'Adet' => 1,
                'Fiyati' => 1250.0,
                'KDVTutari' => 208.33,
                'StokKodu' => 'SCENT-KIT-001',
                'Desi' => 2.5
            ]
        ]
    ];

    // Get request through Ticimax factory
    $cartService = $ticimax->cartService();
    $client = $cartService->request->soap_client("/Servis/SiparisServis.svc?singleWsdl");
    
    $requestData = [
        'SehirId' => 34, // İstanbul
        'ParaBirimi' => 'TL',
        'Sepet' => $cart
    ];

    echo "Request Data:\n";
    echo "=============\n";
    echo "SehirId: " . $requestData['SehirId'] . "\n";
    echo "ParaBirimi: " . $requestData['ParaBirimi'] . "\n";
    echo "Sepet Genel Toplam: " . $requestData['Sepet']->GenelToplam . "\n";
    echo "Sepet Ürün Sayısı: " . count($requestData['Sepet']->Urunler) . "\n\n";

    echo "Making SOAP Call to GetKargoSecenek...\n";
    echo "=====================================\n";

    $response = $client->__soapCall("GetKargoSecenek", [
        [
            'UyeKodu' => $cartService->request->key,
            'request' => (object)$requestData
        ]
    ]);

    echo "Raw SOAP Response:\n";
    echo "==================\n";
    print_r($response);
    
    if (isset($response->GetKargoSecenekResult)) {
        echo "\n\nGetKargoSecenekResult Details:\n";
        echo "==============================\n";
        $result = $response->GetKargoSecenekResult;
        
        echo "Result Type: " . gettype($result) . "\n";
        
        if (is_object($result)) {
            echo "Result Properties:\n";
            foreach (get_object_vars($result) as $key => $value) {
                echo "- $key: " . gettype($value) . "\n";
                if (is_object($value) || is_array($value)) {
                    echo "  Content: ";
                    print_r($value);
                } else {
                    echo "  Value: $value\n";
                }
            }
        } elseif (is_array($result)) {
            echo "Result Array Count: " . count($result) . "\n";
            foreach ($result as $index => $item) {
                echo "Item $index:\n";
                print_r($item);
            }
        } else {
            echo "Result Value: $result\n";
        }
        
        // Check if result is empty
        if (empty((array)$result)) {
            echo "\n⚠️ Result is empty - no shipping options available\n";
        }
        
    } else {
        echo "\n❌ GetKargoSecenekResult not found in response\n";
    }

    // Test with different cities
    echo "\n\n=== Testing Different Cities ===\n";
    
    $cities = [
        1 => 'Adana',
        6 => 'Ankara', 
        34 => 'İstanbul',
        35 => 'İzmir',
        16 => 'Bursa'
    ];
    
    foreach ($cities as $cityId => $cityName) {
        echo "\nTesting $cityName (ID: $cityId):\n";
        echo str_repeat('-', strlen("Testing $cityName (ID: $cityId):")) . "\n";
        
        $cityRequestData = [
            'SehirId' => $cityId,
            'ParaBirimi' => 'TL',
            'Sepet' => $cart
        ];
        
        try {
                         $cityResponse = $client->__soapCall("GetKargoSecenek", [
                 [
                     'UyeKodu' => $cartService->request->key,
                     'request' => (object)$cityRequestData
                 ]
             ]);
            
            if (isset($cityResponse->GetKargoSecenekResult)) {
                $cityResult = $cityResponse->GetKargoSecenekResult;
                
                if (empty((array)$cityResult)) {
                    echo "✅ API Call Success - No shipping options for $cityName\n";
                } else {
                    echo "🎉 SUCCESS! Found shipping options for $cityName:\n";
                    print_r($cityResult);
                }
            } else {
                echo "❌ No result for $cityName\n";
            }
        } catch (Exception $e) {
            echo "❌ Error for $cityName: " . $e->getMessage() . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Debug test sırasında hata oluştu: " . $e->getMessage() . "\n";
    echo "Hata dosyası: " . $e->getFile() . " (Satır: " . $e->getLine() . ")\n";
} 