<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Order\OrderService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$orderService = new OrderService($request);

echo "=== ORDER PRODUCTS TEST ===\n";
echo "Testing OrderService::getOrderProducts()\n";
echo "API URL: {$config['mainDomain']}\n\n";

try {
    // First, let's get some orders to test products
    echo "0. Getting orders to test products...\n";
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

    // Test products for each order
    foreach ($testOrderIds as $index => $orderId) {
        echo ($index + 1) . ". Testing products for order ID: $orderId\n";
        
        $products = $orderService->getOrderProducts($orderId);
        
        if (!empty($products['data'])) {
            echo "✅ Found " . count($products['data']) . " product(s) for order $orderId\n";
            
            $totalValue = 0;
            $totalQuantity = 0;
            
            foreach ($products['data'] as $productIndex => $product) {
                echo "  Product #" . ($productIndex + 1) . ":\n";
                echo "    - ID: " . ($product->ID ?? 'N/A') . "\n";
                echo "    - Order ID: " . ($product->SiparisID ?? 'N/A') . "\n";
                echo "    - Product Name: " . ($product->UrunAdi ?? 'N/A') . "\n";
                echo "    - Product Code: " . ($product->UrunKodu ?? 'N/A') . "\n";
                echo "    - Barcode: " . ($product->Barkod ?? 'N/A') . "\n";
                echo "    - Stock Code: " . ($product->StokKodu ?? 'N/A') . "\n";
                echo "    - Quantity: " . ($product->Adet ?? 'N/A') . "\n";
                echo "    - Unit Price: " . ($product->BirimFiyat ?? 'N/A') . "\n";
                echo "    - Total Price: " . ($product->ToplamFiyat ?? 'N/A') . "\n";
                echo "    - Currency: " . ($product->ParaBirimi ?? 'N/A') . "\n";
                echo "    - Tax Rate: " . ($product->KdvOrani ?? 'N/A') . "%\n";
                echo "    - Tax Amount: " . ($product->KdvTutari ?? 'N/A') . "\n";
                echo "    - Discount Rate: " . ($product->IndirimOrani ?? 'N/A') . "%\n";
                echo "    - Discount Amount: " . ($product->IndirimTutari ?? 'N/A') . "\n";
                echo "    - Product Image: " . ($product->UrunResmi ?? 'N/A') . "\n";
                echo "    - Brand: " . ($product->Marka ?? 'N/A') . "\n";
                echo "    - Category: " . ($product->Kategori ?? 'N/A') . "\n";
                echo "    - Supplier: " . ($product->Tedarikci ?? 'N/A') . "\n";
                echo "    - Status: " . ($product->Durum ?? 'N/A') . "\n";
                echo "    - Notes: " . ($product->Notlar ?? 'N/A') . "\n";
                
                // Calculate totals for summary
                if (isset($product->Adet) && is_numeric($product->Adet)) {
                    $totalQuantity += $product->Adet;
                }
                if (isset($product->ToplamFiyat) && is_numeric($product->ToplamFiyat)) {
                    $totalValue += $product->ToplamFiyat;
                }
                
                echo "  ---\n";
            }
            
            echo "  📊 Order $orderId Summary:\n";
            echo "    - Total Products: " . count($products['data']) . "\n";
            echo "    - Total Quantity: $totalQuantity\n";
            echo "    - Total Value: " . number_format($totalValue, 2) . "\n";
            
        } else {
            echo "ℹ️ No products found for order $orderId\n";
            if (isset($products['message'])) {
                echo "    Message: " . $products['message'] . "\n";
            }
        }
        echo "\n";
    }

    // Test with invalid order ID
    echo "4. Testing with invalid order ID (-1)...\n";
    $invalidProducts = $orderService->getOrderProducts(-1);
    
    if (empty($invalidProducts['data'])) {
        echo "✅ Correctly returned empty result for invalid order ID\n";
        if (isset($invalidProducts['message'])) {
            echo "    Message: " . $invalidProducts['message'] . "\n";
        }
    } else {
        echo "⚠️ Unexpected result for invalid order ID\n";
        print_r($invalidProducts);
    }

    echo "\n";

    // Test with large order ID
    echo "5. Testing with large order ID (999999)...\n";
    $largeIdProducts = $orderService->getOrderProducts(999999);
    
    if (empty($largeIdProducts['data'])) {
        echo "✅ Correctly returned empty result for non-existent order ID\n";
        if (isset($largeIdProducts['message'])) {
            echo "    Message: " . $largeIdProducts['message'] . "\n";
        }
    } else {
        echo "⚠️ Unexpected result for large order ID\n";
        print_r($largeIdProducts);
    }

    echo "\n";

    // Summary
    $totalProducts = 0;
    $totalOrderQuantity = 0;
    $totalOrderValue = 0;
    $brands = [];
    $categories = [];
    $suppliers = [];
    $currencies = [];
    
    foreach ($testOrderIds as $orderId) {
        $products = $orderService->getOrderProducts($orderId);
        
        if (!empty($products['data'])) {
            $totalProducts += count($products['data']);
            
            foreach ($products['data'] as $product) {
                // Count quantities and values
                if (isset($product->Adet) && is_numeric($product->Adet)) {
                    $totalOrderQuantity += $product->Adet;
                }
                if (isset($product->ToplamFiyat) && is_numeric($product->ToplamFiyat)) {
                    $totalOrderValue += $product->ToplamFiyat;
                }
                
                // Collect unique values
                if (!empty($product->Marka)) {
                    $brands[$product->Marka] = true;
                }
                if (!empty($product->Kategori)) {
                    $categories[$product->Kategori] = true;
                }
                if (!empty($product->Tedarikci)) {
                    $suppliers[$product->Tedarikci] = true;
                }
                if (!empty($product->ParaBirimi)) {
                    $currencies[$product->ParaBirimi] = true;
                }
            }
        }
    }

    echo "=== SUMMARY ===\n";
    echo "Orders tested: " . count($testOrderIds) . "\n";
    echo "Total products found: $totalProducts\n";
    echo "Total quantity: $totalOrderQuantity\n";
    echo "Total value: " . number_format($totalOrderValue, 2) . "\n";
    echo "Unique brands: " . count($brands) . " (" . implode(', ', array_slice(array_keys($brands), 0, 5)) . (count($brands) > 5 ? '...' : '') . ")\n";
    echo "Unique categories: " . count($categories) . " (" . implode(', ', array_slice(array_keys($categories), 0, 5)) . (count($categories) > 5 ? '...' : '') . ")\n";
    echo "Unique suppliers: " . count($suppliers) . " (" . implode(', ', array_slice(array_keys($suppliers), 0, 5)) . (count($suppliers) > 5 ? '...' : '') . ")\n";
    echo "Currencies used: " . implode(', ', array_keys($currencies)) . "\n";
    echo "Average products per order: " . ($totalProducts > 0 ? round($totalProducts / count($testOrderIds), 2) : 0) . "\n";

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== ORDER PRODUCTS TEST COMPLETED ===\n"; 