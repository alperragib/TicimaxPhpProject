<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

$ticimax = new Ticimax($mainDomain, $apiKey);
$productService = $ticimax->productService();


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
print_r($products);