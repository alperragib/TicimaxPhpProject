<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Response\ApiResponse;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== CartService Test Process Starting ===\n\n";

// Test start time
$testStart = microtime(true);

try {
    // Initialize Ticimax API
    $ticimax = new Ticimax($mainDomain, $apiKey);
    $cartService = $ticimax->cartService();
    
    echo "✓ Ticimax CartService initialized\n\n";
    
    // Test parameters
    $testUserId = 1; // Test user ID
    $testCampaignId = 0;
    
    // Test counters
    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;
    
    echo "========================================\n";
    echo "           CART TESTS\n";
    echo "========================================\n\n";
    
    // Test 1: Create new cart
    echo "🧪 Test 1: Create New Cart\n";
    echo "------------------------------\n";
    $testCount++;
    
    $createResponse = $cartService->createSepet($testUserId);
    if ($createResponse instanceof ApiResponse) {
        if ($createResponse->isSuccess()) {
            $successCount++;
            $newCart = $createResponse->getData();
            echo "✅ Cart created successfully\n";
            echo "   📋 Cart ID: " . ($newCart['SepetID'] ?? 'N/A') . "\n";
            echo "   💰 Total Amount: " . ($newCart['GenelToplam'] ?? '0') . " TL\n";
            echo "   📦 Product Count: " . ($newCart['ToplamUrunAdedi'] ?? '0') . "\n";
            
            $createdCartId = $newCart['SepetID'] ?? null;
        } else {
            $errorCount++;
            echo "❌ Could not create cart: " . $createResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Invalid response format\n";
    }
    echo "\n";
    
    // Test 2: Get cart (by User ID)
    echo "🧪 Test 2: Get User's Cart\n";
    echo "--------------------------------\n";
    $testCount++;
    
    $getResponse = $cartService->getSepet($testUserId);
    if ($getResponse instanceof ApiResponse) {
        if ($getResponse->isSuccess()) {
            $successCount++;
            $cart = $getResponse->getData();
            echo "✅ User's cart retrieved successfully\n";
            echo "   📋 Cart ID: " . ($cart->ID ?? 'N/A') . "\n";
            echo "   💰 Total Amount: " . ($cart->GenelToplam ?? '0') . " TL\n";
            echo "   📦 Product Count: " . ($cart->ToplamUrunAdedi ?? '0') . "\n";
            echo "   💳 Currency: " . ($cart->SepetParaBirimiDilKodu ?? 'N/A') . "\n";
            
            // List products
            if (!empty($cart->Urunler)) {
                echo "   🛍️ Products in Cart:\n";
                foreach ($cart->Urunler as $index => $product) {
                    echo "      " . ($index + 1) . ". Product ID: " . ($product['UrunID'] ?? 'N/A') . 
                         " - Quantity: " . ($product['Adet'] ?? 'N/A') . "\n";
                }
            } else {
                echo "   📭 Cart is empty\n";
            }
        } else {
            $errorCount++;
            echo "❌ Could not retrieve cart: " . $getResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Invalid response format\n";
    }
    echo "\n";
    
    // Test 3: Get specific cart (by Cart ID)
    if (isset($createdCartId) && $createdCartId) {
        echo "🧪 Test 3: Get Cart by Specific ID\n";
        echo "-----------------------------------\n";
        $testCount++;
        
        $getSpecificResponse = $cartService->getSepet($testUserId, $createdCartId);
        if ($getSpecificResponse instanceof ApiResponse) {
            if ($getSpecificResponse->isSuccess()) {
                $successCount++;
                $specificCart = $getSpecificResponse->getData();
                echo "✅ Specific cart retrieved successfully\n";
                echo "   📋 Cart ID: " . ($specificCart->ID ?? 'N/A') . "\n";
                echo "   💰 Total Amount: " . ($specificCart->GenelToplam ?? '0') . " TL\n";
            } else {
                $errorCount++;
                echo "❌ Could not retrieve specific cart: " . $getSpecificResponse->getMessage() . "\n";
            }
        } else {
            $errorCount++;
            echo "❌ Invalid response format\n";
        }
        echo "\n";
    }
    
    // Test 4: Get Cart List
    echo "🧪 Test 4: Get Cart List\n";
    echo "------------------------------\n";
    $testCount++;
    
    $selectResponse = $cartService->selectSepet(null, $testUserId);
    if ($selectResponse instanceof ApiResponse) {
        if ($selectResponse->isSuccess()) {
            $successCount++;
            $cartData = $selectResponse->getData();
            $cartList = $cartData['carts'] ?? []; // API returns array with [carts] key
            echo "✅ Cart list retrieved successfully\n";
            echo "   📊 Total Cart Count: " . count($cartList) . "\n";
            
            // Show first few carts
            $displayCount = min(3, count($cartList));
            for ($i = 0; $i < $displayCount; $i++) {
                $cart = $cartList[$i];
                // WebCartModel object property access
                echo "   " . ($i + 1) . ". Cart ID: " . ($cart->ID ?? 'N/A') . 
                     " - Guid: " . ($cart->GuidSepetID ?? 'N/A') . 
                     " - Date: " . ($cart->SepetTarihi ?? 'N/A') . "\n";
            }
        } else {
            $errorCount++;
            echo "❌ Could not retrieve cart list: " . $selectResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Cart list API call failed\n";
    }
    echo "\n";
    
    // Test 5: Get Web Cart
    echo "🧪 Test 5: Get Web Cart\n";
    echo "--------------------------\n";
    $testCount++;
    
    $webSelectResponse = $cartService->selectWebSepet(null, null, null, $testUserId);
    if ($webSelectResponse instanceof ApiResponse) {
        if ($webSelectResponse->isSuccess()) {
            $successCount++;
            $webCartData = $webSelectResponse->getData();
            echo "✅ Web cart list retrieved successfully\n";
            
            // stdClass object, single cart
            if (isset($webCartData->WebSepet)) {
                $webCart = $webCartData->WebSepet;
                echo "   📋 Web Cart ID: " . ($webCart->ID ?? 'N/A') . "\n";
                echo "   🆔 Guid: " . ($webCart->GuidSepetID ?? 'N/A') . "\n";
                echo "   📅 Date: " . ($webCart->SepetTarihi ?? 'N/A') . "\n";
                
                // Check if products exist
                if (isset($webCart->Urunler)) {
                    if (is_object($webCart->Urunler) && isset($webCart->Urunler->WebSepetUrun)) {
                        echo "   📦 Has Products: Yes\n";
                    } else {
                        echo "   📦 Has Products: No\n";
                    }
                }
            } else {
                echo "   📭 Web cart is empty\n";
            }
        } else {
            $errorCount++;
            echo "❌ Could not retrieve web cart: " . $webSelectResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Web cart API call failed\n";
    }
    echo "\n";
    
    // Test 6: Get web cart with currency and language filters
    echo "🧪 Test 6: Get Filtered Web Cart\n";
    echo "-----------------------------------\n";
    $testCount++;
    
    $filteredWebResponse = $cartService->selectWebSepet('tr', 'TL');
    if ($filteredWebResponse instanceof ApiResponse) {
        if ($filteredWebResponse->isSuccess()) {
            $successCount++;
            $filteredWebCarts = $filteredWebResponse->getData();
            echo "✅ Filtered web cart list retrieved successfully\n";
            
            // Check stdClass object
            if (isset($filteredWebCarts->WebSepet)) {
                echo "   📊 TR-TL Cart: 1 found\n";
                echo "   🆔 Cart ID: " . ($filteredWebCarts->WebSepet->ID ?? 'N/A') . "\n";
            } else {
                echo "   📊 TR-TL Cart: 0 found\n";
            }
            echo "   🌐 Language: TR, Currency: TL\n";
        } else {
            $errorCount++;
            echo "❌ Could not retrieve filtered web cart: " . $filteredWebResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Invalid response format\n";
    }
    echo "\n";
    
    // Test 7: Get cart with invalid user ID
    echo "🧪 Test 7: Invalid User ID Test\n";
    echo "---------------------------------\n";
    $testCount++;
    
    $invalidUserResponse = $cartService->getSepet(999999);
    if ($invalidUserResponse instanceof ApiResponse) {
        if (!$invalidUserResponse->isSuccess()) {
            $successCount++;
            echo "✅ Invalid user ID correctly rejected\n";
            echo "   📝 Error message: " . $invalidUserResponse->getMessage() . "\n";
        } else {
            $errorCount++;
            echo "❌ Invalid user ID accepted (unexpected)\n";
        }
    } else {
        $errorCount++;
        echo "❌ Invalid response format\n";
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
    
    echo "🏁 CartService test process completed!\n";
    
} catch (Exception $e) {
    echo "💥 FATAL ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=== CartService Test Process Completed ===\n"; 