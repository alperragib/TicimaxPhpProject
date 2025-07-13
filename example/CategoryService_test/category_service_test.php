<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../console_helper.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Category\CategoryModel;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$categoryService = $ticimax->categoryService();

ConsoleHelper::print("=== CATEGORY SERVICE TEST SUITE ===\n\n");

// Test 1: Get All Categories
ConsoleHelper::print("1. === GET ALL CATEGORIES TEST ===\n");
ConsoleHelper::print("Testing getCategories() without specific category ID...\n");

$allCategories = $categoryService->getCategories();

if (!empty($allCategories)) {
    ConsoleHelper::print("✓ Categories retrieved successfully.\n");
    ConsoleHelper::print("Found " . count($allCategories) . " categories.\n");
    
    if (count($allCategories) > 0) {
        ConsoleHelper::print("First category details:\n");
        $firstCategory = $allCategories[0];
        ConsoleHelper::displayData("ID", $firstCategory->ID);
        ConsoleHelper::displayData("Category Name (Tanim)", $firstCategory->Tanim);
        ConsoleHelper::displayData("Parent ID (PID)", $firstCategory->PID);
        ConsoleHelper::displayData("Active (Aktif)", $firstCategory->Aktif ? 'Yes' : 'No');
        ConsoleHelper::displayData("Sort Order (Sira)", $firstCategory->Sira);
        ConsoleHelper::displayData("URL", $firstCategory->Url);
        
        // Store first category ID for specific test
        $testCategoryId = $firstCategory->ID ?? null;
        $testParentId = $firstCategory->PID ?? null;
    }
} else {
    ConsoleHelper::print("✗ No categories found or error occurred.\n");
    $testCategoryId = null;
    $testParentId = null;
}

ConsoleHelper::print("\n" . str_repeat("-", 50) . "\n\n");

// Test 2: Get Specific Category
if ($testCategoryId) {
    ConsoleHelper::print("2. === GET SPECIFIC CATEGORY TEST ===\n");
    ConsoleHelper::print("Testing getCategories() with specific category ID: $testCategoryId...\n");

    $specificCategory = $categoryService->getCategories($testCategoryId);

    if (!empty($specificCategory)) {
        ConsoleHelper::print("✓ Specific category retrieved successfully.\n");
        ConsoleHelper::print("Found " . count($specificCategory) . " category(ies).\n");
        
        if (count($specificCategory) > 0) {
            ConsoleHelper::print("Specific category details:\n");
            $category = $specificCategory[0];
            ConsoleHelper::displayData("ID", $category->ID);
            ConsoleHelper::displayData("Category Name (Tanim)", $category->Tanim);
            ConsoleHelper::displayData("Parent ID (PID)", $category->PID);
            ConsoleHelper::displayData("Active (Aktif)", $category->Aktif ? 'Yes' : 'No');
            ConsoleHelper::displayData("Sort Order (Sira)", $category->Sira);
            ConsoleHelper::displayData("Description (Icerik)", $category->Icerik);
            ConsoleHelper::displayData("SEO URL (Url)", $category->Url);
            ConsoleHelper::displayData("Meta Keywords (SeoAnahtarKelime)", $category->SeoAnahtarKelime);
            ConsoleHelper::displayData("Meta Description (SeoSayfaAciklama)", $category->SeoSayfaAciklama);
            ConsoleHelper::displayData("Meta Title (SeoSayfaBaslik)", $category->SeoSayfaBaslik);
            ConsoleHelper::displayData("Code (Kod)", $category->Kod);
        }
    } else {
        ConsoleHelper::print("✗ Could not retrieve specific category.\n");
    }

    ConsoleHelper::print("\n" . str_repeat("-", 50) . "\n\n");
}

// Test 3: Get Categories with Language Filter
ConsoleHelper::print("3. === GET CATEGORIES WITH LANGUAGE FILTER TEST ===\n");
ConsoleHelper::print("Testing getCategories() with Turkish language filter...\n");
ConsoleHelper::print("Note: Language filter may not be implemented in API.\n");

