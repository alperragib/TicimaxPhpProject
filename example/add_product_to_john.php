<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require __DIR__ . '/config.php';

use AlperRagib\Ticimax\Service\Cart\CartService;
use AlperRagib\Ticimax\Service\Product\ProductService;
use AlperRagib\Ticimax\TicimaxRequest;

// Initialize the request with configuration
$request = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
$cartService = new CartService($request);
$productService = new ProductService($request);

echo "=== ADDING PRODUCT TO JOHN'S CART ===\n";
echo "Domain: {$config['mainDomain']}\n\n";

$johnUserId = 1050; // John's ID

try {
    // First, get some products to add
    echo "1. Getting available products...\n";
    $products = $productService->getProducts([], ['KayitSayisi' => 5]);
    
    if (empty($products)) {
        throw new Exception("No products found!");
    }
    
    echo "✅ Found " . count($products) . " products\n";
    
    // Get the first product
    $product = $products[0];
    echo "Selected product: {$product->UrunAdi} (ID: {$product->ID})\n\n";
    
    // Get product variations to get a valid variation ID
    echo "2. Getting product variations...\n";
    $variations = $productService->GetProductVariations(['UrunID' => $product->ID]);
    
    if (empty($variations)) {
        echo "No variations found, using product ID as variation ID\n";
        $variationId = $product->ID;
    } else {
        $variation = $variations[0];
        $variationId = $variation->ID;
        echo "✅ Using variation ID: $variationId\n";
        echo "Stock: " . ($variation->StokAdedi ?? 'Unknown') . "\n";
    }
    
    echo "\n3. Creating cart for John if needed...\n";
    
    // First check if John has a cart, if not create one
    $cart = $cartService->getSepet($johnUserId);
    
    if (!$cart || empty($cart->ID)) {
        echo "Creating new cart for John...\n";
        $cart = $cartService->createSepet($johnUserId);
        if (!$cart || empty($cart->ID)) {
            throw new Exception("Failed to create cart for John");
        }
        echo "✅ Cart created with ID: {$cart->ID}\n";
    } else {
        echo "✅ Existing cart found with ID: {$cart->ID}\n";
    }
    
    echo "\n4. Adding product to John's cart...\n";
    
    // Add product to cart using updateCart method
    // Parameters: cartId, cartProductId (0 for new), productId, quantity, updateQuantity, removeFromCart
    $addResult = $cartService->updateCart($cart->ID, 0, $variationId, 1, false, false);
    
    if ($addResult['success']) {
        echo "✅ Product added to John's cart successfully!\n";
        echo "Message: " . ($addResult['message'] ?? 'Success') . "\n";
    } else {
        echo "❌ Failed to add product to cart\n";
        echo "Message: " . ($addResult['message'] ?? 'Unknown error') . "\n";
    }
    
    echo "\n5. Checking John's updated cart...\n";
    $updatedCart = $cartService->getSepet($johnUserId);
    
    if ($updatedCart && !empty($updatedCart->ID)) {
        echo "✅ John's cart found!\n";
        echo "Cart ID: {$updatedCart->ID}\n";
        echo "User ID: {$updatedCart->UyeID}\n";
        echo "Total Amount: {$updatedCart->GenelToplam}\n";
        echo "Total Products: {$updatedCart->ToplamUrunAdedi}\n";
        
        // Show cart products
        if (!empty($updatedCart->Urunler)) {
            echo "Cart has " . count($updatedCart->Urunler) . " product(s):\n";
            foreach ($updatedCart->Urunler as $cartProduct) {
                echo "  - {$cartProduct->UrunAdi} (Qty: {$cartProduct->Adet})\n";
            }
        } else {
            echo "Cart is empty\n";
        }
    } else {
        echo "❌ Still no cart found for John\n";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== JOHN CART SETUP COMPLETED ===\n"; 