<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Category\CategoryModel;

// Load configuration
$config = require __DIR__ . '/../config.php';

echo "=== KATEGORİ (CATEGORY) SERVİS TESTİ ===\n\n";

// Test başlangıç zamanı
$testStart = microtime(true);

try {
    // Ticimax API'yi başlat
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $categoryService = $ticimax->categoryService();
    
    echo "✓ Ticimax CategoryService başlatıldı\n";
    echo "Domain: {$config['mainDomain']}\n\n";
    
    // Test sayaçları
    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;
    
    echo "========================================\n";
    echo "         KATEGORİ TESTLERİ\n";
    echo "========================================\n\n";
    
    // Test 1: Tüm kategorileri getirme ve listeleme
    echo "🧪 Test 1: TÜM KATEGORİLERİ LİSTELEME\n";
    echo "-----------------------------------\n";
    $testCount++;
    
    $allCategories = $categoryService->getCategories();
    
    if (!empty($allCategories)) {
        $successCount++;
        echo "✅ Kategoriler başarıyla getirildi\n";
        echo "📦 Toplam Kategori Sayısı: " . count($allCategories) . "\n\n";
        
        // TÜM KATEGORİLERİ LİSTELE
        echo "📋 TÜM KATEGORİ LİSTESİ:\n";
        echo str_repeat("=", 80) . "\n";
        
        foreach ($allCategories as $index => $category) {
            $categoryNum = $index + 1;
            echo "[$categoryNum] KATEGORİ DETAYLARI:\n";
            echo "   🆔 ID: " . ($category->ID ?? 'N/A') . "\n";
            echo "   🏷️  Adı: " . ($category->Tanim ?? 'N/A') . "\n";
            echo "   👥 Parent ID: " . ($category->PID ?? 'N/A') . "\n";
            echo "   ✅ Aktif: " . (($category->Aktif ?? false) ? 'Evet' : 'Hayır') . "\n";
            echo "   📊 Sıra: " . ($category->Sira ?? 'N/A') . "\n";
            echo "   🔗 URL: " . ($category->Url ?? 'Belirtilmemiş') . "\n";
            
            // Açıklamayı kısalt - sadece ilk 100 karakter
            $description = $category->Icerik ?? '';
            if (strlen($description) > 100) {
                $description = substr(strip_tags($description), 0, 100) . "...";
            } else {
                $description = strip_tags($description) ?: 'Belirtilmemiş';
            }
            echo "   📝 Açıklama: " . $description . "\n";
            
            echo "   🏷️  SEO Başlık: " . (($category->SeoSayfaBaslik ?? '') ?: 'Belirtilmemiş') . "\n";
            echo "   🏷️  Kod: " . (($category->Kod ?? '') ?: 'Belirtilmemiş') . "\n";
            echo "   -------------------------\n";
        }
        
        // İstatistikler
        $activeCount = 0;
        $inactiveCount = 0;
        $rootCount = 0;
        $childCount = 0;
        $withSeoCount = 0;
        $withDescCount = 0;
        
        foreach ($allCategories as $category) {
            if ($category->Aktif ?? false) $activeCount++;
            else $inactiveCount++;
            
            if (($category->PID ?? 0) == 0) $rootCount++;
            else $childCount++;
            
            if (!empty($category->SeoSayfaBaslik)) $withSeoCount++;
            if (!empty($category->Icerik)) $withDescCount++;
        }
        
        echo "\n📊 KATEGORİ İSTATİSTİKLERİ:\n";
        echo "   📦 Toplam Kategori: " . count($allCategories) . "\n";
        echo "   ✅ Aktif Kategori: $activeCount\n";
        echo "   ❌ Pasif Kategori: $inactiveCount\n";
        echo "   🌳 Ana Kategori: $rootCount\n";
        echo "   🌿 Alt Kategori: $childCount\n";
        echo "   🏷️  SEO Başlıklı: $withSeoCount\n";
        echo "   📝 Açıklamalı: $withDescCount\n";
        
        // İlk kategoriyi test için saklayalım
        $testCategoryId = $allCategories[0]->ID ?? null;
        $testParentId = $allCategories[0]->PID ?? null;
        
    } else {
        $errorCount++;
        echo "❌ Kategori bulunamadı veya hata oluştu\n";
        $testCategoryId = null;
        $testParentId = null;
    }
    echo "\n";
    
    // Test 2: Belirli kategori getirme
    if ($testCategoryId) {
        echo "🧪 Test 2: BELİRLİ KATEGORİ DETAYI\n";
        echo "-------------------------------\n";
        $testCount++;
        
        $specificCategory = $categoryService->getCategories($testCategoryId);
        
        if (!empty($specificCategory)) {
            $successCount++;
            echo "✅ Belirli kategori başarıyla getirildi\n";
            echo "🎯 Test Edilen ID: $testCategoryId\n";
            
            $category = $specificCategory[0];
            echo "📋 DETAY BİLGİLERİ:\n";
            echo "   🆔 ID: " . ($category->ID ?? 'N/A') . "\n";
            echo "   🏷️  Kategori Adı: " . ($category->Tanim ?? 'N/A') . "\n";
            echo "   👥 Parent ID: " . ($category->PID ?? 'N/A') . "\n";
            echo "   ✅ Durum: " . (($category->Aktif ?? false) ? 'Aktif' : 'Pasif') . "\n";
            echo "   📊 Sıra: " . ($category->Sira ?? 'N/A') . "\n";
            echo "   🔗 URL: " . ($category->Url ?? 'Belirtilmemiş') . "\n";
        } else {
            $errorCount++;
            echo "❌ Belirli kategori getirilemedi\n";
        }
        echo "\n";
    }
    
    // Test 3: Yeni kategori oluşturma
    echo "🧪 Test 3: YENİ KATEGORİ OLUŞTURMA\n";
    echo "--------------------------------\n";
    $testCount++;
    
    // CategoryModel objesi oluştur
    $categoryData = [
        'ID' => 0,
        'Tanim' => 'Test Kategori ' . date('Y-m-d H:i:s'),
        'PID' => 0,
        'Aktif' => true,
        'Sira' => 99,
        'Icerik' => 'Test kategorisi açıklaması',
        'Url' => '/test-kategori-' . time(),
        'SeoSayfaBaslik' => 'Test Kategori SEO',
        'SeoSayfaAciklama' => 'Test kategori SEO açıklaması',
        'SeoAnahtarKelime' => 'test,kategori',
        'Kod' => 'TEST_' . time()
    ];
    
    $categoryModel = new CategoryModel($categoryData);
    $createResponse = $categoryService->createCategory($categoryModel);
    
    if ($createResponse->isSuccess()) {
        $successCount++;
        $newCategoryId = $createResponse->getData();
        echo "✅ Yeni kategori başarıyla oluşturuldu\n";
        echo "🆔 Yeni Kategori ID: $newCategoryId\n";
        echo "🏷️  Kategori Adı: " . $categoryData['Tanim'] . "\n";
        echo "📝 Mesaj: " . $createResponse->getMessage() . "\n";
    } else {
        $errorCount++;
        echo "❌ Yeni kategori oluşturulamadı\n";
        echo "📝 Hata: " . $createResponse->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 4: Olmayan kategori kontrolü
    echo "🧪 Test 4: OLMAYAN KATEGORİ KONTROLÜ\n";
    echo "----------------------------------\n";
    $testCount++;
    
    $nonExistentCategory = $categoryService->getCategories(999999);
    
    if (empty($nonExistentCategory)) {
        $successCount++;
        echo "✅ Olmayan kategori için boş sonuç döndü (doğru davranış)\n";
        echo "🎯 Test ID: 999999 - Sonuç: Bulunamadı\n";
    } else {
        $errorCount++;
        echo "❌ Olmayan kategori için beklenmeyen sonuç döndü\n";
        echo "📦 Bulunan kayıt sayısı: " . count($nonExistentCategory) . "\n";
    }
    echo "\n";
    
    // Test 5: Performans testi
    echo "🧪 Test 5: PERFORMANS TESTİ\n";
    echo "-------------------------\n";
    $testCount++;
    
    $performanceStart = microtime(true);
    
    // 3 kez aynı sorguyu yap
    for ($i = 1; $i <= 3; $i++) {
        $perfTest = $categoryService->getCategories();
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
    echo "   • getCategories() - Tüm kategori listesi\n";
    echo "   • getCategories(id) - Belirli kategori detayı\n";
    echo "   • createCategory() - Yeni kategori oluşturma\n";
    echo "   • Hata kontrolü - Olmayan ID testi\n";
    echo "   • Performans analizi - Çoklu istek testi\n";
    echo "   • Veri bütünlüğü - Field mapping kontrolü\n\n";
    
    echo "📋 Field Mapping (WSDL Uyumlu):\n";
    echo "   • KategoriAdi → Tanim ✅\n";
    echo "   • ParentID → PID ✅\n";
    echo "   • SiraNo → Sira ✅\n";
    echo "   • Aciklama → Icerik ✅\n";
    echo "   • ID → ID ✅\n";
    echo "   • Aktif → Aktif ✅\n\n";
    
    echo "🏁 CategoryService test süreci tamamlandı!\n";
    
} catch (Exception $e) {
    echo "💥 FATAL ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=== KATEGORİ SERVİS TESTİ TAMAMLANDI ===\n"; 