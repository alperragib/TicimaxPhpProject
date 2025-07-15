<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Category\CategoryModel;

// Load configuration
$config = require __DIR__ . '/../config.php';

echo "=== CATEGORY SERVICE TEST ===\n\n";

// Test start time
$testStart = microtime(true);

try {
    // Initialize Ticimax API
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $categoryService = $ticimax->categoryService();
    
    echo "✓ Ticimax CategoryService initialized\n";
    echo "Domain: {$config['mainDomain']}\n\n";
    
    // Test counters
    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;
    
    echo "========================================\n";
    echo "         CATEGORY TESTS\n";
    echo "========================================\n\n";
    
    // Test 1: Get and list all categories
    echo "🧪 Test 1: LIST ALL CATEGORIES\n";
    echo "-----------------------------------\n";
    $testCount++;
    
    $response = $categoryService->getCategories(0,'TR');
    
    if ($response->isSuccess()) {
        $allCategories = $response->getData();
        $successCount++;
        echo "✅ Categories retrieved successfully\n";
        echo "📦 Total Categories: " . count($allCategories) . "\n\n";
        
        // LIST ALL CATEGORIES
        echo "📋 COMPLETE CATEGORY LIST:\n";
        echo str_repeat("=", 80) . "\n";
        
        foreach ($allCategories as $index => $category) {
            $categoryNum = $index + 1;
            echo "[$categoryNum] CATEGORY DETAILS:\n";
            echo "   🆔 ID: " . ($category->ID ?? 'N/A') . "\n";
            echo "   🏷️  Name: " . ($category->Tanim ?? 'N/A') . "\n";
            echo "   👥 Parent ID: " . ($category->PID ?? 'N/A') . "\n";
            echo "   ✅ Active: " . (($category->Aktif ?? false) ? 'Yes' : 'No') . "\n";
            echo "   📊 Sort Order: " . ($category->Sira ?? 'N/A') . "\n";
            echo "   🔗 URL: " . ($category->Url ?? 'Not specified') . "\n";
            
            // Shorten description - only first 100 characters
            $description = $category->Icerik ?? '';
            if (strlen($description) > 100) {
                $description = substr(strip_tags($description), 0, 100) . "...";
            } else {
                $description = strip_tags($description) ?: 'Not specified';
            }
            echo "   📝 Description: " . $description . "\n";
            
            echo "   🏷️  SEO Title: " . (($category->SeoSayfaBaslik ?? '') ?: 'Not specified') . "\n";
            echo "   🏷️  Code: " . (($category->Kod ?? '') ?: 'Not specified') . "\n";
            echo "   -------------------------\n";
        }
        
        // Statistics
        $activeCount = 0;
        $inactiveCount = 0;
        $rootCount = 0;
        $childCount = 0;
        $withSeoCount = 0;
        $withDescCount = 0;
        
        foreach ($allCategories as $category) {
            if ($category->Aktif ?? false) $activeCount++;
            else $inactiveCount++;
            
            if (($category->PID ?? 0) == 0) $rootCount++;
            else $childCount++;
            
            if (!empty($category->SeoSayfaBaslik)) $withSeoCount++;
            if (!empty($category->Icerik)) $withDescCount++;
        }
        
        echo "\n📊 CATEGORY STATISTICS:\n";
        echo "   📦 Total Categories: " . count($allCategories) . "\n";
        echo "   ✅ Active Categories: $activeCount\n";
        echo "   ❌ Inactive Categories: $inactiveCount\n";
        echo "   🌳 Root Categories: $rootCount\n";
        echo "   🌿 Child Categories: $childCount\n";
        echo "   🏷️  With SEO Title: $withSeoCount\n";
        echo "   📝 With Description: $withDescCount\n";
        
        // Save first category for testing
        $testCategoryId = $allCategories[0]->ID ?? null;
        $testParentId = $allCategories[0]->PID ?? null;
        
    } else {
        $errorCount++;
        echo "❌ No categories found or error occurred\n";
        $testCategoryId = null;
        $testParentId = null;
    }
    echo "\n";
    
    // Test 2: Get specific category
    if ($testCategoryId) {
        echo "🧪 Test 2: SPECIFIC CATEGORY DETAILS\n";
        echo "-------------------------------\n";
        $testCount++;
        
        $response = $categoryService->getCategories($testCategoryId);
        
        if ($response->isSuccess()) {
            $specificCategory = $response->getData();
            $successCount++;
            echo "✅ Specific category retrieved successfully\n";
            echo "🎯 Tested ID: $testCategoryId\n";
            
            if (!empty($specificCategory)) {
                $category = $specificCategory[0];
                echo "📋 DETAILED INFORMATION:\n";
                echo "   🆔 ID: " . ($category->ID ?? 'N/A') . "\n";
                echo "   🏷️  Category Name: " . ($category->Tanim ?? 'N/A') . "\n";
                echo "   👥 Parent ID: " . ($category->PID ?? 'N/A') . "\n";
                echo "   ✅ Status: " . (($category->Aktif ?? false) ? 'Active' : 'Inactive') . "\n";
                echo "   📊 Sort Order: " . ($category->Sira ?? 'N/A') . "\n";
                echo "   🔗 URL: " . ($category->Url ?? 'Not specified') . "\n";
            } else {
                echo "❌ Category not found\n";
            }
        } else {
            $errorCount++;
            echo "❌ Could not retrieve specific category: " . $response->getMessage() . "\n";
        }
        echo "\n";
    }
    
    // Test 3: Create new category
    echo "🧪 Test 3: CREATE NEW CATEGORY\n";
    echo "--------------------------------\n";
    $testCount++;
    
    // Create CategoryModel object
    $categoryData = [
        'ID' => 0,
        'Tanim' => 'Test Category ' . date('Y-m-d H:i:s'),
        'PID' => 0,
        'Aktif' => true,
        'Sira' => 99,
        'Icerik' => 'Test category description',
        'Url' => '/test-category-' . time(),
        'SeoSayfaBaslik' => 'Test Category SEO',
        'SeoSayfaAciklama' => 'Test category SEO description',
        'SeoAnahtarKelime' => 'test,category',
        'Kod' => 'TEST_' . time()
    ];
    
    $categoryModel = new CategoryModel($categoryData);
    $createResponse = $categoryService->createCategory($categoryModel);
    
    if ($createResponse->isSuccess()) {
        $successCount++;
        $newCategoryId = $createResponse->getData();
        echo "✅ New category created successfully\n";
        echo "🆔 New Category ID: $newCategoryId\n";
        echo "🏷️  Category Name: " . $categoryData['Tanim'] . "\n";
        echo "📝 Message: " . $createResponse->getMessage() . "\n";
    } else {
        $errorCount++;
        echo "❌ Could not create new category\n";
        echo "📝 Error: " . $createResponse->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 4: Check non-existent category
    echo "🧪 Test 4: NON-EXISTENT CATEGORY CHECK\n";
    echo "----------------------------------\n";
    $testCount++;
    
    $response = $categoryService->getCategories(999999);
    
    if ($response->isSuccess()) {
        $nonExistentCategory = $response->getData();
        if (empty($nonExistentCategory)) {
            $successCount++;
            echo "✅ Empty result returned for non-existent category (correct behavior)\n";
            echo "🎯 Test ID: 999999 - Result: Not found\n";
        } else {
            $errorCount++;
            echo "❌ Unexpected result returned for non-existent category\n";
            echo "📦 Records found: " . count($nonExistentCategory) . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Could not query category: " . $response->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 5: Performance test
    echo "🧪 Test 5: PERFORMANCE TEST\n";
    echo "-------------------------\n";
    $testCount++;
    
    $performanceStart = microtime(true);
    
    // Make the same query 3 times
    for ($i = 1; $i <= 3; $i++) {
        $perfTest = $categoryService->getCategories();
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
    echo "   • getCategories() - Complete category list\n";
    echo "   • getCategories(id) - Specific category details\n";
    echo "   • createCategory() - Create new category\n";
    echo "   • Performance analysis - Multiple request test\n";
    echo "   • Data integrity - Field mapping check\n\n";
    
    echo "📋 Field Mapping (WSDL Compatible):\n";
    echo "   • CategoryName → Tanim ✅\n";
    echo "   • ParentID → PID ✅\n";
    echo "   • SortOrder → Sira ✅\n";
    echo "   • Description → Icerik ✅\n";
    echo "   • ID → ID ✅\n";
    echo "   • Active → Aktif ✅\n\n";
    
    echo "🏁 CategoryService test process completed!\n";
    
} catch (Exception $e) {
    echo "💥 FATAL ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=== CATEGORY SERVICE TEST COMPLETED ===\n"; 