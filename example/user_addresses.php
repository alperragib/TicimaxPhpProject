<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\ApiResponse;

// Configuration
$config = require __DIR__ . '/config.php';
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Instantiate the main Ticimax entrypoint
$ticimax = new Ticimax($mainDomain, $apiKey);
$userService = $ticimax->userService();

echo "=== User Addresses ===\n";

// Example: Save new address
echo "Adding new address for user ID 1:\n";

$newAddressData = [
    'ID' => 0,
    'UyeId' => 1,
    'Tanim' => 'Test Address',
    'AliciAdi' => 'AliciAdi',
    'AliciTelefon' => 'AliciTelefon',
    'Adres' => 'Adres',
    'Sehir' => 'Sehir',
    'Ilce' => 'İlce',
    'Ulke' => 'Ulke',
    'PostaKodu' => 'PostaKodu',
    'FirmaAdi' => '',
    'VergiDairesi' => '',
    'VergiNo' => '',
    'IsKurumsal' => false,
    'Aktif' => false,
    'AdresTarifi' => ''
];

$response = $userService->saveUserAddress($newAddressData);

if ($response->isSuccess()) {
    $savedAddressId = $response->getData();
    echo "Address saved successfully! Address ID: $savedAddressId\n";
} else {
    echo "Failed to save address. Error: " . $response->getMessage() . "\n";
}

echo "\n=== Listing User Addresses ===\n";

$addresses = $userService->getUserAddresses(1);

if (!empty($addresses)) {
    foreach ($addresses as $address) {
        $id = $address->ID ?? '[No ID]';
        $tanim = $address->Tanim ?? '[No Tanim]';
        $aliciAdi = $address->AliciAdi ?? '[No AliciAdi]';
        $aliciTelefon = $address->AliciTelefon ?? '[No AliciTelefon]';
        $adres = $address->Adres ?? '[No Adres]';
        $sehir = $address->Sehir ?? '[No Sehir]';
        $ilce = $address->Ilce ?? '[No Ilce]';
        $ulke = $address->Ulke ?? '[No Ulke]';
        $postaKodu = $address->PostaKodu ?? '[No PostaKodu]';
        $firmaAdi = $address->FirmaAdi ?? '[No FirmaAdi]';
        $vergiDairesi = $address->VergiDairesi ?? '[No VergiDairesi]';
        $vergiNo = $address->VergiNo ?? '[No VergiNo]';
        $isKurumsal = $address->IsKurumsal ?? '[No IsKurumsal]';
        $aktif = $address->Aktif ?? '[No Aktif]';
        $adresTarifi = $address->AdresTarifi ?? '[No AdresTarifi]';
        $uyeId = $address->UyeId ?? '[No UyeId]';

        echo "\nID               : $id";
        echo "\nTanim            : $tanim";
        echo "\nAliciAdi         : $aliciAdi";
        echo "\nAliciTelefon     : $aliciTelefon";
        echo "\nAdres            : $adres";
        echo "\nSehir            : $sehir";
        echo "\nIlce             : $ilce";
        echo "\nUlke             : $ulke";
        echo "\nPostaKodu        : $postaKodu";
        echo "\nFirmaAdi         : $firmaAdi";
        echo "\nVergiDairesi     : $vergiDairesi";
        echo "\nVergiNo          : $vergiNo";
        echo "\nIsKurumsal       : $isKurumsal";
        echo "\nAktif            : $aktif";
        echo "\nAdresTarifi      : $adresTarifi";
        echo "\nUyeId            : $uyeId";

        echo "\n\n---------------------------------------------\n";
    }
} else {
    echo "No addresses found.\n";
}
