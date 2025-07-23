<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\TicimaxRequest;
use AlperRagib\Ticimax\Service\Product\ProductService;
use AlperRagib\Ticimax\Model\Response\ApiResponse;

// Load configuration
$config = require __DIR__ . '/config.php';

echo "=== PRODUCT VARIATIONS TEST ===\n";
echo "Testing ProductService::GetProductVariations()\n";
echo "API URL: {$config['mainDomain']}\n\n";

try {
    // Initialize
    $request        = new TicimaxRequest($config['mainDomain'], $config['apiKey']);
    $productService = new ProductService($request);

    // 1. Get first 10 variations
    echo "1. Getting first 10 product variations...\n";
    $resp = $productService->getProductVariations([], ['KayitSayisi' => 10]);
    if ($resp instanceof ApiResponse && $resp->isSuccess()) {
        $allVariations = $resp->getData();
        echo "Found " . count($allVariations) . " variation(s)\n";
        // show first 3 as example
        foreach (array_slice($allVariations, 0, 3) as $i => $v) {
            printf(
                "  #%d: ID=%s, UrunID=%s, Barkod=%s, StokKodu=%s, StokAdedi=%s, Fiyat=%s, Aktif=%s\n",
                $i + 1,
                $v->ID        ?? 'N/A',
                $v->UrunID   ?? 'N/A',
                $v->Barkod   ?? 'N/A',
                $v->StokKodu ?? 'N/A',
                $v->StokAdedi?? 'N/A',
                $v->Fiyat    ?? 'N/A',
                ($v->Aktif   ?? false) ? 'Yes' : 'No'
            );
        }
    } else {
        echo "Error: " . ($resp->getMessage() ?? 'Unable to fetch variations') . "\n";
        exit(1);
    }
    echo "\n";

    // 2. Active variations only
    echo "2. Getting active variations only (limit 5)...\n";
    $resp = $productService->getProductVariations(['Aktif' => 1], ['KayitSayisi' => 5]);
    if ($resp instanceof ApiResponse && $resp->isSuccess()) {
        $active = $resp->getData();
        echo "Found " . count($active) . " active variation(s)\n";
    } else {
        echo "Error: " . ($resp->getMessage() ?? 'Unable to fetch active variations') . "\n";
    }
    echo "\n";

    // 3. By barcode
    if (!empty($allVariations[0]->Barkod)) {
        $code = $allVariations[0]->Barkod;
        echo "3. Variations by barcode '$code'...\n";
        $resp = $productService->getProductVariations(['Barkod' => $code], ['KayitSayisi' => 5]);
        if ($resp instanceof ApiResponse && $resp->isSuccess()) {
            echo "Found " . count($resp->getData()) . " variation(s)\n";
        } else {
            echo "No variations with that barcode\n";
        }
    } else {
        echo "3. Skipping barcode test (no barcode available)\n";
    }
    echo "\n";

    // 4. By stock code
    if (!empty($allVariations[0]->StokKodu)) {
        $sc = $allVariations[0]->StokKodu;
        echo "4. Variations by stock code '$sc'...\n";
        $resp = $productService->getProductVariations(['StokKodu' => $sc], ['KayitSayisi' => 5]);
        if ($resp instanceof ApiResponse && $resp->isSuccess()) {
            echo "Found " . count($resp->getData()) . " variation(s)\n";
        } else {
            echo "No variations with that stock code\n";
        }
    } else {
        echo "4. Skipping stock code test (no stock code available)\n";
    }
    echo "\n";

    // 5. By product ID
    if (!empty($allVariations[0]->UrunID)) {
        $pid = $allVariations[0]->UrunID;
        echo "5. Variations by product ID '$pid'...\n";
        $resp = $productService->getProductVariations(['UrunID' => $pid], ['KayitSayisi' => 10]);
        if ($resp instanceof ApiResponse && $resp->isSuccess()) {
            echo "Found " . count($resp->getData()) . " variation(s)\n";
        } else {
            echo "No variations for that product ID\n";
        }
    } else {
        echo "5. Skipping product ID test (no product ID available)\n";
    }
    echo "\n";

    // 6. By product card ID
    if (!empty($allVariations[0]->UrunKartiID)) {
        $pcid = $allVariations[0]->UrunKartiID;
        echo "6. Variations by product card ID '$pcid'...\n";
        $resp = $productService->getProductVariations(['UrunKartiID' => $pcid], ['KayitSayisi' => 10]);
        if ($resp instanceof ApiResponse && $resp->isSuccess()) {
            echo "Found " . count($resp->getData()) . " variation(s)\n";
        } else {
            echo "No variations for that product card ID\n";
        }
    } else {
        echo "6. Skipping product card ID test (no product card ID available)\n";
    }
    echo "\n";

    // 7. Pagination test
    echo "7. Pagination: page 1 vs page 2 (3 each)...\n";
    $p1 = $productService->getProductVariations([], ['BaslangicIndex' => 0, 'KayitSayisi' => 3])->getData();
    $p2 = $productService->getProductVariations([], ['BaslangicIndex' => 3, 'KayitSayisi' => 3])->getData();
    echo "Page1: " . count($p1) . ", Page2: " . count($p2) . "\n";
    echo (($p1[0]->ID ?? '') !== ($p2[0]->ID ?? '') ? "Pagination works correctly\n" : "Pagination may be broken\n");
    echo "\n";

    // 8. Sorting test
    echo "8. Sorting: ASC vs DESC (5 each)...\n";
    $asc  = $productService->getProductVariations([], ['SiralamaYonu' => 'ASC',  'KayitSayisi' => 5])->getData();
    $desc = $productService->getProductVariations([], ['SiralamaYonu' => 'DESC', 'KayitSayisi' => 5])->getData();
    echo "First ASC ID: " . ($asc[0]->ID  ?? 'N/A') . "\n";
    echo "First DESC ID: " . ($desc[0]->ID ?? 'N/A') . "\n";
    echo (($asc[0]->ID ?? '') !== ($desc[0]->ID ?? '') ? "Sorting works correctly\n" : "Sorting may be broken\n");
    echo "\n";

    // Summary
    echo "=== SUMMARY ===\n";
    $total       = count($allVariations);
    $activeCount = count(array_filter($allVariations, fn($v) => $v->Aktif));
    $inStock     = count(array_filter($allVariations, fn($v) => ($v->StokAdedi ?? 0) > 0));
    echo "Total variations fetched: $total\n";
    echo "Active: $activeCount, In stock: $inStock\n";

} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== PRODUCT VARIATIONS TEST COMPLETED ===\n";
