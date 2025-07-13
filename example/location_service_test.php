<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Configuration
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$locationService = $ticimax->locationService();

echo "=== LocationService Comprehensive Test ===\n\n";

// Test 1: Get Countries
echo "🌍 Test 1: getCountries() - Ülke Listesi\n";
echo "----------------------------------------\n";
try {
    $response = $locationService->getCountries();
    
    if ($response->isSuccess()) {
        $countries = $response->getData();
        echo "✅ Başarılı! " . count($countries) . " ülke getirildi.\n\n";
        
        if (!empty($countries)) {
            echo "🌎 ÜLKE LİSTESİ:\n";
            foreach ($countries as $index => $country) {
                echo "  " . ($index + 1) . ". ID: " . ($country->ID ?? 'N/A') . "\n";
                echo "     Ad: " . ($country->Tanim ?? 'N/A') . "\n";
                echo "     Kod: " . ($country->UlkeKodu ?? 'N/A') . "\n";
                echo "     ---\n";
                
                if ($index >= 4) { // İlk 5 ülkeyi göster
                    if (count($countries) > 5) {
                        echo "  ... ve " . (count($countries) - 5) . " ülke daha\n";
                    }
                    break;
                }
            }
        }
    } else {
        echo "❌ Hata: " . $response->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Get Cities for Turkey (if exists)
echo "🏙️ Test 2: getCities() - Şehir Listesi\n";
echo "---------------------------------------\n";
try {
    // Türkiye için şehirler (varsayılan olarak tüm şehirler)
    $response = $locationService->getCities();
    
    if ($response->isSuccess()) {
        $cities = $response->getData();
        echo "✅ Başarılı! " . count($cities) . " şehir getirildi.\n\n";
        
        if (!empty($cities)) {
            echo "🏙️ ŞEHİR LİSTESİ:\n";
            foreach ($cities as $index => $city) {
                echo "  " . ($index + 1) . ". ID: " . ($city->ID ?? 'N/A') . "\n";
                echo "     Ad: " . ($city->Tanim ?? 'N/A') . "\n";
                echo "     Ülke ID: " . ($city->UlkeID ?? 'N/A') . "\n";
                echo "     ---\n";
                
                if ($index >= 9) { // İlk 10 şehri göster
                    if (count($cities) > 10) {
                        echo "  ... ve " . (count($cities) - 10) . " şehir daha\n";
                    }
                    break;
                }
            }
        }
    } else {
        echo "❌ Hata: " . $response->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Get Districts for a specific city (İstanbul örneği)
echo "🏘️ Test 3: getDistricts() - İlçe Listesi\n";
echo "-----------------------------------------\n";
try {
    // İstanbul için ilçeler (ID'si bilinmiyorsa tüm ilçeler)
    $response = $locationService->getDistricts();
    
    if ($response->isSuccess()) {
        $districts = $response->getData();
        echo "✅ Başarılı! " . count($districts) . " ilçe getirildi.\n\n";
        
        if (!empty($districts)) {
            echo "🏘️ İLÇE LİSTESİ:\n";
            foreach ($districts as $index => $district) {
                echo "  " . ($index + 1) . ". ID: " . ($district->ID ?? 'N/A') . "\n";
                echo "     Ad: " . ($district->Tanim ?? 'N/A') . "\n";
                echo "     İl ID: " . ($district->ILID ?? 'N/A') . "\n";
                echo "     ---\n";
                
                if ($index >= 14) { // İlk 15 ilçeyi göster
                    if (count($districts) > 15) {
                        echo "  ... ve " . (count($districts) - 15) . " ilçe daha\n";
                    }
                    break;
                }
            }
        }
    } else {
        echo "❌ Hata: " . $response->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Get Menu (MenuService'den)
echo "📋 Test 4: getMenus() - Menü Listesi (MenuService)\n";
echo "--------------------------------------------------\n";
try {
    $menuService = $ticimax->menuService();
    $response = $menuService->getMenus();
    
    if ($response->isSuccess()) {
        $menus = $response->getData();
        echo "✅ Başarılı! " . count($menus) . " menü öğesi getirildi.\n\n";
        
        if (!empty($menus)) {
            echo "📋 MENÜ LİSTESİ:\n";
            foreach ($menus as $index => $menu) {
                echo "  " . ($index + 1) . ". ID: " . ($menu->ID ?? 'N/A') . "\n";
                echo "     Ad: " . ($menu->MenuAdi ?? 'N/A') . "\n";
                echo "     Link: " . ($menu->Link ?? 'N/A') . "\n";
                echo "     Aktif: " . (($menu->Aktif ?? false) ? 'Evet' : 'Hayır') . "\n";
                echo "     Sıra: " . ($menu->Sira ?? 'N/A') . "\n";
                echo "     ---\n";
                
                if ($index >= 9) { // İlk 10 menüyü göster
                    if (count($menus) > 10) {
                        echo "  ... ve " . (count($menus) - 10) . " menü daha\n";
                    }
                    break;
                }
            }
        }
    } else {
        echo "❌ Hata: " . $response->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Get Shipping Companies
echo "🚚 Test 5: getShippingCompanies() - Kargo Firmaları\n";
echo "---------------------------------------------------\n";
try {
    $response = $locationService->getShippingCompanies();
    
    if ($response->isSuccess()) {
        $companies = $response->getData();
        echo "✅ Başarılı! " . count($companies) . " kargo firması getirildi.\n\n";
        
        if (!empty($companies)) {
            echo "🚚 KARGO FİRMALARI:\n";
            foreach ($companies as $index => $company) {
                echo "  " . ($index + 1) . ". ID: " . ($company['ID'] ?? 'N/A') . "\n";
                echo "     Firma Adı: " . ($company['FirmaAdi'] ?? 'N/A') . "\n";
                echo "     Firma Kodu: " . ($company['FirmaKodu'] ?? 'N/A') . "\n";
                echo "     Aktif: " . (($company['Aktif'] ?? false) ? 'Evet' : 'Hayır') . "\n";
                echo "     Website: " . ($company['Website'] ?? 'N/A') . "\n";
                echo "     Takip URL: " . ($company['TakipURL'] ?? 'N/A') . "\n";
                echo "     ---\n";
            }
        }
    } else {
        echo "❌ Hata: " . $response->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== Test Tamamlandı ===\n";
echo "LocationService fonksiyonları ve MenuService test edildi.\n";
echo "\n📝 LocationService fonksiyonları:\n";
echo "  ✅ getCountries() - Ülkeler\n";
echo "  ✅ getCities() - Şehirler\n";  
echo "  ✅ getDistricts() - İlçeler\n";
echo "  ✅ getShippingCompanies() - Kargo firmaları\n";
echo "\n📝 MenuService fonksiyonları:\n";
echo "  ✅ getMenus() - Menü öğeleri\n"; 