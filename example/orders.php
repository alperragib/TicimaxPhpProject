<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$orderService = $ticimax->orderService();

echo "--- List Orders ---\n";

$filters = [
    'DurumTarihiBas'              => null,
    'DurumTarihiSon'              => null,
    'DuzenlemeTarihiBas'         => null,
    'DuzenlemeTarihiSon'         => null,
    'EFaturaURL'                 => null,
    'EntegrasyonAktarildi'       => -1,
    'EntegrasyonParams'          => [
        'AlanDeger'             => '',
        'Deger'                 => '',
        'EntegrasyonKodu'      => '',
        'EntegrasyonParamsAktif' => false,
        'TabloAlan'            => '',
        'Tanim'                => ''
    ],
    'FaturaNo'                   => '',
    'IptalEdilmisUrunler'        => true,
    'KampanyaGetir'              => false,
    'KargoEntegrasyonTakipDurumu' => null,
    'KargoFirmaID'               => -1,
    'OdemeDurumu'                => -1,
    'OdemeGetir'                 => null,
    'OdemeTamamlandi'            => null,
    'OdemeTipi'                  => -1,
    'PaketlemeDurumu'            => null,
    'PazaryeriIhracat'           => null,
    'SiparisDurumu'              => -1,
    'SiparisID'                  => -1,
    'SiparisKaynagi'             => '',
    'SiparisKodu'                => '',
    'SiparisNo'                  => '',
    'SiparisTarihiBas'           => null,
    'SiparisTarihiSon'           => null,
    'StrPaketlemeDurumu'         => '',
    'StrSiparisDurumu'           => '',
    'StrSiparisID'               => '',
    'TedarikciID'                => -1,
    'TeslimatGunuBas'            => null,
    'TeslimatGunuSon'            => null,
    'TeslimatMagazaID'           => null,
    'UrunGetir'                  => null,
    'UyeID'                      => 1,
    'UyeTelefon'                 => '',
];

$pagination = [
    'BaslangicIndex'  => 0,
    'KayitSayisi'     => 5,
    'SiralamaDegeri'  => 'ID',
    'SiralamaYonu'    => 'DESC',
];

$orders = $orderService->getOrders($filters, $pagination);

foreach ($orders as $order) {
    $id = $order->ID ?? '[No ID]';
    $adiSoyadi = $order->AdiSoyadi ?? '[No AdiSoyadi]';
    $durum = $order->Durum ?? '[No Durum]';
    $siparisDurumu = $order->SiparisDurumu ?? '[No SiparisDurumu]';

    $siparisKaynagi = $order->SiparisKaynagi ?? '[No SiparisKaynagi]';
    $siparisNotu = $order->SiparisNotu ?? '[No SiparisNotu]';
    $siparisTarihi = $order->SiparisTarihi ?? '[No SiparisTarihi]';
    $siparisToplamTutari = $order->SiparisToplamTutari ?? '[No SiparisToplamTutari]';
    $stokDustu = $order->StokDustu ?? '[No StokDustu]';;

    $teslimatIl = $order->TeslimatAdresi->Il ?? '[No Il]';
    $teslimatIlce = $order->TeslimatAdresi->Ilce ?? '[No Ilce]';
    $teslimatAdres = $order->TeslimatAdresi->Adres ?? '[No Adres]';

    echo "\nID                      : $id";
    echo "\nAdiSoyadi               : $adiSoyadi";
    echo "\nDurum                   : $durum";
    echo "\nSiparisDurumu           : $siparisDurumu";
    echo "\nSiparisKaynagi          : $siparisKaynagi";
    echo "\nSiparisNotu             : $siparisNotu";
    echo "\nSiparisTarihi           : $siparisTarihi";
    echo "\nSiparisToplamTutari     : $siparisToplamTutari";
    echo "\nStokDustu               : $stokDustu";

    echo "\nTeslimatAdresi Adres    : $teslimatAdres";
    echo "\nTeslimatAdresi Il       : $teslimatIl";
    echo "\nTeslimatAdresi Ilce     : $teslimatIlce";

    echo "\nUrunler:";

    if (!empty($order->Urunler) && is_array($order->Urunler)) {
        foreach ($order->Urunler as $index => $urun) {
            echo sprintf(
                "\n  - [%d] UrunAdi: %s | Barkod: %s | Adet: %s | Tutar: %s",
                $index,
                $urun->UrunAdi ?? '[No UrunAdi]',
                $urun->Barkod ?? '[No Barkod]',
                $urun->Adet ?? '[No Adet]',
                $urun->Tutar ?? '[No Tutar]',
            );
        }
    } else {
        echo " [No Urunler]";
    }

    echo "\n\n---------------------------------------------\n";
}

echo "\n\n=== Test SaveSiparis (Yeni Sipariş Oluşturma) ===\n";

