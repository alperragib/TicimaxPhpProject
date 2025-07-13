<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

// Set your Ticimax domain and API key
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$userService = $ticimax->userService();

echo "=== USER SERVICE TEST SUITE ===\n\n";

// Test 1: Get Users
echo "1. === GET USERS TEST ===\n";
echo "Testing getUsers() with default filters...\n";

$filters = [
    'Aktif' => 1,
    'MailIzin' => -1,
    'SmsIzin' => -1,
];

$pagination = [
    'KayitSayisi' => 5,
    'SayfaNo' => 0,
    'SiralamaDegeri' => 'ID',
    'SiralamaYonu' => 'DESC',
];

$usersResponse = $userService->getUsers($filters, $pagination);

if ($usersResponse->isSuccess()) {
    echo "✓ " . $usersResponse->getMessage() . "\n";
    $users = $usersResponse->getData();
    echo "Found " . count($users) . " users.\n";
    
    if (!empty($users)) {
        echo "First user details:\n";
        $firstUser = $users[0];
        echo "- ID: " . ($firstUser->ID ?? 'N/A') . "\n";
        echo "- Name: " . ($firstUser->Isim ?? 'N/A') . " " . ($firstUser->Soyisim ?? 'N/A') . "\n";
        echo "- Email: " . ($firstUser->Mail ?? 'N/A') . "\n";
        echo "- Phone: " . ($firstUser->Telefon ?? 'N/A') . "\n";
        echo "- Active: " . ($firstUser->Aktif ? 'Yes' : 'No') . "\n";
    }
} else {
    echo "✗ " . $usersResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 2: Save User
echo "2. === SAVE USER TEST ===\n";
echo "Testing saveUser() with new user data...\n";

$newUserData = [
    'ID' => 0, // 0 for new user
    'Isim' => 'Test User',
    'Soyisim' => 'Test Surname',
    'Mail' => 'testuser' . time() . '@example.com', // Unique email
    'Telefon' => '0212 555 12 34',
    'CepTelefonu' => '0555 123 45 67',
    'Sifre' => 'TestPassword123',
    'Aktif' => true,
    'Onay' => true,
    'MailIzin' => true,
    'SmsIzin' => true,
    'KVKKSozlesmeOnay' => true,
    'UyelikSozlesmeOnay' => true,
    'VKayitDil' => 'tr',
    'Cinsiyet' => 0, // 0: belirtilmedi, 1: erkek, 2: kadın
];

$userSettings = [
    'IsimGuncelle' => true,
    'MailGuncelle' => true,
    'SifreGuncelle' => true,
    'MailIzinGuncelle' => true,
    'SmsIzinGuncelle' => true,
    'KVKKSozlesmeOnayGuncelle' => true,
    'UyelikSozlesmeOnayGuncelle' => true,
];

$saveUserResponse = $userService->saveUser($newUserData, $userSettings);

$savedUserId = null;
if ($saveUserResponse->isSuccess()) {
    $savedUserId = $saveUserResponse->getData();
    echo "✓ " . $saveUserResponse->getMessage() . " User ID: $savedUserId\n";
} else {
    echo "✗ " . $saveUserResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 3: Login Test
echo "3. === LOGIN TEST ===\n";
echo "Testing login() function...\n";

// Test with a dummy email and password
$testEmail = 'testuser@example.com';
$testPassword = 'password123';

$loginResponse = $userService->login($testEmail, $testPassword);

if ($loginResponse->isSuccess()) {
    echo "✓ " . $loginResponse->getMessage() . "\n";
    $loginResult = $loginResponse->getData();
    echo "Login result details:\n";
    echo "- User ID: " . ($loginResult->UyeId ?? 'N/A') . "\n";
    echo "- Success: " . ($loginResult->Basarili ? 'Yes' : 'No') . "\n";
    echo "- Message: " . ($loginResult->Mesaj ?? 'N/A') . "\n";
} else {
    echo "✗ " . $loginResponse->getMessage() . "\n";
    echo "Note: This is expected if test credentials don't exist.\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 4: Admin Login Test
echo "4. === ADMIN LOGIN TEST ===\n";
echo "Testing admin login...\n";

$adminLoginResponse = $userService->login($testEmail, $testPassword, null, true);

if ($adminLoginResponse->isSuccess()) {
    echo "✓ " . $adminLoginResponse->getMessage() . "\n";
    $adminLoginResult = $adminLoginResponse->getData();
    echo "Admin login result details:\n";
    echo "- User ID: " . ($adminLoginResult->UyeId ?? 'N/A') . "\n";
    echo "- Success: " . ($adminLoginResult->Basarili ? 'Yes' : 'No') . "\n";
    echo "- Message: " . ($adminLoginResult->Mesaj ?? 'N/A') . "\n";
} else {
    echo "✗ " . $adminLoginResponse->getMessage() . "\n";
    echo "Note: This is expected if admin credentials don't exist.\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 5: Get User Addresses
echo "5. === GET USER ADDRESSES TEST ===\n";
echo "Testing getUserAddresses()...\n";

// Test with user ID if we have one from save operation
$testUserId = $savedUserId ?? 1; // Use saved user ID or default to 1

$addressesResponse = $userService->getUserAddresses($testUserId);

if ($addressesResponse->isSuccess()) {
    echo "✓ " . $addressesResponse->getMessage() . "\n";
    $addresses = $addressesResponse->getData();
    echo "Found " . count($addresses) . " addresses for user ID $testUserId.\n";
    
    if (!empty($addresses)) {
        echo "First address details:\n";
        $firstAddress = $addresses[0];
        echo "- Address ID: " . ($firstAddress->ID ?? 'N/A') . "\n";
        echo "- Description: " . ($firstAddress->Tanim ?? 'N/A') . "\n";
        echo "- Recipient: " . ($firstAddress->AliciAdi ?? 'N/A') . "\n";
        echo "- Address: " . ($firstAddress->Adres ?? 'N/A') . "\n";
        echo "- City: " . ($firstAddress->Sehir ?? 'N/A') . "\n";
        echo "- District: " . ($firstAddress->Ilce ?? 'N/A') . "\n";
        echo "- Active: " . ($firstAddress->Aktif ? 'Yes' : 'No') . "\n";
    }
} else {
    echo "✗ " . $addressesResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 6: Save User Address
echo "6. === SAVE USER ADDRESS TEST ===\n";
echo "Testing saveUserAddress()...\n";

$newAddressData = [
    'ID' => 0, // 0 for new address
    'UyeId' => $testUserId,
    'Tanim' => 'Test Address - ' . date('Y-m-d H:i:s'),
    'AliciAdi' => 'Test Recipient',
    'AliciTelefon' => '0555 123 45 67',
    'Adres' => 'Test Street No: 123',
    'Sehir' => 'Istanbul',
    'Ilce' => 'Kadikoy',
    'Ulke' => 'Turkey',
    'PostaKodu' => '34710',
    'FirmaAdi' => '',
    'VergiDairesi' => '',
    'VergiNo' => '',
    'IsKurumsal' => false,
    'Aktif' => true,
    'AdresTarifi' => 'Test address for UserService testing'
];

$saveAddressResponse = $userService->saveUserAddress($newAddressData);

if ($saveAddressResponse->isSuccess()) {
    $savedAddressId = $saveAddressResponse->getData();
    echo "✓ " . $saveAddressResponse->getMessage() . " Address ID: $savedAddressId\n";
} else {
    echo "✗ " . $saveAddressResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 7: Get All Addresses (without user ID filter)
echo "7. === GET ALL ADDRESSES TEST ===\n";
echo "Testing getUserAddresses() without user ID filter...\n";

$allAddressesResponse = $userService->getUserAddresses();

if ($allAddressesResponse->isSuccess()) {
    echo "✓ " . $allAddressesResponse->getMessage() . "\n";
    $allAddresses = $allAddressesResponse->getData();
    echo "Found " . count($allAddresses) . " total addresses.\n";
} else {
    echo "✗ " . $allAddressesResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 8: Get Users with Different Filters
echo "8. === GET USERS WITH FILTERS TEST ===\n";
echo "Testing getUsers() with different filters...\n";

$customFilters = [
    'Aktif' => 1,
    'MailIzin' => 1,
    'SmsIzin' => 1,
    'Mail' => '', // Empty to get all
    'Telefon' => '', // Empty to get all
];

$customPagination = [
    'KayitSayisi' => 10,
    'SayfaNo' => 0,
    'SiralamaDegeri' => 'UyelikTarihi',
    'SiralamaYonu' => 'DESC',
];

$filteredUsersResponse = $userService->getUsers($customFilters, $customPagination);

if ($filteredUsersResponse->isSuccess()) {
    echo "✓ " . $filteredUsersResponse->getMessage() . "\n";
    $filteredUsers = $filteredUsersResponse->getData();
    echo "Found " . count($filteredUsers) . " filtered users.\n";
    
    if (!empty($filteredUsers)) {
        echo "Sample user from filtered results:\n";
        $sampleUser = $filteredUsers[0];
        echo "- ID: " . ($sampleUser->ID ?? 'N/A') . "\n";
        echo "- Name: " . ($sampleUser->Isim ?? 'N/A') . " " . ($sampleUser->Soyisim ?? 'N/A') . "\n";
        echo "- Email: " . ($sampleUser->Mail ?? 'N/A') . "\n";
        echo "- Email Permission: " . ($sampleUser->MailIzin ? 'Yes' : 'No') . "\n";
        echo "- SMS Permission: " . ($sampleUser->SmsIzin ? 'Yes' : 'No') . "\n";
    }
} else {
    echo "✗ " . $filteredUsersResponse->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 9: Update Existing User
if ($savedUserId) {
    echo "9. === UPDATE USER TEST ===\n";
    echo "Testing saveUser() with existing user ID...\n";
    
    $updateUserData = [
        'ID' => $savedUserId,
        'Isim' => 'Updated Test User',
        'Soyisim' => 'Updated Test Surname',
        'Mail' => 'updateduser' . time() . '@example.com',
        'Telefon' => '0212 555 99 88',
        'CepTelefonu' => '0555 999 88 77',
        'Aktif' => true,
        'Onay' => true,
        'MailIzin' => false, // Changed to false
        'SmsIzin' => false, // Changed to false
        'VKayitDil' => 'en', // Changed to English
    ];
    
    $updateSettings = [
        'IsimGuncelle' => true,
        'MailGuncelle' => true,
        'TelefonGuncelle' => true,
        'CepTelefonuGuncelle' => true,
        'MailIzinGuncelle' => true,
        'SmsIzinGuncelle' => true,
    ];
    
    $updateResponse = $userService->saveUser($updateUserData, $updateSettings);
    
    if ($updateResponse->isSuccess()) {
        $updatedUserId = $updateResponse->getData();
        echo "✓ " . $updateResponse->getMessage() . " Updated User ID: $updatedUserId\n";
    } else {
        echo "✗ " . $updateResponse->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test 10: Login with OTP
echo "10. === LOGIN WITH OTP TEST ===\n";
echo "Testing login() with OTP...\n";

$otpLoginResponse = $userService->login($testEmail, $testPassword, '123456');

if ($otpLoginResponse->isSuccess()) {
    echo "✓ " . $otpLoginResponse->getMessage() . "\n";
    $otpLoginResult = $otpLoginResponse->getData();
    echo "OTP Login result details:\n";
    echo "- User ID: " . ($otpLoginResult->UyeId ?? 'N/A') . "\n";
    echo "- Success: " . ($otpLoginResult->Basarili ? 'Yes' : 'No') . "\n";
    echo "- Message: " . ($otpLoginResult->Mesaj ?? 'N/A') . "\n";
} else {
    echo "✗ " . $otpLoginResponse->getMessage() . "\n";
    echo "Note: This is expected if OTP is not configured or credentials don't exist.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "USER SERVICE TEST SUITE COMPLETED!\n";
echo str_repeat("=", 50) . "\n\n";

echo "SUMMARY:\n";
echo "- All UserService methods have been tested\n";
echo "- Check the results above for any failures\n";
echo "- Some failures are expected for login tests with dummy credentials\n";
echo "- Real API interactions were performed for all tests\n"; 