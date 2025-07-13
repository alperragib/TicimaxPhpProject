<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Response\ApiResponse;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== CartService Test Süreci Başlıyor ===\n\n";

// Test başlangıç zamanı
$testStart = microtime(true);

try {
    // Ticimax API'yi başlat
    $ticimax = new Ticimax($mainDomain, $apiKey);
    $cartService = $ticimax->cartService();
    
    echo "✓ Ticimax CartService başlatıldı\n\n";
    
    // Test parametreleri
    $testUserId = 1; // Test kullanıcı ID
    $testCampaignId = 0;
    
    // Test sayaçları
    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;
    
    echo "========================================\n";
    echo "           SEPET TESTLERİ\n";
    echo "========================================\n\n";
    
    // Test 1: Yeni sepet oluşturma
    echo "🧪 Test 1: Yeni Sepet Oluşturma\n";
    echo "------------------------------\n";
    $testCount++;
    
    $createResponse = $cartService->createSepet($testUserId);
    if ($createResponse instanceof ApiResponse) {
        if ($createResponse->isSuccess()) {
            $successCount++;
            $newCart = $createResponse->getData();
            echo "✅ Sepet başarıyla oluşturuldu\n";
            echo "   📋 Sepet ID: " . ($newCart['SepetID'] ?? 'N/A') . "\n";
            echo "   💰 Genel Toplam: " . ($newCart['GenelToplam'] ?? '0') . " TL\n";
            echo "   📦 Ürün Adedi: " . ($newCart['ToplamUrunAdedi'] ?? '0') . "\n";
            
            $createdCartId = $newCart['SepetID'] ?? null;
        } else {
            $errorCount++;
            echo "❌ Sepet oluşturulamadı: " . $createResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 2: Sepet getirme (Kullanıcı ID ile)
    echo "🧪 Test 2: Kullanıcı Sepeti Getirme\n";
    echo "--------------------------------\n";
    $testCount++;
    
    $getResponse = $cartService->getSepet($testUserId);
    if ($getResponse instanceof ApiResponse) {
        if ($getResponse->isSuccess()) {
            $successCount++;
            $cart = $getResponse->getData();
            echo "✅ Kullanıcı sepeti başarıyla getirildi\n";
            echo "   📋 Sepet ID: " . ($cart->ID ?? 'N/A') . "\n";
            echo "   💰 Genel Toplam: " . ($cart->GenelToplam ?? '0') . " TL\n";
            echo "   📦 Ürün Adedi: " . ($cart->ToplamUrunAdedi ?? '0') . "\n";
            echo "   💳 Para Birimi: " . ($cart->SepetParaBirimiDilKodu ?? 'N/A') . "\n";
            
            // Ürünleri listele
            if (!empty($cart->Urunler)) {
                echo "   🛍️ Sepetteki Ürünler:\n";
                foreach ($cart->Urunler as $index => $urun) {
                    echo "      " . ($index + 1) . ". Ürün ID: " . ($urun['UrunID'] ?? 'N/A') . 
                         " - Adet: " . ($urun['Adet'] ?? 'N/A') . "\n";
                }
            } else {
                echo "   📭 Sepet boş\n";
            }
        } else {
            $errorCount++;
            echo "❌ Sepet getirilemedi: " . $getResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 3: Specific sepet getirme (Sepet ID ile)
    if (isset($createdCartId) && $createdCartId) {
        echo "🧪 Test 3: Belirli Sepet ID ile Getirme\n";
        echo "-----------------------------------\n";
        $testCount++;
        
        $getSpecificResponse = $cartService->getSepet($testUserId, $createdCartId);
        if ($getSpecificResponse instanceof ApiResponse) {
            if ($getSpecificResponse->isSuccess()) {
                $successCount++;
                $specificCart = $getSpecificResponse->getData();
                echo "✅ Belirli sepet başarıyla getirildi\n";
                echo "   📋 Sepet ID: " . ($specificCart->ID ?? 'N/A') . "\n";
                echo "   💰 Genel Toplam: " . ($specificCart->GenelToplam ?? '0') . " TL\n";
            } else {
                $errorCount++;
                echo "❌ Belirli sepet getirilemedi: " . $getSpecificResponse->getMessage() . "\n";
            }
        } else {
            $errorCount++;
            echo "❌ Geçersiz yanıt formatı\n";
        }
        echo "\n";
    }
    
    // Test 4: Sepet Listesi Getirme
    echo "🧪 Test 4: Sepet Listesi Getirme\n";
    echo "------------------------------\n";
    $testCount++;
    
    $selectResponse = $cartService->selectSepet(null, $testUserId);
    if ($selectResponse instanceof ApiResponse) {
        if ($selectResponse->isSuccess()) {
            $successCount++;
            $cartData = $selectResponse->getData();
            $cartList = $cartData['carts'] ?? []; // API array döndürüyor, [carts] key'i var
            echo "✅ Sepet listesi başarıyla getirildi\n";
            echo "   📊 Toplam Sepet Sayısı: " . count($cartList) . "\n";
            
            // İlk birkaç sepeti göster
            $displayCount = min(3, count($cartList));
            for ($i = 0; $i < $displayCount; $i++) {
                $cart = $cartList[$i];
                // WebCartModel object olduğu için property access
                echo "   " . ($i + 1) . ". Sepet ID: " . ($cart->ID ?? 'N/A') . 
                     " - Guid: " . ($cart->GuidSepetID ?? 'N/A') . 
                     " - Tarih: " . ($cart->SepetTarihi ?? 'N/A') . "\n";
            }
        } else {
            $errorCount++;
            echo "❌ Sepet listesi getirilemedi: " . $selectResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Sepet listesi API çağrısı başarısız\n";
    }

    echo "\n";
    
    // Test 5: Web Sepet Getirme
    echo "🧪 Test 5: Web Sepet Getirme\n";
    echo "--------------------------\n";
    $testCount++;
    
    $webSelectResponse = $cartService->selectWebSepet(null, null, null, $testUserId);
    if ($webSelectResponse instanceof ApiResponse) {
        if ($webSelectResponse->isSuccess()) {
            $successCount++;
            $webCartData = $webSelectResponse->getData();
            echo "✅ Web sepet listesi başarıyla getirildi\n";
            
            // stdClass object, tek sepet var
            if (isset($webCartData->WebSepet)) {
                $webCart = $webCartData->WebSepet;
                echo "   📋 Web Sepet ID: " . ($webCart->ID ?? 'N/A') . "\n";
                echo "   🆔 Guid: " . ($webCart->GuidSepetID ?? 'N/A') . "\n";
                echo "   📅 Tarih: " . ($webCart->SepetTarihi ?? 'N/A') . "\n";
                
                // Ürünler var mı kontrol et
                if (isset($webCart->Urunler)) {
                    if (is_object($webCart->Urunler) && isset($webCart->Urunler->WebSepetUrun)) {
                        echo "   📦 Ürün var: Evet\n";
                    } else {
                        echo "   📦 Ürün var: Hayır\n";
                    }
                }
            } else {
                echo "   📭 Web sepet boş\n";
            }
        } else {
            $errorCount++;
            echo "❌ Web sepet getirilemedi: " . $webSelectResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Web sepet API çağrısı başarısız\n";
    }
    echo "\n";
    
    // Test 6: Para birimi ve dil filtreleri ile Web sepet getirme
    echo "🧪 Test 6: Filtreli Web Sepet Getirme\n";
    echo "-----------------------------------\n";
    $testCount++;
    
    $filteredWebResponse = $cartService->selectWebSepet('tr', 'TL');
    if ($filteredWebResponse instanceof ApiResponse) {
        if ($filteredWebResponse->isSuccess()) {
            $successCount++;
            $filteredWebCarts = $filteredWebResponse->getData();
            echo "✅ Filtreli web sepet listesi başarıyla getirildi\n";
            
            // stdClass object kontrol et
            if (isset($filteredWebCarts->WebSepet)) {
                echo "   📊 TR-TL Sepet: 1 adet\n";
                echo "   🆔 Sepet ID: " . ($filteredWebCarts->WebSepet->ID ?? 'N/A') . "\n";
            } else {
                echo "   📊 TR-TL Sepet: 0 adet\n";
            }
            echo "   🌐 Dil: TR, Para Birimi: TL\n";
        } else {
            $errorCount++;
            echo "❌ Filtreli web sepet getirilemedi: " . $filteredWebResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 7: Hatalı kullanıcı ID ile sepet getirme
    echo "🧪 Test 7: Hatalı Kullanıcı ID Testi\n";
    echo "---------------------------------\n";
    $testCount++;
    
    $invalidUserResponse = $cartService->getSepet(999999);
    if ($invalidUserResponse instanceof ApiResponse) {
        if (!$invalidUserResponse->isSuccess()) {
            $successCount++;
            echo "✅ Hatalı kullanıcı ID doğru şekilde reddedildi\n";
            echo "   📝 Hata mesajı: " . $invalidUserResponse->getMessage() . "\n";
        } else {
            $errorCount++;
            echo "❌ Hatalı kullanıcı ID kabul edildi (beklenmeyen durum)\n";
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
    echo "   • createSepet() - Yeni sepet oluşturma\n";
    echo "   • getSepet() - Sepet getirme (kullanıcı/sepet ID)\n";
    echo "   • selectSepet() - Sepet listesi getirme\n";
    echo "   • selectWebSepet() - Web sepet listesi getirme\n";
    echo "   • Filtreli sorgular - Dil ve para birimi filtreleri\n";
    echo "   • Hata senaryoları - Geçersiz kullanıcı ID testleri\n\n";
    
    echo "🏁 CartService test süreci tamamlandı!\n";
    
} catch (Exception $e) {
    echo "💥 FATAL ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=== CartService Test Süreci Tamamlandı ===\n"; 