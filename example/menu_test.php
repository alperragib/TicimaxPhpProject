<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Menu\MenuService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$menuService = new MenuService($request);

echo "=== MENU SERVICE TEST ===\n";
echo "Testing MenuService::getMenus()\n";
echo "API URL: {$config['mainDomain']}\n\n";

try {
    // Test 1: Get all menus
    echo "1. Getting all menus...\n";
    $allMenus = $menuService->getMenus();
    
    if ($allMenus['success']) {
        echo "✅ Success: " . $allMenus['message'] . "\n";
        echo "Found " . count($allMenus['data']) . " menus\n";
        
        foreach ($allMenus['data'] as $index => $menu) {
            echo "Menu #" . ($index + 1) . ":\n";
            echo "  - ID: " . ($menu->ID ?? 'N/A') . "\n";
            echo "  - Name: " . ($menu->MenuAdi ?? 'N/A') . "\n";
            echo "  - Active: " . (($menu->Aktif ?? false) ? 'Yes' : 'No') . "\n";
            echo "  - Language: " . ($menu->Dil ?? 'N/A') . "\n";
            echo "  - Order: " . ($menu->Sira ?? 'N/A') . "\n";
            echo "  ---\n";
        }
    } else {
        echo "❌ Error: " . $allMenus['message'] . "\n";
    }

    echo "\n";

    // Test 2: Get active menus only
    echo "2. Getting active menus only...\n";
    $activeMenus = $menuService->getMenus(['Aktif' => 1]);
    
    if ($activeMenus['success']) {
        echo "✅ Success: Found " . count($activeMenus['data']) . " active menus\n";
    } else {
        echo "❌ Error: " . $activeMenus['message'] . "\n";
    }

    echo "\n";

    // Test 3: Get menus by language
    echo "3. Getting menus by language (TR)...\n";
    $trMenus = $menuService->getMenus(['Dil' => 'TR']);
    
    if ($trMenus['success']) {
        echo "✅ Success: Found " . count($trMenus['data']) . " Turkish menus\n";
    } else {
        echo "❌ Error: " . $trMenus['message'] . "\n";
    }

    echo "\n";

    // Test 4: Get specific menu by ID (if we have any)
    if (!empty($allMenus['data'])) {
        $firstMenu = $allMenus['data'][0];
        $menuId = $firstMenu->ID ?? 1;
        
        echo "4. Getting specific menu (ID: $menuId)...\n";
        $specificMenu = $menuService->getMenus(['MenuID' => $menuId]);
        
        if ($specificMenu['success']) {
            echo "✅ Success: Found " . count($specificMenu['data']) . " menu(s)\n";
            if (!empty($specificMenu['data'])) {
                $menu = $specificMenu['data'][0];
                echo "Menu Details:\n";
                print_r($menu->toArray());
            }
        } else {
            echo "❌ Error: " . $specificMenu['message'] . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== MENU TEST COMPLETED ===\n"; 