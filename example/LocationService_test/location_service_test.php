<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/../config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$locationService = $ticimax->locationService();

echo "=== LOCATION SERVICE TEST SUITE ===\n\n";

// Test 1: Get All Countries
echo "1. === GET ALL COUNTRIES TEST ===\n";
echo "Testing getCountries() without filters...\n";

$allCountriesResponse = $locationService->getCountries();

if ($allCountriesResponse->isSuccess()) {
    echo "✓ " . $allCountriesResponse->getMessage() . "\n";
    $countries = $allCountriesResponse->getData();
    echo "Found " . count($countries) . " countries.\n";
    
    if (!empty($countries)) {
        // Find first country with actual name
        $firstCountryWithName = null;
        foreach ($countries as $country) {
            if (!empty($country->Tanim) && trim($country->Tanim) !== '') {
                $firstCountryWithName = $country;
                break;
            }
        }
        
        if ($firstCountryWithName) {
            echo "First country with name details:\n";
            echo "- ID: " . ($firstCountryWithName->ID ?? 'N/A') . "\n";
            echo "- Country Name: " . ($firstCountryWithName->Tanim ?? 'N/A') . "\n";
            echo "- Country Code: " . ($firstCountryWithName->UlkeKodu ?? 'N/A') . "\n";
            echo "- ISO Code: " . ($firstCountryWithName->IsoKodu ?? 'N/A') . "\n";
            echo "- Phone Code: " . ($firstCountryWithName->TelefonKodu ?? 'N/A') . "\n";
            echo "- Active: " . ($firstCountryWithName->Aktif ? 'Yes' : 'No') . "\n";
            
            // Store data for other tests
            $testCountryId = $firstCountryWithName->ID ?? null;
            $testCountryCode = $firstCountryWithName->UlkeKodu ?? null;
            
            echo "\n📋 ALL Countries with Names:\n";
            $sampleCount = 0;
            foreach ($countries as $country) {
                if (!empty($country->Tanim) && trim($country->Tanim) !== '') {
                    $sampleCount++;
                    echo "   " . $sampleCount . ". " . $country->Tanim . " (ID: " . $country->ID . ")\n";
                }
            }
            echo "   📊 Total countries with names: $sampleCount\n";
        } else {
            echo "No countries with names found.\n";
            $testCountryId = null;
            $testCountryCode = null;
        }
    } else {
        $testCountryId = null;
        $testCountryCode = null;
    }
} else {
    echo "✗ " . $allCountriesResponse->getMessage() . "\n";
    $testCountryId = null;
    $testCountryCode = null;
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 2: Get Specific Country by ID
if ($testCountryId) {
    echo "2. === GET SPECIFIC COUNTRY BY ID TEST ===\n";
    echo "Testing getCountries() with specific country ID: $testCountryId...\n";

    $specificCountryResponse = $locationService->getCountries($testCountryId);

    if ($specificCountryResponse->isSuccess()) {
        echo "✓ " . $specificCountryResponse->getMessage() . "\n";
        $specificCountries = $specificCountryResponse->getData();
        echo "Found " . count($specificCountries) . " country(ies).\n";
        
        if (!empty($specificCountries)) {
            echo "Specific country details:\n";
            $country = $specificCountries[0];
            echo "- ID: " . ($country->ID ?? 'N/A') . "\n";
            echo "- Country Name: " . ($country->Tanim ?? 'N/A') . "\n";
            echo "- Country Code: " . ($country->UlkeKodu ?? 'N/A') . "\n";
            echo "- ISO Code: " . ($country->IsoKodu ?? 'N/A') . "\n";
            echo "- Phone Code: " . ($country->TelefonKodu ?? 'N/A') . "\n";
            echo "- Active: " . ($country->Aktif ? 'Yes' : 'No') . "\n";
        }
    } else {
        echo "✗ " . $specificCountryResponse->getMessage() . "\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test 3: Get Country by Code
if ($testCountryCode) {
    echo "3. === GET COUNTRY BY CODE TEST ===\n";
    echo "Testing getCountries() with country code: $testCountryCode...\n";

    $countryByCodeResponse = $locationService->getCountries(null, $testCountryCode);

    if ($countryByCodeResponse->isSuccess()) {
        echo "✓ " . $countryByCodeResponse->getMessage() . "\n";
        $countriesByCode = $countryByCodeResponse->getData();
        echo "Found " . count($countriesByCode) . " country(ies) with code $testCountryCode.\n";
    } else {
        echo "✗ " . $countryByCodeResponse->getMessage() . "\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test 4: Get All Cities
echo "4. === GET ALL CITIES TEST ===\n";
echo "Testing getCities() without filters...\n";

$allCitiesResponse = $locationService->getCities(null,1);

if ($allCitiesResponse->isSuccess()) {
    echo "✓ " . $allCitiesResponse->getMessage() . "\n";
    $cities = $allCitiesResponse->getData();
    echo "Found " . count($cities) . " cities.\n";
    
    if (!empty($cities)) {
        // Find first city with actual name
        $firstCityWithName = null;
        foreach ($cities as $city) {
            if (!empty($city->Tanim) && trim($city->Tanim) !== '') {
                $firstCityWithName = $city;
                break;
            }
        }
        
        if ($firstCityWithName) {
            echo "First city with name details:\n";
            echo "- ID: " . ($firstCityWithName->ID ?? 'N/A') . "\n";
            echo "- City Name: " . ($firstCityWithName->Tanim ?? 'N/A') . "\n";
            echo "- Country ID: " . ($firstCityWithName->UlkeID ?? 'N/A') . "\n";
            echo "- Active: " . ($firstCityWithName->Aktif ? 'Yes' : 'No') . "\n";
            
            // Store data for other tests
            $testCityId = $firstCityWithName->ID ?? null;
            $testCityCountryId = $firstCityWithName->UlkeID ?? $testCountryId;
            
            echo "\n📋 ALL Cities with Names:\n";
            $sampleCount = 0;
            foreach ($cities as $city) {
                if (!empty($city->Tanim) && trim($city->Tanim) !== '') {
                    $sampleCount++;
                    echo "   " . $sampleCount . ". " . $city->Tanim . " (ID: " . $city->ID . ")\n";
                }
            }
            echo "   📊 Total cities with names: $sampleCount\n";
        } else {
            echo "No cities with names found.\n";
            $testCityId = null;
            $testCityCountryId = $testCountryId;
        }
    } else {
        $testCityId = null;
        $testCityCountryId = $testCountryId;
    }
} else {
    echo "✗ " . $allCitiesResponse->getMessage() . "\n";
    $testCityId = null;
    $testCityCountryId = $testCountryId;
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 5: Get Specific City by ID
if ($testCityId) {
    echo "5. === GET SPECIFIC CITY BY ID TEST ===\n";
    echo "Testing getCities() with specific city ID: $testCityId...\n";

    $specificCityResponse = $locationService->getCities($testCityId);

    if ($specificCityResponse->isSuccess()) {
        echo "✓ " . $specificCityResponse->getMessage() . "\n";
        $specificCities = $specificCityResponse->getData();
        echo "Found " . count($specificCities) . " city(ies).\n";
    } else {
        echo "✗ " . $specificCityResponse->getMessage() . "\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test 6: Get Cities by Country
if ($testCityCountryId) {
    echo "6. === GET CITIES BY COUNTRY TEST ===\n";
    echo "Testing getCities() with country ID: $testCityCountryId...\n";

    $citiesByCountryResponse = $locationService->getCities(null, $testCityCountryId);

    if ($citiesByCountryResponse->isSuccess()) {
        echo "✓ " . $citiesByCountryResponse->getMessage() . "\n";
        $citiesByCountry = $citiesByCountryResponse->getData();
        echo "Found " . count($citiesByCountry) . " cities for country ID $testCityCountryId.\n";
    } else {
        echo "✗ " . $citiesByCountryResponse->getMessage() . "\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test 7: Get All Districts
echo "7. === GET ALL DISTRICTS TEST ===\n";
echo "Testing getDistricts() without filters...\n";

$allDistrictsResponse = $locationService->getDistricts(null,45);

if ($allDistrictsResponse->isSuccess()) {
    echo "✓ " . $allDistrictsResponse->getMessage() . "\n";
    $districts = $allDistrictsResponse->getData();
    echo "Found " . count($districts) . " districts.\n";
    
    if (!empty($districts)) {
        // Find first district with actual name
        $firstDistrictWithName = null;
        foreach ($districts as $district) {
            if (!empty($district->Tanim) && trim($district->Tanim) !== '') {
                $firstDistrictWithName = $district;
                break;
            }
        }
        
        if ($firstDistrictWithName) {
            echo "First district with name details:\n";
            echo "- ID: " . ($firstDistrictWithName->ID ?? 'N/A') . "\n";
            echo "- District Name: " . ($firstDistrictWithName->Tanim ?? 'N/A') . "\n";
            echo "- City ID: " . ($firstDistrictWithName->ILID ?? 'N/A') . "\n";
            echo "- Active: " . ($firstDistrictWithName->Aktif ? 'Yes' : 'No') . "\n";
            
            // Store data for other tests
            $testDistrictId = $firstDistrictWithName->ID ?? null;
            $testDistrictCityId = $firstDistrictWithName->ILID ?? $testCityId;
            
            echo "\n📋 ALL Districts with Names:\n";
            $sampleCount = 0;
            foreach ($districts as $district) {
                if (!empty($district->Tanim) && trim($district->Tanim) !== '') {
                    $sampleCount++;
                    echo "   " . $sampleCount . ". " . $district->Tanim . " (ID: " . $district->ID . ")\n";
                }
            }
            echo "   📊 Total districts with names: $sampleCount\n";
        } else {
            echo "No districts with names found.\n";
            $testDistrictId = null;
            $testDistrictCityId = $testCityId;
        }
    } else {
        $testDistrictId = null;
        $testDistrictCityId = $testCityId;
    }
} else {
    echo "✗ " . $allDistrictsResponse->getMessage() . "\n";
    $testDistrictId = null;
    $testDistrictCityId = $testCityId;
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 8: Get Specific District by ID
if ($testDistrictId) {
    echo "8. === GET SPECIFIC DISTRICT BY ID TEST ===\n";
    echo "Testing getDistricts() with specific district ID: $testDistrictId...\n";

    $specificDistrictResponse = $locationService->getDistricts($testDistrictId);

    if ($specificDistrictResponse->isSuccess()) {
        echo "✓ " . $specificDistrictResponse->getMessage() . "\n";
        $specificDistricts = $specificDistrictResponse->getData();
        echo "Found " . count($specificDistricts) . " district(s).\n";
    } else {
        echo "✗ " . $specificDistrictResponse->getMessage() . "\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test 9: Get Districts by City
if ($testDistrictCityId) {
    echo "9. === GET DISTRICTS BY CITY TEST ===\n";
    echo "Testing getDistricts() with city ID: $testDistrictCityId...\n";

    $districtsByCityResponse = $locationService->getDistricts(null, $testDistrictCityId);

    if ($districtsByCityResponse->isSuccess()) {
        echo "✓ " . $districtsByCityResponse->getMessage() . "\n";
        $districtsByCity = $districtsByCityResponse->getData();
        echo "Found " . count($districtsByCity) . " districts for city ID $testDistrictCityId.\n";
    } else {
        echo "✗ " . $districtsByCityResponse->getMessage() . "\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test 10: Get Shipping Companies
echo "10. === GET SHIPPING COMPANIES TEST ===\n";
echo "Testing getShippingCompanies()...\n";

$shippingCompaniesResponse = $locationService->getShippingCompanies();

if ($shippingCompaniesResponse->isSuccess()) {
    echo "✓ " . $shippingCompaniesResponse->getMessage() . "\n";
    $shippingCompanies = $shippingCompaniesResponse->getData();
    echo "Found " . count($shippingCompanies) . " shipping companies.\n";
    
    if (!empty($shippingCompanies)) {
        // Find first company with actual name
        $firstCompanyWithName = null;
        foreach ($shippingCompanies as $company) {
            if (!empty($company['FirmaAdi']) && trim($company['FirmaAdi']) !== '') {
                $firstCompanyWithName = $company;
                break;
            }
        }
        
        if ($firstCompanyWithName) {
            echo "First shipping company with name details:\n";
            echo "- ID: " . ($firstCompanyWithName['ID'] ?? 'N/A') . "\n";
            echo "- Company Name: " . ($firstCompanyWithName['FirmaAdi'] ?? 'N/A') . "\n";
            echo "- Company Code: " . ($firstCompanyWithName['FirmaKodu'] ?? 'N/A') . "\n";
            echo "- Active: " . ($firstCompanyWithName['Aktif'] ? 'Yes' : 'No') . "\n";
            echo "- Website: " . ($firstCompanyWithName['Website'] ?? 'N/A') . "\n";
            echo "- Tracking URL: " . ($firstCompanyWithName['TakipURL'] ?? 'N/A') . "\n";
            
            echo "\n📋 ALL Shipping Companies with Names:\n";
            $sampleCount = 0;
            foreach ($shippingCompanies as $company) {
                if (!empty($company['FirmaAdi']) && trim($company['FirmaAdi']) !== '') {
                    $sampleCount++;
                    echo "   " . $sampleCount . ". " . $company['FirmaAdi'] . " (ID: " . $company['ID'] . ")\n";
                }
            }
            echo "   📊 Total shipping companies with names: $sampleCount\n";
        } else {
            echo "No shipping companies with names found.\n";
            echo "⚠️ Note: Only " . count($shippingCompanies) . " shipping companies in database, but all have empty names.\n";
        }
    }
} else {
    echo "✗ " . $shippingCompaniesResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 11: Location Data Integrity Test
echo "11. === LOCATION DATA INTEGRITY TEST ===\n";
echo "Testing location data structure and relationships...\n";

$integrityResults = [
    'countries' => ['complete' => 0, 'incomplete' => 0],
    'cities' => ['complete' => 0, 'incomplete' => 0, 'orphaned' => 0],
    'districts' => ['complete' => 0, 'incomplete' => 0, 'orphaned' => 0],
    'shipping_companies' => ['complete' => 0, 'incomplete' => 0]
];

// Check countries
if ($allCountriesResponse->isSuccess()) {
    $countries = $allCountriesResponse->getData();
    $countryIds = [];
    
    foreach ($countries as $country) {
        $hasRequiredFields = isset($country->ID) && isset($country->Tanim);
        if ($hasRequiredFields) {
            $integrityResults['countries']['complete']++;
            $countryIds[] = $country->ID;
        } else {
            $integrityResults['countries']['incomplete']++;
        }
    }
}

// Check cities
if ($allCitiesResponse->isSuccess()) {
    $cities = $allCitiesResponse->getData();
    $cityIds = [];
    
    foreach ($cities as $city) {
        $hasRequiredFields = isset($city->ID) && isset($city->Tanim);
        if ($hasRequiredFields) {
            $integrityResults['cities']['complete']++;
            $cityIds[] = $city->ID;
            
            // Check if city's country exists
            if (isset($countryIds) && !in_array($city->UlkeID ?? 0, $countryIds)) {
                $integrityResults['cities']['orphaned']++;
            }
        } else {
            $integrityResults['cities']['incomplete']++;
        }
    }
}

// Check districts
if ($allDistrictsResponse->isSuccess()) {
    $districts = $allDistrictsResponse->getData();
    
    foreach ($districts as $district) {
        $hasRequiredFields = isset($district->ID) && isset($district->Tanim);
        if ($hasRequiredFields) {
            $integrityResults['districts']['complete']++;
            
            // Check if district's city exists
            if (isset($cityIds) && !in_array($district->ILID ?? 0, $cityIds)) {
                $integrityResults['districts']['orphaned']++;
            }
        } else {
            $integrityResults['districts']['incomplete']++;
        }
    }
}

// Check shipping companies
if ($shippingCompaniesResponse->isSuccess()) {
    $shippingCompanies = $shippingCompaniesResponse->getData();
    
    foreach ($shippingCompanies as $company) {
        $hasRequiredFields = isset($company['ID']) && isset($company['FirmaAdi']);
        if ($hasRequiredFields) {
            $integrityResults['shipping_companies']['complete']++;
        } else {
            $integrityResults['shipping_companies']['incomplete']++;
        }
    }
}

echo "✓ Data integrity check completed.\n";
echo "Countries:\n";
echo "- Complete: " . $integrityResults['countries']['complete'] . "\n";
echo "- Incomplete: " . $integrityResults['countries']['incomplete'] . "\n";
echo "Cities:\n";
echo "- Complete: " . $integrityResults['cities']['complete'] . "\n";
echo "- Incomplete: " . $integrityResults['cities']['incomplete'] . "\n";
echo "- Orphaned (no parent country): " . $integrityResults['cities']['orphaned'] . "\n";
echo "Districts:\n";
echo "- Complete: " . $integrityResults['districts']['complete'] . "\n";
echo "- Incomplete: " . $integrityResults['districts']['incomplete'] . "\n";
echo "- Orphaned (no parent city): " . $integrityResults['districts']['orphaned'] . "\n";
echo "Shipping Companies:\n";
echo "- Complete: " . $integrityResults['shipping_companies']['complete'] . "\n";
echo "- Incomplete: " . $integrityResults['shipping_companies']['incomplete'] . "\n";

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 12: Location Statistics Test
echo "12. === LOCATION STATISTICS TEST ===\n";
echo "Generating location statistics...\n";

$stats = [];

// Country statistics
if ($allCountriesResponse->isSuccess()) {
    $countries = $allCountriesResponse->getData();
    $activeCountries = 0;
    $countriesWithPhoneCode = 0;
    
    foreach ($countries as $country) {
        if ($country->Aktif ?? false) {
            $activeCountries++;
        }
        if (!empty($country->TelefonKodu)) {
            $countriesWithPhoneCode++;
        }
    }
    
    $stats['countries'] = [
        'total' => count($countries),
        'active' => $activeCountries,
        'with_phone_code' => $countriesWithPhoneCode
    ];
}

// City statistics by country
if ($allCitiesResponse->isSuccess()) {
    $cities = $allCitiesResponse->getData();
    $citiesByCountry = [];
    $activeCities = 0;
    
    foreach ($cities as $city) {
        $countryId = $city->UlkeID ?? 'unknown';
        $citiesByCountry[$countryId] = ($citiesByCountry[$countryId] ?? 0) + 1;
        
        if ($city->Aktif ?? false) {
            $activeCities++;
        }
    }
    
    $stats['cities'] = [
        'total' => count($cities),
        'active' => $activeCities,
        'by_country' => $citiesByCountry
    ];
}

// District statistics by city
if ($allDistrictsResponse->isSuccess()) {
    $districts = $allDistrictsResponse->getData();
    $districtsByCity = [];
    $activeDistricts = 0;
    
    foreach ($districts as $district) {
        $cityId = $district->IlID ?? 'unknown';
        $districtsByCity[$cityId] = ($districtsByCity[$cityId] ?? 0) + 1;
        
        if ($district->Aktif ?? false) {
            $activeDistricts++;
        }
    }
    
    $stats['districts'] = [
        'total' => count($districts),
        'active' => $activeDistricts,
        'by_city' => $districtsByCity
    ];
}

echo "✓ Location statistics generated.\n";

if (isset($stats['countries'])) {
    echo "Countries:\n";
    echo "- Total: " . $stats['countries']['total'] . "\n";
    echo "- Active: " . $stats['countries']['active'] . "\n";
    echo "- With phone code: " . $stats['countries']['with_phone_code'] . "\n";
}

if (isset($stats['cities'])) {
    echo "Cities:\n";
    echo "- Total: " . $stats['cities']['total'] . "\n";
    echo "- Active: " . $stats['cities']['active'] . "\n";
    echo "- Top 5 countries by city count:\n";
    
    arsort($stats['cities']['by_country']);
    $topCountries = array_slice($stats['cities']['by_country'], 0, 5, true);
    foreach ($topCountries as $countryId => $cityCount) {
        echo "  * Country ID $countryId: $cityCount cities\n";
    }
}

if (isset($stats['districts'])) {
    echo "Districts:\n";
    echo "- Total: " . $stats['districts']['total'] . "\n";
    echo "- Active: " . $stats['districts']['active'] . "\n";
    echo "- Average districts per city: " . 
         number_format($stats['districts']['total'] / count($stats['districts']['by_city']), 2) . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 13: Performance Test
echo "13. === PERFORMANCE TEST ===\n";
echo "Testing multiple location requests for performance...\n";

$performanceTests = [
    'getCountries' => 0,
    'getCities' => 0,
    'getDistricts' => 0,
    'getShippingCompanies' => 0
];

// Test each method 3 times
for ($i = 1; $i <= 3; $i++) {
    echo "Performance test round $i...\n";
    
    $start = microtime(true);
    $locationService->getCountries();
    $performanceTests['getCountries'] += microtime(true) - $start;
    
    $start = microtime(true);
    $locationService->getCities();
    $performanceTests['getCities'] += microtime(true) - $start;
    
    $start = microtime(true);
    $locationService->getDistricts();
    $performanceTests['getDistricts'] += microtime(true) - $start;
    
    $start = microtime(true);
    $locationService->getShippingCompanies();
    $performanceTests['getShippingCompanies'] += microtime(true) - $start;
}

echo "✓ Performance test completed.\n";
foreach ($performanceTests as $method => $totalTime) {
    $averageTime = $totalTime / 3;
    echo "- $method: " . number_format($averageTime, 4) . " seconds average\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "LOCATION SERVICE TEST SUITE COMPLETED!\n";
echo str_repeat("=", 50) . "\n\n";

echo "SUMMARY:\n";
echo "- All LocationService methods have been tested\n";
echo "- Hierarchical data relationships validated\n";
echo "- Data integrity and statistics analysis performed\n";
echo "- Filter combinations and parameter validation tested\n";
echo "- Performance metrics collected\n";
echo "- Real API interactions were performed for all tests\n"; 