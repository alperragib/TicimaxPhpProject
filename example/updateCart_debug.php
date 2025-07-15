<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

echo "=== updateCart PARAMETRE DEBUG (DÜZELTME) ===\n\n";

try {
    $config = require __DIR__ . '/config.php';
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $cartService = $ticimax->cartService();
    
    // Test edilecek ürünler
    $testProducts = [6]; // Sadece ilk ürünü test et
    
    foreach ($testProducts as $productId) {
        echo "🔍 ÜRÜN ID {$productId} İÇİN DETAYLI DEBUG:\n";
        echo str_repeat("-", 60) . "\n";
        
        // Yeni sepet oluştur
        $createResponse = $cartService->createCart(1055);
        $cartData = $createResponse->getData();
        $cartId = $cartData['SepetID'] ?? null;
        echo "✅ Sepet ID: {$cartId}\n";
        
        // Direct SOAP call ile parametreleri kontrol et
        $soapClient = new SoapClient($config['mainDomain'] . "/Servis/SiparisServis.svc?singleWsdl", [
            'trace' => 1,
            'exceptions' => true,
            'soap_version' => SOAP_1_1
        ]);
        
        // Schema'ya göre WebUpdateSepetRequest parametreleri - DÜZELTİLDİ
        $requestData = [
            'SepetID' => (int)$cartId,
            'SepetUrunID' => 0,
            'UrunID' => (int)$productId,      // Cast to int
            'Adet' => 1,
            'AdetGuncelle' => false,
            'SepettenCikar' => false,
            'KampanyaID' => 0,
        ];
        
        echo "📤 SOAP Request Parametreleri (WebUpdateSepetRequest):\n";
        foreach ($requestData as $key => $value) {
            $displayValue = is_bool($value) ? ($value ? 'true' : 'false') : $value;
            echo "   {$key}: {$displayValue}\n";
        }
        
        // SOAP call - Schema'ya göre UpdateSepet
        $updateParams = [
            'UyeKodu' => $config['apiKey'],
            'request' => (object)$requestData  // Object olarak gönder - düzeltildi
        ];
        
        echo "\n📤 Full SOAP Parameters:\n";
        echo "   UyeKodu: {$config['apiKey']}\n";
        echo "   request: " . json_encode($requestData) . "\n";
        
        $response = $soapClient->__soapCall("UpdateSepet", [$updateParams]);
        
        echo "\n📥 SOAP Response (WebUpdateSepetResponse):\n";
        $result = $response->UpdateSepetResult ?? null;
        
        if ($result) {
            echo "   IsError: " . (($result->IsError ?? false) ? 'true' : 'false') . "\n";
            echo "   Message: " . ($result->Message ?? 'N/A') . "\n";
            
            if ($result->IsError ?? false) {
                echo "   ❌ HATA: " . ($result->Message ?? 'Bilinmeyen hata') . "\n";
                continue;
            }
        }
        
        // Raw Request ve Response'u kontrol et
        echo "\n🔍 Raw SOAP Request Check:\n";
        $lastRequest = $soapClient->__getLastRequest();
        if ($lastRequest) {
            echo "   📋 FULL XML Request:\n";
            echo htmlspecialchars($lastRequest) . "\n\n";
            
            if (preg_match('/<ns2:UrunID>(\d+)<\/ns2:UrunID>/', $lastRequest, $matches)) {
                echo "   ✅ XML'deki UrunID: " . $matches[1] . "\n";
            } else {
                echo "   ❌ XML'de UrunID bulunamadı!\n";
            }
        } else {
            echo "   ❌ Raw request alınamadı\n";
        }
        
        // Kısa bekleme
        sleep(1);
        
        // Sepet bilgilerini kontrol et
        echo "\n📊 Sepet Kontrolü (GetSepetRequest):\n";
        
        // GetSepetRequest parametreleri
        $getSepetParams = [
            'UyeKodu' => $config['apiKey'],
            'request' => [
                'KampanyaID' => 0,
                'SepetID' => (int)$cartId,
                'UyeID' => 1055
            ]
        ];
        
        $getSepetResponse = $soapClient->__soapCall("GetSepet", [$getSepetParams]);
        
        echo "📥 GetSepet Response:\n";
        $getSepetResult = $getSepetResponse->GetSepetResult ?? null;
        
        if ($getSepetResult) {
            echo "   IsError: " . (($getSepetResult->IsError ?? false) ? 'true' : 'false') . "\n";
            echo "   Message: " . ($getSepetResult->Message ?? 'N/A') . "\n";
            
            if (!($getSepetResult->IsError ?? false)) {
                // Sepet verilerini kontrol et
                $sepetData = $getSepetResult->Sepetler->WebSepet ?? null;
                if ($sepetData && isset($sepetData->Urunler->WebSepetUrun)) {
                    $urun = $sepetData->Urunler->WebSepetUrun;
                    echo "   ✅ Sepet dolu!\n";
                    echo "   Sepetteki Ürün ID: " . ($urun->UrunID ?? 'N/A') . "\n";
                    echo "   Sepetteki Ürün Adı: " . ($urun->UrunAdi ?? 'N/A') . "\n";
                    echo "   Sepetteki UrunKartiID: " . ($urun->UrunKartiID ?? 'N/A') . "\n";
                    
                    // Karşılaştırma
                    if (($urun->UrunKartiID ?? 0) == $productId) {
                        echo "   ✅ DOĞRU: Gönderilen ve dönen ürün ID'si eşleşiyor\n";
                    } else {
                        echo "   ❌ YANLŞ: Gönderilen ID: {$productId}, Dönen ID: " . ($urun->UrunKartiID ?? 'N/A') . "\n";
                        echo "   🎯 SORUN TESPİT EDİLDİ: API yanlış ürün döndürüyor!\n";
                    }
                    
                    // Fiyat bilgilerini de kontrol et
                    echo "   Ürün Sepet Fiyatı: " . ($urun->UrunSepetFiyati ?? 'N/A') . " TL\n";
                    echo "   KDV Tutarı: " . ($urun->KDVTutari ?? 'N/A') . " TL\n";
                    echo "   Adet: " . ($urun->Adet ?? 'N/A') . "\n";
                    
                } else {
                    echo "   ❌ Sepette ürün bulunamadı - sepet boş\n";
                    echo "   Sepet yapısı: " . json_encode($sepetData) . "\n";
                }
            }
        } else {
            echo "   ❌ GetSepet response alınamadı\n";
        }
        
        echo "\n" . str_repeat("=", 60) . "\n\n";
    }
    
    echo "🎯 SONUÇ:\n";
    echo "- SOAP parametreleri düzeltildi\n";
    echo "- UrunID casting ve array yapısı düzeltildi\n";
    echo "- Raw request/response kontrolleri eklendi\n";
    echo "- Sepet yapısı debug bilgileri eklendi\n";
    echo str_repeat("=", 60) . "\n";
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 