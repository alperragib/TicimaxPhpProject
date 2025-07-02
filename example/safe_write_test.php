<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Konfigürasyon yükle
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Test modu kontrolü
define('TEST_MODE', true);
define('WRITE_TEST_MODE', false); // ⚠️ Yazma testleri varsayılan olarak kapalı

echo "=== Ticimax API Yazma İşlemleri Test ===\n";
echo "Domain: " . $mainDomain . "\n";
echo "Test Modu: " . (TEST_MODE ? 'Aktif' : 'Pasif') . "\n";
echo "Yazma Testi: " . (WRITE_TEST_MODE ? 'AKTİF - DİKKAT!' : 'Pasif (Güvenli)') . "\n\n";

if (!WRITE_TEST_MODE) {
    die("! GÜVENLİK UYARISI: Yazma testleri devre dışı. Aktifleştirmek için WRITE_TEST_MODE'u true yapın.\n");
}

// Güvenlik kontrolleri
$securityChecks = [
    'domain_check' => strpos($mainDomain, 'test') !== false || strpos($mainDomain, 'dev') !== false,
    'time_check' => (date('G') >= 1 && date('G') <= 5), // Gece 01:00 - 05:00 arası
    'small_amount' => true, // Test siparişleri için düşük tutarlar
    'test_products' => true // Sadece test ürünleri
];

echo "Güvenlik Kontrolleri:\n";
foreach ($securityChecks as $check => $passed) {
    echo sprintf("- %s: %s\n", $check, $passed ? '✓' : '×');
}

if (!$securityChecks['domain_check']) {
    die("\n! GÜVENLİK UYARISI: Testler sadece test/dev ortamında çalıştırılabilir.\n");
}

if (!$securityChecks['time_check']) {
    die("\n! GÜVENLİK UYARISI: Yazma testleri sadece gece 01:00 - 05:00 arası çalıştırılabilir.\n");
}

try {
    $ticimax = new Ticimax($mainDomain, $apiKey);
    $orderService = $ticimax->orderService();

    echo "\nTest Sipariş Oluşturma Hazırlığı:\n";
    echo "--------------------------------\n";

    // Test için minimum tutarlı ürün ve ödeme bilgileri
    $testOrder = [
        'Odeme' => [
            'BankaKomisyonu' => 0.0,
            'HavaleHesapID' => null,
            'KapidaOdemeTutari' => 0.0,
            'OdemeDurumu' => 1,
            'OdemeIndirimi' => 0.0,
            'OdemeNotu' => 'TEST SİPARİŞ - SİLİNECEK',
            'OdemeSecenekID' => 1,
            'OdemeTipi' => 1,
            'TaksitSayisi' => 1,
            'Tarih' => date('c'),
            'Tutar' => 1.0 // Minimum test tutarı
        ],
        'Urunler' => [
            [
                'Adet' => 1,
                'KdvOrani' => 18,
                'KdvTutari' => 0.15,
                'Maliyet' => 0.5,
                'Tutar' => 1.0,
                'UrunID' => 2 // Test ürün ID'si
            ]
        ],
        'FaturaAdresId' => 1,
        'KargoAdresId' => 1,
        'KargoFirmaId' => 1,
        'ParaBirimi' => 'TL',
        'SiparisKaynagi' => 'API_TEST',
        'SiparisNotu' => 'Bu bir test siparişidir - SİLİNECEK',
        'UyeId' => 1,
        'TeslimatSaati' => '14:00',
        'TeslimatTarihi' => date('c', strtotime('+2 days'))
    ];

    echo "Test sipariş bilgileri hazırlandı:\n";
    echo "- Toplam Tutar: 1.0 TL (Minimum test tutarı)\n";
    echo "- Ürün Adedi: 1\n";
    echo "- Sipariş Notu: TEST SİPARİŞ - SİLİNECEK\n\n";

    echo "! DİKKAT: Bu test gerçek bir sipariş oluşturacak!\n";
    echo "! Devam etmek için 5 saniye bekleyin...\n";
    sleep(5);

    // Test siparişi oluştur
    $orderService->data = $testOrder;
    $response = $orderService->saveOrder();

    if ($response->isSuccess()) {
        $orderId = $response->getData()['orderId'] ?? null;
        echo "\n✓ Test sipariş oluşturuldu!\n";
        echo "Sipariş ID: " . $orderId . "\n";
        echo "! Bu siparişi yönetim panelinden iptal etmeyi unutmayın!\n";
    } else {
        echo "\n× Test sipariş oluşturulamadı:\n";
        echo $response->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "\n! TEST HATASI: " . $e->getMessage() . "\n";
    echo "! Test güvenli bir şekilde durduruldu.\n";
}

echo "\n=== Test Tamamlandı ===\n"; 