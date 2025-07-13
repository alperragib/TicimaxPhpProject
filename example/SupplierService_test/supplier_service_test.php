<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$supplierService = $ticimax->supplierService();

echo "=== SUPPLIER SERVICE TEST SUITE ===\n\n";

// Test 1: Get All Suppliers
echo "1. === GET ALL SUPPLIERS TEST ===\n";
echo "Testing getSuppliers() without specific supplier ID...\n";

$allSuppliers = $supplierService->getSuppliers();

if (!empty($allSuppliers)) {
    echo "✓ Tedarikçiler başarıyla getirildi.\n";
    echo "Found " . count($allSuppliers) . " suppliers.\n";
    
    if (count($allSuppliers) > 0) {
        echo "First supplier details:\n";
        $firstSupplier = $allSuppliers[0];
        echo "- ID: " . ($firstSupplier->ID ?? 'N/A') . "\n";
        echo "- Supplier Name: " . ($firstSupplier->Tanim ?? 'N/A') . "\n";
        echo "- Active: " . ($firstSupplier->Aktif ? 'Yes' : 'No') . "\n";
        echo "- Email: " . ($firstSupplier->Mail ?? 'N/A') . "\n";
        echo "- Note: " . ($firstSupplier->Not ?? 'N/A') . "\n";
        
        // Store first supplier ID for specific test
        $testSupplierId = $firstSupplier->ID ?? null;
    }
} else {
    echo "✗ Tedarikçi bulunamadı veya hata oluştu.\n";
    $testSupplierId = null;
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 2: Get Specific Supplier
if ($testSupplierId) {
    echo "2. === GET SPECIFIC SUPPLIER TEST ===\n";
    echo "Testing getSuppliers() with specific supplier ID: $testSupplierId...\n";

    $specificSupplier = $supplierService->getSuppliers($testSupplierId);

    if (!empty($specificSupplier)) {
        echo "✓ Belirli tedarikçi başarıyla getirildi.\n";
        echo "Found " . count($specificSupplier) . " supplier(s).\n";
        
        if (count($specificSupplier) > 0) {
            echo "Specific supplier details:\n";
            $supplier = $specificSupplier[0];
            echo "- ID: " . ($supplier->ID ?? 'N/A') . "\n";
            echo "- Supplier Name: " . ($supplier->Tanim ?? 'N/A') . "\n";
            echo "- Active: " . ($supplier->Aktif ? 'Yes' : 'No') . "\n";
            echo "- Email: " . ($supplier->Mail ?? 'N/A') . "\n";
            echo "- Note: " . ($supplier->Not ?? 'N/A') . "\n";
        }
    } else {
        echo "✗ Belirli tedarikçi getirilemedi.\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test 3: Get Non-existent Supplier
echo "3. === GET NON-EXISTENT SUPPLIER TEST ===\n";
echo "Testing getSuppliers() with non-existent supplier ID: 999999...\n";

$nonExistentSupplier = $supplierService->getSuppliers(999999);

if (empty($nonExistentSupplier)) {
    echo "✓ Olmayan tedarikçi için boş sonuç döndü (beklendiği gibi).\n";
} else {
    echo "✗ Olmayan tedarikçi için beklenmeyen sonuç döndü.\n";
    echo "Found " . count($nonExistentSupplier) . " supplier(s).\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 4: Supplier Data Integrity Test
echo "4. === SUPPLIER DATA INTEGRITY TEST ===\n";
echo "Testing supplier data structure and field completeness...\n";

$suppliers = $supplierService->getSuppliers();
$completeDataCount = 0;
$incompleteDataCount = 0;

foreach ($suppliers as $supplier) {
    $hasRequiredFields = isset($supplier->ID) && isset($supplier->Tanim);
    
    if ($hasRequiredFields) {
        $completeDataCount++;
    } else {
        $incompleteDataCount++;
        echo "⚠ Incomplete supplier data found - ID: " . ($supplier->ID ?? 'missing') . 
             ", Name: " . ($supplier->Tanim ?? 'missing') . "\n";
    }
}

echo "✓ Data integrity check completed.\n";
echo "- Suppliers with complete data: $completeDataCount\n";
echo "- Suppliers with incomplete data: $incompleteDataCount\n";

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 5: Supplier Statistics
echo "5. === SUPPLIER STATISTICS TEST ===\n";
echo "Generating supplier statistics...\n";

$activeSuppliers = 0;
$inactiveSuppliers = 0;
$suppliersWithEmail = 0;
$suppliersWithNote = 0;

foreach ($suppliers as $supplier) {
    if ($supplier->Aktif ?? false) {
        $activeSuppliers++;
    } else {
        $inactiveSuppliers++;
    }
    
    if (!empty($supplier->Mail)) {
        $suppliersWithEmail++;
    }
    
    if (!empty($supplier->Not)) {
        $suppliersWithNote++;
    }
}

echo "✓ Supplier statistics generated.\n";
echo "- Total suppliers: " . count($suppliers) . "\n";
echo "- Active suppliers: $activeSuppliers\n";
echo "- Inactive suppliers: $inactiveSuppliers\n";
echo "- Suppliers with email: $suppliersWithEmail\n";
echo "- Suppliers with note: $suppliersWithNote\n";

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 6: Supplier Contact Information Test
echo "6. === SUPPLIER CONTACT INFORMATION TEST ===\n";
echo "Analyzing supplier contact information completeness...\n";

$contactStats = [
    'with_email' => 0,
    'with_note' => 0,
    'complete_info' => 0,
    'minimal_info' => 0
];

foreach ($suppliers as $supplier) {
    $hasEmail = !empty($supplier->Mail);
    $hasNote = !empty($supplier->Not);
    
    if ($hasEmail) {
        $contactStats['with_email']++;
    }
    
    if ($hasNote) {
        $contactStats['with_note']++;
    }
    
    if ($hasEmail && $hasNote) {
        $contactStats['complete_info']++;
    } elseif (!$hasEmail && !$hasNote) {
        $contactStats['minimal_info']++;
    }
}

echo "✓ Contact information analysis completed.\n";
echo "- Suppliers with email: " . $contactStats['with_email'] . "\n";
echo "- Suppliers with note: " . $contactStats['with_note'] . "\n";
echo "- Suppliers with complete info: " . $contactStats['complete_info'] . "\n";
echo "- Suppliers with minimal info: " . $contactStats['minimal_info'] . "\n";

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 7: Performance Test
echo "7. === PERFORMANCE TEST ===\n";
echo "Testing multiple supplier requests for performance...\n";

$startTime = microtime(true);

// Make 5 requests to test performance
for ($i = 1; $i <= 5; $i++) {
    $supplierService->getSuppliers();
    echo "Request $i completed...\n";
}

$endTime = microtime(true);
$totalTime = $endTime - $startTime;
$averageTime = $totalTime / 5;

echo "✓ Performance test completed.\n";
echo "- Total time for 5 requests: " . number_format($totalTime, 4) . " seconds\n";
echo "- Average time per request: " . number_format($averageTime, 4) . " seconds\n";

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 8: Supplier ID Validation Test
echo "8. === SUPPLIER ID VALIDATION TEST ===\n";
echo "Testing supplier ID uniqueness...\n";

$supplierIds = [];
$duplicateIds = [];

foreach ($suppliers as $supplier) {
    $id = $supplier->ID ?? null;
    
    if ($id === null) {
        continue;
    }
    
    if (in_array($id, $supplierIds)) {
        $duplicateIds[] = $id;
    } else {
        $supplierIds[] = $id;
    }
}

echo "✓ Supplier ID validation completed.\n";
echo "- Unique supplier IDs: " . count($supplierIds) . "\n";
echo "- Duplicate IDs found: " . count($duplicateIds) . "\n";

if (!empty($duplicateIds)) {
    echo "⚠ Duplicate IDs detected: " . implode(', ', array_unique($duplicateIds)) . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "SUPPLIER SERVICE TEST SUITE COMPLETED!\n";
echo str_repeat("=", 50) . "\n\n";

echo "SUMMARY:\n";
echo "- All SupplierService methods have been tested\n";
echo "- Data integrity and validation tests completed\n";
echo "- Contact information analysis performed\n";
echo "- Supplier statistics and ID validation done\n";
echo "- Performance test completed\n";
echo "- Real API interactions were performed for all tests\n";
echo "- Field mapping corrected: TedarikciAdi → Tanim\n"; 