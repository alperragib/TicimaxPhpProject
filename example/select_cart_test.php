<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Konfigürasyon yükle
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

try {
    // Ticimax instance oluştur
    $ticimax = new Ticimax($mainDomain, $apiKey);
    $cartService = $ticimax->cartService();

    echo "=== Sepet Listeleme Testi ===\n";
    echo "Domain: " . $mainDomain . "\n\n";

    // Test 1: Son 30 günlük sepetler
    echo "1. Son 30 Günlük Sepetler\n";
    echo "-------------------------\n";
    
    $response = $cartService->selectSepet(
        sepetId: -1,  // Tüm sepetler
        uyeId: -1,    // Tüm üyeler
        baslangicTarihi: date('Y-m-d', strtotime('-30 days')),
        bitisTarihi: date('Y-m-d')
    );

    if (!$response['IsError']) {
        $sepetler = $response['Data'];
        echo "Toplam Sepet Sayısı: " . count($sepetler) . "\n\n";

        foreach ($sepetler as $sepet) {
            echo "Sepet ID: " . ($sepet->ID ?? 'Belirtilmemiş') . "\n";
            echo "Üye: " . ($sepet->UyeAdi ?? 'Belirtilmemiş') . "\n";
            echo "Tarih: " . ($sepet->SepetTarihi ?? 'Belirtilmemiş') . "\n";
            
            if (isset($sepet->Urunler) && is_array($sepet->Urunler)) {
                echo "Ürünler:\n";
                foreach ($sepet->Urunler as $urun) {
                    echo sprintf(
                        "- %s (Adet: %.2f, Fiyat: %.2f %s)\n",
                        $urun->UrunAdi ?? 'Belirtilmemiş',
                        $urun->Adet ?? 0,
                        $urun->Fiyati ?? 0,
                        $urun->ParaBirimi ?? 'TL'
                    );
                }
            }
            echo "------------------------\n";
        }
    } else {
        echo "! Hata: " . $response['ErrorMessage'] . "\n";
    }

    // Test 2: Belirli bir üyenin sepetleri
    echo "\n2. Belirli Üyenin Sepetleri (Üye ID: 1)\n";
    echo "----------------------------------------\n";
    
    $response = $cartService->selectSepet(
        sepetId: -1,
        uyeId: 1,
        baslangicTarihi: date('Y-m-d', strtotime('-30 days')),
        bitisTarihi: date('Y-m-d')
    );

    if (!$response['IsError']) {
        $sepetler = $response['Data'];
        echo "Üyenin Toplam Sepet Sayısı: " . count($sepetler) . "\n\n";

        foreach ($sepetler as $sepet) {
            echo "Sepet ID: " . ($sepet->ID ?? 'Belirtilmemiş') . "\n";
            echo "Tarih: " . ($sepet->SepetTarihi ?? 'Belirtilmemiş') . "\n";
            
            if (isset($sepet->Urunler) && is_array($sepet->Urunler)) {
                echo "Ürünler:\n";
                foreach ($sepet->Urunler as $urun) {
                    echo sprintf(
                        "- %s (Adet: %.2f, Fiyat: %.2f %s)\n" .
                        "  Stok Kodu: %s, KDV: %%%.2f\n",
                        $urun->UrunAdi ?? 'Belirtilmemiş',
                        $urun->Adet ?? 0,
                        $urun->Fiyati ?? 0,
                        $urun->ParaBirimi ?? 'TL',
                        $urun->StokKodu ?? 'Belirtilmemiş',
                        $urun->KDVOrani ?? 0
                    );
                }
            }
            echo "------------------------\n";
        }
    } else {
        echo "! Hata: " . $response['ErrorMessage'] . "\n";
    }

} catch (Exception $e) {
    echo "! HATA: " . $e->getMessage() . "\n";
}

echo "\n=== Test Tamamlandı ===\n"; 