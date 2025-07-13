<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$brandService = $ticimax->brandService();

echo "=== BRAND SERVICE TEST SUITE ===\n\n";

// Test 1: Get All Brands
echo "1. === GET ALL BRANDS TEST ===\n";
echo "Testing getBrands() without specific brand ID...\n";

$allBrands = $brandService->getBrands();

if (!empty($allBrands)) {
    echo "✓ Markalar başarıyla getirildi.\n";
    echo "Found " . count($allBrands) . " brands.\n";
    
    if (count($allBrands) > 0) {
        echo "First brand details:\n";
        $firstBrand = $allBrands[0];
        echo "- ID: " . ($firstBrand->ID ?? 'N/A') . "\n";
        echo "- Brand Name (Tanim): " . ($firstBrand->Tanim ?? 'N/A') . "\n";
        echo "- Active (Aktif): " . ($firstBrand->Aktif ? 'Yes' : 'No') . "\n";
        echo "- Logo (Resim): " . ($firstBrand->Resim ?? 'N/A') . "\n";
        echo "- URL: " . ($firstBrand->Url ?? 'N/A') . "\n";
        echo "- Sort Order (Sira): " . ($firstBrand->Sira ?? 'N/A') . "\n";
        echo "- Created Date (EklemeTarihi): " . ($firstBrand->EklemeTarihi ?? 'N/A') . "\n";
        echo "- Updated Date (GuncellemeTarihi): " . ($firstBrand->GuncellemeTarihi ?? 'N/A') . "\n";
        
        // Store first brand ID for specific test
        $testBrandId = $firstBrand->ID ?? null;
    }
} else {
    echo "✗ Marka bulunamadı veya hata oluştu.\n";
    $testBrandId = null;
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 2: Get Specific Brand
if ($testBrandId) {
    echo "2. === GET SPECIFIC BRAND TEST ===\n";
    echo "Testing getBrands() with specific brand ID: $testBrandId...\n";

    $specificBrand = $brandService->getBrands($testBrandId);

    if (!empty($specificBrand)) {
        echo "✓ Belirli marka başarıyla getirildi.\n";
        echo "Found " . count($specificBrand) . " brand(s).\n";
        
        if (count($specificBrand) > 0) {
            echo "Specific brand details:\n";
            $brand = $specificBrand[0];
            echo "- ID: " . ($brand->ID ?? 'N/A') . "\n";
            echo "- Brand Name (Tanim): " . ($brand->Tanim ?? 'N/A') . "\n";
            echo "- Active (Aktif): " . ($brand->Aktif ? 'Yes' : 'No') . "\n";
            echo "- Logo (Resim): " . ($brand->Resim ?? 'N/A') . "\n";
            echo "- URL: " . ($brand->Url ?? 'N/A') . "\n";
            echo "- Description (Icerik): " . ($brand->Icerik ?? 'N/A') . "\n";
            echo "- Sort Order (Sira): " . ($brand->Sira ?? 'N/A') . "\n";
            echo "- Meta Keywords (SeoAnahtarKelime): " . ($brand->SeoAnahtarKelime ?? 'N/A') . "\n";
            echo "- Meta Description (SeoSayfaAciklama): " . ($brand->SeoSayfaAciklama ?? 'N/A') . "\n";
            echo "- Meta Title (SeoSayfaBaslik): " . ($brand->SeoSayfaBaslik ?? 'N/A') . "\n";
            echo "- Breadcrumb: " . ($brand->Breadcrumb ?? 'N/A') . "\n";
            echo "- Created Date (EklemeTarihi): " . ($brand->EklemeTarihi ?? 'N/A') . "\n";
            echo "- Updated Date (GuncellemeTarihi): " . ($brand->GuncellemeTarihi ?? 'N/A') . "\n";
        }
    } else {
        echo "✗ Belirli marka getirilemedi.\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test 3: Get Non-existent Brand
echo "3. === GET NON-EXISTENT BRAND TEST ===\n";
echo "Testing getBrands() with non-existent brand ID: 999999...\n";

$nonExistentBrand = $brandService->getBrands(999999);

if (empty($nonExistentBrand)) {
    echo "✓ Olmayan marka için boş sonuç döndü (beklendiği gibi).\n";
} else {
    echo "✗ Olmayan marka için beklenmeyen sonuç döndü.\n";
    echo "Found " . count($nonExistentBrand) . " brand(s).\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 4: Brand Data Integrity Test
echo "4. === BRAND DATA INTEGRITY TEST ===\n";
echo "Testing brand data structure and field completeness...\n";

$brands = $brandService->getBrands();
$completeDataCount = 0;
$incompleteDataCount = 0;

foreach ($brands as $brand) {
    // Use correct field names
    $hasRequiredFields = isset($brand->ID) && isset($brand->Tanim);
    
    if ($hasRequiredFields) {
        $completeDataCount++;
    } else {
        $incompleteDataCount++;
        echo "⚠ Incomplete brand data found - ID: " . ($brand->ID ?? 'missing') . 
             ", Name (Tanim): " . ($brand->Tanim ?? 'missing') . "\n";
    }
}

echo "✓ Data integrity check completed.\n";
echo "- Brands with complete data: $completeDataCount\n";
echo "- Brands with incomplete data: $incompleteDataCount\n";

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 5: Brand Statistics
echo "5. === BRAND STATISTICS TEST ===\n";
echo "Generating brand statistics...\n";

$activeBrands = 0;
$inactiveBrands = 0;
$brandsWithLogo = 0;
$brandsWithDescription = 0;
$brandsWithSeoData = 0;

foreach ($brands as $brand) {
    if ($brand->Aktif ?? false) {
        $activeBrands++;
    } else {
        $inactiveBrands++;
    }
    
    if (!empty($brand->Resim)) {
        $brandsWithLogo++;
    }
    
    if (!empty($brand->Icerik)) {
        $brandsWithDescription++;
    }
    
    if (!empty($brand->SeoSayfaBaslik) || !empty($brand->SeoAnahtarKelime) || !empty($brand->SeoSayfaAciklama)) {
        $brandsWithSeoData++;
    }
}

echo "✓ Brand statistics generated.\n";
echo "- Total brands: " . count($brands) . "\n";
echo "- Active brands: $activeBrands\n";
echo "- Inactive brands: $inactiveBrands\n";
echo "- Brands with logo: $brandsWithLogo\n";
echo "- Brands with description: $brandsWithDescription\n";
echo "- Brands with SEO data: $brandsWithSeoData\n";

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 6: Performance Test
echo "6. === PERFORMANCE TEST ===\n";
echo "Testing multiple brand requests for performance...\n";

$startTime = microtime(true);

// Make 5 requests to test performance
for ($i = 1; $i <= 5; $i++) {
    $brandService->getBrands();
    echo "Request $i completed...\n";
}

$endTime = microtime(true);
$totalTime = $endTime - $startTime;
$averageTime = $totalTime / 5;

echo "✓ Performance test completed.\n";
echo "- Total time for 5 requests: " . number_format($totalTime, 4) . " seconds\n";
echo "- Average time per request: " . number_format($averageTime, 4) . " seconds\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "BRAND SERVICE TEST SUITE COMPLETED!\n";
echo str_repeat("=", 50) . "\n\n";

echo "SUMMARY:\n";
echo "- All BrandService methods have been tested\n";
echo "- Data integrity and performance tests completed\n";
echo "- Brand statistics generated\n";
echo "- Real API interactions were performed for all tests\n";
echo "- Field names corrected to match WSDL schema\n"; 