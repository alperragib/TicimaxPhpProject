<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$productService = $ticimax->productService();

echo "--- List Products ---\n";

$filters = [
    'Aktif'       => 1,
    'Firsat'      => -1,
    'Indirimli'   => -1,
    'Vitrin'      => -1,
    'KategoriID'  => 0,
    'MarkaID'     => 0,
    'UrunKartiID' => 0,
];

$pagination = [
    'BaslangicIndex'            => 0,
    'KayitSayisi'               => 10,
    'KayitSayisinaGoreGetir'    => true,
    'SiralamaDegeri'            => 'Sira',
    'SiralamaYonu'              => 'ASC',
];

$products = $productService->getProducts($filters, $pagination);

foreach ($products as $product) {
    $id = $product->ID ?? '[No ID]';
    $urunAdi = $product->UrunAdi ?? '[No UrunAdi]';
    $onYazi = $product->OnYazi ?? '[No OnYazi]';
    $ucretsizKargo = $product->UcretsizKargo ?? '[No UcretsizKargo]';

    $satisFiyati = '[No SatisFiyati]';
    $indirimliFiyati = '[No IndirimliFiyati]';
    $kdvOrani = '[No KdvOrani]';
    $paraBirimi = '[No ParaBirimi]';
    $desi = '[No Desi]';
    $kargoUcreti = '[No KargoUcreti]';

    if (!empty($product->Varyasyonlar) && is_array($product->Varyasyonlar)) {
        foreach ($product->Varyasyonlar as $varyasyon) {
            if (($varyasyon->Aktif ?? 0) == 1) {
                $satisFiyati = $varyasyon->SatisFiyati ?? $satisFiyati;
                $indirimliFiyati = $varyasyon->IndirimliFiyati ?? $indirimliFiyati;
                $kdvOrani = $varyasyon->KdvOrani ?? $kdvOrani;
                $paraBirimi = $varyasyon->ParaBirimi ?? $paraBirimi;
                $desi = $varyasyon->Desi ?? $desi;
                $kargoUcreti = $varyasyon->KargoUcreti ?? $kargoUcreti;
                break;
            }
        }
    }

    $resimler = '[No Resimler]';
    if (!empty($product->Resimler) && isset($product->Resimler['string'])) {
        $resimler = implode(', ', $product->Resimler['string']);
    }

    echo "\nID                   : $id";
    echo "\nUrunAdi              : $urunAdi";
    echo "\nOnYazi               : $onYazi";
    echo "\nUcretsizKargo        : $ucretsizKargo";
    echo "\nSatisFiyati          : $satisFiyati";
    echo "\nIndirimliFiyati      : $indirimliFiyati";
    echo "\nKdvOrani             : $kdvOrani";
    echo "\nParaBirimi           : $paraBirimi";
    echo "\nDesi                 : $desi";
    echo "\nKargoUcreti          : $kargoUcreti";
    echo "\nResimler             : $resimler";
    echo "\n\n---------------------------------------------\n";
}

echo "\n\n=== Yeni Metodların Testleri ===\n";

// 1. Ürün Yorumlarını Test Et
echo "\n--- Ürün Yorumları Testi ---\n";
try {
    $productId = 1; // Test edilecek ürün ID'si
    $reviews = $productService->GetProductReviews($productId);
    echo "Ürün ID: $productId için yorumlar:\n";
    foreach ($reviews as $review) {
        echo "Yorum ID: " . ($review['id'] ?? 'N/A') . "\n";
        echo "İsim: " . ($review['isim'] ?? 'N/A') . "\n";
        echo "Mesaj: " . ($review['mesaj'] ?? 'N/A') . "\n";
        echo "Tarih: " . ($review['eklemeTarihi'] ?? 'N/A') . "\n";
        echo "------------------------\n";
    }
} catch (Exception $e) {
    echo "Ürün yorumları alınırken hata: " . $e->getMessage() . "\n";
}

// 2. Ürün Varyasyonlarını Test Et
echo "\n--- Test Product Variations ---\n";
$variationFilters = [
    'Aktif' => 1,
    'UrunKartiID' => 1 // Test için örnek bir ürün kartı ID'si
];
$variationPagination = [
    'BaslangicIndex' => 0,
    'KayitSayisi' => 5
];
$variations = $productService->GetProductVariations($variationFilters, $variationPagination);
echo "Ürün Varyasyonları:\n";
foreach ($variations as $variation) {
    echo "\nVaryasyon ID   : " . ($variation->ID ?? 'N/A');
    echo "\nStok Kodu      : " . ($variation->StokKodu ?? 'N/A');
    echo "\nBarkod         : " . ($variation->Barkod ?? 'N/A');
    echo "\nStok Adedi     : " . ($variation->StokAdedi ?? 'N/A');
    echo "\nSatış Fiyatı   : " . ($variation->SatisFiyati ?? 'N/A');
    echo "\nİndirimli Fiyat: " . ($variation->IndirimliFiyati ?? 'N/A');
    echo "\nPara Birimi    : " . ($variation->ParaBirimi ?? 'N/A');
    echo "\nDesi           : " . ($variation->Desi ?? 'N/A');
    echo "\nKargo Ücreti   : " . ($variation->KargoUcreti ?? 'N/A');
    echo "\n---------------------------------------------\n";
}

