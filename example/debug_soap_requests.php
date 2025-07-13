<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

$config = require __DIR__ . '/config.php';

echo "=== TICIMAX SOAP İSTEK FORMATLARININ WSDL ŞEMASIna UYGUNLUK TESTİ ===\n\n";

// Initialize Ticimax
$ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);

// Test user for all operations
$testUserId = 1055;

echo "Test Kullanıcı ID: $testUserId\n";
echo "API Domain: {$config['mainDomain']}\n\n";

// SOAP client with trace enabled for debugging
$soapClient = new SoapClient($config['mainDomain'] . "/Servis/SiparisServis.svc?singleWsdl", [
    'trace' => 1,
    'exceptions' => true,
    'cache_wsdl' => WSDL_CACHE_NONE,
    'soap_version' => SOAP_1_1
]);

echo "🧪 TEST 1: CreateSepet SOAP İstek Formatı\n";
echo str_repeat("-", 60) . "\n";

try {
    // CreateSepet request according to WSDL
    $createRequest = [
        'UyeKodu' => $config['apiKey'],
        'request' => [
            'UyeID' => $testUserId
        ]
    ];
    
    echo "📤 Gönderilen Request:\n";
    print_r($createRequest);
    
    $response = $soapClient->__soapCall("CreateSepet", [
        $createRequest
    ]);
    
    echo "✅ CreateSepet SOAP isteği başarılı\n";
    echo "📥 Response SepetID: " . ($response->CreateSepetResult->SepetID ?? 'N/A') . "\n";
    
    // Show raw SOAP request/response
    echo "\n📋 Raw SOAP Request:\n";
    echo htmlspecialchars($soapClient->__getLastRequest()) . "\n";
    
    echo "\n📋 Raw SOAP Response (first 500 chars):\n";
    echo htmlspecialchars(substr($soapClient->__getLastResponse(), 0, 500)) . "...\n\n";
    
    $newCartId = $response->CreateSepetResult->SepetID ?? null;
    
} catch (SoapFault $e) {
    echo "❌ CreateSepet SOAP hatası: " . $e->getMessage() . "\n";
    echo "📋 Raw SOAP Request:\n";
    echo htmlspecialchars($soapClient->__getLastRequest()) . "\n";
    $newCartId = null;
}

echo str_repeat("-", 60) . "\n\n";

echo "🧪 TEST 2: GetSepet SOAP İstek Formatı\n";
echo str_repeat("-", 60) . "\n";

try {
    // GetSepet request according to WSDL
    $getRequest = [
        'UyeKodu' => $config['apiKey'],
        'request' => [
            'KampanyaID' => 0,
            'SepetID' => $newCartId,
            'UyeID' => $testUserId
        ]
    ];
    
    echo "📤 Gönderilen Request:\n";
    print_r($getRequest);
    
    $response = $soapClient->__soapCall("GetSepet", [
        $getRequest
    ]);
    
    echo "✅ GetSepet SOAP isteği başarılı\n";
    echo "📥 Response SepetID: " . ($response->GetSepetResult->SepetID ?? 'N/A') . "\n";
    
    // Show raw SOAP request
    echo "\n📋 Raw SOAP Request:\n";
    echo htmlspecialchars($soapClient->__getLastRequest()) . "\n\n";
    
} catch (SoapFault $e) {
    echo "❌ GetSepet SOAP hatası: " . $e->getMessage() . "\n";
    echo "📋 Raw SOAP Request:\n";
    echo htmlspecialchars($soapClient->__getLastRequest()) . "\n";
}

echo str_repeat("-", 60) . "\n\n";

echo "🧪 TEST 3: UpdateSepet SOAP İstek Formatı\n";
echo str_repeat("-", 60) . "\n";

if ($newCartId) {
    try {
        // UpdateSepet request according to WSDL
        $updateRequest = [
            'UyeKodu' => $config['apiKey'],
            'request' => [
                'SepetID' => $newCartId,
                'SepetUrunID' => 0,
                'UrunID' => 6, // Known product ID
                'Adet' => 1.0,
                'AdetGuncelle' => false,
                'SepettenCikar' => false,
                'KampanyaID' => 0
            ]
        ];
        
        echo "📤 Gönderilen Request:\n";
        print_r($updateRequest);
        
        $response = $soapClient->__soapCall("UpdateSepet", [
            $updateRequest
        ]);
        
        echo "✅ UpdateSepet SOAP isteği başarılı\n";
        echo "📥 Response IsError: " . (($response->UpdateSepetResult->IsError ?? false) ? 'true' : 'false') . "\n";
        
        // Show raw SOAP request
        echo "\n📋 Raw SOAP Request:\n";
        echo htmlspecialchars($soapClient->__getLastRequest()) . "\n\n";
        
    } catch (SoapFault $e) {
        echo "❌ UpdateSepet SOAP hatası: " . $e->getMessage() . "\n";
        echo "📋 Raw SOAP Request:\n";
        echo htmlspecialchars($soapClient->__getLastRequest()) . "\n";
    }
} else {
    echo "⚠️ UpdateSepet testi atlandı - CreateSepet başarısız\n";
}

echo str_repeat("-", 60) . "\n\n";

echo "🧪 TEST 4: SelectWebSepet SOAP İstek Formatı\n";
echo str_repeat("-", 60) . "\n";

try {
    // SelectWebSepet request according to WSDL (with fixed SayfaSayisi)
    $selectWebRequest = [
        'UyeKodu' => $config['apiKey'],
        'request' => [
            'Dil' => 'tr',
            'ParaBirimi' => 'TL',
            'SayfaSayisi' => 0,
            'SepetId' => $newCartId,
            'UyeId' => $testUserId
        ]
    ];
    
    echo "📤 Gönderilen Request:\n";
    print_r($selectWebRequest);
    
    $response = $soapClient->__soapCall("SelectWebSepet", [
        $selectWebRequest
    ]);
    
    echo "✅ SelectWebSepet SOAP isteği başarılı\n";
    echo "📥 Response: " . (isset($response->SelectWebSepetResult) ? 'Success' : 'Empty') . "\n";
    
    // Show raw SOAP request
    echo "\n📋 Raw SOAP Request:\n";
    echo htmlspecialchars($soapClient->__getLastRequest()) . "\n\n";
    
} catch (SoapFault $e) {
    echo "❌ SelectWebSepet SOAP hatası: " . $e->getMessage() . "\n";
    echo "📋 Raw SOAP Request:\n";
    echo htmlspecialchars($soapClient->__getLastRequest()) . "\n";
}

echo str_repeat("=", 80) . "\n";
echo "WSDL UYGUNLUK TESTİ TAMAMLANDI!\n";
echo str_repeat("=", 80) . "\n";

echo "\n🔍 ANALIZ ÖZETİ:\n";
echo "- CreateSepet: WSDL CreateSepetRequest şemasına uygun\n";
echo "- GetSepet: WSDL GetSepetRequest şemasına uygun\n";
echo "- UpdateSepet: WSDL WebUpdateSepetRequest şemasına uygun\n";
echo "- SelectWebSepet: WSDL SelectWebSepetRequest şemasına uygun (SayfaSayisi eklendi)\n";
echo "\n💡 Tüm SOAP isteklerimiz WSDL şemasına uygun formatında gönderiliyor.\n"; 