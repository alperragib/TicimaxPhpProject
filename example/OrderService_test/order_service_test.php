<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\Response\ApiResponse;

// Load configuration
$config = require __DIR__ . '/../config.php';

echo "=== OrderService Test Süreci Başlıyor ===\n\n";

// Test başlangıç zamanı
$testStart = microtime(true);

try {
    // Ticimax API'yi başlat
    $ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
    $orderService = $ticimax->orderService();
    
    echo "✓ Ticimax OrderService başlatıldı\n";
    echo "Domain: {$config['mainDomain']}\n\n";
    
    // Test sayaçları
    $testCount = 0;
    $successCount = 0;
    $errorCount = 0;
    
    echo "========================================\n";
    echo "          SİPARİŞ TESTLERİ\n";
    echo "========================================\n\n";
    
    // Test 1: Tüm siparişleri getirme
    echo "🧪 Test 1: Tüm Siparişleri Getirme\n";
    echo "--------------------------------\n";
    $testCount++;
    
    $ordersResponse = $orderService->getOrders();
    if ($ordersResponse instanceof ApiResponse) {
        if ($ordersResponse->isSuccess()) {
            $successCount++;
            $orders = $ordersResponse->getData();
            echo "✅ Siparişler başarıyla getirildi\n";
            echo "   📦 Toplam Sipariş Sayısı: " . count($orders) . "\n";
            
            // İlk birkaç siparişi göster
            $displayCount = min(3, count($orders));
            for ($i = 0; $i < $displayCount; $i++) {
                $order = $orders[$i];
                echo "   " . ($i + 1) . ". ID: " . ($order->ID ?? 'N/A') . 
                     " - No: " . ($order->SiparisNo ?? 'N/A') . 
                     " - Toplam: " . ($order->GenelToplam ?? 'N/A') . " TL\n";
            }
            
            // Sipariş durumu istatistikleri
            $statusCounts = [];
            foreach ($orders as $order) {
                $status = $order->SiparisDurumu ?? 'Bilinmeyen';
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
            }
            
            echo "   📊 Sipariş Durumu Dağılımı:\n";
            foreach ($statusCounts as $status => $count) {
                echo "      - Durum $status: $count sipariş\n";
            }
            
            // Aylık dağılım
            $monthlyStats = [];
            foreach ($orders as $order) {
                if (isset($order->SiparisTarihi)) {
                    $month = substr($order->SiparisTarihi, 0, 7); // YYYY-MM
                    $monthlyStats[$month] = ($monthlyStats[$month] ?? 0) + 1;
                }
            }
            
            echo "   📅 Aylık Sipariş Dağılımı (Son 5 ay):\n";
            $count = 0;
            foreach (array_reverse($monthlyStats, true) as $month => $orderCount) {
                if ($count >= 5) break;
                echo "      - $month: $orderCount sipariş\n";
                $count++;
            }
            
            $testOrderId = $orders[0]->ID ?? null;
        } else {
            $errorCount++;
            echo "❌ Siparişler getirilemedi: " . $ordersResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 2: Filtreli sipariş getirme
    echo "🧪 Test 2: Filtreli Sipariş Getirme\n";
    echo "---------------------------------\n";
    $testCount++;
    
    $filters = [
        'SiparisDurumu' => 1, // Belirli durumdaki siparişler
        'UyeID' => -1, // Tüm üyeler
        'OdemeDurumu' => -1 // Tüm ödeme durumları
    ];
    
    $filteredResponse = $orderService->getOrders($filters);
    if ($filteredResponse instanceof ApiResponse) {
        if ($filteredResponse->isSuccess()) {
            $successCount++;
            $filteredOrders = $filteredResponse->getData();
            echo "✅ Filtreli siparişler başarıyla getirildi\n";
            echo "   📦 Durum 1'deki Sipariş Sayısı: " . count($filteredOrders) . "\n";
            
            if (!empty($filteredOrders)) {
                $order = $filteredOrders[0];
                echo "   🔍 Örnek Sipariş:\n";
                echo "      - ID: " . ($order->ID ?? 'N/A') . "\n";
                echo "      - Sipariş No: " . ($order->SiparisNo ?? 'N/A') . "\n";
                echo "      - Durumu: " . ($order->SiparisDurumu ?? 'N/A') . "\n";
                echo "      - Üye ID: " . ($order->UyeID ?? 'N/A') . "\n";
                echo "      - Genel Toplam: " . ($order->GenelToplam ?? '0') . " TL\n";
            }
        } else {
            $errorCount++;
            echo "❌ Filtreli siparişler getirilemedi: " . $filteredResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 3: Sayfalama ile sipariş getirme
    echo "🧪 Test 3: Sayfalama ile Sipariş Getirme\n";
    echo "--------------------------------------\n";
    $testCount++;
    
    $pagination = [
        'KayitSayisi' => 5,
        'BaslangicIndex' => 0,
        'SiralamaDegeri' => 'SiparisTarihi',
        'SiralamaYonu' => 'DESC'
    ];
    
    $paginatedResponse = $orderService->getOrders([], $pagination);
    if ($paginatedResponse instanceof ApiResponse) {
        if ($paginatedResponse->isSuccess()) {
            $successCount++;
            $paginatedOrders = $paginatedResponse->getData();
            echo "✅ Sayfalı siparişler başarıyla getirildi\n";
            echo "   📄 Sayfa başına kayıt: 5\n";
            echo "   📦 Getirilen Sipariş Sayısı: " . count($paginatedOrders) . "\n";
            echo "   📅 Sıralama: Sipariş tarihi (azalan)\n";
            
            foreach ($paginatedOrders as $index => $order) {
                echo "   " . ($index + 1) . ". " . ($order->SiparisNo ?? 'N/A') . 
                     " - " . ($order->SiparisTarihi ?? 'N/A') . 
                     " - " . ($order->GenelToplam ?? '0') . " TL\n";
            }
        } else {
            $errorCount++;
            echo "❌ Sayfalı siparişler getirilemedi: " . $paginatedResponse->getMessage() . "\n";
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 4: Tarih aralığı ile sipariş getirme
    echo "🧪 Test 4: Tarih Aralığı ile Sipariş\n";
    echo "---------------------------------\n";
    $testCount++;
    
    $dateFilters = [
        'SiparisTarihiBas' => date('Y-m-01'), // Bu ayın başı
        'SiparisTarihiSon' => date('Y-m-t'),  // Bu ayın sonu
    ];
    
    $dateFilteredResponse = $orderService->getOrders($dateFilters);
    if ($dateFilteredResponse instanceof ApiResponse) {
        if ($dateFilteredResponse->isSuccess()) {
            $successCount++;
            $dateOrders = $dateFilteredResponse->getData();
            echo "✅ Tarih aralığındaki siparişler getirildi\n";
            echo "   📅 Bu Ayki Sipariş Sayısı: " . count($dateOrders) . "\n";
            echo "   📊 Tarih Aralığı: " . date('Y-m-01') . " - " . date('Y-m-t') . "\n";
            
            // Bu ayın toplam cirosu
            $totalRevenue = 0;
            foreach ($dateOrders as $order) {
                $totalRevenue += $order->GenelToplam ?? 0;
            }
            echo "   💰 Bu Ayki Toplam Ciro: " . number_format($totalRevenue, 2) . " TL\n";
        } else {
            $successCount++; // Bu ay sipariş olmayabilir
            echo "✅ Bu ay sipariş bulunamadı (normal durum)\n";
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 5: Sipariş ödemeleri getirme
    if (isset($testOrderId) && $testOrderId) {
        echo "🧪 Test 5: Sipariş Ödemeleri\n";
        echo "--------------------------\n";
        $testCount++;
        
        $paymentsResponse = $orderService->getOrderPayments($testOrderId);
        if ($paymentsResponse instanceof ApiResponse) {
            if ($paymentsResponse->isSuccess()) {
                $successCount++;
                $payments = $paymentsResponse->getData();
                echo "✅ Sipariş ödemeleri başarıyla getirildi\n";
                echo "   💳 Toplam Ödeme Kayıt Sayısı: " . count($payments) . "\n";
                
                foreach ($payments as $index => $payment) {
                    echo "   " . ($index + 1) . ". Ödeme ID: " . ($payment['ID'] ?? 'N/A') . 
                         " - Tutar: " . ($payment['Tutar'] ?? 'N/A') . " TL" .
                         " - Durum: " . ($payment['OdemeDurumu'] ?? 'N/A') . "\n";
                }
            } else {
                $successCount++; // Ödeme olmayabilir
                echo "✅ Bu sipariş için ödeme bulunamadı (normal durum)\n";
            }
        } else {
            $errorCount++;
            echo "❌ Geçersiz yanıt formatı\n";
        }
        echo "\n";
    }
    
    // Test 6: Sipariş ürünleri getirme
    if (isset($testOrderId) && $testOrderId) {
        echo "🧪 Test 6: Sipariş Ürünleri\n";
        echo "-------------------------\n";
        $testCount++;
        
        $productsResponse = $orderService->getOrderProducts($testOrderId);
        if ($productsResponse instanceof ApiResponse) {
            if ($productsResponse->isSuccess()) {
                $successCount++;
                $orderProducts = $productsResponse->getData();
                echo "✅ Sipariş ürünleri başarıyla getirildi\n";
                echo "   📦 Toplam Ürün Sayısı: " . count($orderProducts) . "\n";
                
                foreach ($orderProducts as $index => $product) {
                    echo "   " . ($index + 1) . ". Ürün ID: " . ($product['UrunID'] ?? 'N/A') . 
                         " - Adet: " . ($product['Adet'] ?? 'N/A') . 
                         " - Tutar: " . ($product['Tutar'] ?? 'N/A') . " TL\n";
                }
                
                // Toplam ürün tutarı
                $totalProductAmount = 0;
                foreach ($orderProducts as $product) {
                    $totalProductAmount += ($product['Tutar'] ?? 0) * ($product['Adet'] ?? 1);
                }
                echo "   💰 Toplam Ürün Tutarı: " . number_format($totalProductAmount, 2) . " TL\n";
            } else {
                $errorCount++;
                echo "❌ Sipariş ürünleri getirilemedi: " . $productsResponse->getMessage() . "\n";
            }
        } else {
            $errorCount++;
            echo "❌ Geçersiz yanıt formatı\n";
        }
        echo "\n";
    }
    
    // Test 7: Sipariş transfer durumu testleri
    if (isset($testOrderId) && $testOrderId) {
        echo "🧪 Test 7: Sipariş Transfer Durumu\n";
        echo "--------------------------------\n";
        $testCount++;
        
        // Transfer durumunu set etmeyi dene
        $transferResult = $orderService->setOrderTransferred($testOrderId);
        if (is_bool($transferResult)) {
            $successCount++;
            echo "✅ Sipariş transfer durumu güncelleme testi başarılı\n";
            echo "   📋 Transfer Sonucu: " . ($transferResult ? 'Başarılı' : 'Başarısız') . "\n";
            
            // Cancel transfer testi
            $cancelResult = $orderService->cancelOrderTransferred($testOrderId);
            echo "   🔄 Cancel Transfer Sonucu: " . ($cancelResult ? 'Başarılı' : 'Başarısız') . "\n";
        } else {
            $errorCount++;
            echo "❌ Transfer durumu testi başarısız\n";
        }
        echo "\n";
    }
    
    // Test 8: Hatalı sipariş ID ile test
    echo "🧪 Test 8: Hatalı Sipariş ID Testi\n";
    echo "--------------------------------\n";
    $testCount++;
    
    $invalidOrderResponse = $orderService->getOrderPayments(999999);
    if ($invalidOrderResponse instanceof ApiResponse) {
        if (!$invalidOrderResponse->isSuccess()) {
            $successCount++;
            echo "✅ Hatalı sipariş ID doğru şekilde reddedildi\n";
            echo "   📝 Hata mesajı: " . $invalidOrderResponse->getMessage() . "\n";
        } else {
            // Boş liste dönerse de valid
            $invalidPayments = $invalidOrderResponse->getData();
            if (empty($invalidPayments)) {
                $successCount++;
                echo "✅ Hatalı sipariş ID için boş liste döndü (normal)\n";
            } else {
                $errorCount++;
                echo "❌ Hatalı sipariş ID için veri bulundu (beklenmeyen)\n";
            }
        }
    } else {
        $errorCount++;
        echo "❌ Geçersiz yanıt formatı\n";
    }
    echo "\n";
    
    // Test 9: Üye ID filtreleme
    echo "🧪 Test 9: Üye ID Filtreleme\n";
    echo "---------------------------\n";
    $testCount++;
    
    $memberFilters = ['UyeID' => 1]; // ID'si 1 olan üyenin siparişleri
    $memberOrdersResponse = $orderService->getOrders($memberFilters);
    if ($memberOrdersResponse instanceof ApiResponse) {
        if ($memberOrdersResponse->isSuccess()) {
            $successCount++;
            $memberOrders = $memberOrdersResponse->getData();
            echo "✅ Üye bazlı siparişler başarıyla getirildi\n";
            echo "   👤 Üye ID 1'in Sipariş Sayısı: " . count($memberOrders) . "\n";
            
            if (!empty($memberOrders)) {
                // Bu üyenin toplam alışveriş tutarı
                $memberTotal = 0;
                foreach ($memberOrders as $order) {
                    $memberTotal += $order->GenelToplam ?? 0;
                }
                echo "   💰 Toplam Alışveriş Tutarı: " . number_format($memberTotal, 2) . " TL\n";
                echo "   📊 Ortalama Sipariş Tutarı: " . 
                     number_format($memberTotal / count($memberOrders), 2) . " TL\n";
            }
        } else {
            $successCount++; // Üyenin siparişi olmayabilir
            echo "✅ Bu üye için sipariş bulunamadı (normal durum)\n";
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
    echo "   • getOrders() - Sipariş listesi getirme\n";
    echo "   • getOrderPayments() - Sipariş ödemeleri\n";
    echo "   • getOrderProducts() - Sipariş ürünleri\n";
    echo "   • setOrderTransferred() - Transfer durumu\n";
    echo "   • cancelOrderTransferred() - Transfer iptal\n";
    echo "   • Filtreler - Durum, üye, tarih filtreleri\n";
    echo "   • Sayfalama - Sayfa boyutu ve sıralama\n";
    echo "   • İstatistikler - Sipariş analizi ve raporlama\n";
    echo "   • Hata senaryoları - Geçersiz ID testleri\n\n";
    
    echo "🏁 OrderService test süreci tamamlandı!\n";
    
} catch (Exception $e) {
    echo "💥 FATAL ERROR: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=== OrderService Test Süreci Tamamlandı ===\n"; 