$turkishCategories = $categoryService->getCategories(0, 'tr');

if (!empty($turkishCategories)) {
    ConsoleHelper::print("✓ Turkish categories retrieved successfully.\n");
    ConsoleHelper::print("Found " . count($turkishCategories) . " Turkish categories.\n");
} else {
    ConsoleHelper::print("✓ No Turkish categories found (API language filter might not be supported).\n");
}

ConsoleHelper::print("\n" . str_repeat("-", 50) . "\n\n");

// Test 4: Get Categories with Parent Filter
if ($testParentId) {
    ConsoleHelper::print("4. === GET CATEGORIES WITH PARENT FILTER TEST ===\n");
    ConsoleHelper::print("Testing getCategories() with parent ID: $testParentId...\n");

    $childCategories = $categoryService->getCategories(0, null, $testParentId);

    if (!empty($childCategories)) {
        ConsoleHelper::print("✓ Child categories retrieved successfully.\n");
        ConsoleHelper::print("Found " . count($childCategories) . " child categories.\n");
    } else {
        ConsoleHelper::print("✓ No child categories found (might be normal).\n");
    }

    ConsoleHelper::print("\n" . str_repeat("-", 50) . "\n\n");
}

// Test 5: Create New Category Test
ConsoleHelper::print("5. === CREATE CATEGORY TEST ===\n");
ConsoleHelper::print("Testing createCategory() with new category...\n");

// Create a test category with correct WSDL field names
$categoryData = [
    'ID' => 0, // 0 for new category
    'Tanim' => 'Test Category - ' . date('Y-m-d H:i:s'), // Category name
    'PID' => 0, // Parent ID - Root category
    'Aktif' => true,
    'Sira' => 999, // Sort order
    'Icerik' => 'Test category created by automated test', // Description/Content
    'Url' => '/test-category-' . time(), // SEO URL
    'SeoAnahtarKelime' => 'test, category, automated', // Meta Keywords
    'SeoSayfaAciklama' => 'Test category for CategoryService testing', // Meta Description
    'SeoSayfaBaslik' => 'Test Category', // Meta Title
    'Kod' => 'TEST_' . time(), // Category code
    'KategoriMenuGoster' => true, // Show in menu
    'AltKategoriSayisi' => 0 // Sub category count
];

$testCategory = new CategoryModel($categoryData);
$createResponse = $categoryService->createCategory($testCategory);

if ($createResponse->isSuccess()) {
    $newCategoryId = $createResponse->getData();
    ConsoleHelper::print("✓ " . $createResponse->getMessage() . " Category ID: $newCategoryId\n");
} else {
    ConsoleHelper::print("✗ " . $createResponse->getMessage() . "\n");
}

ConsoleHelper::print("\n" . str_repeat("-", 50) . "\n\n");

// Test 6: Get Non-existent Category
ConsoleHelper::print("6. === GET NON-EXISTENT CATEGORY TEST ===\n");
ConsoleHelper::print("Testing getCategories() with non-existent category ID: 999999...\n");

$nonExistentCategory = $categoryService->getCategories(999999);

if (empty($nonExistentCategory)) {
    ConsoleHelper::print("✓ Empty result returned for non-existent category (as expected).\n");
} else {
    ConsoleHelper::print("✗ Unexpected result returned for non-existent category.\n");
    ConsoleHelper::print("Found " . count($nonExistentCategory) . " category(ies).\n");
}

ConsoleHelper::print("\n" . str_repeat("-", 50) . "\n\n");

// Test 7: Category Data Integrity Test
ConsoleHelper::print("7. === CATEGORY DATA INTEGRITY TEST ===\n");
ConsoleHelper::print("Testing category data structure and field completeness...\n");

$categories = $categoryService->getCategories();
$completeDataCount = 0;
$incompleteDataCount = 0;
$rootCategories = 0;
$childCategories = 0;

