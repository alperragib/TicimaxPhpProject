<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Load configuration
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Test mode check
define('TEST_MODE', true);
define('SAFE_MODE', true);

echo "=== Ticimax API Order Listing Test ===\n";
echo "Domain: " . $mainDomain . "\n";
echo "Test Mode: " . (TEST_MODE ? 'Active' : 'Inactive') . "\n\n";

try {
    $ticimax = new Ticimax($mainDomain, $apiKey);
    $orderService = $ticimax->orderService();

    // Test 1: Orders from last 24 hours
    echo "1. Last 24 Hours Orders\n";
    echo "-------------------------\n";
    
    $filters = [
        'SiparisTarihiBas' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'SiparisTarihiSon' => date('Y-m-d H:i:s'),
        'KayitSayisi' => 5
    ];

    $response = $orderService->getOrders($filters);
    printOrders($response, "Last 24 Hours");

    // Test 2: Orders from last 7 days
    echo "\n2. Last 7 Days Orders\n";
    echo "-------------------------\n";
    
    $filters = [
        'SiparisTarihiBas' => date('Y-m-d H:i:s', strtotime('-7 days')),
        'SiparisTarihiSon' => date('Y-m-d H:i:s'),
        'KayitSayisi' => 5
    ];

    $response = $orderService->getOrders($filters);
    printOrders($response, "Last 7 Days");

    // Test 3: Orders from specific date range
    echo "\n3. Custom Date Range (Last Month)\n";
    echo "--------------------------------\n";
    
    $filters = [
        'SiparisTarihiBas' => date('Y-m-d H:i:s', strtotime('first day of last month')),
        'SiparisTarihiSon' => date('Y-m-d H:i:s', strtotime('last day of last month')),
        'KayitSayisi' => 5
    ];

    $response = $orderService->getOrders($filters);
    printOrders($response, "Last Month");

    // Test 4: Filter by order status
    echo "\n4. Completed Orders (Last 30 Days)\n";
    echo "------------------------------------\n";
    
    $filters = [
        'SiparisTarihiBas' => date('Y-m-d H:i:s', strtotime('-30 days')),
        'SiparisTarihiSon' => date('Y-m-d H:i:s'),
        'SiparisDurumu' => 2, // Completed orders
        'KayitSayisi' => 5
    ];

    $response = $orderService->getOrders($filters);
    printOrders($response, "Completed Orders");

} catch (Exception $e) {
    echo "\n! TEST ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Test Completed ===\n";

// Helper function: Print orders
function printOrders($response, $title) {
    if ($response->isSuccess()) {
        $orders = $response->getData();
        echo "Total " . $title . " Order Count: " . count($orders) . "\n\n";
        
        if (!empty($orders)) {
            foreach ($orders as $order) {
                echo sprintf(
                    "Order ID: %s\n" .
                    "Customer: %s\n" .
                    "Date: %s\n" .
                    "Amount: %.2f TL\n" .
                    "Status: %s\n",
                    $order->ID ?? 'Not specified',
                    $order->AdiSoyadi ?? 'Not specified',
                    $order->SiparisTarihi ?? 'Not specified',
                    $order->SiparisToplamTutari ?? 0,
                    getOrderStatus($order->SiparisDurumu ?? 0)
                );
                echo "------------------------\n";
            }
        } else {
            echo "! No orders found in this period.\n";
        }
    } else {
        echo "! Error: " . $response->getMessage() . "\n";
    }
}

// Helper function: Order status description
function getOrderStatus($status) {
    $statuses = [
        0 => 'Pending',
        1 => 'Approved',
        2 => 'Completed',
        3 => 'Cancelled',
        4 => 'Returned'
    ];
    
    return $statuses[$status] ?? 'Unknown';
} 