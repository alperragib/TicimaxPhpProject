<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Order\OrderService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$orderService = new OrderService($request);

echo "=== ORDER PAYMENTS TEST ===\n";
echo "Testing OrderService::getOrderPayments()\n";
echo "API URL: {$config['mainDomain']}\n\n";

try {
    // First, let's get some orders to test payments
    echo "0. Getting orders to test payments...\n";
    $orders = $orderService->getOrders([], ['KayitSayisi' => 5]);
    
    $testOrderIds = [];
    
    if (empty($orders['data'])) {
        echo "⚠️ No orders found. Using test order IDs.\n";
        $testOrderIds = [1, 2, 3];
    } else {
        echo "✅ Found " . count($orders['data']) . " orders. Testing with real IDs.\n";
        
        foreach ($orders['data'] as $order) {
            if (isset($order->ID) && count($testOrderIds) < 3) {
                $testOrderIds[] = $order->ID;
            }
        }
        
        if (empty($testOrderIds)) {
            $testOrderIds = [1, 2, 3]; // Fallback
        }
    }

    echo "Test order IDs: " . implode(', ', $testOrderIds) . "\n\n";

    // Test payments for each order
    foreach ($testOrderIds as $index => $orderId) {
        echo ($index + 1) . ". Testing payments for order ID: $orderId\n";
        
        $payments = $orderService->getOrderPayments($orderId);
        
        if (!empty($payments['data'])) {
            echo "✅ Found " . count($payments['data']) . " payment(s) for order $orderId\n";
            
            foreach ($payments['data'] as $paymentIndex => $payment) {
                echo "  Payment #" . ($paymentIndex + 1) . ":\n";
                echo "    - ID: " . ($payment->ID ?? 'N/A') . "\n";
                echo "    - Order ID: " . ($payment->SiparisID ?? 'N/A') . "\n";
                echo "    - Payment Method: " . ($payment->OdemeYontemi ?? 'N/A') . "\n";
                echo "    - Amount: " . ($payment->Tutar ?? 'N/A') . "\n";
                echo "    - Currency: " . ($payment->ParaBirimi ?? 'N/A') . "\n";
                echo "    - Payment Date: " . ($payment->OdemeTarihi ?? 'N/A') . "\n";
                echo "    - Transaction ID: " . ($payment->IslemNo ?? 'N/A') . "\n";
                echo "    - Bank: " . ($payment->Banka ?? 'N/A') . "\n";
                echo "    - Status: " . ($payment->Durum ?? 'N/A') . "\n";
                echo "    - Authorization Code: " . ($payment->OnayKodu ?? 'N/A') . "\n";
                echo "    - Error Code: " . ($payment->HataKodu ?? 'N/A') . "\n";
                echo "    - Error Message: " . ($payment->HataMesaji ?? 'N/A') . "\n";
                echo "    - Description: " . ($payment->Aciklama ?? 'N/A') . "\n";
                echo "  ---\n";
            }
        } else {
            echo "ℹ️ No payments found for order $orderId\n";
            if (isset($payments['message'])) {
                echo "    Message: " . $payments['message'] . "\n";
            }
        }
        echo "\n";
    }

    // Test with invalid order ID
    echo "4. Testing with invalid order ID (-1)...\n";
    $invalidPayments = $orderService->getOrderPayments(-1);
    
    if (empty($invalidPayments['data'])) {
        echo "✅ Correctly returned empty result for invalid order ID\n";
        if (isset($invalidPayments['message'])) {
            echo "    Message: " . $invalidPayments['message'] . "\n";
        }
    } else {
        echo "⚠️ Unexpected result for invalid order ID\n";
        print_r($invalidPayments);
    }

    echo "\n";

    // Test with large order ID
    echo "5. Testing with large order ID (999999)...\n";
    $largeIdPayments = $orderService->getOrderPayments(999999);
    
    if (empty($largeIdPayments['data'])) {
        echo "✅ Correctly returned empty result for non-existent order ID\n";
        if (isset($largeIdPayments['message'])) {
            echo "    Message: " . $largeIdPayments['message'] . "\n";
        }
    } else {
        echo "⚠️ Unexpected result for large order ID\n";
        print_r($largeIdPayments);
    }

    echo "\n";

    // Summary
    $totalPayments = 0;
    $paymentMethods = [];
    $totalAmount = 0;
    $currencies = [];
    
    foreach ($testOrderIds as $orderId) {
        $payments = $orderService->getOrderPayments($orderId);
        
        if (!empty($payments['data'])) {
            $totalPayments += count($payments['data']);
            
            foreach ($payments['data'] as $payment) {
                // Count payment methods
                $method = $payment->OdemeYontemi ?? 'Unknown';
                $paymentMethods[$method] = ($paymentMethods[$method] ?? 0) + 1;
                
                // Sum amounts
                if (isset($payment->Tutar) && is_numeric($payment->Tutar)) {
                    $totalAmount += $payment->Tutar;
                }
                
                // Count currencies
                $currency = $payment->ParaBirimi ?? 'Unknown';
                $currencies[$currency] = true;
            }
        }
    }

    echo "=== SUMMARY ===\n";
    echo "Orders tested: " . count($testOrderIds) . "\n";
    echo "Total payments found: $totalPayments\n";
    echo "Total amount: " . number_format($totalAmount, 2) . "\n";
    echo "Currencies used: " . implode(', ', array_keys($currencies)) . "\n";
    echo "Payment methods:\n";
    foreach ($paymentMethods as $method => $count) {
        echo "  - $method: $count payment(s)\n";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== ORDER PAYMENTS TEST COMPLETED ===\n"; 