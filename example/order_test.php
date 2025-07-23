<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Tests;

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Response\ApiResponse;

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';
// Initialize services
$ticimax        = new Ticimax($config['mainDomain'], $config['apiKey']);
$orderService   = $ticimax->orderService();
$productService = $ticimax->productService();

// Counters and timing
$testCount    = 0;
$successCount = 0;
$errorCount   = 0;
$startTime    = microtime(true);

// Helper to check response
function isSuccess(ApiResponse $r): bool { return $r->isSuccess(); }

// Print dividers
function divider(string $title): void {
    echo "\n==== $title ========\n";
}

// Test runner

divider('ORDER RETRIEVAL TESTS');

// 1. Basic getOrders
$testCount++;
$response = $orderService->getOrders();
if (isSuccess($response)) {
    $successCount++;
    $orders = $response->getData();
    echo "getOrders(): " . count($orders) . " orders retrieved\n";
    // Print first 3 orders
    foreach (array_slice($orders, 0, 3) as $o) {
        echo sprintf("  ID:%s, No:%s, Date:%s, Total:%s\n",
            $o->SiparisID ?? $o->ID,
            $o->SiparisNo ?? 'N/A',
            $o->SiparisTarihi ?? 'N/A',
            $o->ToplamTutar ?? '0'
        );
    }
    $testOrderId = $orders[0]->SiparisID ?? $orders[0]->ID ?? null;
} else {
    $errorCount++;
    echo "getOrders() failed: " . $response->getMessage() . "\n";
}

// 2. Paginated getOrders
$testCount++;
$response = $orderService->getOrders([], ['page'=>1,'per_page'=>5]);
if (isSuccess($response)) {
    $successCount++;
    $paged = $response->getData();
    echo "getOrders(page=1, per_page=5): " . count($paged) . " orders\n";
    foreach ($paged as $o) {
        echo "  No:{$o->SiparisNo}, Total:{$o->ToplamTutar}\n";
    }
} else {
    $errorCount++;
    echo "Paginated getOrders failed: " . $response->getMessage() . "\n";
}

// 3. User-specific getOrders
$testCount++;
$userId = 1055;
$response = $orderService->getOrders(['UyeID'=>$userId]);
if (isSuccess($response)) {
    $successCount++;
    $userOrders = $response->getData();
    echo "getOrders(UyeID=$userId): " . count($userOrders) . " orders\n";
    foreach ($userOrders as $o) {
        echo "  #{$o->SiparisNo}, Status:{$o->StrSiparisDurumu}\n";
    }
} else {
    $errorCount++;
    echo "User-specific getOrders failed: " . $response->getMessage() . "\n";
}

// 4. Status filter
$testCount++;
$response = $orderService->getOrders(['SiparisDurumu'=>5], ['per_page'=>5]);
if (isSuccess($response)) {
    $successCount++;
    echo "getOrders(SiparisDurumu=5): " . count($response->getData()) . " orders\n";
} else {
    $successCount++;
    echo "No delivered orders\n";
}

// 5. Date range filter
$testCount++;
$end   = date('Y-m-d H:i:s');
$start = date('Y-m-d H:i:s', strtotime('-30 days'));
$response = $orderService->getOrders(['SiparisTarihiBas'=>$start,'SiparisTarihiSon'=>$end],['per_page'=>5]);
if (isSuccess($response)) {
    $successCount++;
    echo "getOrders(date range $start - $end): " . count($response->getData()) . " orders\n";
} else {
    $successCount++;
    echo "No orders in date range\n";
}

// 6. getOrderPayments
if (!empty($testOrderId)) {
    $testCount++;
    $r = $orderService->getOrderPayments($testOrderId);
    if (isSuccess($r)) {
        $successCount++;
        echo "getOrderPayments($testOrderId): " . count($r->getData()) . " records\n";
        foreach ($r->getData() as $p) {
            echo "  Payment ID:{$p['ID']}, Amount:{$p['Tutar']}\n";
        }
    } else {
        $errorCount++;
        echo "getOrderPayments failed: " . $r->getMessage() . "\n";
    }
}

// 7. getOrderProducts
if (!empty($testOrderId)) {
    $testCount++;
    $r = $orderService->getOrderProducts($testOrderId);
    if (isSuccess($r)) {
        $successCount++;
        echo "getOrderProducts($testOrderId): " . count($r->getData()) . " products\n";
        foreach ($r->getData() as $prod) {
            echo "  Product:{$prod['UrunAdi']}, Qty:{$prod['Adet']}, Price:{$prod['Tutar']}\n";
        }
    } else {
        $errorCount++;
        echo "getOrderProducts failed: " . $r->getMessage() . "\n";
    }
}

divider('PRODUCT RETRIEVAL TESTS');

// 8. getProducts
$testCount++;
$filters    = ['Aktif'=>1,'KategoriID'=>0,'MarkaID'=>0];
$pagination = ['BaslangicIndex'=>0,'KayitSayisi'=>10,'SiralamaDegeri'=>'Sira','SiralamaYonu'=>'ASC'];
$r = $productService->getProducts($filters, $pagination);
if (isSuccess($r)) {
    $successCount++;
    echo "getProducts(): " . count($r->getData()) . " products retrieved\n";
    foreach ($r->getData() as $pr) {
        echo "  ID:{$pr->UrunID}, Name:{$pr->UrunAdi}\n";
    }
} else {
    $errorCount++;
    echo "getProducts failed: " . $r->getMessage() . "\n";
}

// Summary
divider('TEST SUMMARY');
$endTime = microtime(true);
echo "Total Tests: $testCount\n";
echo "Success: $successCount\n";
echo "Errors: $errorCount\n";
echo "Duration: " . round($endTime - $startTime, 2) . "s\n";
