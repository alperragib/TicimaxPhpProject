<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Product\ProductService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$productService = new ProductService($request);

echo "=== PRODUCT REVIEWS TEST ===\n";
echo "Testing ProductService::GetProductReviews()\n";
echo "API URL: {$config['mainDomain']}\n\n";

try {
    // First, let's get some products to get product card IDs
    echo "0. Getting products to test reviews...\n";
    $products = $productService->getProducts([], ['KayitSayisi' => 5]);
    
    if (empty($products)) {
        echo "⚠️ No products found. Using test product card ID: 1\n";
        $testProductCardIds = [1, 2, 3];
    } else {
        echo "✅ Found " . count($products) . " products. Testing with real IDs.\n";
        $testProductCardIds = [];
        foreach ($products as $product) {
            if (isset($product->ID)) {
                $testProductCardIds[] = $product->ID;
                if (count($testProductCardIds) >= 3) break;
            }
        }
        if (empty($testProductCardIds)) {
            $testProductCardIds = [1, 2, 3]; // Fallback
        }
    }

    echo "\n";

    // Test reviews for each product
    foreach ($testProductCardIds as $index => $productCardId) {
        echo ($index + 1) . ". Testing reviews for product card ID: $productCardId\n";
        
        $reviews = $productService->GetProductReviews($productCardId);
        
        if (!empty($reviews)) {
            echo "✅ Found " . count($reviews) . " review(s)\n";
            
            foreach ($reviews as $reviewIndex => $review) {
                echo "  Review #" . ($reviewIndex + 1) . ":\n";
                echo "    - ID: " . ($review['id'] ?? 'N/A') . "\n";
                echo "    - Product Card ID: " . ($review['urunKartiId'] ?? 'N/A') . "\n";
                echo "    - User ID: " . ($review['uyeId'] ?? 'N/A') . "\n";
                echo "    - Name: " . ($review['isim'] ?? 'N/A') . "\n";
                echo "    - Email: " . ($review['mail'] ?? 'N/A') . "\n";
                echo "    - Product Name: " . ($review['urunAdi'] ?? 'N/A') . "\n";
                echo "    - Date: " . ($review['eklemeTarihi'] ?? 'N/A') . "\n";
                echo "    - Message: " . (strlen($review['mesaj'] ?? '') > 100 ? 
                     substr($review['mesaj'], 0, 100) . '...' : 
                     ($review['mesaj'] ?? 'N/A')) . "\n";
                echo "  ---\n";
            }
        } else {
            echo "ℹ️ No reviews found for product card $productCardId\n";
        }
        echo "\n";
    }

    // Test with invalid product card ID
    echo "4. Testing with invalid product card ID (-1)...\n";
    $invalidReviews = $productService->GetProductReviews(-1);
    
    if (empty($invalidReviews)) {
        echo "✅ Correctly returned empty result for invalid product card ID\n";
    } else {
        echo "⚠️ Unexpected result for invalid product card ID\n";
        print_r($invalidReviews);
    }

    echo "\n";

    // Test with large product card ID
    echo "5. Testing with large product card ID (999999)...\n";
    $largeIdReviews = $productService->GetProductReviews(999999);
    
    if (empty($largeIdReviews)) {
        echo "✅ Correctly returned empty result for non-existent product card ID\n";
    } else {
        echo "⚠️ Unexpected result for large product card ID\n";
        print_r($largeIdReviews);
    }

    echo "\n";

    // Summary
    $totalReviews = 0;
    foreach ($testProductCardIds as $productCardId) {
        $reviews = $productService->GetProductReviews($productCardId);
        $totalReviews += count($reviews);
    }

    echo "=== SUMMARY ===\n";
    echo "Products tested: " . count($testProductCardIds) . "\n";
    echo "Total reviews found: $totalReviews\n";
    echo "Average reviews per product: " . ($totalReviews > 0 ? round($totalReviews / count($testProductCardIds), 2) : 0) . "\n";

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== PRODUCT REVIEWS TEST COMPLETED ===\n"; 