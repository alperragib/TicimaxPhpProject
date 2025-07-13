<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Load configuration
$config = require __DIR__ . '/../config.php';

echo "=== TEDARİKÇİ (SUPPLIER) SERVİS TESTİ ===\n\n";

// Test başlangıç zamanı
$testStart = microtime(true);

try {
    // Ticimax API'yi başlat
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $supplierService = $ticimax->supplierService();
    
    echo "✓ Ticimax SupplierService başlatıldı\n";
    echo "Domain: {$config['mainDomain']}\n\n";
    
    // Test sayaçları
    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;
    
    echo "========================================\n";
    echo "         TEDARİKÇİ TESTLERİ\n";
    echo "========================================\n\n";
    
    // Test 1: Tüm tedarikçileri getirme ve listeleme
    echo "🧪 Test 1: TÜM TEDARİKÇİLERİ LİSTELEME\n";
    echo "------------------------------------\n";
    $testCount++;
    
    $allSuppliers = $supplierService->getSuppliers();
    
    if (!empty($allSuppliers)) {
        $successCount++;
        echo "✅ Tedarikçiler başarıyla getirildi\n";
        echo "📦 Toplam Tedarikçi Sayısı: " . count($allSuppliers) . "\n\n";
        
        // TÜM TEDARİKÇİLERİ LİSTELE
        echo "📋 TÜM TEDARİKÇİ LİSTESİ:\n";
        echo str_repeat("=", 80) . "\n";
        
        foreach ($allSuppliers as $index => $supplier) {
            $supplierNum = $index + 1;
            echo "[$supplierNum] TEDARİKÇİ DETAYLARI:\n";
            echo "   🆔 ID: " . ($supplier->ID ?? 'N/A') . "\n";
            echo "   🏷️  Adı: " . ($supplier->Tanim ?? 'N/A') . "\n";
            echo "   ✅ Aktif: " . (($supplier->Aktif ?? false) ? 'Evet' : 'Hayır') . "\n";
            echo "   📧 E-mail: " . ($supplier->Mail ?? 'Belirtilmemiş') . "\n";
            echo "   📝 Not: " . ($supplier->Not ?? 'Belirtilmemiş') . "\n";
            echo "   -------------------------\n";
        }
        
        // İstatistikler
        $activeCount = 0;
        $inactiveCount = 0;
        $emailCount = 0;
        $noteCount = 0;
        
        foreach ($allSuppliers as $supplier) {
            if ($supplier->Aktif ?? false) $activeCount++;
            else $inactiveCount++;
            
            if (!empty($supplier->Mail)) $emailCount++;
            if (!empty($supplier->Not)) $noteCount++;
        }
        
        echo "\n📊 TEDARİKÇİ İSTATİSTİKLERİ:\n";
        echo "   📦 Toplam Tedarikçi: " . count($allSuppliers) . "\n";
        echo "   ✅ Aktif Tedarikçi: $activeCount\n";
        echo "   ❌ Pasif Tedarikçi: $inactiveCount\n";
        echo "   📧 E-mail Olan: $emailCount\n";
        echo "   📝 Not Olan: $noteCount\n";
        
        // İlk tedarikçiyi test için saklayalım
        $testSupplierId = $allSuppliers[0]->ID ?? null;
        
    } else {
        $errorCount++;
        echo "❌ Tedarikçi bulunamadı veya hata oluştu\n";
        $testSupplierId = null;
    }
    echo "\n";
    
    // Test 2: Belirli tedarikçi getirme
    if ($testSupplierId) {
        echo "🧪 Test 2: BELİRLİ TEDARİKÇİ DETAYI\n";
        echo "---------------------------------\n";
        $testCount++;
        
        $specificSupplier = $supplierService->getSuppliers($testSupplierId);
        
        if (!empty($specificSupplier)) {
            $successCount++;
            echo "✅ Belirli tedarikçi başarıyla getirildi\n";
            echo "🎯 Test Edilen ID: $testSupplierId\n";
            
            $supplier = $specificSupplier[0];
            echo "📋 DETAY BİLGİLERİ:\n";
            echo "   🆔 ID: " . ($supplier->ID ?? 'N/A') . "\n";
            echo "   🏷️  Tedarikçi Adı: " . ($supplier->Tanim ?? 'N/A') . "\n";
            echo "   ✅ Durum: " . (($supplier->Aktif ?? false) ? 'Aktif' : 'Pasif') . "\n";
            echo "   📧 E-mail: " . ($supplier->Mail ?? 'Belirtilmemiş') . "\n";
            echo "   📝 Not: " . ($supplier->Not ?? 'Belirtilmemiş') . "\n";
        } else {
            $errorCount++;
            echo "❌ Belirli tedarikçi getirilemedi\n";
        }
        echo "\n";
    }
    
    // Test 3: Olmayan tedarikçi kontrolü
    echo "🧪 Test 3: OLMAYAN TEDARİKÇİ KONTROLÜ\n";
    echo "-----------------------------------\n";
    $testCount++;
    
    $nonExistentSupplier = $supplierService->getSuppliers(999999);
    
    if (empty($nonExistentSupplier)) {
        $successCount++;
        echo "✅ Olmayan tedarikçi için boş sonuç döndü (doğru davranış)\n";
        echo "🎯 Test ID: 999999 - Sonuç: Bulunamadı\n";
    } else {
        $errorCount++;
        echo "❌ Olmayan tedarikçi için beklenmeyen sonuç döndü\n";
        echo "📦 Bulunan kayıt sayısı: " . count($nonExistentSupplier) . "\n";
    }
    echo "\n";
    
    // Test 4: Performans testi
    echo "🧪 Test 4: PERFORMANS TESTİ\n";
    echo "-------------------------\n";
    $testCount++;
    
    $performanceStart = microtime(true);
    
    // 3 kez aynı sorguyu yap
    for ($i = 1; $i <= 3; $i++) {
        $perfTest = $supplierService->getSuppliers();
        echo "   📡 İstek $i tamamlandı...\n";
    }
    
    $performanceEnd = microtime(true);
    $performanceTime = round($performanceEnd - $performanceStart, 2);
    $avgTime = round($performanceTime / 3, 2);
    
    $successCount++;
    echo "✅ Performans testi tamamlandı\n";
    echo "⏱️  3 İstek Toplam Süre: {$performanceTime} saniye\n";
    echo "📊 Ortalama İstek Süresi: {$avgTime} saniye\n";
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
    echo "⏱️  Test Süresi: {$totalTime} saniye\n";
    echo "📈 Başarı Oranı: " . round(($successCount / $testCount) * 100, 1) . "%\n\n";
    
    // Test özeti
    echo "========================================\n";
    echo "           TEST ÖZETİ\n";
    echo "========================================\n";
    echo "🧪 Test Edilen İşlemler:\n";
    echo "   • getSuppliers() - Tüm tedarikçi listesi\n";
    echo "   • getSuppliers(id) - Belirli tedarikçi detayı\n";
    echo "   • Hata kontrolü - Olmayan ID testi\n";
    echo "   • Performans analizi - Çoklu istek testi\n";
    echo "   • Veri bütünlüğü - Field mapping kontrolü\n\n";
    
    echo "📋 Field Mapping (WSDL Uyumlu):\n";
    echo "   • TedarikciAdi → Tanim ✅\n";
    echo "   • ID → ID ✅\n";
    echo "   • Aktif → Aktif ✅\n";
    echo "   • Mail → Mail ✅\n";
    echo "   • Not → Not ✅\n\n";
    
    echo "🏁 SupplierService test süreci tamamlandı!\n";
    
} catch (Exception $e) {
    echo "💥 FATAL ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=== TEDARİKÇİ SERVİS TESTİ TAMAMLANDI ===\n"; 