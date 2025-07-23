<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Load configuration
$config = require __DIR__ . '/../config.php';

echo "=== SUPPLIER SERVICE TEST ===\n\n";

$testStart = microtime(true);

try {
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $supplierService = $ticimax->supplierService();

    echo "Ticimax SupplierService initialized\n";
    echo "Domain: {$config['mainDomain']}\n\n";

    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;

    echo "========================================\n";
    echo "         SUPPLIER TESTS\n";
    echo "========================================\n\n";

    // Test 1: List all suppliers
    echo "Test 1: LIST ALL SUPPLIERS\n";
    echo "------------------------------------\n";
    $testCount++;

    $response = $supplierService->getSuppliers();

    // ApiResponse object, get data array from ->getData()
    $allSuppliers = $response->getData() ?? [];

    if (!empty($allSuppliers)) {
        $successCount++;
        echo "Suppliers retrieved successfully\n";
        echo "Total Suppliers: " . count($allSuppliers) . "\n\n";

        foreach ($allSuppliers as $index => $supplier) {
            $num = $index + 1;
            echo "Supplier #$num\n";
            echo "ID: " . ($supplier->ID ?? 'N/A') . "\n";
            echo "Name: " . ($supplier->Tanim ?? 'N/A') . "\n";
            echo "Active: " . (($supplier->Aktif ?? false) ? 'Yes' : 'No') . "\n";
            echo "Email: " . ($supplier->Mail ?: 'Not specified') . "\n";
            echo "Note: " . ($supplier->Not ?: 'Not specified') . "\n";
            echo "---------------------------\n";
        }

        $testSupplierId = $allSuppliers[0]->ID ?? null;

    } else {
        $errorCount++;
        echo "No suppliers found or error occurred\n";
        $testSupplierId = null;
    }
    echo "\n";

    // Test 2: Get specific supplier details
    if ($testSupplierId) {
        echo "Test 2: SPECIFIC SUPPLIER DETAILS\n";
        echo "---------------------------------\n";
        $testCount++;

        $response = $supplierService->getSuppliers($testSupplierId);
        $specificSuppliers = $response->getData() ?? [];

        if (!empty($specificSuppliers)) {
            $successCount++;
            echo "Specific supplier retrieved successfully\n";
            echo "Tested ID: $testSupplierId\n";

            $supplier = $specificSuppliers[0];
            echo "ID: " . ($supplier->ID ?? 'N/A') . "\n";
            echo "Name: " . ($supplier->Tanim ?? 'N/A') . "\n";
            echo "Active: " . (($supplier->Aktif ?? false) ? 'Yes' : 'No') . "\n";
            echo "Email: " . ($supplier->Mail ?: 'Not specified') . "\n";
            echo "Note: " . ($supplier->Not ?: 'Not specified') . "\n";
        } else {
            $errorCount++;
            echo "Could not retrieve specific supplier\n";
        }
        echo "\n";
    }

    // Test 3: Non-existent supplier check
    echo "Test 3: NON-EXISTENT SUPPLIER CHECK\n";
    echo "-----------------------------------\n";
    $testCount++;

    $response = $supplierService->getSuppliers(999999);
    $nonExistentSupplier = $response->getData() ?? [];

    if (empty($nonExistentSupplier)) {
        $successCount++;
        echo "Empty result returned for non-existent supplier (correct behavior)\n";
        echo "Test ID: 999999 - Result: Not found\n";
    } else {
        $errorCount++;
        echo "Unexpected result returned for non-existent supplier\n";
        echo "Records found: " . count($nonExistentSupplier) . "\n";
    }
    echo "\n";

    // Summary
    $testEnd = microtime(true);
    $totalTime = round($testEnd - $testStart, 2);

    echo "========================================\n";
    echo "           TEST RESULTS\n";
    echo "========================================\n";
    echo "Total Tests: $testCount\n";
    echo "Successful: $successCount\n";
    echo "Failed: $errorCount\n";
    echo "Test Duration: {$totalTime} seconds\n";
    echo "Success Rate: " . round(($successCount / $testCount) * 100, 1) . "%\n\n";

    echo "========================================\n";
    echo "           TEST SUMMARY\n";
    echo "========================================\n";
    echo "Tested Operations:\n";
    echo " - getSuppliers() - List all suppliers\n";
    echo " - getSuppliers(id) - Get specific supplier\n";
    echo " - Non-existent supplier error handling\n\n";

    echo "Field Mapping:\n";
    echo " - SupplierName → Tanim\n";
    echo " - ID → ID\n";
    echo " - Active → Aktif\n";
    echo " - Mail → Mail\n";
    echo " - Note → Not\n\n";

    echo "SupplierService test process completed.\n";

} catch (Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== SUPPLIER SERVICE TEST COMPLETED ===\n";
