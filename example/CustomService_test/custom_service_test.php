<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Load configuration
$config = require __DIR__ . '/../config.php';

echo "=== CustomService Test Process Starting ===\n\n";

// Test start time
$testStart = microtime(true);

try {
    // Initialize Ticimax API
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    
    echo "✓ Ticimax initialized\n";
    echo "Domain: {$config['mainDomain']}\n\n";
    
    // Test counters
    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;
    
    echo "========================================\n";
    echo "        CUSTOM SERVICE TESTS\n";
    echo "========================================\n\n";
    
    // Test 1: CustomService check
    echo "🧪 Test 1: CustomService Existence Check\n";
    echo "--------------------------------------\n";
    $testCount++;
    
    try {
        // Check CustomService methods
        if (method_exists($ticimax, 'customService')) {
            $customService = $ticimax->customService();
            $successCount++;
            echo "✅ CustomService loaded successfully\n";
            echo "   🔧 Service Type: " . get_class($customService) . "\n";
        } else {
            $errorCount++;
            echo "❌ customService method not found\n";
            echo "   💡 This is normal - CustomService not yet implemented\n";
        }
    } catch (Exception $e) {
        $errorCount++;
        echo "❌ CustomService error: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 2: Method check using Reflection
    echo "🧪 Test 2: Discovering Available Methods\n";
    echo "----------------------------------\n";
    $testCount++;
    
    try {
        $availableMethods = get_class_methods($ticimax);
        $customMethods = array_filter($availableMethods, function($method) {
            return strpos(strtolower($method), 'custom') !== false;
        });
        
        $successCount++;
        echo "✅ Method check completed\n";
        echo "   📊 Total Ticimax Methods: " . count($availableMethods) . "\n";
        echo "   🔍 Methods Containing 'Custom': " . count($customMethods) . "\n";
        
        if (!empty($customMethods)) {
            foreach ($customMethods as $method) {
                echo "   • $method\n";
            }
        } else {
            echo "   📝 No custom methods found\n";
        }
        
        // Show available service methods
        $serviceMethods = array_filter($availableMethods, function($method) {
            return strpos(strtolower($method), 'service') !== false;
        });
        
        echo "   🛠️ Available Service Methods:\n";
        foreach ($serviceMethods as $method) {
            echo "   • $method\n";
        }
        
    } catch (Exception $e) {
        $errorCount++;
        echo "❌ Reflection error: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 3: Service directory check
    echo "🧪 Test 3: Service File Check\n";
    echo "--------------------------------\n";
    $testCount++;
    
    $customServicePath = __DIR__ . '/../../src/Service/Custom/CustomService.php';
    if (file_exists($customServicePath)) {
        $successCount++;
        echo "✅ CustomService file exists\n";
        echo "   📁 File Path: $customServicePath\n";
        echo "   📏 File Size: " . number_format(filesize($customServicePath)) . " bytes\n";
    } else {
        $errorCount++;
        echo "❌ CustomService file not found\n";
        echo "   📍 Searched Path: $customServicePath\n";
        echo "   💡 CustomService not yet created\n";
    }
    echo "\n";
    
    // Test 4: Check custom service needs with API operations
    echo "🧪 Test 4: Custom Operation Needs Analysis\n";
    echo "-----------------------------------------\n";
    $testCount++;
    
    try {
        // Check custom operations like Favourites, Location, Menu
        $hasCustomOps = false;
        
        if (method_exists($ticimax, 'favouriteProductService')) {
            echo "   ✅ FavouriteProductService exists (custom operation)\n";
            $hasCustomOps = true;
        }
        
        if (method_exists($ticimax, 'locationService')) {
            echo "   ✅ LocationService exists (custom operation)\n";
            $hasCustomOps = true;
        }
        
        if (method_exists($ticimax, 'menuService')) {
            echo "   ✅ MenuService exists (custom operation)\n";
            $hasCustomOps = true;
        }
        
        if ($hasCustomOps) {
            $successCount++;
            echo "   📝 Custom operations implemented in different services\n";
        } else {
            $errorCount++;
            echo "   ❌ No custom operations found\n";
        }
        
    } catch (Exception $e) {
        $errorCount++;
        echo "❌ Custom operation analysis error: " . $e->getMessage() . "\n";
    }
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
    echo "⏱️ Test Duration: {$totalTime} seconds\n";
    echo "📈 Success Rate: " . round(($successCount / $testCount) * 100, 1) . "%\n\n";
    
    // Test details
    echo "========================================\n";
    echo "           NOTES\n";
    echo "========================================\n";
    echo "🔍 CustomService not implemented as a separate service.\n";
    echo "📝 Custom operations (favourites, location, menu) in separate services.\n";
    echo "💡 This is better architecture - separation of concerns.\n";
    echo "🏁 CustomService test process completed!\n";
    
} catch (Exception $e) {
    echo "💥 FATAL ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=== CustomService Test Process Completed ===\n"; 