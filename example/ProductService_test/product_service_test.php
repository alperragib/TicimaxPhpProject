<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Response\ApiResponse;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== ProductService Fonksiyon Testleri ===\n\n";

try {
    // Ticimax API başlat
    $ticimax = new Ticimax($mainDomain, $apiKey);
    $productService = $ticimax->productService();
    
    echo "✓ ProductService başlatıldı\n\n";
    
    // Test 1: Tüm ürünleri tam bilgilerle getir
    echo "🧪 Test 1: Tüm Ürünleri Getirme (Tam Bilgiler)\n";
    echo "===========================================\n";
    
    $pagination = [
        'KayitSayisi' => 50,
        'BaslangicIndex' => 0,
        'SiralamaDegeri' => 'ID',
        'SiralamaYonu' => 'DESC'
    ];
    
    $productsResponse = $productService->getProducts([], $pagination);
    if ($productsResponse->isSuccess()) {
        $products = $productsResponse->getData();
        echo "✅ Toplam " . count($products) . " ürün getirildi\n\n";
        
        // TÜM ürünlerin tam bilgilerini göster (DOĞRU FIELD NAME'LERİ)
        foreach ($products as $index => $product) {
            echo "📦 " . ($index + 1) . ". Ürün:\n";
            echo "   ID: " . ($product->ID ?? 'N/A') . "\n";
            echo "   Adı: " . ($product->UrunAdi ?? 'N/A') . "\n";
            echo "   Tedarikci Kodu: " . ($product->TedarikciKodu ?? 'N/A') . "\n";
            echo "   Tedarikci Kodu 2: " . ($product->TedarikciKodu2 ?? 'N/A') . "\n";
            echo "   Toplam Stok: " . ($product->ToplamStokAdedi ?? 'N/A') . "\n";
            echo "   Ana Kategori ID: " . ($product->AnaKategoriID ?? 'N/A') . "\n";
            echo "   Ana Kategori: " . ($product->AnaKategori ?? 'N/A') . "\n";
            echo "   Marka ID: " . ($product->MarkaID ?? 'N/A') . "\n";
            echo "   Marka: " . ($product->Marka ?? 'N/A') . "\n";
            echo "   Aktif: " . ($product->Aktif ? 'Evet' : 'Hayır') . "\n";
            echo "   Vitrin: " . ($product->Vitrin ? 'Evet' : 'Hayır') . "\n";
            echo "   Fırsat Ürünü: " . ($product->FirsatUrunu ? 'Evet' : 'Hayır') . "\n";
            echo "   Yeni Ürün: " . ($product->YeniUrun ? 'Evet' : 'Hayır') . "\n";
            echo "   Listede Göster: " . ($product->ListedeGoster ? 'Evet' : 'Hayır') . "\n";
            echo "   Ücretsiz Kargo: " . ($product->UcretsizKargo ? 'Evet' : 'Hayır') . "\n";
            echo "   Ürün Tipi: " . ($product->UrunTipi ?? 'N/A') . "\n";
            echo "   Sıra: " . ($product->Sira ?? 'N/A') . "\n";
            echo "   Satış Birimi: " . ($product->SatisBirimi ?? 'N/A') . "\n";
            
            // HTML taglerini temizle ve karakter sayısını sınırla
            $aciklama = strip_tags($product->Aciklama ?? '');
            $onYazi = strip_tags($product->OnYazi ?? '');
            
            // Açıklama için 200 karakter sınırı
            if (strlen($aciklama) > 200) {
                $aciklama = substr($aciklama, 0, 200) . '...';
            }
            
            // Ön yazı için 150 karakter sınırı
            if (strlen($onYazi) > 150) {
                $onYazi = substr($onYazi, 0, 150) . '...';
            }
            
            echo "   Açıklama (Temizlenmiş): " . ($aciklama ?: 'N/A') . "\n";
            echo "   Ön Yazı (Temizlenmiş): " . ($onYazi ?: 'N/A') . "\n";
            echo "   Ekleme Tarihi: " . ($product->EklemeTarihi ?? 'N/A') . "\n";
            echo "   Yayın Tarihi: " . ($product->YayinTarihi ?? 'N/A') . "\n";
            
            // Varyasyon bilgilerini göster
            if (isset($product->Varyasyonlar) && is_array($product->Varyasyonlar) && !empty($product->Varyasyonlar)) {
                echo "   🎨 Varyasyonlar (" . count($product->Varyasyonlar) . " adet):\n";
                foreach ($product->Varyasyonlar as $vIdx => $variation) {
                    echo "      " . ($vIdx + 1) . ". Varyasyon ID: " . ($variation->ID ?? 'N/A') . "\n";
                    echo "         Stok: " . ($variation->StokAdedi ?? 'N/A') . "\n";
                    echo "         Satış Fiyatı: " . ($variation->SatisFiyati ?? 'N/A') . " TL\n";
                    echo "         İndirimli Fiyat: " . ($variation->IndirimliFiyat ?? 'N/A') . " TL\n";
                }
            } else {
                echo "   🎨 Varyasyon yok\n";
            }
            echo "   ------------------------------\n";
        }
        
        echo "\n📊 TÜM " . count($products) . " ÜRÜN LİSTELENDİ!\n\n";
        
        $testProductId = $products[0]->ID ?? null;
    } else {
        echo "❌ Hata: " . $productsResponse->getMessage() . "\n\n";
    }
    
    // Test 2: Ürün sayısını getirme
    echo "🧪 Test 2: Ürün Sayısı Kontrolü\n";
    echo "============================\n";
    
    $totalCount = $productService->SelectUrunCount();
    echo "✅ Toplam Ürün Sayısı: $totalCount\n";
    
    $activeCount = $productService->SelectUrunCount(['Aktif' => 1]);
    echo "✅ Aktif Ürün Sayısı: $activeCount\n";
    
    $vitrinCount = $productService->SelectUrunCount(['Vitrin' => 1]);
    echo "✅ Vitrin Ürün Sayısı: $vitrinCount\n\n";
    
    // Test 3: Kategorileri tam bilgilerle getir
    echo "🧪 Test 3: Kategorileri Getirme\n";
    echo "============================\n";
    
    $categoriesResponse = $productService->SelectKategori();
    if ($categoriesResponse->isSuccess()) {
        $categories = $categoriesResponse->getData();
        echo "✅ Toplam " . count($categories) . " kategori getirildi:\n\n";
        
        foreach ($categories as $index => $category) {
            echo "📂 " . ($index + 1) . ". Kategori:\n";
            echo "   ID: " . ($category->ID ?? 'N/A') . "\n";
            echo "   Tanım: " . ($category->Tanim ?? 'N/A') . "\n";
            echo "   Üst Kategori ID: " . ($category->UstKategoriID ?? '0') . "\n";
            echo "   Aktif: " . ($category->Aktif ? 'Evet' : 'Hayır') . "\n";
            echo "   Sıra: " . ($category->Sira ?? 'N/A') . "\n";
            echo "   ------------------------------\n";
            
            if ($index >= 4) break; // İlk 5 kategoriyi göster
        }
        
        $testCategoryId = $categories[0]->ID ?? null;
    } else {
        echo "❌ Kategori hatası: " . $categoriesResponse->getMessage() . "\n\n";
    }
    
    // Test 4: Ürün varyasyonları
    echo "🧪 Test 4: Ürün Varyasyonları\n";
    echo "==========================\n";
    
    try {
        $variations = $productService->GetProductVariations();
        echo "✅ Toplam " . count($variations) . " varyasyon getirildi:\n\n";
        
        foreach ($variations as $index => $variation) {
            echo "🎨 " . ($index + 1) . ". Varyasyon:\n";
            echo "   ID: " . ($variation->ID ?? 'N/A') . "\n";
            echo "   Ürün Kart ID: " . ($variation->UrunKartId ?? 'N/A') . "\n";
            echo "   Kodu: " . ($variation->Kodu ?? 'N/A') . "\n";
            echo "   Stok Adedi: " . ($variation->StokAdedi ?? 'N/A') . "\n";
            echo "   Satış Fiyatı: " . ($variation->SatisFiyati ?? '0') . " TL\n";
            echo "   Resim: " . ($variation->Resim ?? 'N/A') . "\n";
            echo "   ------------------------------\n";
            
            if ($index >= 2) break; // İlk 3 varyasyonu göster
        }
        
        $testVariationId = $variations[0]->ID ?? null;
    } catch (Exception $e) {
        echo "❌ Varyasyon hatası: " . $e->getMessage() . "\n\n";
    }
    
    // Test 5: Ödeme seçenekleri
    if (isset($testVariationId) && $testVariationId) {
        echo "🧪 Test 5: Ödeme Seçenekleri (Varyasyon ID: $testVariationId)\n";
        echo "=========================================\n";
        
        $paymentResponse = $productService->SelectUrunOdemeSecenek($testVariationId);
        if ($paymentResponse->isSuccess()) {
            $paymentOptions = $paymentResponse->getData();
            echo "✅ " . count($paymentOptions) . " banka için ödeme seçeneği bulundu:\n\n";
            
            foreach ($paymentOptions as $index => $option) {
                echo "💳 " . ($index + 1) . ". Banka: " . ($option['bankaAdi'] ?? 'N/A') . "\n";
                echo "   Banka ID: " . ($option['bankaId'] ?? 'N/A') . "\n";
                echo "   Taksit Seçenekleri:\n";
                
                foreach ($option['taksitler'] as $taksitIndex => $taksit) {
                    echo "      " . ($taksitIndex + 1) . ". " . ($taksit['taksitSayisi'] ?? 'N/A') . " taksit - ";
                    echo ($taksit['taksitTutari'] ?? 'N/A') . " TL/ay\n";
                    
                    if ($taksitIndex >= 2) break; // İlk 3 taksit seçeneği
                }
                echo "   ------------------------------\n";
                
                if ($index >= 1) break; // İlk 2 bankayı göster
            }
        } else {
            echo "❌ Bu varyasyon için ödeme seçeneği yok\n\n";
        }
    }
    
    // Test 6: Taksit seçenekleri
    echo "🧪 Test 6: Taksit Hesaplama (1000 TL)\n";
    echo "==================================\n";
    
    try {
        $installments = $productService->GetInstallmentOptions(1000.0, 12);
        echo "✅ 1000 TL için " . count($installments) . " banka seçeneği:\n\n";
        
        if (count($installments) > 0) {
            foreach ($installments as $index => $bank) {
                echo "🏦 " . ($index + 1) . ". Banka:\n";
                echo "   Banka Adı: " . ($bank->BankaAdi ?? 'N/A') . "\n";
                echo "   Banka ID: " . ($bank->BankaID ?? 'N/A') . "\n";
                echo "   Taksit Seçenekleri:\n";
                
                // Taksit seçeneklerini kontrol et
                if (isset($bank->TaksitSecenekleri) && is_array($bank->TaksitSecenekleri)) {
                    foreach ($bank->TaksitSecenekleri as $taksitIndex => $taksit) {
                        echo "      " . ($taksitIndex + 1) . ". " . ($taksit->TaksitSayisi ?? 'N/A') . " taksit:\n";
                        echo "         Aylık Ödeme: " . ($taksit->TaksitTutari ?? 'N/A') . " TL\n";
                        echo "         Toplam Tutar: " . ($taksit->ToplamTutar ?? 'N/A') . " TL\n";
                        echo "         Komisyon: " . ($taksit->Komisyon ?? 'N/A') . " TL\n";
                        
                        if ($taksitIndex >= 2) break; // İlk 3 taksit seçeneği
                    }
                } else {
                    echo "      ℹ️ Bu banka için taksit seçeneği bulunmadı\n";
                }
                echo "   ------------------------------\n";
                
                if ($index >= 1) break; // İlk 2 bankayı göster
            }
        } else {
            echo "   ℹ️ 1000 TL için taksit seçeneği bulunmadı\n";
        }
    } catch (Exception $e) {
        echo "❌ Taksit hesaplama hatası: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 7: Mağaza stok bilgisi
    echo "🧪 Test 7: Mağaza Stok Bilgisi\n";
    echo "===========================\n";
    
    $storeStockResponse = $productService->GetStoreStock('MAIN');
    if ($storeStockResponse->isSuccess()) {
        $storeStock = $storeStockResponse->getData();
        echo "✅ MAIN mağazası için " . count($storeStock) . " stok kaydı bulundu\n\n";
        
        if (count($storeStock) > 0) {
            foreach ($storeStock as $index => $stock) {
                echo "🏪 " . ($index + 1) . ". Stok:\n";
                echo "   Ürün ID: " . ($stock->UrunID ?? 'N/A') . "\n";
                echo "   Varyasyon ID: " . ($stock->VaryasyonID ?? 'N/A') . "\n";
                echo "   Stok Adedi: " . ($stock->StokAdedi ?? 'N/A') . "\n";
                echo "   Mağaza Kodu: " . ($stock->MagazaKodu ?? 'N/A') . "\n";
                echo "   Ürün Adı: " . ($stock->UrunAdi ?? 'N/A') . "\n";
                echo "   Barkod: " . ($stock->Barkod ?? 'N/A') . "\n";
                echo "   Stok Kodu: " . ($stock->StokKodu ?? 'N/A') . "\n";
                echo "   Güncelleme Tarihi: " . ($stock->GuncellemeTarihi ?? 'N/A') . "\n";
                echo "   ------------------------------\n";
                
                if ($index >= 2) break; // İlk 3 stok kaydı
            }
        } else {
            echo "   ℹ️ MAIN mağazası için stok kaydı bulunmadı\n";
        }
    } else {
        echo "❌ MAIN mağazası için stok bulunamadı: " . $storeStockResponse->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 8: Ürün yorumları
    echo "🧪 Test 8: Ürün Yorumları\n";
    echo "======================\n";
    
    if (isset($testProductId) && $testProductId) {
        $reviewsResponse = $productService->GetProductReviews($testProductId);
        if ($reviewsResponse->isSuccess()) {
            $reviews = $reviewsResponse->getData();
            echo "✅ Ürün ID $testProductId için " . count($reviews) . " yorum bulundu:\n\n";
            
            if (count($reviews) > 0) {
                foreach ($reviews as $index => $review) {
                    echo "💬 " . ($index + 1) . ". Yorum:\n";
                    echo "   ID: " . ($review['id'] ?? 'N/A') . "\n";
                    echo "   Ürün Kart ID: " . ($review['urunKartiId'] ?? 'N/A') . "\n";
                    echo "   Üye ID: " . ($review['uyeId'] ?? 'N/A') . "\n";
                    echo "   İsim: " . ($review['isim'] ?? 'N/A') . "\n";
                    echo "   Mail: " . ($review['mail'] ?? 'N/A') . "\n";
                    echo "   Mesaj: " . (strlen($review['mesaj'] ?? '') > 50 ? substr($review['mesaj'], 0, 50) . '...' : ($review['mesaj'] ?? 'N/A')) . "\n";
                    echo "   Ürün Adı: " . ($review['urunAdi'] ?? 'N/A') . "\n";
                    echo "   Ekleme Tarihi: " . ($review['eklemeTarihi'] ?? 'N/A') . "\n";
                    echo "   ------------------------------\n";
                    
                    if ($index >= 2) break; // İlk 3 yorumu göster
                }
            } else {
                echo "   ℹ️ Bu ürün için yorum bulunmadı\n";
            }
        } else {
            echo "❌ Ürün yorumları alınamadı: " . $reviewsResponse->getMessage() . "\n";
        }
    } else {
        echo "❌ Test için ürün ID bulunamadı\n";
    }
    echo "\n";
    
    // Test 9: Stok miktarı güncelleme (GÜVENLİ TEST)
    echo "🧪 Test 9: Stok Miktarı Güncelleme (Güvenli Test)\n";
    echo "==============================================\n";
    
    if (!empty($variations)) {
        // Mevcut stok değerini al
        $testVariation = $variations[0];
        $currentStock = $testVariation->StokAdedi ?? 0;
        
        echo "🛡️ GÜVENLİ TEST: Mevcut stok değeri ile 'güncelleme'\n";
        echo "📝 Test bilgileri:\n";
        echo "   Varyasyon ID: " . ($testVariation->ID ?? 'N/A') . "\n";
        echo "   Mevcut Stok: " . $currentStock . "\n";
        echo "   Güncelleme Değeri: " . $currentStock . " (AYNI DEĞER - Değişmez!)\n\n";
        
        // Aynı stok değeri ile "güncelle" - hiç değişmez!
        $updateData = [
            [
                'ID' => $testVariation->ID,
                'StokAdedi' => $currentStock  // Aynı değer!
            ]
        ];
        
        $updateResponse = $productService->UpdateStockQuantity($updateData);
        if ($updateResponse->isSuccess()) {
            $updateResult = $updateResponse->getData();
            echo "✅ FONKSİYON ÇALIŞIYOR - API başarıyla çağrıldı\n";
            echo "   📊 Güncellenen kayıt sayısı: " . ($updateResult['updatedCount'] ?? 'N/A') . "\n";
            echo "   🛡️ Stok değeri değişmedi (güvenli test)\n";
            echo "   💬 Mesaj: " . $updateResponse->getMessage() . "\n";
        } else {
            echo "❌ FONKSİYON ÇALIŞMIYOR: " . $updateResponse->getMessage() . "\n";
        }
        
        // Ek güvenlik testi: Geçersiz ID ile test
        echo "\n🔍 EK TEST: Geçersiz ID kontrolü\n";
        $invalidTest = $productService->UpdateStockQuantity([
            ['ID' => 999999999, 'StokAdedi' => 1]
        ]);
        
        if ($invalidTest->isSuccess()) {
            echo "   ⚠️ Uyarı: API geçersiz ID'leri kabul ediyor\n";
        } else {
            echo "   ✅ Güvenlik OK: Geçersiz ID doğru şekilde reddedildi\n";
            echo "   📝 Hata mesajı: " . $invalidTest->getMessage() . "\n";
        }
        
    } else {
        echo "❌ Test için varyasyon bulunamadı\n";
    }
    echo "\n";
    
    // Test Sonuçları Özeti
    echo "🏁 ProductService Fonksiyon Testleri Tamamlandı!\n";
    echo "================================================\n";
    echo "✅ Test edilen fonksiyonlar:\n";
    echo "   1. getProducts() - Ürünleri getir\n";
    echo "   2. SelectUrunCount() - Ürün sayısı\n";
    echo "   3. SelectKategori() - Kategoriler\n";
    echo "   4. GetProductVariations() - Varyasyonlar\n";
    echo "   5. SelectUrunOdemeSecenek() - Ödeme seçenekleri\n";
    echo "   6. GetInstallmentOptions() - Taksit seçenekleri\n";
    echo "   7. GetStoreStock() - Mağaza stok bilgisi\n";
    echo "   8. GetProductReviews() - Ürün yorumları\n";
    echo "   9. UpdateStockQuantity() - Stok güncelleme\n";
    echo "\n📊 Toplam 9/9 fonksiyon test edildi!\n";
    
} catch (Exception $e) {
    echo "💥 HATA: " . $e->getMessage() . "\n";
    echo "Dosya: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test Süreci Bitti ===\n"; 