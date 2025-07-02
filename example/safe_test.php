<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Konfigürasyon yükle
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Test modu kontrolü
define('TEST_MODE', true);
define('SAFE_MODE', true);

echo "=== Ticimax API Sipariş Listeleme Testi ===\n";
echo "Domain: " . $mainDomain . "\n";
echo "Test Modu: " . (TEST_MODE ? 'Aktif' : 'Pasif') . "\n\n";

try {
    $ticimax = new Ticimax($mainDomain, $apiKey);
    $orderService = $ticimax->orderService();

    // Test 1: Son 24 saatteki siparişler
    echo "1. Son 24 Saat Siparişleri\n";
    echo "-------------------------\n";
    
    $filters = [
        'SiparisTarihiBas' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'SiparisTarihiSon' => date('Y-m-d H:i:s'),
        'KayitSayisi' => 5
    ];

    $response = $orderService->getOrders($filters);
    printOrders($response, "Son 24 Saat");

    // Test 2: Son 7 gündeki siparişler
    echo "\n2. Son 7 Gün Siparişleri\n";
    echo "-------------------------\n";
    
    $filters = [
        'SiparisTarihiBas' => date('Y-m-d H:i:s', strtotime('-7 days')),
        'SiparisTarihiSon' => date('Y-m-d H:i:s'),
        'KayitSayisi' => 5
    ];

    $response = $orderService->getOrders($filters);
    printOrders($response, "Son 7 Gün");

    // Test 3: Belirli bir tarih aralığı
    echo "\n3. Özel Tarih Aralığı (Geçen Ay)\n";
    echo "--------------------------------\n";
    
    $filters = [
        'SiparisTarihiBas' => date('Y-m-d H:i:s', strtotime('first day of last month')),
        'SiparisTarihiSon' => date('Y-m-d H:i:s', strtotime('last day of last month')),
        'KayitSayisi' => 5
    ];

    $response = $orderService->getOrders($filters);
    printOrders($response, "Geçen Ay");

    // Test 4: Sipariş durumuna göre filtreleme
    echo "\n4. Tamamlanan Siparişler (Son 30 Gün)\n";
    echo "------------------------------------\n";
    
    $filters = [
        'SiparisTarihiBas' => date('Y-m-d H:i:s', strtotime('-30 days')),
        'SiparisTarihiSon' => date('Y-m-d H:i:s'),
        'SiparisDurumu' => 2, // Tamamlanan siparişler
        'KayitSayisi' => 5
    ];

    $response = $orderService->getOrders($filters);
    printOrders($response, "Tamamlanan Siparişler");

} catch (Exception $e) {
    echo "\n! TEST HATASI: " . $e->getMessage() . "\n";
}

echo "\n=== Test Tamamlandı ===\n";

// Yardımcı fonksiyon: Siparişleri yazdır
function printOrders($response, $title) {
    if ($response->isSuccess()) {
        $orders = $response->getData();
        echo "Toplam " . $title . " Sipariş Sayısı: " . count($orders) . "\n\n";
        
        if (!empty($orders)) {
            foreach ($orders as $order) {
                echo sprintf(
                    "Sipariş ID: %s\n" .
                    "Müşteri: %s\n" .
                    "Tarih: %s\n" .
                    "Tutar: %.2f TL\n" .
                    "Durum: %s\n",
                    $order->ID ?? 'Belirtilmemiş',
                    $order->AdiSoyadi ?? 'Belirtilmemiş',
                    $order->SiparisTarihi ?? 'Belirtilmemiş',
                    $order->SiparisToplamTutari ?? 0,
                    getSiparisDurumu($order->SiparisDurumu ?? 0)
                );
                echo "------------------------\n";
            }
        } else {
            echo "! Bu dönemde sipariş bulunamadı.\n";
        }
    } else {
        echo "! Hata: " . $response->getMessage() . "\n";
    }
}

// Yardımcı fonksiyon: Sipariş durumu açıklaması
function getSiparisDurumu($durum) {
    $durumlar = [
        0 => 'Beklemede',
        1 => 'Onaylandı',
        2 => 'Tamamlandı',
        3 => 'İptal Edildi',
        4 => 'İade Edildi'
    ];
    
    return $durumlar[$durum] ?? 'Bilinmiyor';
} 