// 3. Taksit Seçeneklerini Test Et
echo "\n--- Taksit Seçenekleri Testi ---\n";
try {
    $amount = 1000.00;
    $maxInstallments = 12;
    $options = $productService->GetInstallmentOptions($amount, $maxInstallments, 'TRY');
    foreach ($options as $bank) {
        echo "Banka: " . ($bank->BankaAdi ?? 'N/A') . "\n";
        if (isset($bank->Taksitler->UrunOdemeSecenekTaksit)) {
            $taksitler = $bank->Taksitler->UrunOdemeSecenekTaksit;
            if (is_object($taksitler)) {
                $taksitler = [$taksitler];
            }
            foreach ($taksitler as $taksit) {
                echo "  Taksit Sayısı: " . ($taksit->TaksitSayisi ?? 'N/A') . "\n";
                echo "  Taksit Tutarı: " . ($taksit->TaksitTutari ?? 'N/A') . "\n";
                echo "  Toplam Tutar: " . ($taksit->ToplamTutar ?? 'N/A') . "\n";
            }
        }
        echo "------------------------\n";
    }
} catch (Exception $e) {
    echo "Taksit seçenekleri alınırken hata: " . $e->getMessage() . "\n";
}

// 4. Mağaza Stok Bilgilerini Test Et
echo "\n--- Mağaza Stok Bilgileri Testi ---\n";
try {
    $storeCode = 'STORE001'; // Test edilecek mağaza kodu
    $storeStock = $productService->GetStoreStock($storeCode);
    if ($storeStock['success']) {
        echo "Mağaza: $storeCode için stok bilgileri:\n";
        foreach ($storeStock['data'] as $stock) {
            echo "Ürün ID: " . ($stock->UrunID ?? 'N/A') . "\n";
            echo "Stok Kodu: " . ($stock->StokKodu ?? 'N/A') . "\n";
            echo "Stok Adedi: " . ($stock->StokAdedi ?? 'N/A') . "\n";
            echo "------------------------\n";
        }
    } else {
        echo "Hata: " . $storeStock['message'] . "\n";
    }
} catch (Exception $e) {
    echo "Mağaza stok bilgileri alınırken hata: " . $e->getMessage() . "\n";
}

// 5. Kategori Bilgilerini Test Et
echo "\n--- Kategori Bilgileri Testi ---\n";
try {
    $categories = $productService->SelectKategori(0, 'TR');
    foreach ($categories as $category) {
        echo "Kategori ID: " . ($category->ID ?? 'N/A') . "\n";
        echo "Kategori Adı: " . ($category->Tanim ?? 'N/A') . "\n";
        echo "Üst Kategori: " . ($category->UstID ?? 'N/A') . "\n";
        echo "------------------------\n";
    }
} catch (Exception $e) {
    echo "Kategori bilgileri alınırken hata: " . $e->getMessage() . "\n";
}

// 6. Ürün Sayısını Test Et
echo "\n--- Ürün Sayısı Testi ---\n";
try {
    $countFilters = [
        'Aktif' => 1
    ];
    $totalProducts = $productService->SelectUrunCount($countFilters);
    echo "Aktif Ürün Sayısı: $totalProducts\n";
} catch (Exception $e) {
    echo "Ürün sayısı alınırken hata: " . $e->getMessage() . "\n";
}

// 7. Ödeme Seçeneklerini Test Et
echo "\n--- Ödeme Seçenekleri Testi ---\n";
try {
    $variationId = 1; // Test edilecek varyasyon ID'si
    $paymentOptions = $productService->SelectUrunOdemeSecenek($variationId);
    foreach ($paymentOptions as $option) {
        echo "Banka: " . ($option['bankaAdi'] ?? 'N/A') . "\n";
        foreach ($option['taksitler'] as $taksit) {
            echo "  Taksit Sayısı: " . ($taksit['taksitSayisi'] ?? 'N/A') . "\n";
            echo "  Taksit Tutarı: " . ($taksit['taksitTutari'] ?? 'N/A') . "\n";
            echo "  Toplam Tutar: " . ($taksit['toplamTutar'] ?? 'N/A') . "\n";
        }
        echo "------------------------\n";
    }
} catch (Exception $e) {
    echo "Ödeme seçenekleri alınırken hata: " . $e->getMessage() . "\n";
}

// 8. Stok Güncelleme Testi (DİKKAT: Bu test gerçek verileri değiştirir!)
echo "\n--- Stok Güncelleme Testi ---\n";
echo "NOT: Bu test şu anda devre dışı bırakılmıştır çünkü gerçek verileri değiştirir.\n";
echo "Test etmek için aşağıdaki kodu aktif hale getirin:\n";
/*
try {
    $stockUpdates = [
        [
            'ID' => 1, // Güncellenecek varyasyon ID'si
            'StokAdedi' => 100
        ]
    ];
    $updateResult = $productService->UpdateStockQuantity($stockUpdates);
    echo "Başarılı: " . ($updateResult['success'] ? 'Evet' : 'Hayır') . "\n";
    echo "Mesaj: " . $updateResult['message'] . "\n";
    if ($updateResult['success']) {
        echo "Güncellenen Kayıt Sayısı: " . $updateResult['data']['updatedCount'] . "\n";
    }
} catch (Exception $e) {
    echo "Stok güncellenirken hata: " . $e->getMessage() . "\n";
}
*/
