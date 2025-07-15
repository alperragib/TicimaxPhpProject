<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Response\ApiResponse;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== ProductService Function Tests ===\n\n";

try {
    // Initialize Ticimax API
    $ticimax = new Ticimax($mainDomain, $apiKey);
    $productService = $ticimax->productService();
    
    echo "✓ ProductService initialized\n\n";
    
    // Test 1: Get all products with full details
    echo "🧪 Test 1: Get All Products (Full Details)\n";
    echo "===========================================\n";
    
    $pagination = [
        'KayitSayisi' => 5,
        'BaslangicIndex' => 5,
        'SiralamaDegeri' => 'ID',
        'SiralamaYonu' => 'ASC'
    ];
    
    $productsResponse = $productService->getProducts([], $pagination);
    if ($productsResponse->isSuccess()) {
        $products = $productsResponse->getData();
        echo "✅ Retrieved " . count($products) . " products\n\n";
        
        // Show full details of ALL products (CORRECT FIELD NAMES)
        foreach ($products as $index => $product) {
            echo "📦 " . ($index + 1) . ". Product:\n";
            echo "   ID: " . ($product->ID ?? 'N/A') . "\n";
            echo "   Name: " . ($product->UrunAdi ?? 'N/A') . "\n";
            echo "   Supplier Code: " . ($product->TedarikciKodu ?? 'N/A') . "\n";
            echo "   Supplier Code 2: " . ($product->TedarikciKodu2 ?? 'N/A') . "\n";
            echo "   Total Stock: " . ($product->ToplamStokAdedi ?? 'N/A') . "\n";
            echo "   Main Category ID: " . ($product->AnaKategoriID ?? 'N/A') . "\n";
            echo "   Main Category: " . ($product->AnaKategori ?? 'N/A') . "\n";
            echo "   Brand ID: " . ($product->MarkaID ?? 'N/A') . "\n";
            echo "   Brand: " . ($product->Marka ?? 'N/A') . "\n";
            echo "   Active: " . ($product->Aktif ? 'Yes' : 'No') . "\n";
            echo "   Showcase: " . ($product->Vitrin ? 'Yes' : 'No') . "\n";
            echo "   Featured Product: " . ($product->FirsatUrunu ? 'Yes' : 'No') . "\n";
            echo "   New Product: " . ($product->YeniUrun ? 'Yes' : 'No') . "\n";
            echo "   Show in List: " . ($product->ListedeGoster ? 'Yes' : 'No') . "\n";
            echo "   Free Shipping: " . ($product->UcretsizKargo ? 'Yes' : 'No') . "\n";
            echo "   Product Type: " . ($product->UrunTipi ?? 'N/A') . "\n";
            echo "   Sort Order: " . ($product->Sira ?? 'N/A') . "\n";
            echo "   Sales Unit: " . ($product->SatisBirimi ?? 'N/A') . "\n";
            
            // Clean HTML tags and limit character count
            $description = strip_tags($product->Aciklama ?? '');
            $summary = strip_tags($product->OnYazi ?? '');
            
            // 200 character limit for description
            if (strlen($description) > 200) {
                $description = substr($description, 0, 200) . '...';
            }
            
            // 150 character limit for summary
            if (strlen($summary) > 150) {
                $summary = substr($summary, 0, 150) . '...';
            }
            
            echo "   Description (Cleaned): " . ($description ?: 'N/A') . "\n";
            echo "   Summary (Cleaned): " . ($summary ?: 'N/A') . "\n";
            echo "   Creation Date: " . ($product->EklemeTarihi ?? 'N/A') . "\n";
            echo "   Publication Date: " . ($product->YayinTarihi ?? 'N/A') . "\n";
            
            // Show variation information
            if (isset($product->Varyasyonlar) && is_array($product->Varyasyonlar) && !empty($product->Varyasyonlar)) {
                echo "   🎨 Variations (" . count($product->Varyasyonlar) . " total):\n";
                foreach ($product->Varyasyonlar as $vIdx => $variation) {
                    echo "      " . ($vIdx + 1) . ". Variation ID: " . ($variation->ID ?? 'N/A') . "\n";
                    echo "         Stock: " . ($variation->StokAdedi ?? 'N/A') . "\n";
                    echo "         Sale Price: " . ($variation->SatisFiyati ?? 'N/A') . " TL\n";
                    echo "         Discounted Price: " . ($variation->IndirimliFiyat ?? 'N/A') . " TL\n";
                }
            } else {
                echo "   🎨 No variations\n";
            }
            echo "   ------------------------------\n";
        }
        
        echo "\n📊 ALL " . count($products) . " PRODUCTS LISTED!\n\n";
        
        $testProductId = $products[0]->ID ?? null;
    } else {
        echo "❌ Error: " . $productsResponse->getMessage() . "\n\n";
    }
    
    // Test 2: Get product count
    echo "🧪 Test 2: Product Count Check\n";
    echo "============================\n";
    
    $totalCountResponse = $productService->getProductCount();
    $totalCountData = $totalCountResponse->getData();
    echo "✅ Total Products: ";
    print_r($totalCountData);
    echo "\n";
    
    $activeCountResponse = $productService->getProductCount(['Aktif' => 1]);
    $activeCountData = $activeCountResponse->getData();
    echo "✅ Active Products: ";
    print_r($activeCountData);
    echo "\n";
    
    $vitrinCountResponse = $productService->getProductCount(['Vitrin' => 1]);
    $vitrinCountData = $vitrinCountResponse->getData();
    echo "✅ Showcase Products: ";
    print_r($vitrinCountData);
    echo "\n\n";
    
    // Test 3: Get categories with full details
    echo "🧪 Test 3: Get Categories\n";
    echo "============================\n";
    
    $categoriesResponse = $productService->getCategory();
    if ($categoriesResponse->isSuccess()) {
        $categories = $categoriesResponse->getData();
        echo "✅ Retrieved " . count($categories) . " categories:\n\n";
        
        foreach ($categories as $index => $category) {
            echo "📂 " . ($index + 1) . ". Category:\n";
            echo "   ID: " . ($category->ID ?? 'N/A') . "\n";
            echo "   Name: " . ($category->Tanim ?? 'N/A') . "\n";
            echo "   Parent Category ID: " . ($category->UstKategoriID ?? '0') . "\n";
            echo "   Active: " . ($category->Aktif ? 'Yes' : 'No') . "\n";
            echo "   Sort Order: " . ($category->Sira ?? 'N/A') . "\n";
            echo "   ------------------------------\n";
            
            if ($index >= 4) break; // Show first 5 categories
        }
        
        $testCategoryId = $categories[0]->ID ?? null;
    } else {
        echo "❌ Category error: " . $categoriesResponse->getMessage() . "\n\n";
    }
    
    // Test 4: Product variations
    echo "🧪 Test 4: Product Variations\n";
    echo "==========================\n";
    
    try {
        $variationsResponse = $productService->GetProductVariations();
        $variations = $variationsResponse->getData();
        echo "✅ Retrieved " . count($variations) . " variations:\n\n";
        
        foreach ($variations as $index => $variation) {
            echo "🎨 " . ($index + 1) . ". Variation:\n";
            echo "   ID: " . ($variation->ID ?? 'N/A') . "\n";
            echo "   Product Card ID: " . ($variation->UrunKartId ?? 'N/A') . "\n";
            echo "   Code: " . ($variation->Kodu ?? 'N/A') . "\n";
            echo "   Stock Quantity: " . ($variation->StokAdedi ?? 'N/A') . "\n";
            echo "   Sale Price: " . ($variation->SatisFiyati ?? '0') . " TL\n";
            echo "   Image: " . ($variation->Resim ?? 'N/A') . "\n";
            echo "   ------------------------------\n";
            
            if ($index >= 2) break; // Show first 3 variations
        }
        
        $testVariationId = $variations[0]->ID ?? null;
    } catch (Exception $e) {
        echo "❌ Variation error: " . $e->getMessage() . "\n\n";
    }
    
    // Test 5: Payment options
    if (isset($testVariationId) && $testVariationId) {
        echo "🧪 Test 5: Payment Options (Variation ID: $testVariationId)\n";
        echo "=========================================\n";
        
        $paymentResponse = $productService->getProductPaymentOptions($testVariationId);
        if ($paymentResponse->isSuccess()) {
            $paymentOptions = $paymentResponse->getData();
            echo "✅ Found payment options for " . count($paymentOptions) . " banks:\n\n";
            
            foreach ($paymentOptions as $index => $option) {
                echo "💳 " . ($index + 1) . ". Bank: " . ($option['bankaAdi'] ?? 'N/A') . "\n";
                echo "   Bank ID: " . ($option['bankaId'] ?? 'N/A') . "\n";
                echo "   Installment Options:\n";
                
                foreach ($option['taksitler'] as $installmentIndex => $installment) {
                    echo "      " . ($installmentIndex + 1) . ". " . ($installment['taksitSayisi'] ?? 'N/A') . " installments - ";
                    echo ($installment['taksitTutari'] ?? 'N/A') . " TL/month\n";
                    
                    if ($installmentIndex >= 2) break; // Show first 3 installment options
                }
                echo "   ------------------------------\n";
                
                if ($index >= 1) break; // Show first 2 banks
            }
        } else {
            echo "❌ No payment options for this variation\n\n";
        }
    }
    
    // Test 6: Installment options
    echo "🧪 Test 6: Installment Calculation (1000 TL)\n";
    echo "==================================\n";
    
    try {
        $installmentsResponse = $productService->GetInstallmentOptions(1000.0, 12);
        $installments = $installmentsResponse->getData();
        echo "✅ " . count($installments) . " bank options for 1000 TL:\n\n";
        
        if (count($installments) > 0) {
            foreach ($installments as $index => $bank) {
                echo "🏦 " . ($index + 1) . ". Bank:\n";
                echo "   Bank Name: " . ($bank->BankaAdi ?? 'N/A') . "\n";
                echo "   Bank ID: " . ($bank->BankaID ?? 'N/A') . "\n";
                echo "   Installment Options:\n";
                
                // Check installment options
                if (isset($bank->TaksitSecenekleri) && is_array($bank->TaksitSecenekleri)) {
                    foreach ($bank->TaksitSecenekleri as $installmentIndex => $installment) {
                        echo "      " . ($installmentIndex + 1) . ". " . ($installment->TaksitSayisi ?? 'N/A') . " installments:\n";
                        echo "         Monthly Payment: " . ($installment->TaksitTutari ?? 'N/A') . " TL\n";
                        echo "         Total Amount: " . ($installment->ToplamTutar ?? 'N/A') . " TL\n";
                        echo "         Commission: " . ($installment->Komisyon ?? 'N/A') . " TL\n";
                        
                        if ($installmentIndex >= 2) break; // Show first 3 installment options
                    }
                } else {
                    echo "      ℹ️ No installment options found for this bank\n";
                }
                echo "   ------------------------------\n";
                
                if ($index >= 1) break; // Show first 2 banks
            }
        } else {
            echo "   ℹ️ No installment options found for 1000 TL\n";
        }
    } catch (Exception $e) {
        echo "❌ Installment calculation error: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 7: Store stock information
    echo "🧪 Test 7: Store Stock Information\n";
    echo "===========================\n";
    
    try {
        $storeStockResponse = $productService->GetStoreStock('MAIN');  // Ana mağaza için 'MAIN' kodunu kullanıyoruz
        $storeStock = $storeStockResponse->getData();
        echo "✅ Retrieved store stock information:\n\n";
        
        if (!empty($storeStock)) {
            foreach ($storeStock as $index => $store) {
                echo "🏪 " . ($index + 1) . ". Store:\n";
                echo "   Store ID: " . ($store->MagazaID ?? 'N/A') . "\n";
                echo "   Store Name: " . ($store->MagazaAdi ?? 'N/A') . "\n";
                echo "   Product ID: " . ($store->UrunID ?? 'N/A') . "\n";
                echo "   Stock: " . ($store->Stok ?? 'N/A') . "\n";
                echo "   ------------------------------\n";
                
                if ($index >= 2) break; // İlk 3 mağazayı göster
            }
        } else {
            echo "   ℹ️ No store stock information found\n";
        }
    } catch (Exception $e) {
        echo "❌ Store stock error: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 8: Product reviews
    echo "🧪 Test 8: Product Reviews\n";
    echo "=======================\n";
    
    if (isset($testProductId) && $testProductId) {
        try {
            $reviewsResponse = $productService->GetProductReviews($testProductId);
            $reviews = $reviewsResponse->getData();
            echo "✅ Retrieved reviews for product ID $testProductId:\n\n";
            
            if (!empty($reviews)) {
                foreach ($reviews as $index => $review) {
                    echo "💬 " . ($index + 1) . ". Review:\n";
                    echo "   Review ID: " . ($review->ID ?? 'N/A') . "\n";
                    echo "   User: " . ($review->KullaniciAdi ?? 'N/A') . "\n";
                    echo "   Rating: " . ($review->Puan ?? 'N/A') . "/5\n";
                    echo "   Comment: " . ($review->Yorum ?? 'N/A') . "\n";
                    echo "   Date: " . ($review->Tarih ?? 'N/A') . "\n";
                    echo "   ------------------------------\n";
                    
                    if ($index >= 2) break; // Show first 3 reviews
                }
            } else {
                echo "   ℹ️ No reviews found for this product\n";
            }
        } catch (Exception $e) {
            echo "❌ Reviews error: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
    
    // Test 9: Stock quantity update
    echo "🧪 Test 9: Stock Quantity Update\n";
    echo "============================\n";
    
    if (isset($testProductId) && isset($products[0]->Varyasyonlar[0])) {
        try {
            // Mevcut varyasyon ve stok bilgisini al
            $currentVariation = $products[0]->Varyasyonlar[0];
            $currentStockAmount = $currentVariation->StokAdedi;
            $variationId = $currentVariation->ID;
            
            echo "ℹ️ Testing with:\n";
            echo "   Product: " . $products[0]->UrunAdi . "\n";
            echo "   Variation ID: " . $variationId . "\n";
            echo "   Current Stock: " . $currentStockAmount . "\n\n";
            
            // Aynı stok miktarıyla güncelleme yap (değişiklik olmasın)
            $variations = [
                [
                    'ID' => $variationId,
                    'StokAdedi' => $currentStockAmount
                ]
            ];
            
            $updateResponse = $productService->UpdateStockQuantity($variations);
            if ($updateResponse->isSuccess()) {
                echo "✅ Stock quantity update test successful\n";
                echo "   Variation ID: $variationId\n";
                echo "   Stock Amount: $currentStockAmount (unchanged)\n";
                $data = $updateResponse->getData();
                if (isset($data['updatedCount'])) {
                    echo "   Updated variations: " . $data['updatedCount'] . "\n";
                }
            } else {
                echo "❌ Could not update stock quantity\n";
                echo "   Error: " . $updateResponse->getMessage() . "\n";
            }
        } catch (Exception $e) {
            echo "❌ Stock update error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "ℹ️ No test product or variation available for stock update test\n";
    }
    echo "\n";
    
    // Test Results Summary
    echo "🏁 ProductService Function Tests Completed!\n";
    echo "================================================\n";
    echo "✅ Tested functions:\n";
    echo "   1. getProducts() - Get products\n";
    echo "   2. getProductCount() - Product count\n";
    echo "   3. getCategory() - Categories\n";
    echo "   4. GetProductVariations() - Variations\n";
    echo "   5. getProductPaymentOptions() - Payment options\n";
    echo "   6. GetInstallmentOptions() - Installment options\n";
    echo "   7. GetStoreStock() - Store stock information\n";
    echo "   8. GetProductReviews() - Product reviews\n";
    echo "   9. UpdateStockQuantity() - Stock update\n";
    echo "\n📊 Total 9/9 functions tested!\n";
    
} catch (Exception $e) {
    echo "💥 ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test Process Completed ===\n"; 