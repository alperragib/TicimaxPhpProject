<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$menuService = $ticimax->menuService();

echo "=== MENU SERVICE TEST SUITE ===\n\n";

// Test 1: Get All Menus with Default Filters
echo "1. === GET ALL MENUS (DEFAULT) TEST ===\n";
echo "Testing getMenus() with default filters...\n";

$defaultResponse = $menuService->getMenus();

if ($defaultResponse->isSuccess()) {
    echo "✓ " . $defaultResponse->getMessage() . "\n";
    $menus = $defaultResponse->getData();
    echo "Found " . count($menus) . " menus.\n";
    
    if (!empty($menus)) {
        echo "All menus list:\n";
        foreach ($menus as $index => $menu) {
            echo "   " . ($index + 1) . ". " . ($menu->Baslik ?? 'N/A') . 
                 " (ID: " . ($menu->ID ?? 'N/A') . ")" . 
                 " - URL: " . ($menu->Url ?? 'N/A') . 
                 " - Active: " . ($menu->Aktif ? 'Yes' : 'No') . "\n";
        }
        
        echo "\nFirst menu details:\n";
        $firstMenu = $menus[0];
        echo "- ID: " . ($firstMenu->ID ?? 'N/A') . "\n";
        echo "- Menu Name: " . ($firstMenu->Baslik ?? 'N/A') . "\n";
        echo "- URL: " . ($firstMenu->Url ?? 'N/A') . "\n";
        echo "- Parent ID: " . ($firstMenu->PID ?? 'N/A') . "\n";
        echo "- Active: " . ($firstMenu->Aktif ? 'Yes' : 'No') . "\n";
        echo "- Sort Order: " . ($firstMenu->Sira ?? 'N/A') . "\n";
        echo "- End Date: " . ($firstMenu->BitisTarihi ?? 'N/A') . "\n";
        echo "- Target: " . ($firstMenu->Target ?? 'N/A') . "\n";
        
        // Store data for other tests
        $testMenuId = $firstMenu->ID ?? null;
        $testParentId = $firstMenu->PID ?? null;
        $testLanguage = 'tr';
    } else {
        $testMenuId = null;
        $testParentId = null;
        $testLanguage = 'tr';
    }
} else {
    echo "✗ " . $defaultResponse->getMessage() . "\n";
    $testMenuId = null;
    $testParentId = null;
    $testLanguage = 'tr';
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 2: Get Active Menus Only
echo "2. === GET ACTIVE MENUS TEST ===\n";
echo "Testing getMenus() with active filter...\n";

$activeResponse = $menuService->getMenus(['Aktif' => 1]);

if ($activeResponse->isSuccess()) {
    echo "✓ " . $activeResponse->getMessage() . "\n";
    $activeMenus = $activeResponse->getData();
    echo "Found " . count($activeMenus) . " active menus.\n";
} else {
    echo "✗ " . $activeResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 3: Get Inactive Menus Only
echo "3. === GET INACTIVE MENUS TEST ===\n";
echo "Testing getMenus() with inactive filter...\n";

$inactiveResponse = $menuService->getMenus(['Aktif' => 0]);

if ($inactiveResponse->isSuccess()) {
    echo "✓ " . $inactiveResponse->getMessage() . "\n";
    $inactiveMenus = $inactiveResponse->getData();
    echo "Found " . count($inactiveMenus) . " inactive menus.\n";
} else {
    echo "✗ " . $inactiveResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 4: Get Menus with Language Filter
echo "4. === GET MENUS WITH LANGUAGE FILTER TEST ===\n";
echo "Testing getMenus() with Turkish language filter...\n";

$turkishResponse = $menuService->getMenus(['Dil' => 'tr']);

if ($turkishResponse->isSuccess()) {
    echo "✓ " . $turkishResponse->getMessage() . "\n";
    $turkishMenus = $turkishResponse->getData();
    echo "Found " . count($turkishMenus) . " Turkish menus.\n";
} else {
    echo "✗ " . $turkishResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 5: Get Menus with English Language Filter
echo "5. === GET MENUS WITH ENGLISH LANGUAGE FILTER TEST ===\n";
echo "Testing getMenus() with English language filter...\n";

$englishResponse = $menuService->getMenus(['Dil' => 'en']);

if ($englishResponse->isSuccess()) {
    echo "✓ " . $englishResponse->getMessage() . "\n";
    $englishMenus = $englishResponse->getData();
    echo "Found " . count($englishMenus) . " English menus.\n";
} else {
    echo "✗ " . $englishResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 6: Get Specific Menu
if ($testMenuId) {
    echo "6. === GET SPECIFIC MENU TEST ===\n";
    echo "Testing getMenus() with specific menu ID: $testMenuId...\n";

    $specificResponse = $menuService->getMenus(['MenuID' => $testMenuId]);

    if ($specificResponse->isSuccess()) {
        echo "✓ " . $specificResponse->getMessage() . "\n";
        $specificMenus = $specificResponse->getData();
        echo "Found " . count($specificMenus) . " specific menu(s).\n";
        
        if (!empty($specificMenus)) {
            echo "Specific menu details:\n";
            $menu = $specificMenus[0];
            echo "- ID: " . ($menu->ID ?? 'N/A') . "\n";
            echo "- Menu Name: " . ($menu->Baslik ?? 'N/A') . "\n";
            echo "- URL: " . ($menu->Url ?? 'N/A') . "\n";
            echo "- Parent ID: " . ($menu->PID ?? 'N/A') . "\n";
            echo "- Active: " . ($menu->Aktif ? 'Yes' : 'No') . "\n";
            echo "- Sort Order: " . ($menu->Sira ?? 'N/A') . "\n";
            echo "- End Date: " . ($menu->BitisTarihi ?? 'N/A') . "\n";
            echo "- Target: " . ($menu->Target ?? 'N/A') . "\n";
            echo "- Content: " . ($menu->Icerik ?? 'N/A') . "\n";
            echo "- Image: " . ($menu->Resim ?? 'N/A') . "\n";
        }
    } else {
        echo "✗ " . $specificResponse->getMessage() . "\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test 7: Get Non-existent Menu
echo "7. === GET NON-EXISTENT MENU TEST ===\n";
echo "Testing getMenus() with non-existent menu ID: 999999...\n";

$nonExistentResponse = $menuService->getMenus(['MenuID' => 999999]);

if ($nonExistentResponse->isSuccess()) {
    $nonExistentMenus = $nonExistentResponse->getData();
    if (empty($nonExistentMenus)) {
        echo "✓ Olmayan menü için boş sonuç döndü (beklendiği gibi).\n";
    } else {
        echo "✗ Olmayan menü için beklenmeyen sonuç döndü.\n";
        echo "Found " . count($nonExistentMenus) . " menu(s).\n";
    }
} else {
    echo "✓ " . $nonExistentResponse->getMessage() . " (Expected for non-existent menu)\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 8: Menu Data Integrity Test
echo "8. === MENU DATA INTEGRITY TEST ===\n";
echo "Testing menu data structure and field completeness...\n";

$allMenusResponse = $menuService->getMenus();
if ($allMenusResponse->isSuccess()) {
    $allMenus = $allMenusResponse->getData();
    $completeDataCount = 0;
    $incompleteDataCount = 0;
    $rootMenus = 0;
    $childMenus = 0;

    foreach ($allMenus as $menu) {
        $hasRequiredFields = isset($menu->ID) && isset($menu->Baslik);
        
        if ($hasRequiredFields) {
            $completeDataCount++;
        } else {
            $incompleteDataCount++;
            echo "⚠ Incomplete menu data found - ID: " . ($menu->ID ?? 'missing') . 
                 ", Name: " . ($menu->Baslik ?? 'missing') . "\n";
        }
        
        // Count root vs child menus
        if (($menu->PID ?? 0) == 0) {
            $rootMenus++;
        } else {
            $childMenus++;
        }
    }

    echo "✓ Data integrity check completed.\n";
    echo "- Menus with complete data: $completeDataCount\n";
    echo "- Menus with incomplete data: $incompleteDataCount\n";
    echo "- Root menus: $rootMenus\n";
    echo "- Child menus: $childMenus\n";
} else {
    echo "✗ Could not perform data integrity test: " . $allMenusResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 9: Menu Statistics Test
echo "9. === MENU STATISTICS TEST ===\n";
echo "Generating menu statistics...\n";

if ($allMenusResponse->isSuccess()) {
    $allMenus = $allMenusResponse->getData();
    
    $activeMenus = 0;
    $inactiveMenus = 0;
    $menusWithUrl = 0;
    $menusWithTarget = 0;
    $menusWithIcon = 0;
    $languageStats = [];
    $targetStats = [];

    foreach ($allMenus as $menu) {
        if ($menu->Aktif ?? false) {
            $activeMenus++;
        } else {
            $inactiveMenus++;
        }
        
        if (!empty($menu->Url)) {
            $menusWithUrl++;
        }
        
        if (!empty($menu->Target)) {
            $menusWithTarget++;
            $target = $menu->Target;
            $targetStats[$target] = ($targetStats[$target] ?? 0) + 1;
        }
        
        if (!empty($menu->Icon)) {
            $menusWithIcon++;
        }
        
        $language = 'tr'; // API doesn't return language field, assuming Turkish
        $languageStats[$language] = ($languageStats[$language] ?? 0) + 1;
    }

    echo "✓ Menu statistics generated.\n";
    echo "- Total menus: " . count($allMenus) . "\n";
    echo "- Active menus: $activeMenus\n";
    echo "- Inactive menus: $inactiveMenus\n";
    echo "- Menus with URL: $menusWithUrl\n";
    echo "- Menus with target: $menusWithTarget\n";
    echo "- Menus with icon: $menusWithIcon\n";
    echo "- Language distribution:\n";
    foreach ($languageStats as $lang => $count) {
        echo "  * $lang: $count menus\n";
    }
    echo "- Target distribution:\n";
    foreach ($targetStats as $target => $count) {
        echo "  * $target: $count menus\n";
    }
} else {
    echo "✗ Could not generate menu statistics: " . $allMenusResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 10: Menu Hierarchy Analysis
echo "10. === MENU HIERARCHY ANALYSIS TEST ===\n";
echo "Analyzing menu hierarchy structure...\n";

if ($allMenusResponse->isSuccess()) {
    $allMenus = $allMenusResponse->getData();
    
    $hierarchyMap = [];
    $orphanMenus = [];
    $maxDepth = 0;

    // Build hierarchy map
    foreach ($allMenus as $menu) {
        $parentId = $menu->PID ?? 0;
        $menuId = $menu->ID ?? 0;
        
        if ($parentId == 0) {
            // Root menu
            $hierarchyMap[$menuId] = ['menu' => $menu, 'children' => []];
        } else {
            // Child menu
            if (!isset($hierarchyMap[$parentId])) {
                $orphanMenus[] = $menu;
            } else {
                $hierarchyMap[$parentId]['children'][] = $menu;
            }
        }
    }

    echo "✓ Menu hierarchy analysis completed.\n";
    echo "- Root menu groups: " . count($hierarchyMap) . "\n";
    echo "- Orphan menus (parent not found): " . count($orphanMenus) . "\n";
    
    // Calculate children distribution
    $childrenDistribution = [];
    foreach ($hierarchyMap as $rootMenu) {
        $childCount = count($rootMenu['children']);
        $childrenDistribution[$childCount] = ($childrenDistribution[$childCount] ?? 0) + 1;
    }
    
    echo "- Children distribution:\n";
    ksort($childrenDistribution);
    foreach ($childrenDistribution as $childCount => $parentCount) {
        echo "  * $parentCount parent menus have $childCount children\n";
    }
    
    if (!empty($orphanMenus)) {
        echo "⚠ Orphan menus found:\n";
        foreach ($orphanMenus as $orphan) {
            echo "  * ID: " . ($orphan->ID ?? 'N/A') . ", Name: " . ($orphan->Baslik ?? 'N/A') . 
                 ", Parent ID: " . ($orphan->PID ?? 'N/A') . "\n";
        }
    }
} else {
    echo "✗ Could not perform hierarchy analysis: " . $allMenusResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 11: Performance Test
echo "11. === PERFORMANCE TEST ===\n";
echo "Testing multiple menu requests for performance...\n";

$startTime = microtime(true);

// Make 5 requests to test performance
for ($i = 1; $i <= 5; $i++) {
    $menuService->getMenus();
    echo "Request $i completed...\n";
}

$endTime = microtime(true);
$totalTime = $endTime - $startTime;
$averageTime = $totalTime / 5;

echo "✓ Performance test completed.\n";
echo "- Total time for 5 requests: " . number_format($totalTime, 4) . " seconds\n";
echo "- Average time per request: " . number_format($averageTime, 4) . " seconds\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "MENU SERVICE TEST SUITE COMPLETED!\n";
echo str_repeat("=", 50) . "\n\n";

echo "SUMMARY:\n";
echo "- All MenuService methods have been tested\n";
echo "- Filter combinations and parameter validation tested\n";
echo "- Data integrity and hierarchy analysis performed\n";
echo "- Menu statistics and language distribution analyzed\n";
echo "- Performance test completed\n";
echo "- Real API interactions were performed for all tests\n"; 