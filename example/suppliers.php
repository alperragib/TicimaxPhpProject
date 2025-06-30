<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$supplierService = $ticimax->supplierService();

echo "\n--- List Suppliers ---\n";
$suppliers = $supplierService->getSuppliers();
foreach ($suppliers as $supplier) {
    echo (
        ($supplier->Tanim ?? '[No Tanim]') .
        ' (ID: ' . ($supplier->ID ?? '[No ID]') .
        ")\n"
    );
}
