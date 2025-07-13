<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== WSDL COMPLIANCE TEST ===\n\n";

// WSDL bilgilerine göre API doğru çalışması gerekiyor
// GetFavoriUrunlerResponse.Urunler = ArrayOfWebFavoriUrunler tipinde olmalı

echo "1. WSDL Tanımlarını Kontrol Edelim:\n";
echo str_repeat("-", 50) . "\n";

try {
    $wsdlUrl = $mainDomain . '/services/TicimaxService.svc?wsdl';
    echo "Ana WSDL URL: $wsdlUrl\n";
    
    $customWsdlUrl = $mainDomain . '/Servis/CustomServis.svc?singleWsdl';
    echo "Custom WSDL URL: $customWsdlUrl\n";
    
    // Try to create SOAP client with more detailed options
    $soapOptions = [
        'soap_version' => SOAP_1_1,
        'trace' => true,
        'exceptions' => true,
        'cache_wsdl' => WSDL_CACHE_NONE,
        'user_agent' => 'TicimaxPHP/1.0'
    ];
    
    echo "\n2. SOAP Client ile WSDL Yükleme:\n";
    $client = new SoapClient($customWsdlUrl, $soapOptions);
    
    echo "✓ SOAP Client başarıyla oluşturuldu\n";
    
    // Get WSDL functions
    $functions = $client->__getFunctions();
    echo "\nMevcut fonksiyonlar:\n";
    foreach ($functions as $function) {
        if (strpos($function, 'Favori') !== false) {
            echo "  - $function\n";
        }
    }
    
    // Get WSDL types
    $types = $client->__getTypes();
    echo "\nFavori ile ilgili tipler:\n";
    foreach ($types as $type) {
        if (strpos($type, 'Favori') !== false || strpos($type, 'WebFavori') !== false) {
            echo "  - $type\n";
        }
    }
    
    echo "\n" . str_repeat("-", 50) . "\n";
    echo "3. Doğru Parametre Yapısı ile Test:\n";
    
    // Test with exact WSDL compliance
    $yusufUserId = 1055;
    $productId = 16;
    
    // WSDL'e göre AddFavoriUrunRequest nasıl olmalı?
    echo "3a. AddFavoriUrun ile ekleme:\n";
    
    // Farklı parametre yapıları deneyelim
    $addParams1 = [
        'UyeKodu' => $apiKey,
        'request' => [
            'UyeID' => $yusufUserId,
            'UrunKartiID' => $productId,
            'UrunSayisi' => 1.0
        ]
    ];
    
    echo "Parametre Yapısı 1 (array):\n";
    print_r($addParams1);
    
    $addResponse1 = $client->__soapCall("AddFavoriUrun", [$addParams1]);
    echo "Response 1:\n";
    print_r($addResponse1);
    
    // Object ile dene
    $addParams2 = [
        'UyeKodu' => $apiKey,
        'request' => (object)[
            'UyeID' => $yusufUserId,
            'UrunKartiID' => $productId,
            'UrunSayisi' => 1.0
        ]
    ];
    
    echo "\nParametre Yapısı 2 (object):\n";
    print_r($addParams2);
    
    $addResponse2 = $client->__soapCall("AddFavoriUrun", [$addParams2]);
    echo "Response 2:\n";
    print_r($addResponse2);
    
    echo "\n" . str_repeat("-", 50) . "\n";
    echo "3b. GetFavoriUrunler ile çekme:\n";
    
    // Farklı GetFavoriUrunlerRequest yapıları
    $getParams1 = [
        'UyeKodu' => $apiKey,
        'request' => [
            'UyeID' => $yusufUserId,
            'KayitSayisi' => 50,
            'SayfaNo' => 1,
            'BaslangicTarihi' => null,
            'BitisTarihi' => null
        ]
    ];
    
    echo "Get Parametre Yapısı 1 (array):\n";
    print_r($getParams1);
    
    $getResponse1 = $client->__soapCall("GetFavoriUrunler", [$getParams1]);
    echo "Get Response 1:\n";
    print_r($getResponse1);
    
    // Analyse Urunler field
    if (isset($getResponse1->GetFavoriUrunlerResult->Urunler)) {
        $urunler = $getResponse1->GetFavoriUrunlerResult->Urunler;
        echo "\nUrunler analizi:\n";
        echo "  Tip: " . gettype($urunler) . "\n";
        echo "  Boş mu: " . (empty($urunler) ? 'Evet' : 'Hayır') . "\n";
        
        if (is_object($urunler)) {
            $props = get_object_vars($urunler);
            echo "  Object properties: " . count($props) . "\n";
            if (count($props) > 0) {
                foreach ($props as $key => $value) {
                    echo "    [$key]: " . print_r($value, true) . "\n";
                }
            }
        } elseif (is_array($urunler)) {
            echo "  Array elements: " . count($urunler) . "\n";
        }
    }
    
    echo "\n" . str_repeat("-", 50) . "\n";
    echo "4. Raw SOAP Request/Response:\n";
    
    echo "Son request:\n";
    echo $client->__getLastRequest() . "\n\n";
    
    echo "Son response:\n";
    echo $client->__getLastResponse() . "\n\n";
    
    echo "\n" . str_repeat("-", 50) . "\n";
    echo "5. Alternatif Parametre Denemeleri:\n";
    
    // Minimal parametre
    $getParamsMinimal = [
        'UyeKodu' => $apiKey,
        'request' => (object)[
            'UyeID' => $yusufUserId
        ]
    ];
    
    echo "Minimal parametreler:\n";
    $getResponseMinimal = $client->__soapCall("GetFavoriUrunler", [$getParamsMinimal]);
    print_r($getResponseMinimal);
    
    // Parametresiz (sadece UyeKodu)
    echo "\nSadece UyeKodu ile:\n";
    $getParamsOnly = ['UyeKodu' => $apiKey];
    $getResponseOnly = $client->__soapCall("GetFavoriUrunler", [$getParamsOnly]);
    print_r($getResponseOnly);
    
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== WSDL COMPLIANCE TEST TAMAMLANDI ===\n"; 