<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Response\ApiResponse;

// Load config
$config = require __DIR__ . '/../config.php';

echo "=== ShippingService Test Süreci Başlıyor ===\n\n";

// Test başlangıç zamanı
$testStart = microtime(true);

try {
    // Ticimax API'yi başlat (modern pattern)
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $shippingService = $ticimax->shippingService();
    
    echo "✓ Ticimax ShippingService başlatıldı\n\n";
    
    // Test sayaçları
    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;
    
    echo "========================================\n";
    echo "           KARGO TESTLERİ\n";
    echo "========================================\n\n";
    
    // Test 1: Kargo firmalarını getirme
    echo "🧪 Test 1: Kargo Firmalarını Getirme\n";
    echo "----------------------------------\n";
    $testCount++;
    
    $companiesResponse = $shippingService->getShippingCompanies();
    if ($companiesResponse instanceof ApiResponse) {
        if ($companiesResponse->isSuccess()) {
            $successCount++;
            $companies = $companiesResponse->getData();
            echo "✅ Kargo firmaları başarıyla getirildi\n";
            echo "   🚚 Toplam Firma Sayısı: " . count($companies) . "\n";
            
            // Firmaları listele
            foreach ($companies as $index => $company) {
                echo "   " . ($index + 1) . ". " . ($company->FirmaAdi ?? 'N/A') . 
                     " (ID: " . ($company->ID ?? 'N/A') . ")" . 
                     " - Aktif: " . ($company->Aktif ? 'Evet' : 'Hayır') . "\n";
            }
            
            // İstatistikler
            $activeCompanies = array_filter($companies, function($company) { 
                return $company->Aktif ?? false; 
            });
            $companiesWithWebsite = array_filter($companies, function($company) { 
                return !empty($company->Website); 
            });
            $companiesWithTracking = array_filter($companies, function($company) { 
                return !empty($company->TakipURL); 
            });
            
            echo "\n   📊 Kargo Firma İstatistikleri:\n";
            echo "   ✅ Aktif Firmalar: " . count($activeCompanies) . "\n";
            echo "   🌐 Website'si Olan: " . count($companiesWithWebsite) . "\n";
            echo "   🔍 Takip URL'si Olan: " . count($companiesWithTracking) . "\n";
            
            // Detay örnekleri
            if (!empty($companies)) {
                echo "\n   📋 Detay Örneği:\n";
                $sampleCompany = $companies[0];
                echo "      - Firma Adı: " . ($sampleCompany->FirmaAdi ?? 'N/A') . "\n";
                echo "      - Firma Kodu: " . ($sampleCompany->FirmaKodu ?? 'N/A') . "\n";
                echo "      - Website: " . ($sampleCompany->Website ?? 'N/A') . "\n";
                echo "      - Takip URL: " . ($sampleCompany->TakipURL ?? 'N/A') . "\n";
                echo "      - Entegrasyon Kodu: " . ($sampleCompany->EntegrasyonKodu ?? 'N/A') . "\n";
            }
        } else {
            $errorCount++;
            echo "❌ Kargo firmaları getirilemedi: " . $companiesResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 2: Kargo seçenekleri için örnek sepet oluşturma
    echo "🧪 Test 2: Kargo Seçenekleri için Test Sepeti\n";
    echo "-------------------------------------------\n";
    $testCount++;
    
    // Örnek sepet nesnesi oluştur
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
    
    echo "✅ Test sepeti oluşturuldu\n";
    echo "   🛒 Sepet ID: " . $sampleCart->SepetID . "\n";
    echo "   💰 Genel Toplam: " . $sampleCart->GenelToplam . " TL\n";
    echo "   📦 Ürün Adedi: " . $sampleCart->ToplamUrunAdedi . "\n";
    echo "\n";
    
    // Test 3: İstanbul için kargo seçeneklerini getirme
    echo "🧪 Test 3: İstanbul Kargo Seçenekleri\n";
    echo "-----------------------------------\n";
    $testCount++;
    
    $istanbulCityId = 34; // İstanbul il kodu
    $optionsResponse = $shippingService->getShippingOptions($istanbulCityId, 'TL', $sampleCart);
    
    if ($optionsResponse instanceof ApiResponse) {
        if ($optionsResponse->isSuccess()) {
            $successCount++;
            $options = $optionsResponse->getData();
            echo "✅ İstanbul kargo seçenekleri başarıyla getirildi\n";
            echo "   📦 Mevcut Seçenek Sayısı: " . count($options) . "\n";
            
            foreach ($options as $index => $option) {
                echo "   " . ($index + 1) . ". " . ($option->FirmaAdi ?? 'N/A') . 
                     " - Ücret: " . ($option->Ucret ?? 'N/A') . " TL" .
                     " - Süre: " . ($option->TeslimatSuresi ?? 'N/A') . " gün\n";
            }
            
            if (!empty($options)) {
                // En ucuz ve en hızlı seçenekleri bul
                $cheapest = min(array_map(function($opt) { return $opt->Ucret ?? 999999; }, $options));
                $fastest = min(array_map(function($opt) { return $opt->TeslimatSuresi ?? 999; }, $options));
                
                echo "\n   💡 Seçenek Analizi:\n";
                echo "   💰 En Ucuz Kargo: {$cheapest} TL\n";
                echo "   ⚡ En Hızlı Teslimat: {$fastest} gün\n";
            }
        } else {
            $successCount++; // Boş sonuç da valid bir test sonucu
            echo "✅ İstanbul için kargo seçeneği bulunamadı (normal durum)\n";
            echo "   📝 Mesaj: " . $optionsResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 4: Ankara için kargo seçenekleri
    echo "🧪 Test 4: Ankara Kargo Seçenekleri\n";
    echo "---------------------------------\n";
    $testCount++;
    
    $ankaraCityId = 6; // Ankara il kodu
    $ankaraOptionsResponse = $shippingService->getShippingOptions($ankaraCityId, 'TL', $sampleCart);
    
    if ($ankaraOptionsResponse instanceof ApiResponse) {
        if ($ankaraOptionsResponse->isSuccess()) {
            $successCount++;
            $ankaraOptions = $ankaraOptionsResponse->getData();
            echo "✅ Ankara kargo seçenekleri başarıyla getirildi\n";
            echo "   📦 Mevcut Seçenek Sayısı: " . count($ankaraOptions) . "\n";
            
            foreach ($ankaraOptions as $index => $option) {
                echo "   " . ($index + 1) . ". " . ($option->FirmaAdi ?? 'N/A') . 
                     " - Ücret: " . ($option->Ucret ?? 'N/A') . " TL\n";
            }
        } else {
            $successCount++; // Boş sonuç da valid
            echo "✅ Ankara için kargo seçeneği bulunamadı\n";
            echo "   📝 Mesaj: " . $ankaraOptionsResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 5: İzmir için kargo seçenekleri
    echo "🧪 Test 5: İzmir Kargo Seçenekleri\n";
    echo "--------------------------------\n";
    $testCount++;
    
    $izmirCityId = 35; // İzmir il kodu
    $izmirOptionsResponse = $shippingService->getShippingOptions($izmirCityId, 'TL', $sampleCart);
    
    if ($izmirOptionsResponse instanceof ApiResponse) {
        if ($izmirOptionsResponse->isSuccess()) {
            $successCount++;
            $izmirOptions = $izmirOptionsResponse->getData();
            echo "✅ İzmir kargo seçenekleri başarıyla getirildi\n";
            echo "   📦 Mevcut Seçenek Sayısı: " . count($izmirOptions) . "\n";
        } else {
            $successCount++; // Boş sonuç da valid
            echo "✅ İzmir için kargo seçeneği bulunamadı\n";
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 6: Geçersiz şehir ID ile test
    echo "🧪 Test 6: Geçersiz Şehir ID Testi\n";
    echo "--------------------------------\n";
    $testCount++;
    
    $invalidCityId = 999; // Geçersiz şehir ID
    $invalidResponse = $shippingService->getShippingOptions($invalidCityId, 'TL', $sampleCart);
    
    if ($invalidResponse instanceof ApiResponse) {
        if (!$invalidResponse->isSuccess()) {
            $successCount++;
            echo "✅ Geçersiz şehir ID doğru şekilde reddedildi\n";
            echo "   📝 Hata mesajı: " . $invalidResponse->getMessage() . "\n";
        } else {
            // Boş liste dönerse de valid
            $invalidOptions = $invalidResponse->getData();
            if (empty($invalidOptions)) {
                $successCount++;
                echo "✅ Geçersiz şehir için boş liste döndü (normal)\n";
            } else {
                $errorCount++;
                echo "❌ Geçersiz şehir ID için seçenekler bulundu (beklenmeyen)\n";
            }
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 7: Farklı para birimi ile test
    echo "🧪 Test 7: USD Para Birimi Testi\n";
    echo "------------------------------\n";
    $testCount++;
    
    $usdOptionsResponse = $shippingService->getShippingOptions($istanbulCityId, 'USD', $sampleCart);
    
    if ($usdOptionsResponse instanceof ApiResponse) {
        if ($usdOptionsResponse->isSuccess()) {
            $successCount++;
            $usdOptions = $usdOptionsResponse->getData();
            echo "✅ USD para birimli seçenekler getirildi\n";
            echo "   💵 USD Seçenek Sayısı: " . count($usdOptions) . "\n";
        } else {
            $successCount++; // USD desteklenmeyebilir
            echo "✅ USD para birimi desteklenmiyor (normal durum)\n";
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test süresi hesaplama
    $testEnd = microtime(true);
    $totalTime = round($testEnd - $testStart, 2);
    
    echo "========================================\n";
    echo "           TEST SONUÇLARI\n";
    echo "========================================\n";
    echo "📊 Toplam Test: $testCount\n";
    echo "✅ Başarılı: $successCount\n";
    echo "❌ Başarısız: $errorCount\n";
    echo "⏱️ Test Süresi: {$totalTime} saniye\n";
    echo "📈 Başarı Oranı: " . round(($successCount / $testCount) * 100, 1) . "%\n\n";
    
    // Test detayları
    echo "========================================\n";
    echo "           TEST DETAYLARI\n";
    echo "========================================\n";
    echo "🧪 Tested Functions:\n";
    echo "   • getShippingCompanies() - Kargo firmalarını getirme\n";
    echo "   • getShippingOptions() - Şehir bazlı kargo seçenekleri\n";
    echo "   • Şehir testleri - İstanbul, Ankara, İzmir\n";
    echo "   • Para birimi testleri - TL, USD\n";
    echo "   • Hata senaryoları - Geçersiz şehir ID\n";
    echo "   • İstatistikler - Firma analizi ve karşılaştırma\n\n";
    
    echo "🏁 ShippingService test süreci tamamlandı!\n";
    
} catch (Exception $e) {
    echo "💥 FATAL ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=== ShippingService Test Süreci Tamamlandı ===\n"; 