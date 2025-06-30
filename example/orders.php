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
