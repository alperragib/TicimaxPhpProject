<?php

require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;

echo "=== CustomService Test Süreci Başlıyor ===\n\n";

// Test başlangıç zamanı
$testStart = microtime(true);

try {
    // Ticimax API'yi başlat
    $ticimax = new Ticimax($uyeKodu, $kullaniciAdi, $sifre);
    
    echo "✓ Ticimax başlatıldı\n\n";
    
    // Test sayaçları
    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;
    
    echo "========================================\n";
    echo "        CUSTOM SERVİS TESTLERİ\n";
    echo "========================================\n\n";
    
    // Test 1: CustomService kontrolü
    echo "🧪 Test 1: CustomService Varlık Kontrolü\n";
    echo "--------------------------------------\n";
    $testCount++;
    
    try {
        // CustomService metodlarını kontrol et
        if (method_exists($ticimax, 'CustomService')) {
            $customService = $ticimax->CustomService();
            $successCount++;
            echo "✅ CustomService başarıyla yüklendi\n";
            echo "   🔧 Servis Tipi: " . get_class($customService) . "\n";
        } else {
            $errorCount++;
            echo "❌ CustomService metodu bulunamadı\n";
        }
    } catch (Exception $e) {
        $errorCount++;
        echo "❌ CustomService hatası: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 2: Reflection ile metod kontrolü
    echo "🧪 Test 2: Mevcut Metodları Keşfetme\n";
    echo "----------------------------------\n";
    $testCount++;
    
    try {
        $availableMethods = get_class_methods($ticimax);
        $customMethods = array_filter($availableMethods, function($method) {
            return strpos(strtolower($method), 'custom') !== false;
        });
        
        $successCount++;
        echo "✅ Metod kontrolü tamamlandı\n";
        echo "   📊 Toplam Ticimax Metod: " . count($availableMethods) . "\n";
        echo "   🔍 Custom İçeren Metodlar: " . count($customMethods) . "\n";
        
        if (!empty($customMethods)) {
            foreach ($customMethods as $method) {
                echo "   • $method\n";
            }
        }
    } catch (Exception $e) {
        $errorCount++;
        echo "❌ Reflection hatası: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 3: Servis klasörü kontrolü
    echo "🧪 Test 3: Servis Dosyası Kontrolü\n";
    echo "--------------------------------\n";
    $testCount++;
    
    $customServicePath = __DIR__ . '/../../src/Service/Custom/CustomService.php';
    if (file_exists($customServicePath)) {
        $successCount++;
        echo "✅ CustomService dosyası mevcut\n";
        echo "   📁 Dosya Yolu: $customServicePath\n";
        echo "   📏 Dosya Boyutu: " . number_format(filesize($customServicePath)) . " bytes\n";
    } else {
        $errorCount++;
        echo "❌ CustomService dosyası bulunamadı\n";
        echo "   📍 Aranan Yol: $customServicePath\n";
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
    echo "           NOTLAR\n";
    echo "========================================\n";
    echo "🔍 CustomService henüz implement edilmemiş görünüyor.\n";
    echo "📝 Bu normal bir durumdur - ihtiyaç halinde eklenebilir.\n";
    echo "🏁 CustomService test süreci tamamlandı!\n";
    
} catch (Exception $e) {
    echo "💥 FATAL ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=== CustomService Test Süreci Tamamlandı ===\n"; 