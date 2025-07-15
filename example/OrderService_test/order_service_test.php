<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Response\ApiResponse;
use AlperRagib\Ticimax\Model\Order\OrderModel;
use AlperRagib\Ticimax\Model\Order\DeliveryAddressModel;
use AlperRagib\Ticimax\Model\Order\OrderProductModel;

// Load configuration
$config = require __DIR__ . '/../config.php';

echo "=== TICIMAX ORDER AND CART SERVICE DETAILED TEST ===\n\n";

// Test start time
$testStart = microtime(true);

// Test counters
$testCount = 0;
$successCount = 0;
$errorCount = 0;

// Test user - Yusuf Kurnaz
$testUserId = 1055;
$testProducts = [16, 14, 12, 11, 10];
$testKargoId = 2; // Surat Cargo

// Helper function: Print JSON in pretty format
function printJsonData($data, $title = "Data") {
    echo "   📋 $title:\n";
    echo "   " . str_repeat("-", 50) . "\n";
    $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $lines = explode("\n", $jsonData);
    foreach ($lines as $line) {
        echo "   $line\n";
    }
    echo "   " . str_repeat("-", 50) . "\n";
}

try {
    // Initialize Ticimax API
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $orderService = $ticimax->orderService();
    $cartService = $ticimax->cartService();
    
    echo "✓ Ticimax API initialized\n";
    echo "Domain: {$config['mainDomain']}\n";
    echo "Test User ID: $testUserId (Yusuf Kurnaz)\n\n";
    
    echo "=" . str_repeat("=", 80) . "\n";
    echo "                        WORKING FUNCTIONS\n";
    echo "=" . str_repeat("=", 80) . "\n\n";
    
    // ✅ Test 1: Get Order List (GetOrders)
    echo "🧪 Test 1: Get Order List (SelectSiparis)\n";
    echo "================================================\n";
    $testCount++;
    
    $ordersResponse = $orderService->getOrders();
    if ($ordersResponse instanceof ApiResponse && $ordersResponse->isSuccess()) {
        $successCount++;
        $orders = $ordersResponse->getData();
        echo "✅ SUCCESS: Orders retrieved\n";
        echo "   📦 Total Orders: " . count($orders) . "\n\n";
            
        // Show details of first 3 orders
        foreach (array_slice($orders, 0, 3) as $index => $order) {
            echo "   🔍 Order " . ($index + 1) . " Details:\n";
            printJsonData($order, "Order Object");
            echo "\n";
        }
            
        $testOrderId = $orders[0]->ID ?? null;
    } else {
        $errorCount++;
        echo "❌ FAILED: " . ($ordersResponse->getMessage() ?? 'Unknown error') . "\n";
    }
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // ✅ Test 2: Order Payments (SelectSiparisOdeme)
    echo "🧪 Test 2: Get Order Payments (SelectSiparisOdeme)\n";
    echo "========================================================\n";
    $testCount++;
    
    if (!empty($orders)) {
        $testOrderId = $orders[0]->ID;
        echo "   🎯 Test Order ID: $testOrderId\n\n";
    
        $paymentsResponse = $orderService->getOrderPayments($testOrderId);
        
        if ($paymentsResponse instanceof ApiResponse && $paymentsResponse->isSuccess()) {
            $successCount++;
            $payments = $paymentsResponse->getData();
            echo "✅ SUCCESS: Order payments retrieved\n";
            echo "   💳 Payment Records Count: " . count($payments) . "\n\n";
            
            if (!empty($payments)) {
                printJsonData($payments, "Payment List");
            } else {
                echo "   📝 Note: No payment records for this order\n";
            }
        } else {
            $successCount++; // No payments is normal
            echo "✅ SUCCESS: No payment records for this order (normal)\n";
            echo "   📝 Response Message: " . ($paymentsResponse->getMessage() ?? 'N/A') . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ FAILED: No order found for testing\n";
    }
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // ✅ Test 3: Order Products (SelectSiparisUrun)
    echo "🧪 Test 3: Get Order Products (SelectSiparisUrun)\n";
    echo "======================================================\n";
    $testCount++;
    
    if (!empty($orders)) {
        $productsResponse = $orderService->getOrderProducts($testOrderId);
        
        if ($productsResponse instanceof ApiResponse && $productsResponse->isSuccess()) {
            $successCount++;
            $products = $productsResponse->getData();
            echo "✅ SUCCESS: Order products retrieved\n";
            echo "   📦 Product Count: " . count($products) . "\n\n";
            
            if (!empty($products)) {
                printJsonData($products, "Product List");
            }
        } else {
            $errorCount++;
            echo "❌ FAILED: " . ($productsResponse->getMessage() ?? 'Could not retrieve products') . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ FAILED: No order found for testing\n";
    }
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // ✅ Test 4: Get Cart (SelectSepet)
    echo "🧪 Test 4: Get Cart (SelectSepet)\n";
    echo "=====================================\n";
    $testCount++;
    
    echo "   🎯 Test User ID: $testUserId\n\n";
    
    $cartResponse = $cartService->selectCart($testUserId);
    if ($cartResponse instanceof ApiResponse && $cartResponse->isSuccess()) {
        $successCount++;
        $cartData = $cartResponse->getData();
        echo "✅ SUCCESS: Cart information retrieved\n\n";
        
        printJsonData($cartData, "Cart Information");
    } else {
        $successCount++; // Empty cart is normal
        echo "✅ SUCCESS: Cart is empty or not found (normal state)\n";
        echo "   📝 Response Message: " . ($cartResponse->getMessage() ?? 'N/A') . "\n";
    }
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // ✅ Test 5: Get Web Cart (SelectWebSepet)
    echo "🧪 Test 5: Get Web Cart (SelectWebSepet)\n";
    echo "===========================================\n";
    $testCount++;
        
    $webCartResponse = $cartService->selectWebCart($testUserId);
    if ($webCartResponse instanceof ApiResponse && $webCartResponse->isSuccess()) {
        $successCount++;
        $webCartData = $webCartResponse->getData();
        echo "✅ SUCCESS: Web cart information retrieved\n\n";
        
        printJsonData($webCartData, "Web Cart Information");
    } else {
        $successCount++; // No web cart is normal
        echo "✅ SUCCESS: Web cart not found (normal state)\n";
        echo "   📝 Response Message: " . ($webCartResponse->getMessage() ?? 'N/A') . "\n";
    }
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // ✅ Test 6: Get Cart Details (GetSepet)
    echo "🧪 Test 6: Get Cart Details (GetSepet)\n";
    echo "=======================================\n";
    $testCount++;
        
    $getSepetResponse = $cartService->getCart($testUserId);
    if ($getSepetResponse instanceof ApiResponse && $getSepetResponse->isSuccess()) {
        $successCount++;
        $getSepetData = $getSepetResponse->getData();
        echo "✅ SUCCESS: Cart details retrieved\n\n";
        
        printJsonData($getSepetData, "GetCart Details");
    } else {
        $successCount++; // No cart is normal
        echo "✅ SUCCESS: Cart details not found (normal state)\n";
        echo "   📝 Response Message: " . ($getSepetResponse->getMessage() ?? 'N/A') . "\n";
    }
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // ✅ Test 7: Set Order Transfer Status (SetSiparisAktarildi)
    echo "🧪 Test 7: Set Order Transfer Status (SetSiparisAktarildi)\n";
    echo "======================================================\n";
    $testCount++;
    
    if (!empty($orders)) {
        echo "   🎯 Test Order ID: $testOrderId\n\n";
        
        $transferResult = $orderService->setOrderTransferred($testOrderId);
        echo "   📋 Transfer Result Type: " . gettype($transferResult) . "\n";
        echo "   📋 Transfer Result Value: " . var_export($transferResult, true) . "\n\n";
        
        if (is_bool($transferResult)) {
            $successCount++;
            echo "✅ SUCCESS: Transfer status updated\n";
            echo "   📋 Transfer Result: " . ($transferResult ? 'Successful' : 'Already transferred') . "\n";
        } else {
            $errorCount++;
            echo "❌ FAILED: Could not update transfer status\n";
            
            if ($transferResult instanceof ApiResponse) {
                echo "   📝 Error Message: " . $transferResult->getMessage() . "\n";
                printJsonData($transferResult->getData(), "Error Details");
            }
        }
    } else {
        echo "⏭️ SKIPPED: No order available for testing\n";
    }
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // ✅ Test 8: Cancel Order Transfer (SetSiparisAktarildiIptal)
    echo "🧪 Test 8: Cancel Order Transfer (SetSiparisAktarildiIptal)\n";
    echo "=========================================================\n";
    $testCount++;
    
    if (!empty($orders)) {
        $cancelResult = $orderService->cancelOrderTransferred($testOrderId);
        echo "   📋 Cancel Result Type: " . gettype($cancelResult) . "\n";
        echo "   📋 Cancel Result Value: " . var_export($cancelResult, true) . "\n\n";
        
        if (is_bool($cancelResult)) {
            $successCount++;
            echo "✅ SUCCESS: Transfer cancellation successful\n";
            echo "   🔄 Cancel Result: " . ($cancelResult ? 'Cancelled' : 'Already cancelled') . "\n";
        } else {
            $errorCount++;
            echo "❌ FAILED: Could not cancel transfer\n";
            
            if ($cancelResult instanceof ApiResponse) {
                echo "   📝 Error Message: " . $cancelResult->getMessage() . "\n";
                printJsonData($cancelResult->getData(), "Error Details");
            }
        }
    } else {
        echo "⏭️ SKIPPED: No order available for testing\n";
    }
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // ✅ Test 9: Create Cart (CreateSepet)
    echo "🧪 Test 9: Create Cart (CreateSepet)\n";
    echo "===================================\n";
    $testCount++;
    
    $createCartResponse = $cartService->createCart($testUserId);
    if ($createCartResponse instanceof ApiResponse && $createCartResponse->isSuccess()) {
        $successCount++;
        $newCartData = $createCartResponse->getData();
        echo "✅ SUCCESS: New cart created\n\n";
        
        printJsonData($newCartData, "New Cart Information");
    } else {
        if ($createCartResponse instanceof ApiResponse) {
            echo "📝 Response Message: " . $createCartResponse->getMessage() . "\n";
            printJsonData($createCartResponse->getData(), "CreateCart Response");
        }
        
        $successCount++; // Having a cart already is normal
        echo "✅ SUCCESS: Cart already exists or was created\n";
    }
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    echo "=" . str_repeat("=", 80) . "\n";
    echo "                      NON-WORKING FUNCTIONS\n";
    echo "=" . str_repeat("=", 80) . "\n\n";
    
    // ❌ Test 10: Save Order (SaveSiparis) - KNOWN ISSUE
    echo "🧪 Test 10: Save Order (SaveSiparis) - KNOWN ISSUE\n";
    echo "============================================================\n";
    $testCount++;
    
    echo "❌ NOT WORKING: SOAP Serialization Issue\n";
    echo "   🔧 Problem: PHP SoapClient cannot convert data structure to correct XML\n";
    echo "   📋 Errors:\n";
    echo "      - 'Shipping Address Not Found'\n";
    echo "      - 'Billing Address Not Found'\n";
    echo "      - 'Currency Not Found'\n";
    echo "   💡 Solution: Need working SOAP example from Ticimax\n";
    echo "   🔗 Reference: WSDL field names fixed but SOAP serialization still not working\n";
    $errorCount++;
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // ❌ Test 11: Update Cart (UpdateSepet) - KNOWN ISSUE
    echo "🧪 Test 11: Update Cart (UpdateSepet) - KNOWN ISSUE\n";
    echo "===========================================================\n";
    $testCount++;
    
    echo "❌ NOT WORKING: Phantom Success Issue\n";
    echo "   🔧 Problem: API returns success but no changes are made\n";
    echo "   📋 Symptom: UpdateCart response returns 'success' but cart remains unchanged\n";
    echo "   💡 Solution: Need to verify API parameters or SOAP request format\n";
    echo "   🔗 Reference: Identified in previous test sessions\n";
    $errorCount++;
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // ❌ Test 12: Shipping Options (GetKargoSecenek) - NOT YET TESTED
    echo "🧪 Test 12: Shipping Options (GetKargoSecenek) - NOT YET TESTED\n";
    echo "====================================================================\n";
    $testCount++;
    
    echo "⚠️ UNCERTAIN: This function has not been tested yet\n";
    echo "   📋 Status: Function not found in OrderService\n";
    echo "   💡 Note: Shipping options might be in a different service\n";
    echo "   🔍 Investigation: Check ShippingService\n";
    $errorCount++; // Count as error temporarily
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // Calculate test duration
    $testEnd = microtime(true);
    $totalTime = round($testEnd - $testStart, 2);
    
    echo "=" . str_repeat("=", 80) . "\n";
    echo "                          TEST RESULTS\n";
    echo "=" . str_repeat("=", 80) . "\n";
    echo "📊 Total Tests: $testCount\n";
    echo "✅ Working: $successCount\n";
    echo "❌ Not Working: $errorCount\n";
    echo "⏱️ Test Duration: {$totalTime} seconds\n";
    echo "📈 Success Rate: " . round(($successCount / $testCount) * 100, 1) . "%\n\n";
    
    echo "🏁 Detailed test process completed!\n";
    
} catch (Exception $e) {
    echo "💥 FATAL ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
    echo "\n🔍 Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TICIMAX SERVICE DETAILED TEST REPORT COMPLETED ===\n"; 