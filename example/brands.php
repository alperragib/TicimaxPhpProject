<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$brandService = $ticimax->brandService();

echo "\n--- List Brands ---\n";
$brands = $brandService->getBrands();
foreach ($brands as $brand) {
    echo (
        ($brand->Tanim ?? '[No Tanim]') .
        ' (ID: ' . ($brand->ID ?? '[No ID]') .
        ")\n"
    );
}
