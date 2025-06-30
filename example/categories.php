<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$categoryService = $ticimax->categoryService();

/*
echo "\n--- Create Category ---\n";

$newCategoryData = [
    'Aktif'              => true,
    'AltKategoriSayisi'  => 0,
    'ID'                 => 0,
    'Icerik'             => null,
    'KategoriMenuGoster' => true,
    'Kod'                => 'NEW-CAT-001',
    'PID'                => 0,
    'SeoAnahtarKelime'   => 'category, new, products',
    'SeoSayfaAciklama'   => 'This category contains the newest products.',
    'SeoSayfaBaslik'     => 'New Category - Latest Products',
    'Sira'               => 999,
    'Tanim'              => 'New Category',
    'Url'                => 'new-category'
];

$category = new CategoryModel($newCategoryData);
$result = $categoryService->createCategory($category);
echo "Create Category Result: " . $result . "\n";
*/

echo "\n--- List Categories ---\n";
$categories = $categoryService->getCategories();
foreach ($categories as $category) {
    echo (
        ($category->Tanim ?? '[No Tanim]') .
        ' (ID: ' . ($category->ID ?? '[No ID]') .
        ', PID: ' . ($category->PID ?? '[No PID]') .
        ")\n"
    );
}