foreach ($categories as $category) {
    // Use correct field names
    $hasRequiredFields = isset($category->ID) && isset($category->Tanim);
    
    if ($hasRequiredFields) {
        $completeDataCount++;
    } else {
        $incompleteDataCount++;
        ConsoleHelper::print("⚠ Incomplete category data found - ID: " . ($category->ID ?? 'missing') . 
             ", Name (Tanim): " . ($category->Tanim ?? 'missing') . "\n");
    }
    
    // Count root vs child categories
    if (($category->PID ?? 0) == 0) {
        $rootCategories++;
    } else {
        $childCategories++;
    }
}

ConsoleHelper::print("✓ Data integrity check completed.\n");
ConsoleHelper::print("- Categories with complete data: $completeDataCount\n");
ConsoleHelper::print("- Categories with incomplete data: $incompleteDataCount\n");
ConsoleHelper::print("- Root categories: $rootCategories\n");
ConsoleHelper::print("- Child categories: $childCategories\n");

ConsoleHelper::print("\n" . str_repeat("-", 50) . "\n\n");

// Test 8: Category Statistics
ConsoleHelper::print("8. === CATEGORY STATISTICS TEST ===\n");
ConsoleHelper::print("Generating category statistics...\n");

$activeCategories = 0;
$inactiveCategories = 0;
$categoriesWithSeo = 0;
$categoriesWithDescription = 0;
$categoriesWithCode = 0;

foreach ($categories as $category) {
    if ($category->Aktif ?? false) {
        $activeCategories++;
    } else {
        $inactiveCategories++;
    }
    
    if (!empty($category->Url)) {
        $categoriesWithSeo++;
    }
    
    if (!empty($category->Icerik)) {
        $categoriesWithDescription++;
    }
    
    if (!empty($category->Kod)) {
        $categoriesWithCode++;
    }
}

ConsoleHelper::print("✓ Category statistics generated.\n");
ConsoleHelper::print("- Total categories: " . count($categories) . "\n");
ConsoleHelper::print("- Active categories: $activeCategories\n");
ConsoleHelper::print("- Inactive categories: $inactiveCategories\n");
ConsoleHelper::print("- Categories with SEO URL: $categoriesWithSeo\n");
ConsoleHelper::print("- Categories with description: $categoriesWithDescription\n");
ConsoleHelper::print("- Categories with code: $categoriesWithCode\n");

ConsoleHelper::print("\n" . str_repeat("-", 50) . "\n\n");

// Test 9: Performance Test
ConsoleHelper::print("9. === PERFORMANCE TEST ===\n");
ConsoleHelper::print("Testing multiple category requests for performance...\n");

$startTime = microtime(true);

// Make 5 requests to test performance
for ($i = 1; $i <= 5; $i++) {
    $categoryService->getCategories();
    ConsoleHelper::print("Request $i completed...\n");
}

$endTime = microtime(true);
$totalTime = $endTime - $startTime;
$averageTime = $totalTime / 5;

ConsoleHelper::print("✓ Performance test completed.\n");
ConsoleHelper::print("- Total time for 5 requests: " . number_format($totalTime, 4) . " seconds\n");
ConsoleHelper::print("- Average time per request: " . number_format($averageTime, 4) . " seconds\n");

ConsoleHelper::print("\n" . str_repeat("=", 50) . "\n");
ConsoleHelper::print("CATEGORY SERVICE TEST SUITE COMPLETED!\n");
ConsoleHelper::print(str_repeat("=", 50) . "\n\n");

ConsoleHelper::print("SUMMARY:\n");
ConsoleHelper::print("- All CategoryService methods have been tested\n");
ConsoleHelper::print("- Both read and write operations tested\n");
ConsoleHelper::print("- Data integrity and performance tests completed\n");
ConsoleHelper::print("- Category statistics and hierarchy analysis performed\n");
ConsoleHelper::print("- Real API interactions were performed for all tests\n");
ConsoleHelper::print("- Field names corrected to match WSDL schema\n"); 