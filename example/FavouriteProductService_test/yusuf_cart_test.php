<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== YUSUF KURNAZ SEPET TESTİ ===\n\n";

// Initialize Ticimax
$ticimax = new Ticimax($mainDomain, $apiKey);

// Yusuf'un bilgileri ve gerçek ürün ID'leri
$yusufUserId = 1055;
$realProductIds = [16, 14, 12, 11, 10, 9, 8, 7, 6, 5]; // Önceki testlerden bildiğimiz gerçek ID'ler

echo "Kullanıcı: Yusuf Kurnaz (ID: $yusufUserId)\n";
echo "Eklenecek ürünler: " . implode(', ', $realProductIds) . "\n\n";

// 1. Mevcut sepet durumunu kontrol et
echo "1. Yusuf'un mevcut sepet durumu:\n";
echo str_repeat("-", 50) . "\n";

$currentCart = $ticimax->cartService()->getCart($yusufUserId);

if ($currentCart->isSuccess()) {
    $cartData = $currentCart->getData();
    if (is_array($cartData) && count($cartData) > 0) {
        echo "✓ Mevcut sepette " . count($cartData) . " ürün var\n";
        foreach ($cartData as $index => $item) {
            echo "  " . ($index + 1) . ". " . ($item->UrunAdi ?? 'İsimsiz') . " - Adet: " . ($item->UrunSayisi ?? 'N/A') . "\n";
        }
    } else {
        echo "✓ Sepet boş\n";
    }
} else {
    echo "✗ Sepet getirilemedi: " . $currentCart->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n";

// 2. Sepeti temizle (eğer varsa)
echo "2. Sepeti temizleme:\n";
$clearResult = $ticimax->cartService()->clearCart($yusufUserId);
if ($clearResult->isSuccess()) {
    echo "✓ Sepet temizlendi\n";
} else {
    echo "✗ Sepet temizlenemedi: " . $clearResult->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n";

// 3. Gerçek ürünlerle sepete ekleme
echo "3. Gerçek ürünlerle sepete ekleme:\n";
$addedCount = 0;
$failedCount = 0;

foreach ($realProductIds as $index => $productId) {
    $quantity = ($index % 3) + 1; // 1, 2, 3 adet rotasyonu
    
    echo "  Ürün ID $productId ekleniyor (Adet: $quantity)...\n";
    
    $addResult = $ticimax->cartService()->addProductToCart($yusufUserId, $productId, $quantity);
    
    if ($addResult->isSuccess()) {
        echo "  ✓ Ürün ID $productId başarıyla eklendi ($quantity adet)\n";
        $addedCount++;
    } else {
        echo "  ✗ Ürün ID $productId eklenemedi: " . $addResult->getMessage() . "\n";
        $failedCount++;
    }
    
    // Kısa bekle
    usleep(300000); // 0.3 saniye
}

echo "\nÖzet: $addedCount ürün eklendi, $failedCount ürün eklenemedi\n";

echo "\n" . str_repeat("-", 50) . "\n";

// 4. Sepeti kontrol et
echo "4. Ekleme sonrası sepet kontrolü:\n";
$updatedCart = $ticimax->cartService()->getCart($yusufUserId);

if ($updatedCart->isSuccess()) {
    $updatedCartData = $updatedCart->getData();
    if (is_array($updatedCartData) && count($updatedCartData) > 0) {
        echo "✓ Sepette toplam " . count($updatedCartData) . " ürün var\n\n";
        
        $totalItems = 0;
        $totalValue = 0;
        
        foreach ($updatedCartData as $index => $item) {
            echo sprintf("  %d. %-30s | Adet: %-3s | Fiyat: %-10s | Toplam: %-10s\n",
                $index + 1,
                substr($item->UrunAdi ?? 'İsimsiz', 0, 30),
                $item->UrunSayisi ?? 'N/A',
                ($item->UrunFiyati ?? 0) . ' TL',
                (($item->UrunFiyati ?? 0) * ($item->UrunSayisi ?? 1)) . ' TL'
            );
            
            $totalItems += $item->UrunSayisi ?? 0;
            $totalValue += ($item->UrunFiyati ?? 0) * ($item->UrunSayisi ?? 1);
        }
        
        echo "\n  " . str_repeat("-", 80) . "\n";
        echo sprintf("  TOPLAM: %d ürün çeşidi | %d adet | %.2f TL\n", count($updatedCartData), $totalItems, $totalValue);
        
    } else {
        echo "⚠ Sepet hala boş görünüyor\n";
    }
} else {
    echo "✗ Güncel sepet getirilemedi: " . $updatedCart->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n";

// 5. Sepet işlemleri testi
echo "5. Sepet işlemleri testi:\n";

if ($updatedCart->isSuccess() && is_array($updatedCart->getData()) && count($updatedCart->getData()) > 0) {
    $cartItems = $updatedCart->getData();
    $firstItem = $cartItems[0];
    
    if (isset($firstItem->UrunKartiID)) {
        echo "5a. Ürün adedi güncelleme:\n";
        $newQuantity = 5;
        $updateResult = $ticimax->cartService()->updateCartItemQuantity($yusufUserId, $firstItem->UrunKartiID, $newQuantity);
        
        if ($updateResult->isSuccess()) {
            echo "  ✓ İlk ürünün adedi $newQuantity olarak güncellendi\n";
        } else {
            echo "  ✗ Ürün adedi güncellenemedi: " . $updateResult->getMessage() . "\n";
        }
        
        echo "\n5b. Belirli ürünü sepetten çıkarma:\n";
        if (count($cartItems) > 1) {
            $secondItem = $cartItems[1];
            if (isset($secondItem->UrunKartiID)) {
                $removeResult = $ticimax->cartService()->removeProductFromCart($yusufUserId, $secondItem->UrunKartiID);
                
                if ($removeResult->isSuccess()) {
                    echo "  ✓ İkinci ürün sepetten çıkarıldı\n";
                } else {
                    echo "  ✗ Ürün sepetten çıkarılamadı: " . $removeResult->getMessage() . "\n";
                }
            }
        }
    }
}

echo "\n" . str_repeat("-", 50) . "\n";

// 6. Final sepet durumu
echo "6. Final sepet durumu:\n";
$finalCart = $ticimax->cartService()->getCart($yusufUserId);

if ($finalCart->isSuccess()) {
    $finalCartData = $finalCart->getData();
    if (is_array($finalCartData) && count($finalCartData) > 0) {
        echo "✓ Final sepette " . count($finalCartData) . " ürün var\n";
        
        foreach ($finalCartData as $index => $item) {
            echo sprintf("  %d. %-30s | Adet: %s\n",
                $index + 1,
                substr($item->UrunAdi ?? 'İsimsiz', 0, 30),
                $item->UrunSayisi ?? 'N/A'
            );
        }
    } else {
        echo "✓ Sepet boş\n";
    }
} else {
    echo "✗ Final sepet getirilemedi: " . $finalCart->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "YUSUF KURNAZ SEPET TESTİ TAMAMLANDI!\n";
echo str_repeat("=", 60) . "\n";

// Test özeti
echo "\nTEST ÖZETİ:\n";
echo "- Kullanıcı: Yusuf Kurnaz (ID: $yusufUserId)\n";
echo "- Test edilen ürün sayısı: " . count($realProductIds) . "\n";
echo "- Başarıyla eklenen ürün sayısı: $addedCount\n";
echo "- Başarısız ekleme sayısı: $failedCount\n";
echo "- Sepet işlemleri: Ekleme, Güncelleme, Çıkarma, Temizleme\n";
echo "- Tüm sepet fonksiyonları test edildi\n\n";

echo "=== TEST TAMAMLANDI ===\n"; 