try {
    // Örnek ödeme bilgileri
    $odeme = [
        'BankaKomisyonu' => 0.0,
        'HavaleHesapID' => null,
        'KapidaOdemeTutari' => 0.0,
        'OdemeDurumu' => 1, // 1 = Ödendi
        'OdemeIndirimi' => 0.0,
        'OdemeNotu' => 'Test siparişi',
        'OdemeSecenekID' => 1, // Kredi kartı ödemesi
        'OdemeTipi' => 1,
        'TaksitSayisi' => 1,
        'Tarih' => date('c'),
        'Tutar' => 149.99
    ];

    // Örnek ürün bilgileri
    $urunler = [
        [
            'Adet' => 1,
            'KdvOrani' => 18,
            'KdvTutari' => 22.88,
            'Maliyet' => 100.0,
            'Tutar' => 149.99,
            'UrunID' => 2 // Var olan bir ürün ID'si kullanılmalı
        ]
    ];

    // Sipariş verisi hazırlama
    $orderService->data = [
        'FaturaAdresId' => 1, // Kayıtlı bir fatura adresi ID'si
        'IndirimTutari' => 0.0,
        'KargoAdresId' => 1, // Kayıtlı bir kargo adresi ID'si
        'KargoFirmaId' => 1, // Kayıtlı bir kargo firması ID'si
        'KargoTutari' => 0.0,
        'Odeme' => $odeme,
        'ParaBirimi' => 'TL',
        'SiparisKaynagi' => 'API Test',
        'SiparisNotu' => 'Bu bir test siparişidir',
        'Urunler' => $urunler,
        'UrunTutari' => 149.99,
        'UrunTutariKdv' => 22.88,
        'UyeId' => 1, // Kayıtlı bir üye ID'si
        'TeslimatSaati' => '14:00',
        'TeslimatTarihi' => date('c', strtotime('+2 days'))
    ];

    // Siparişi kaydetme
    $result = $orderService->saveOrder();

    if ($result) {
        echo "✓ Sipariş başarıyla oluşturuldu!\n";
    } else {
        echo "✗ Sipariş oluşturulurken bir hata oluştu.\n";
    }

} catch (Exception $e) {
    echo "✗ Hata: " . $e->getMessage() . "\n";
}

echo "\n\n=== Test Sipariş Ürünleri (getOrderProducts) ===\n";

try {
    // Önce aktif ürünleri getir
    echo "\n1. Sadece Aktif Ürünler:\n";
    $response = $orderService->getOrderProducts(588, false);

    if ($response->isSuccess()) {
        $products = $response->getData();
        echo "Mesaj: " . $response->getMessage() . "\n";
        
        if (!empty($products)) {
            foreach ($products as $product) {
                echo sprintf(
                    "\nÜrün ID: %s\n" .
                    "Ürün Adı: %s\n" .
                    "Barkod: %s\n" .
                    "Adet: %.2f\n" .
                    "Birim Fiyat: %.2f TL\n" .
                    "KDV Oranı: %%%.2f\n" .
                    "KDV Tutarı: %.2f TL\n" .
                    "Toplam Tutar: %.2f TL\n" .
                    "Durum: %s\n" .
                    "Stok Kodu: %s\n",
                    $product['ID'] ?? 'Belirtilmemiş',
                    $product['UrunAdi'] ?? 'Belirtilmemiş',
                    $product['Barkod'] ?? 'Belirtilmemiş',
                    $product['Adet'] ?? 0,
                    $product['Tutar'] ?? 0,
                    $product['KdvOrani'] ?? 0,
                    $product['KdvTutari'] ?? 0,
                    ($product['Tutar'] ?? 0) * ($product['Adet'] ?? 0),
                    $product['DurumAd'] ?? 'Belirtilmemiş',
                    $product['StokKodu'] ?? 'Belirtilmemiş'
                );

                // Eğer kampanya varsa göster
                if (!empty($product['KampanyaID'])) {
                    echo sprintf(
                        "Kampanya ID: %d\n" .
                        "Kampanya İndirim Tutarı: %.2f TL\n",
                        $product['KampanyaID'],
                        $product['KampanyaIndirimTutari'] ?? 0
                    );
                }

                // Eğer mağaza bilgisi varsa göster
                if (!empty($product['MagazaID'])) {
                    echo sprintf(
                        "Mağaza ID: %d\n" .
                        "Mağaza Kodu: %s\n" .
                        "Mağaza Durumu: %s\n" .
                        "Atama Tarihi: %s\n",
                        $product['MagazaID'],
                        $product['MagazaKodu'] ?? 'Belirtilmemiş',
                        $product['MagazaDurum'] ?? 'Belirtilmemiş',
                        $product['MagazaAtamaTarihi'] ?? 'Belirtilmemiş'
                    );
                }

                echo "\n------------------------\n";
            }
        } else {
            echo "Bu siparişte ürün bulunamadı.\n";
        }
    } else {
        echo "Hata: " . $response->getMessage() . "\n";
    }

    // Şimdi iptal edilmiş ürünler dahil tümünü getir
    echo "\n2. Tüm Ürünler (İptal Edilenler Dahil):\n";
    $response = $orderService->getOrderProducts(588, true);

    if ($response->isSuccess()) {
        $products = $response->getData();
        echo "Mesaj: " . $response->getMessage() . "\n";
        
        if (!empty($products)) {
            foreach ($products as $product) {
                echo sprintf(
                    "\nÜrün ID: %s | Durum: %s | Ürün Adı: %s | Adet: %.2f | Tutar: %.2f TL\n",
                    $product['ID'] ?? 'Belirtilmemiş',
                    $product['DurumAd'] ?? 'Belirtilmemiş',
                    $product['UrunAdi'] ?? 'Belirtilmemiş',
                    $product['Adet'] ?? 0,
                    $product['Tutar'] ?? 0
                );
            }
        } else {
            echo "Bu siparişte ürün bulunamadı.\n";
        }
    } else {
        echo "Hata: " . $response->getMessage() . "\n";
    }

    // Olmayan bir sipariş ID'si ile test
    echo "\n3. Geçersiz Sipariş ID Testi:\n";
    $response = $orderService->getOrderProducts(999999);
    echo "Mesaj: " . $response->getMessage() . "\n";

} catch (Exception $e) {
    echo "Test sırasında bir hata oluştu: " . $e->getMessage() . "\n";
}
