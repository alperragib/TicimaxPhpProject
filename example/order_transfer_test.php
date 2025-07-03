<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

use AlperRagib\Ticimax\Service\Order\OrderService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($mainDomain, $apiKey);
$orderService = new OrderService($request);

echo "Testing with domain: $mainDomain\n\n";

try {
    // Example order ID - replace with a real order ID
    $orderId = 1234;

    echo "Testing order transfer functions with Order ID: $orderId\n\n";

    // Test setOrderTransferred
    echo "1. Marking order as transferred...\n";
    $result = $orderService->setOrderTransferred($orderId);
    echo "Result: " . ($result ? "Success" : "Failed") . "\n\n";

    // Wait a bit before canceling
    sleep(2);

    // Test cancelOrderTransferred
    echo "2. Canceling order transferred status...\n";
    $result = $orderService->cancelOrderTransferred($orderId);
    echo "Result: " . ($result ? "Success" : "Failed") . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
} 