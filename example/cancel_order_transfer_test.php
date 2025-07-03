<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Order\OrderService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$orderService = new OrderService($request);

echo "=== CANCEL ORDER TRANSFER TEST ===\n";
echo "Testing OrderService::cancelOrderTransferred()\n";
echo "API URL: {$config['mainDomain']}\n\n";

echo "⚠️  WARNING: This test will attempt to cancel order transfers!\n";
echo "⚠️  Make sure you are testing on a development/test environment!\n";
echo "⚠️  This operation may affect actual orders and their transfer status!\n";
echo "⚠️  Press Ctrl+C to cancel if this is a production environment.\n\n";

// Wait 3 seconds to allow cancellation
echo "Starting in 3 seconds...\n";
sleep(1);
echo "Starting in 2 seconds...\n";
sleep(1);
echo "Starting in 1 second...\n";
sleep(1);
echo "Starting tests...\n\n";

try {
    // First, let's get some orders that might have transfers
    echo "0. Getting orders to test transfer cancellation...\n";
    $orders = $orderService->getOrders([], ['KayitSayisi' => 10]);
    
    $testOrderIds = [];
    $transferredOrderIds = [];
    
    if (empty($orders['data'])) {
        echo "⚠️ No orders found. Using test order IDs.\n";
        $testOrderIds = [1, 2, 3];
    } else {
        echo "✅ Found " . count($orders['data']) . " orders. Checking for transferred orders.\n";
        
        foreach ($orders['data'] as $order) {
            if (isset($order->ID)) {
                $testOrderIds[] = $order->ID;
                
                // Check if order appears to be transferred (you might need to adjust this logic)
                // Looking for orders that might have transfer status
                if (isset($order->Durum) && (
                    stripos($order->Durum, 'transfer') !== false ||
                    stripos($order->Durum, 'gönder') !== false ||
                    stripos($order->Durum, 'aktarım') !== false ||
                    $order->Durum == 'Transferred' ||
                    $order->Durum == 'Sent'
                )) {
                    $transferredOrderIds[] = $order->ID;
                }
            }
            
            if (count($testOrderIds) >= 5) break;
        }
        
        if (empty($testOrderIds)) {
            $testOrderIds = [1, 2, 3];
        }
    }

    echo "Test order IDs: " . implode(', ', $testOrderIds) . "\n";
    echo "Potentially transferred orders: " . (empty($transferredOrderIds) ? 'None found' : implode(', ', $transferredOrderIds)) . "\n\n";

    // Test 1: Cancel transfer for potentially transferred orders
    if (!empty($transferredOrderIds)) {
        echo "1. Testing transfer cancellation for potentially transferred orders...\n";
        foreach ($transferredOrderIds as $index => $orderId) {
            echo "  " . ($index + 1) . ".1. Attempting to cancel transfer for order $orderId\n";
            
            $cancelResult = $orderService->cancelOrderTransferred($orderId);
            
            if ($cancelResult['success']) {
                echo "    ✅ Transfer cancellation successful\n";
                echo "    Message: " . ($cancelResult['message'] ?? 'No message') . "\n";
                
                if (isset($cancelResult['data'])) {
                    echo "    Data: " . json_encode($cancelResult['data']) . "\n";
                }
            } else {
                echo "    ❌ Transfer cancellation failed\n";
                echo "    Message: " . ($cancelResult['message'] ?? 'No message') . "\n";
            }
            echo "\n";
        }
    } else {
        echo "1. No transferred orders found to test cancellation.\n\n";
    }

    // Test 2: Cancel transfer for regular test orders
    echo "2. Testing transfer cancellation for regular orders...\n";
    foreach (array_slice($testOrderIds, 0, 3) as $index => $orderId) {
        echo "  " . ($index + 1) . ".2. Attempting to cancel transfer for order $orderId\n";
        
        $cancelResult = $orderService->cancelOrderTransferred($orderId);
        
        if ($cancelResult['success']) {
            echo "    ✅ Transfer cancellation successful\n";
            echo "    Message: " . ($cancelResult['message'] ?? 'No message') . "\n";
            
            if (isset($cancelResult['data'])) {
                echo "    Data: " . json_encode($cancelResult['data']) . "\n";
            }
        } else {
            echo "    ❌ Transfer cancellation failed\n";
            echo "    Message: " . ($cancelResult['message'] ?? 'No message') . "\n";
            
            // This is expected for orders that were not transferred
            if (stripos($cancelResult['message'] ?? '', 'transfer') !== false ||
                stripos($cancelResult['message'] ?? '', 'aktarım') !== false ||
                stripos($cancelResult['message'] ?? '', 'not found') !== false) {
                echo "    ℹ️ This is expected if the order was not transferred\n";
            }
        }
        echo "\n";
    }

    // Test 3: Test with invalid order IDs
    echo "3. Testing with invalid order IDs...\n";
    
    echo "  3.1. Testing with negative order ID (-1)...\n";
    $negativeResult = $orderService->cancelOrderTransferred(-1);
    if (!$negativeResult['success']) {
        echo "    ✅ Correctly rejected negative order ID\n";
        echo "    Message: " . ($negativeResult['message'] ?? 'No message') . "\n";
    } else {
        echo "    ⚠️ Unexpected success for negative order ID\n";
    }

    echo "  3.2. Testing with zero order ID (0)...\n";
    $zeroResult = $orderService->cancelOrderTransferred(0);
    if (!$zeroResult['success']) {
        echo "    ✅ Correctly rejected zero order ID\n";
        echo "    Message: " . ($zeroResult['message'] ?? 'No message') . "\n";
    } else {
        echo "    ⚠️ Unexpected success for zero order ID\n";
    }

    echo "  3.3. Testing with large order ID (999999)...\n";
    $largeResult = $orderService->cancelOrderTransferred(999999);
    if (!$largeResult['success']) {
        echo "    ✅ Correctly rejected non-existent order ID\n";
        echo "    Message: " . ($largeResult['message'] ?? 'No message') . "\n";
    } else {
        echo "    ⚠️ Unexpected success for non-existent order ID\n";
    }

    echo "\n";

    // Test 4: Test with string/non-numeric values
    echo "4. Testing with invalid data types...\n";
    
    echo "  4.1. Testing with string order ID ('abc')...\n";
    try {
        $stringResult = $orderService->cancelOrderTransferred('abc');
        if (!$stringResult['success']) {
            echo "    ✅ Correctly rejected string order ID\n";
            echo "    Message: " . ($stringResult['message'] ?? 'No message') . "\n";
        } else {
            echo "    ⚠️ Unexpected success for string order ID\n";
        }
    } catch (Exception $e) {
        echo "    ✅ Exception thrown for string order ID: " . $e->getMessage() . "\n";
    }

    echo "  4.2. Testing with null order ID...\n";
    try {
        $nullResult = $orderService->cancelOrderTransferred(null);
        if (!$nullResult['success']) {
            echo "    ✅ Correctly rejected null order ID\n";
            echo "    Message: " . ($nullResult['message'] ?? 'No message') . "\n";
        } else {
            echo "    ⚠️ Unexpected success for null order ID\n";
        }
    } catch (Exception $e) {
        echo "    ✅ Exception thrown for null order ID: " . $e->getMessage() . "\n";
    }

    echo "\n";

    // Summary
    $successfulCancellations = 0;
    $failedCancellations = 0;
    
    // Test all the orders again for summary
    foreach ($testOrderIds as $orderId) {
        $result = $orderService->cancelOrderTransferred($orderId);
        if ($result['success']) {
            $successfulCancellations++;
        } else {
            $failedCancellations++;
        }
    }

    echo "=== SUMMARY ===\n";
    echo "Orders tested: " . count($testOrderIds) . "\n";
    echo "Potentially transferred orders found: " . count($transferredOrderIds) . "\n";
    echo "Successful cancellations: $successfulCancellations\n";
    echo "Failed cancellations: $failedCancellations\n";
    echo "Success rate: " . (count($testOrderIds) > 0 ? round(($successfulCancellations / count($testOrderIds)) * 100, 2) : 0) . "%\n";
    echo "\n";
    echo "ℹ️ Note: Failed cancellations are often expected for orders that were not transferred.\n";
    echo "⚠️ Please verify that no important order transfers were accidentally cancelled.\n";

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== CANCEL ORDER TRANSFER TEST COMPLETED ===\n"; 