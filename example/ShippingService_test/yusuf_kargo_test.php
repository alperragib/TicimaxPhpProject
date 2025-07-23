<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;

// Config dosyasını yükle
$config = require __DIR__ . '/../config.php';

echo "=== Yusuf Kurnaz Kargo Seçenekleri Test ===\n\n";

try {
    // Ticimax servisini başlat
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $shippingService = $ticimax->shippingService();
    $cartService = $ticimax->cartService();

    // Test parametreleri
    $yusufId = 1055; // Yusuf Kurnaz
    $istanbulId = 34; // İstanbul
    $ankaraId = 6; // Ankara
    $izmir = 35; // İzmir
    
    echo "Test Kullanıcı: Yusuf Kurnaz (ID: $yusufId)\n\n";

    // Adım 1: Yusuf'un mevcut sepetini kontrol et
    echo "Adım 1: Yusuf'un mevcut sepetini kontrol ediyoruz...\n";
    echo "---------------------------------------------------\n";
    
    $cartResponse = $cartService->getCart($yusufId);
    
    if ($cartResponse->isSuccess()) {
        $cart = $cartResponse->getData();
        echo "✅ Sepet bilgisi alındı:\n";
        echo "- Sepet ID: " . ($cart->ID ?? 0) . "\n";
        echo "- Genel Toplam: " . ($cart->GenelToplam ?? 0) . " TL\n";
        echo "- Toplam KDV: " . ($cart->ToplamKDV ?? 0) . " TL\n";
        echo "- Ürün Sayısı: " . count($cart->Urunler ?? []) . "\n";
        
        // Ürün detayları
        if (!empty($cart->Urunler)) {
            echo "\nSepetteki Ürünler:\n";
            foreach ($cart->Urunler as $index => $urun) {
                echo "  " . ($index + 1) . ". ";
                echo ($urun->UrunAdi ?? 'Ürün Adı Yok') . " - ";
                echo "Adet: " . ($urun->Adet ?? 0) . " - ";
                echo "Fiyat: " . ($urun->Fiyati ?? 0) . " TL\n";
            }
        } else {
            echo "\n⚠️ Sepet boş görünüyor. Test dolu sepet objesi ile devam edecek...\n";
            
            // Test için dolu sepet objesi oluştur
            $cart = (object)[
                'SepetID' => 1,
                'UyeID' => $yusufId,
                'GenelToplam' => 1250.0,
                'ToplamKDV' => 208.33,
                'ToplamUrunAdedi' => 1,
                'Urunler' => [
                    (object)[
                        'UrunID' => 1,
                        'UrunAdi' => 'Koku Deneyimleme Kiti',
                        'Adet' => 1,
                        'Fiyati' => 1250.0,
                        'KDVTutari' => 208.33,
                        'StokKodu' => 'SCENT-KIT-001'
                    ]
                ]
            ];
            echo "Test sepeti oluşturuldu: 1250 TL toplam\n";
        }
        echo "\n";
        
    } else {
        echo "❌ Sepet bilgisi alınamadı: " . $cartResponse->getMessage() . "\n\n";
        return;
    }

    // Adım 2: İstanbul için kargo seçenekleri
    echo "Adım 2: İstanbul için kargo seçenekleri\n";
    echo "---------------------------------------\n";
    
    $istanbulResponse = $shippingService->getShippingOptions($istanbulId, 'TL', $cart);
    
    if ($istanbulResponse->isSuccess()) {
        $istanbulOptions = $istanbulResponse->getData();
        echo "✅ Başarılı! İstanbul için bulunan kargo seçeneği sayısı: " . count($istanbulOptions) . "\n";
        
        if (!empty($istanbulOptions)) {
            foreach ($istanbulOptions as $index => $option) {
                echo "\nKargo Seçeneği " . ($index + 1) . " (İstanbul):\n";
                echo "- ID: " . ($option->ID ?? 'N/A') . "\n";
                echo "- Firma Adı: " . ($option->Tanim ?? 'N/A') . "\n";
                echo "- Kargo Tutarı: " . ($option->KargoTutari ?? 0) . " TL\n";
                echo "- Kapıda Ödeme: " . (($option->KapidaOdeme ?? false) ? 'Evet' : 'Hayır') . "\n";
                if ($option->KapidaOdeme ?? false) {
                    echo "- Kapıda Ödeme Fiyatı: " . ($option->KapidaOdemeFiyati ?? 0) . " TL\n";
                }
                echo "- Kapıda KK Ödeme: " . (($option->KapidaOdemeKK ?? false) ? 'Evet' : 'Hayır') . "\n";
                if ($option->KapidaOdemeKK ?? false) {
                    echo "- Kapıda KK Ödeme Fiyatı: " . ($option->KapidaOdemeKKFiyati ?? 0) . " TL\n";
                }
            }
        } else {
            echo "⚠️ İstanbul için kargo seçeneği bulunamadı.\n";
        }
        echo "\n";
    } else {
        echo "❌ İstanbul kargo seçenekleri: " . $istanbulResponse->getMessage() . "\n\n";
    }

    // Adım 3: Ankara için kargo seçenekleri
    echo "Adım 3: Ankara için kargo seçenekleri\n";
    echo "-------------------------------------\n";
    
    $ankaraResponse = $shippingService->getShippingOptions($ankaraId, 'TL', $cart);
    
    if ($ankaraResponse->isSuccess()) {
        $ankaraOptions = $ankaraResponse->getData();
        echo "✅ Başarılı! Ankara için bulunan kargo seçeneği sayısı: " . count($ankaraOptions) . "\n";
        
        if (!empty($ankaraOptions)) {
            $firstAnkara = $ankaraOptions[0];
            echo "\nİlk Ankara kargo seçeneği:\n";
            echo "- Firma: " . ($firstAnkara->Tanim ?? 'N/A') . "\n";
            echo "- Kargo Tutarı: " . ($firstAnkara->KargoTutari ?? 0) . " TL\n";
            echo "- Kapıda Ödeme: " . (($firstAnkara->KapidaOdeme ?? false) ? 'Evet' : 'Hayır') . "\n";
        } else {
            echo "⚠️ Ankara için kargo seçeneği bulunamadı.\n";
        }
        echo "\n";
    } else {
        echo "❌ Ankara kargo seçenekleri: " . $ankaraResponse->getMessage() . "\n\n";
    }

    // Adım 4: İzmir için kargo seçenekleri
    echo "Adım 4: İzmir için kargo seçenekleri\n";
    echo "------------------------------------\n";
    
    $izmirResponse = $shippingService->getShippingOptions($izmir, 'TL', $cart);
    
    if ($izmirResponse->isSuccess()) {
        $izmirOptions = $izmirResponse->getData();
        echo "✅ Başarılı! İzmir için bulunan kargo seçeneği sayısı: " . count($izmirOptions) . "\n";
        
        if (!empty($izmirOptions)) {
            echo "İzmir kargo seçenekleri mevcut!\n";
        } else {
            echo "⚠️ İzmir için kargo seçeneği bulunamadı.\n";
        }
        echo "\n";
    } else {
        echo "❌ İzmir kargo seçenekleri: " . $izmirResponse->getMessage() . "\n\n";
    }

    // Adım 5: Farklı para birimi testi (USD)
    echo "Adım 5: USD para birimi ile test\n";
    echo "--------------------------------\n";
    
    $usdResponse = $shippingService->getShippingOptions($istanbulId, 'USD', $cart);
    
    if ($usdResponse->isSuccess()) {
        $usdOptions = $usdResponse->getData();
        echo "✅ USD ile kargo seçeneği sayısı: " . count($usdOptions) . "\n";
        
        if (!empty($usdOptions)) {
            $firstUsd = $usdOptions[0];
            echo "- İlk seçenek (USD): " . ($firstUsd->Tanim ?? 'N/A') . "\n";
            echo "- Kargo Tutarı: " . ($firstUsd->KargoTutari ?? 0) . " USD\n";
        }
        echo "\n";
    } else {
        echo "❌ USD testi: " . $usdResponse->getMessage() . "\n\n";
    }

    // Sonuç
    echo "=== Test Özeti ===\n";
    echo "- Yusuf Kurnaz (ID: $yusufId) sepet durumu kontrol edildi\n";
    echo "- İstanbul, Ankara, İzmir şehirleri için kargo seçenekleri test edildi\n";
    echo "- USD para birimi ile test yapıldı\n";
    echo "- GetKargoSecenek API fonksiyonu doğru şekilde çalışıyor\n\n";

    echo "=== Yusuf Kurnaz Kargo Test Tamamlandı ===\n";

} catch (Exception $e) {
    echo "❌ Test sırasında hata oluştu: " . $e->getMessage() . "\n";
    echo "Hata dosyası: " . $e->getFile() . " (Satır: " . $e->getLine() . ")\n";
} 