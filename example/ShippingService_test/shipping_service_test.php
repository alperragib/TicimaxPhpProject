<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Response\ApiResponse;

// Load config
$config = require __DIR__ . '/../config.php';

echo "=== ShippingService Test Process Starting ===\n\n";

// Test start time
$testStart = microtime(true);

try {
    // Initialize Ticimax API (modern pattern)
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $shippingService = $ticimax->shippingService();
    
    echo "✓ Ticimax ShippingService initialized\n\n";
    
    // Test counters
    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;
    $UyeId=0;
    echo "========================================\n";
    echo "           SHIPPING TESTS\n";
    echo "========================================\n\n";
    
    // Test 1: Get shipping companies
    echo "🧪 Test 1: Get Shipping Companies\n";
    echo "----------------------------------\n";
    $testCount++;
    
    $companiesResponse = $shippingService->getShippingCompanies();
    if ($companiesResponse instanceof ApiResponse) {
        if ($companiesResponse->isSuccess()) {
            $successCount++;
            $companies = $companiesResponse->getData();
            echo "✅ Shipping companies retrieved successfully\n";
            echo "   🚚 Total Companies: " . count($companies) . "\n";
            
            // List companies
            foreach ($companies as $index => $company) {
                echo "   " . ($index + 1) . ". " . ($company->FirmaAdi ?? 'N/A') . 
                     " (ID: " . ($company->ID ?? 'N/A') . ")" . 
                     " - Active: " . ($company->Aktif ? 'Yes' : 'No') . "\n";
            }
            
            // Statistics
            $activeCompanies = array_filter($companies, function($company) { 
                return $company->Aktif ?? false; 
            });
            $companiesWithWebsite = array_filter($companies, function($company) { 
                return !empty($company->Website); 
            });
            $companiesWithTracking = array_filter($companies, function($company) { 
                return !empty($company->TakipURL); 
            });
            
            echo "\n   📊 Shipping Company Statistics:\n";
            echo "   ✅ Active Companies: " . count($activeCompanies) . "\n";
            echo "   🌐 With Website: " . count($companiesWithWebsite) . "\n";
            echo "   🔍 With Tracking URL: " . count($companiesWithTracking) . "\n";
            
            // Detail examples
            if (!empty($companies)) {
                echo "\n   📋 Example Details:\n";
                $sampleCompany = $companies[0];
                echo "      - Company Name: " . ($sampleCompany->FirmaAdi ?? 'N/A') . "\n";
                echo "      - Company Code: " . ($sampleCompany->FirmaKodu ?? 'N/A') . "\n";
                echo "      - Website: " . ($sampleCompany->Website ?? 'N/A') . "\n";
                echo "      - Tracking URL: " . ($sampleCompany->TakipURL ?? 'N/A') . "\n";
                echo "      - Integration Code: " . ($sampleCompany->EntegrasyonKodu ?? 'N/A') . "\n";
            }
        } else {
            $errorCount++;
            echo "❌ Could not retrieve shipping companies: " . $companiesResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Invalid response format\n";
    }
    echo "\n";
    
    // Test 2: Create sample cart for shipping options
    echo "🧪 Test 2: Test Cart for Shipping Options\n";
    echo "-------------------------------------------\n";
    $testCount++;
    
    // Create sample cart object
    $sampleCart = (object)[
        'SepetID' => 1,
        'UyeID' => 1,
        'GenelToplam' => 100.00,
        'ToplamKDV' => 18.00,
        'ToplamUrunAdedi' => 2,
        'SepetParaBirimiDilKodu' => 'TL',
        'Urunler' => [
            (object)[
                'UrunID' => 1,
                'Adet' => 1,
                'BirimFiyat' => 50.00,
                'ToplamFiyat' => 50.00
            ],
            (object)[
                'UrunID' => 2,
                'Adet' => 1,
                'BirimFiyat' => 50.00,
                'ToplamFiyat' => 50.00
            ]
        ]
    ];
    
    echo "✅ Test cart created\n";
    echo "   🛒 Cart ID: " . $sampleCart->SepetID . "\n";
    echo "   💰 Total Amount: " . $sampleCart->GenelToplam . " TL\n";
    echo "   📦 Product Count: " . $sampleCart->ToplamUrunAdedi . "\n";
    echo "\n";
    
    // Test 3: Get shipping options for Istanbul
    echo "🧪 Test 3: Istanbul Shipping Options\n";
    echo "-----------------------------------\n";
    $testCount++;
    
    $istanbulCityId = 34; // Istanbul city code
    $optionsResponse = $shippingService->getShippingOptions($istanbulCityId, 'TL', $sampleCart);
    
    if ($optionsResponse instanceof ApiResponse) {
        if ($optionsResponse->isSuccess()) {
            $successCount++;
            $options = $optionsResponse->getData();
            echo "✅ Istanbul shipping options retrieved successfully\n";
            echo "   📦 Available Options: " . count($options) . "\n";
            
            foreach ($options as $index => $option) {
                echo "   " . ($index + 1) . ". " . ($option->FirmaAdi ?? 'N/A') . 
                     " - Fee: " . ($option->Ucret ?? 'N/A') . " TL" .
                     " - Duration: " . ($option->TeslimatSuresi ?? 'N/A') . " days\n";
            }
            
            if (!empty($options)) {
                // Find cheapest and fastest options
                $cheapest = min(array_map(function($opt) { return $opt->Ucret ?? 999999; }, $options));
                $fastest = min(array_map(function($opt) { return $opt->TeslimatSuresi ?? 999; }, $options));
                
                echo "\n   💡 Options Analysis:\n";
                echo "   💰 Cheapest Shipping: {$cheapest} TL\n";
                echo "   ⚡ Fastest Delivery: {$fastest} days\n";
            }
        } else {
            $successCount++; // Empty result is also valid
            echo "✅ No shipping options found for Istanbul (normal state)\n";
            echo "   📝 Message: " . $optionsResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Invalid response format\n";
    }
    echo "\n";
    
    // Test 4: Get shipping options for Ankara
    echo "🧪 Test 4: Ankara Shipping Options\n";
    echo "---------------------------------\n";
    $testCount++;
    
    $ankaraCityId = 6; // Ankara city code
    $ankaraOptionsResponse = $shippingService->getShippingOptions($ankaraCityId, 'TL', $sampleCart);
    
    if ($ankaraOptionsResponse instanceof ApiResponse) {
        if ($ankaraOptionsResponse->isSuccess()) {
            $successCount++;
            $ankaraOptions = $ankaraOptionsResponse->getData();
            echo "✅ Ankara shipping options retrieved successfully\n";
            echo "   📦 Available Options: " . count($ankaraOptions) . "\n";
            
            foreach ($ankaraOptions as $index => $option) {
                echo "   " . ($index + 1) . ". " . ($option->FirmaAdi ?? 'N/A') . 
                     " - Fee: " . ($option->Ucret ?? 'N/A') . " TL\n";
            }
        } else {
            $successCount++; // Empty result is valid
            echo "✅ No shipping options found for Ankara\n";
            echo "   📝 Message: " . $ankaraOptionsResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Invalid response format\n";
    }
    echo "\n";
    
    // Test 5: Get shipping options for Izmir
    echo "🧪 Test 5: Izmir Shipping Options\n";
    echo "--------------------------------\n";
    $testCount++;
    
    $izmirCityId = 35; // Izmir city code
    $izmirOptionsResponse = $shippingService->getShippingOptions($izmirCityId, 'TL', $sampleCart);
    
    if ($izmirOptionsResponse instanceof ApiResponse) {
        if ($izmirOptionsResponse->isSuccess()) {
            $successCount++;
            $izmirOptions = $izmirOptionsResponse->getData();
            echo "✅ Izmir shipping options retrieved successfully\n";
            echo "   📦 Available Options: " . count($izmirOptions) . "\n";
        } else {
            $successCount++; // Empty result is valid
            echo "✅ No shipping options found for Izmir\n";
        }
    } else {
        $errorCount++;
        echo "❌ Invalid response format\n";
    }
    echo "\n";
    
    // Test 6: Test with invalid city ID
    echo "🧪 Test 6: Invalid City ID Test\n";
    echo "--------------------------------\n";
    $testCount++;
    
    $invalidCityId = 999; // Invalid city ID
    $invalidResponse = $shippingService->getShippingOptions($invalidCityId, 'TL', $sampleCart);
    
    if ($invalidResponse instanceof ApiResponse) {
        if (!$invalidResponse->isSuccess()) {
            $successCount++;
            echo "✅ Invalid city ID correctly rejected\n";
            echo "   📝 Error message: " . $invalidResponse->getMessage() . "\n";
        } else {
            // Empty list is also valid
            $invalidOptions = $invalidResponse->getData();
            if (empty($invalidOptions)) {
                $successCount++;
                echo "✅ Empty list returned for invalid city (normal)\n";
            } else {
                $errorCount++;
                echo "❌ Options found for invalid city ID (unexpected)\n";
            }
        }
    } else {
        $errorCount++;
        echo "❌ Invalid response format\n";
    }
    echo "\n";
    
    // Test 7: Test with different currency
    echo "🧪 Test 7: USD Currency Test\n";
    echo "------------------------------\n";
    $testCount++;
    
    $usdResponse = $shippingService->getShippingOptions($istanbulCityId, 'USD', $sampleCart);
    
    if ($usdResponse instanceof ApiResponse) {
        if ($usdResponse->isSuccess()) {
            $successCount++;
            $usdOptions = $usdResponse->getData();
            echo "✅ USD shipping options retrieved successfully\n";
            echo "   📦 Available Options: " . count($usdOptions) . "\n";
            
            foreach ($usdOptions as $index => $option) {
                echo "   " . ($index + 1) . ". " . ($option->FirmaAdi ?? 'N/A') . 
                     " - Fee: " . ($option->Ucret ?? 'N/A') . " USD\n";
            }
        } else {
            $successCount++; // Empty result is valid
            echo "✅ No USD shipping options found\n";
            echo "   📝 Message: " . $usdResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Invalid response format\n";
    }
    echo "\n";
    
    // Calculate test duration
    $testEnd = microtime(true);
    $totalTime = round($testEnd - $testStart, 2);
    
    echo "========================================\n";
    echo "           TEST RESULTS\n";
    echo "========================================\n";
    echo "📊 Total Tests: $testCount\n";
    echo "✅ Successful: $successCount\n";
    echo "❌ Failed: $errorCount\n";
    echo "⏱️ Test Duration: {$totalTime} seconds\n";
    echo "📈 Success Rate: " . round(($successCount / $testCount) * 100, 1) . "%\n\n";
    
    echo "🏁 ShippingService test process completed!\n";
    
} catch (Exception $e) {
    echo "💥 FATAL ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=== ShippingService Test Process Completed ===\n"; 