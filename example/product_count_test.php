<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Product\ProductService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$productService = new ProductService($request);

echo "=== PRODUCT COUNT TEST ===\n";
echo "Testing ProductService::SelectUrunCount()\n";
echo "API URL: {$config['mainDomain']}\n\n";

try {
    // Test 1: Get total product count (no filters)
    echo "1. Getting total product count...\n";
    $totalCount = $productService->SelectUrunCount();
    echo "✅ Total products: $totalCount\n\n";

    // Test 2: Count active products only
    echo "2. Getting active products count...\n";
    $activeCount = $productService->SelectUrunCount(['Aktif' => 1]);
    echo "✅ Active products: $activeCount\n\n";

    // Test 3: Count inactive products
    echo "3. Getting inactive products count...\n";
    $inactiveCount = $productService->SelectUrunCount(['Aktif' => 0]);
    echo "✅ Inactive products: $inactiveCount\n\n";

    // Test 4: Count opportunity products (Firsat)
    echo "4. Getting opportunity products count...\n";
    $opportunityCount = $productService->SelectUrunCount(['Firsat' => 1]);
    echo "✅ Opportunity products: $opportunityCount\n\n";

    // Test 5: Count discounted products (Indirimli)
    echo "5. Getting discounted products count...\n";
    $discountedCount = $productService->SelectUrunCount(['Indirimli' => 1]);
    echo "✅ Discounted products: $discountedCount\n\n";

    // Test 6: Count showcase products (Vitrin)
    echo "6. Getting showcase products count...\n";
    $showcaseCount = $productService->SelectUrunCount(['Vitrin' => 1]);
    echo "✅ Showcase products: $showcaseCount\n\n";

    // Test 7: Count by category (if we have categories)
    echo "7. Getting products count by category ID 1...\n";
    $categoryCount = $productService->SelectUrunCount(['KategoriID' => 1]);
    echo "✅ Products in category 1: $categoryCount\n\n";

    // Test 8: Count by brand (if we have brands)
    echo "8. Getting products count by brand ID 1...\n";
    $brandCount = $productService->SelectUrunCount(['MarkaID' => 1]);
    echo "✅ Products from brand 1: $brandCount\n\n";

    // Test 9: Count by stock range
    echo "9. Getting products count with stock between 1-100...\n";
    $stockRangeCount = $productService->SelectUrunCount([
        'ToplamStokAdediBas' => 1,
        'ToplamStokAdediSon' => 100
    ]);
    echo "✅ Products with stock 1-100: $stockRangeCount\n\n";

    // Test 10: Complex filter combination
    echo "10. Getting count with complex filters (Active + Showcase + Stock > 0)...\n";
    $complexCount = $productService->SelectUrunCount([
        'Aktif' => 1,
        'Vitrin' => 1,
        'ToplamStokAdediBas' => 1
    ]);
    echo "✅ Complex filter result: $complexCount\n\n";

    // Summary
    echo "=== SUMMARY ===\n";
    echo "Total Products: $totalCount\n";
    echo "Active: $activeCount\n";
    echo "Inactive: $inactiveCount\n";
    echo "Opportunity: $opportunityCount\n";
    echo "Discounted: $discountedCount\n";
    echo "Showcase: $showcaseCount\n";
    echo "Category 1: $categoryCount\n";
    echo "Brand 1: $brandCount\n";
    echo "Stock 1-100: $stockRangeCount\n";
    echo "Complex filter: $complexCount\n";

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== PRODUCT COUNT TEST COMPLETED ===\n"; 