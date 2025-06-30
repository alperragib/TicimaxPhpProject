<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$productService = $ticimax->productService();

echo "--- List Products ---\n";

$filters = [
    'Aktif'       => 1,
    'Firsat'      => -1,
    'Indirimli'   => -1,
    'Vitrin'      => -1,
    'KategoriID'  => 0,
    'MarkaID'     => 0,
    'UrunKartiID' => 0,
];

$pagination = [
    'BaslangicIndex'            => 0,
    'KayitSayisi'               => 10,
    'KayitSayisinaGoreGetir'    => true,
    'SiralamaDegeri'            => 'Sira',
    'SiralamaYonu'              => 'ASC',
];

$products = $productService->getProducts($filters, $pagination);

foreach ($products as $product) {
    $id = $product->ID ?? '[No ID]';
    $urunAdi = $product->UrunAdi ?? '[No UrunAdi]';
    $onYazi = $product->OnYazi ?? '[No OnYazi]';
    $ucretsizKargo = $product->UcretsizKargo ?? '[No UcretsizKargo]';

    $satisFiyati = '[No SatisFiyati]';
    $indirimliFiyati = '[No IndirimliFiyati]';
    $kdvOrani = '[No KdvOrani]';
    $paraBirimi = '[No ParaBirimi]';
    $desi = '[No Desi]';
    $kargoUcreti = '[No KargoUcreti]';

    if (!empty($product->Varyasyonlar) && is_array($product->Varyasyonlar)) {
        foreach ($product->Varyasyonlar as $varyasyon) {
            if (($varyasyon->Aktif ?? 0) == 1) {
                $satisFiyati = $varyasyon->SatisFiyati ?? $satisFiyati;
                $indirimliFiyati = $varyasyon->IndirimliFiyati ?? $indirimliFiyati;
                $kdvOrani = $varyasyon->KdvOrani ?? $kdvOrani;
                $paraBirimi = $varyasyon->ParaBirimi ?? $paraBirimi;
                $desi = $varyasyon->Desi ?? $desi;
                $kargoUcreti = $varyasyon->KargoUcreti ?? $kargoUcreti;
                break;
            }
        }
    }

    $resimler = '[No Resimler]';
    if (!empty($product->Resimler) && isset($product->Resimler['string'])) {
        $resimler = implode(', ', $product->Resimler['string']);
    }

    echo "\nID                   : $id";
    echo "\nUrunAdi              : $urunAdi";
    echo "\nOnYazi               : $onYazi";
    echo "\nUcretsizKargo        : $ucretsizKargo";
    echo "\nSatisFiyati          : $satisFiyati";
    echo "\nIndirimliFiyati      : $indirimliFiyati";
    echo "\nKdvOrani             : $kdvOrani";
    echo "\nParaBirimi           : $paraBirimi";
    echo "\nDesi                 : $desi";
    echo "\nKargoUcreti          : $kargoUcreti";
    echo "\nResimler             : $resimler";
    echo "\n\n---------------------------------------------\n";
}
