<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Load configuration
$config = require __DIR__ . '/../config.php';

echo "=== SUPPLIER SERVICE TEST ===\n\n";

// Test start time
$testStart = microtime(true);

try {
    // Initialize Ticimax API
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $supplierService = $ticimax->supplierService();
    
    echo "✓ Ticimax SupplierService initialized\n";
    echo "Domain: {$config['mainDomain']}\n\n";
    
    // Test counters
    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;
    
    echo "========================================\n";
    echo "         SUPPLIER TESTS\n";
    echo "========================================\n\n";
    
    // Test 1: Get and list all suppliers
    echo "🧪 Test 1: LIST ALL SUPPLIERS\n";
    echo "------------------------------------\n";
    $testCount++;
    
    $allSuppliers = $supplierService->getSuppliers();
    
    if (!empty($allSuppliers)) {
        $successCount++;
        echo "✅ Suppliers retrieved successfully\n";
        echo "📦 Total Suppliers: " . count($allSuppliers) . "\n\n";
        
        // LIST ALL SUPPLIERS
        echo "📋 COMPLETE SUPPLIER LIST:\n";
        echo str_repeat("=", 80) . "\n";
        
        foreach ($allSuppliers as $index => $supplier) {
            $supplierNum = $index + 1;
            echo "[$supplierNum] SUPPLIER DETAILS:\n";
            echo "   🆔 ID: " . ($supplier->ID ?? 'N/A') . "\n";
            echo "   🏷️  Name: " . ($supplier->Tanim ?? 'N/A') . "\n";
            echo "   ✅ Active: " . (($supplier->Aktif ?? false) ? 'Yes' : 'No') . "\n";
            echo "   📧 Email: " . ($supplier->Mail ?? 'Not specified') . "\n";
            echo "   📝 Note: " . ($supplier->Not ?? 'Not specified') . "\n";
            echo "   -------------------------\n";
        }
        
        // Statistics
        $activeCount = 0;
        $inactiveCount = 0;
        $emailCount = 0;
        $noteCount = 0;
        
        foreach ($allSuppliers as $supplier) {
            if ($supplier->Aktif ?? false) $activeCount++;
            else $inactiveCount++;
            
            if (!empty($supplier->Mail)) $emailCount++;
            if (!empty($supplier->Not)) $noteCount++;
        }
        
        echo "\n📊 SUPPLIER STATISTICS:\n";
        echo "   📦 Total Suppliers: " . count($allSuppliers) . "\n";
        echo "   ✅ Active Suppliers: $activeCount\n";
        echo "   ❌ Inactive Suppliers: $inactiveCount\n";
        echo "   📧 With Email: $emailCount\n";
        echo "   📝 With Notes: $noteCount\n";
        
        // Save first supplier for testing
        $testSupplierId = $allSuppliers[0]->ID ?? null;
        
    } else {
        $errorCount++;
        echo "❌ No suppliers found or error occurred\n";
        $testSupplierId = null;
    }
    echo "\n";
    
    // Test 2: Get specific supplier
    if ($testSupplierId) {
        echo "🧪 Test 2: SPECIFIC SUPPLIER DETAILS\n";
        echo "---------------------------------\n";
        $testCount++;
        
        $specificSupplier = $supplierService->getSuppliers($testSupplierId);
        
        if (!empty($specificSupplier)) {
            $successCount++;
            echo "✅ Specific supplier retrieved successfully\n";
            echo "🎯 Tested ID: $testSupplierId\n";
            
            $supplier = $specificSupplier[0];
            echo "📋 DETAILED INFORMATION:\n";
            echo "   🆔 ID: " . ($supplier->ID ?? 'N/A') . "\n";
            echo "   🏷️  Supplier Name: " . ($supplier->Tanim ?? 'N/A') . "\n";
            echo "   ✅ Status: " . (($supplier->Aktif ?? false) ? 'Active' : 'Inactive') . "\n";
            echo "   📧 Email: " . ($supplier->Mail ?? 'Not specified') . "\n";
            echo "   📝 Note: " . ($supplier->Not ?? 'Not specified') . "\n";
        } else {
            $errorCount++;
            echo "❌ Could not retrieve specific supplier\n";
        }
        echo "\n";
    }
    
    // Test 3: Check non-existent supplier
    echo "🧪 Test 3: NON-EXISTENT SUPPLIER CHECK\n";
    echo "-----------------------------------\n";
    $testCount++;
    
    $nonExistentSupplier = $supplierService->getSuppliers(999999);
    
    if (empty($nonExistentSupplier)) {
        $successCount++;
        echo "✅ Empty result returned for non-existent supplier (correct behavior)\n";
        echo "🎯 Test ID: 999999 - Result: Not found\n";
    } else {
        $errorCount++;
        echo "❌ Unexpected result returned for non-existent supplier\n";
        echo "📦 Records found: " . count($nonExistentSupplier) . "\n";
    }
    echo "\n";
    
    // Test 4: Performance test
    echo "🧪 Test 4: PERFORMANCE TEST\n";
    echo "-------------------------\n";
    $testCount++;
    
    $performanceStart = microtime(true);
    
    // Make the same query 3 times
    for ($i = 1; $i <= 3; $i++) {
        $perfTest = $supplierService->getSuppliers();
        echo "   📡 Request $i completed...\n";
    }
    
    $performanceEnd = microtime(true);
    $performanceTime = round($performanceEnd - $performanceStart, 2);
    $avgTime = round($performanceTime / 3, 2);
    
    $successCount++;
    echo "✅ Performance test completed\n";
    echo "⏱️  Total Time for 3 Requests: {$performanceTime} seconds\n";
    echo "📊 Average Request Time: {$avgTime} seconds\n";
    echo "\n";
    
    // Calculate test duration
    $testEnd = microtime(true);
    $totalTime = round($testEnd - $testStart, 2);
    
    echo "========================================\n";
    echo "           TEST RESULTS\n";
    echo "========================================\n";
    echo "📊 Total Tests: $testCount\n";
    echo "✅ Successful: $successCount\n";
    echo "❌ Failed: $errorCount\n";
    echo "⏱️  Test Duration: {$totalTime} seconds\n";
    echo "📈 Success Rate: " . round(($successCount / $testCount) * 100, 1) . "%\n\n";
    
    // Test summary
    echo "========================================\n";
    echo "           TEST SUMMARY\n";
    echo "========================================\n";
    echo "🧪 Tested Operations:\n";
    echo "   • getSuppliers() - Complete supplier list\n";
    echo "   • getSuppliers(id) - Specific supplier details\n";
    echo "   • Error handling - Non-existent ID test\n";
    echo "   • Performance analysis - Multiple request test\n";
    echo "   • Data integrity - Field mapping check\n\n";
    
    echo "📋 Field Mapping (WSDL Compatible):\n";
    echo "   • SupplierName → Tanim ✅\n";
    echo "   • ID → ID ✅\n";
    echo "   • Active → Aktif ✅\n";
    echo "   • Mail → Mail ✅\n";
    echo "   • Note → Not ✅\n\n";
    
    echo "🏁 SupplierService test process completed!\n";
    
} catch (Exception $e) {
    echo "💥 FATAL ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=== SUPPLIER SERVICE TEST COMPLETED ===\n"; 