<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

echo "=== YUSUF KURNAZ BASIT SEPET TESTİ ===\n\n";

// Initialize Ticimax
$ticimax = new Ticimax($mainDomain, $apiKey);

$yusufUserId = 1055;

echo "Kullanıcı: Yusuf Kurnaz (ID: $yusufUserId)\n\n";

// 1. Mevcut sepeti kontrol et
echo "1. Yusuf'un mevcut sepeti:\n";
echo str_repeat("-", 40) . "\n";

$currentCart = $ticimax->cartService()->getSepet($yusufUserId);

if ($currentCart->isSuccess()) {
    $cartData = $currentCart->getData();
    echo "✓ Sepet başarıyla getirildi\n";
    
    if ($cartData) {
        echo "Sepet bilgileri:\n";
        echo "  - Sepet ID: " . ($cartData->ID ?? 'N/A') . "\n";
        echo "  - Genel Toplam: " . (string)($cartData->GenelToplam ?? '0') . " TL\n";
        echo "  - Toplam Ürün Adedi: " . (string)($cartData->ToplamUrunAdedi ?? '0') . "\n";
        echo "  - Para Birimi: " . ($cartData->SepetParaBirimiDilKodu ?? 'N/A') . "\n";
        
        if (!empty($cartData->Urunler)) {
            echo "  - Sepetteki Ürünler (" . count($cartData->Urunler) . " adet):\n";
            foreach ($cartData->Urunler as $index => $urun) {
                echo "    " . ($index + 1) . ". Ürün ID: " . ($urun['UrunID'] ?? 'N/A') . 
                     " - Adet: " . ($urun['Adet'] ?? 'N/A') . 
                     " - Ürün Adı: " . ($urun['UrunAdi'] ?? 'N/A') . "\n";
            }
        } else {
            echo "  - Sepet boş\n";
        }
        
        $currentCartId = $cartData->ID ?? null;
    } else {
        echo "  - Sepet verisi null\n";
        $currentCartId = null;
    }
} else {
    echo "✗ Sepet getirilemedi: " . $currentCart->getMessage() . "\n";
    $currentCartId = null;
}

echo "\n" . str_repeat("-", 40) . "\n";

// 2. Yeni sepet oluştur
echo "2. Yeni sepet oluşturma:\n";

$newCart = $ticimax->cartService()->createSepet($yusufUserId);

if ($newCart->isSuccess()) {
    $newCartData = $newCart->getData();
    echo "✓ Yeni sepet başarıyla oluşturuldu\n";
    
    if (is_array($newCartData)) {
        echo "Yeni sepet bilgileri:\n";
        echo "  - Sepet ID: " . ($newCartData['SepetID'] ?? 'N/A') . "\n";
        echo "  - Genel Toplam: " . (string)($newCartData['GenelToplam'] ?? '0') . " TL\n";
        echo "  - Toplam Ürün Adedi: " . (string)($newCartData['ToplamUrunAdedi'] ?? '0') . "\n";
        
        $newCartId = $newCartData['SepetID'] ?? null;
    } else {
        echo "  - Yeni sepet verisi beklenmedik format\n";
        echo "  - Data type: " . gettype($newCartData) . "\n";
        echo "  - Data: " . print_r($newCartData, true) . "\n";
        $newCartId = null;
    }
} else {
    echo "✗ Yeni sepet oluşturulamadı: " . $newCart->getMessage() . "\n";
    $newCartId = null;
}

echo "\n" . str_repeat("-", 40) . "\n";

// 3. Yeni sepeti kontrol et
if ($newCartId) {
    echo "3. Yeni sepeti ID ile kontrol:\n";
    
    $checkNewCart = $ticimax->cartService()->getSepet($yusufUserId, $newCartId);
    
    if ($checkNewCart->isSuccess()) {
        $checkCartData = $checkNewCart->getData();
        echo "✓ Yeni sepet ID ile başarıyla getirildi\n";
        
        if ($checkCartData) {
            echo "Kontrol sepet bilgileri:\n";
            echo "  - Sepet ID: " . ($checkCartData->ID ?? 'N/A') . "\n";
            echo "  - Genel Toplam: " . ($checkCartData->GenelToplam ?? '0') . " TL\n";
            echo "  - Toplam Ürün Adedi: " . ($checkCartData->ToplamUrunAdedi ?? '0') . "\n";
        }
    } else {
        echo "✗ Yeni sepet ID ile getirilemedi: " . $checkNewCart->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("-", 40) . "\n";

// 4. selectSepet ile sepetleri listele
echo "4. Kullanıcının tüm sepetlerini listele:\n";

$allCarts = $ticimax->cartService()->selectSepet(null, $yusufUserId);

if ($allCarts->isSuccess()) {
    $allCartsData = $allCarts->getData();
    echo "✓ Tüm sepetler başarıyla getirildi\n";
    
    if (is_array($allCartsData) && count($allCartsData) > 0) {
        echo "Toplam " . count($allCartsData) . " sepet bulundu:\n";
        foreach ($allCartsData as $index => $cart) {
            echo "  " . ($index + 1) . ". Sepet ID: " . ($cart->ID ?? 'N/A') . 
                 " - Toplam: " . (string)($cart->GenelToplam ?? '0') . " TL" .
                 " - Tarih: " . ($cart->SepetTarihi ?? 'N/A') . "\n";
        }
    } else {
        echo "Hiç sepet bulunamadı\n";
    }
} else {
    echo "✗ Sepetler listelenemedi: " . $allCarts->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 40) . "\n";

// 5. selectWebSepet ile web sepetlerini kontrol et
echo "5. Web sepetlerini kontrol:\n";

$webCarts = $ticimax->cartService()->selectWebSepet(null, null, null, $yusufUserId);

if ($webCarts->isSuccess()) {
    $webCartsData = $webCarts->getData();
    echo "✓ Web sepetler başarıyla getirildi\n";
    
    if ($webCartsData) {
        echo "Web sepet verisi:\n";
        echo print_r($webCartsData, true) . "\n";
    } else {
        echo "Web sepet verisi null\n";
    }
} else {
    echo "✗ Web sepetler getirilemedi: " . $webCarts->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "YUSUF KURNAZ BASIT SEPET TESTİ TAMAMLANDI!\n";
echo str_repeat("=", 50) . "\n";

echo "\nTEST ÖZETİ:\n";
echo "- Kullanıcı: Yusuf Kurnaz (ID: $yusufUserId)\n";
echo "- Mevcut sepet: " . ($currentCartId ? "ID $currentCartId" : "Yok") . "\n";
echo "- Yeni sepet: " . ($newCartId ? "ID $newCartId" : "Oluşturulamadı") . "\n";

echo "\n=== TEST TAMAMLANDI ===\n"; 