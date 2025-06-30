<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Cart\WebCartModel;
use AlperRagib\Ticimax\Model\Cart\WebCartProductModel;

// Helper functions for output formatting
function printSectionHeader($title) {
    echo "=== $title ===\n";
}

function printCartDetails($cart, $prefix = "") {
    echo $prefix . "Cart ID: " . $cart->ID . "\n";
    echo $prefix . "User ID: " . $cart->UyeID . "\n";
    echo $prefix . "Total Amount: " . $cart->GenelToplam . "\n";
    echo $prefix . "Total VAT: " . $cart->ToplamKDV . "\n";
    echo $prefix . "Total Product Count: " . $cart->ToplamUrunAdedi . "\n";
}

function printProductDetails($urun, $index) {
    echo "\nProduct " . ($index + 1) . ":\n";
    echo "  Product ID: " . ($urun->UrunID ?? 'N/A') . "\n";
    echo "  Product Name: " . ($urun->UrunAdi ?? 'N/A') . "\n";
    echo "  Quantity: " . ($urun->Adet ?? 'N/A') . "\n";
    echo "  Unit Price: " . ($urun->UrunSepetFiyati ?? 'N/A') . "\n";
    echo "  Unit Price (VAT): " . ($urun->UrunSepetFiyatiKDV ?? 'N/A') . "\n";
    echo "  Total Price: " . ($urun->ToplamUrunSepetFiyati ?? 'N/A') . "\n";
    echo "  Total Price (VAT): " . ($urun->ToplamUrunSepetFiyatiKDVli ?? 'N/A') . "\n";
    echo "  VAT Rate: " . ($urun->KDVOrani ?? 'N/A') . "\n";
    echo "  VAT Amount: " . ($urun->KDVTutari ?? 'N/A') . "\n";
    echo "  Stock Code: " . ($urun->StokKodu ?? 'N/A') . "\n";
    echo "  Brand: " . ($urun->Marka ?? 'N/A') . "\n";
    echo "  Free Shipping: " . (isset($urun->isUcretsizKargo) && $urun->isUcretsizKargo ? 'Yes' : 'No') . "\n";
    echo "  Spot Image: " . ($urun->SpotResim ?? 'N/A') . "\n";
}

function printCartProducts($cart, $sectionTitle = "Cart Products") {
    if (isset($cart->Urunler) && !empty($cart->Urunler)) {
        echo "\n$sectionTitle:\n";
        foreach ($cart->Urunler as $index => $urun) {
            printProductDetails($urun, $index);
        }
    } else {
        echo "\n$sectionTitle: No products found.\n";
    }
}

function printMessage($message) {
    echo "$message\n";
}

// Set your Ticimax domain and API key
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$cartService = $ticimax->cartService();

printSectionHeader("Cart Service Examples");
echo "\n";

// Example 1: Get or Create Cart for User
printSectionHeader("Get or Create Cart Example");
printMessage("Checking cart for user:");

$userId = 1050;
$cartId = 1; // Initially null, will be set if cart exists

// First, try to get existing cart for the user
$cart = $cartService->getSepet($userId, $cartId);

if ($cart !== null && $cart->ID > 0) {
    printMessage("Existing cart found!");
    $cartId = $cart->ID;
    printCartDetails($cart);
} else {
    printMessage("No existing cart found. Creating new cart...");
    
    // Create new cart for the user
    $newCart = $cartService->createSepet($userId);
    
    if ($newCart !== null && $newCart->ID > 0) {
        printMessage("New cart created successfully!");
        $cartId = $newCart->ID;
        printCartDetails($newCart, "New ");
    } else {
        printMessage("Failed to create cart.");
        $cartId = null;
    }
}

// Example 2: Get specific cart by ID (if we have one)
if ($cartId !== null && $cartId > 0) {
    echo "\n";
    printSectionHeader("Get Specific Cart Example");
    printMessage("Getting cart with ID: $cartId");
    
    $specificCart = $cartService->getSepet($userId, $cartId);
    
    if ($specificCart !== null) {
        printMessage("Cart details retrieved successfully!");
        printCartDetails($specificCart);
        printCartProducts($specificCart);
    } else {
        printMessage("Failed to retrieve cart details.");
    }
}

// Example 3: Add product to cart
if ($cartId !== null && $cartId > 0) {
    echo "\n";
    printSectionHeader("Add Product to Cart Example");
    printMessage("Adding product to cart ID: $cartId");
    
    // Example product data
    $productId = 6;
    $quantity = 2;
    $cartProductId = 6; // Use 0 for new products, existing cart product ID for updates
    
    printMessage("Adding Product ID: $productId, Quantity: $quantity");
    
    // Add product to cart (updateQuantity = false, removeFromCart = false)
    $result = $cartService->updateCart($cartId, $cartProductId, $productId, $quantity, false, false);
    
    if (!$result['IsError']) {
        printMessage("Product added successfully!");
        
        // Get updated cart to show the new product
        printMessage("\nGetting updated cart...");
        $updatedCart = $cartService->getSepet($userId, $cartId);
        
        if ($updatedCart !== null) {
            printMessage("Updated Cart Details:");
            printCartDetails($updatedCart);
            printCartProducts($updatedCart, "Updated Cart Products");
        } else {
            printMessage("Failed to get updated cart.");
        }
    } else {
        printMessage("Failed to add product: " . $result['ErrorMessage']);
    }
} else {
    echo "\n";
    printMessage("Cannot proceed with cart operations - no valid cart available.");
}

printMessage("\n=== End of Cart Service Examples ===");
