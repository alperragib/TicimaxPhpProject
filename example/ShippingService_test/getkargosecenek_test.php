<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;

// Config dosyasını yükle
$config = require __DIR__ . '/../config.php';

echo "=== GetKargoSecenek Test Başlıyor ===\n\n";

try {
    // Ticimax servisini başlat
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $shippingService = $ticimax->shippingService();
    $cartService = $ticimax->cartService();

    // Test parametreleri
    $testUserId = 1055; // Yusuf Kurnaz
    $testCityId = 34; // İstanbul
    $testCurrency = 'TL';

    echo "Test Parametreleri:\n";
    echo "- Test Kullanıcı ID: $testUserId (Yusuf Kurnaz)\n";
    echo "- Test Şehir ID: $testCityId (İstanbul)\n";
    echo "- Para Birimi: $testCurrency\n\n";

    // Önce sepet bilgisini al (GetKargoSecenek için sepet objesi gerekli)
    echo "Adım 1: Kullanıcının sepet bilgisini alıyoruz...\n";
    echo "----------------------------------------------------\n";
    
    $cartResponse = $cartService->getCart($testUserId);
    
    if (!$cartResponse->isSuccess()) {
        echo "❌ Sepet bilgisi alınamadı: " . $cartResponse->getMessage() . "\n";
        echo "Test boş sepet ile devam edecek...\n\n";
        
        // Boş sepet objesi oluştur
        $cart = (object)[
            'SepetID' => 0,
            'UyeID' => $testUserId,
            'GenelToplam' => 0,
            'ToplamKDV' => 0,
            'ToplamUrunAdedi' => 0,
            'Urunler' => []
        ];
    } else {
        $cart = $cartResponse->getData();
        echo "✅ Sepet bilgisi alındı:\n";
        echo "- Sepet ID: " . ($cart->ID ?? 0) . "\n";
        echo "- Genel Toplam: " . ($cart->GenelToplam ?? 0) . " TL\n";
        echo "- Ürün Sayısı: " . count($cart->Urunler ?? []) . "\n\n";
    }

    // Test 1: İstanbul için kargo seçenekleri
    echo "Test 1: İstanbul için kargo seçenekleri\n";
    echo "---------------------------------------\n";
    
    $response = $shippingService->getShippingOptions($testCityId, $testCurrency, $cart);
    
    if ($response->isSuccess()) {
        $shippingOptions = $response->getData();
        echo "✅ Başarılı! Bulunan kargo seçeneği sayısı: " . count($shippingOptions) . "\n\n";
        
        if (!empty($shippingOptions)) {
            foreach ($shippingOptions as $index => $option) {
                echo "Kargo Seçeneği " . ($index + 1) . ":\n";
                echo "- ID: " . ($option->ID ?? 'N/A') . "\n";
                echo "- Firma Adı: " . ($option->Tanim ?? 'N/A') . "\n";
                echo "- Kargo Tutarı: " . ($option->KargoTutari ?? 0) . " TL\n";
                echo "- Kapıda Ödeme: " . (($option->KapidaOdeme ?? false) ? 'Evet' : 'Hayır') . "\n";
                echo "- Kapıda Ödeme Fiyatı: " . ($option->KapidaOdemeFiyati ?? 0) . " TL\n";
                echo "- Kapıda KK Ödeme: " . (($option->KapidaOdemeKK ?? false) ? 'Evet' : 'Hayır') . "\n";
                echo "- Kapıda KK Ödeme Fiyatı: " . ($option->KapidaOdemeKKFiyati ?? 0) . " TL\n\n";
            }
        } else {
            echo "⚠️ Bu şehir için kargo seçeneği bulunamadı.\n\n";
        }
    } else {
        echo "❌ Kargo seçenekleri alınamadı: " . $response->getMessage() . "\n\n";
    }

    // Test 2: Ankara için kargo seçenekleri
    echo "Test 2: Ankara için kargo seçenekleri\n";
    echo "-------------------------------------\n";
    
    $ankaraResponse = $shippingService->getShippingOptions(6, $testCurrency, $cart); // 6 = Ankara
    
    if ($ankaraResponse->isSuccess()) {
        $ankaraOptions = $ankaraResponse->getData();
        echo "✅ Başarılı! Ankara için kargo seçeneği sayısı: " . count($ankaraOptions) . "\n";
        
        if (!empty($ankaraOptions)) {
            echo "İlk kargo seçeneği:\n";
            $firstOption = $ankaraOptions[0];
            echo "- Firma: " . ($firstOption->Tanim ?? 'N/A') . "\n";
            echo "- Kargo Tutarı: " . ($firstOption->KargoTutari ?? 0) . " TL\n\n";
        }
    } else {
        echo "❌ Ankara kargo seçenekleri: " . $ankaraResponse->getMessage() . "\n\n";
    }

    // Test 3: Farklı para birimi ile test (USD)
    echo "Test 3: USD para birimi ile test\n";
    echo "--------------------------------\n";
    
    $usdResponse = $shippingService->getShippingOptions($testCityId, 'USD', $cart);
    
    if ($usdResponse->isSuccess()) {
        $usdOptions = $usdResponse->getData();
        echo "✅ USD ile kargo seçeneği sayısı: " . count($usdOptions) . "\n";
        
        if (!empty($usdOptions)) {
            $firstUsd = $usdOptions[0];
            echo "- İlk seçenek tutarı: " . ($firstUsd->KargoTutari ?? 0) . " USD\n\n";
        }
    } else {
        echo "❌ USD testi: " . $usdResponse->getMessage() . "\n\n";
    }

    // Test 4: Geçersiz şehir ID ile test
    echo "Test 4: Geçersiz şehir ID ile test\n";
    echo "----------------------------------\n";
    
    $invalidResponse = $shippingService->getShippingOptions(999, $testCurrency, $cart);
    
    if ($invalidResponse->isSuccess()) {
        echo "⚠️ Geçersiz şehir ID kabul edildi\n\n";
    } else {
        echo "✅ Beklenen sonuç: " . $invalidResponse->getMessage() . "\n\n";
    }

    echo "=== GetKargoSecenek Test Tamamlandı ===\n";

} catch (Exception $e) {
    echo "❌ Test sırasında hata oluştu: " . $e->getMessage() . "\n";
    echo "Hata dosyası: " . $e->getFile() . " (Satır: " . $e->getLine() . ")\n";
} 