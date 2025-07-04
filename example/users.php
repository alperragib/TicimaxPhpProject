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

echo "=== Save User Example ===\n";

// Example: Save new user
echo "Adding new user:\n";

$newUserData = [
    'ID' => 0, // 0 for new user, existing ID for update
    'Isim' => 'John',
    'Soyisim' => 'Doe',
    'Mail' => 'john.doe@example.com',
    'Telefon' => '0212 555 12 34',
    'CepTelefonu' => '0555 123 45 67',
    'Sifre' => 'password123',
    'Aktif' => true,
    'Onay' => true,
    'MailIzin' => true,
    'SmsIzin' => true,
    'KVKKSozlesmeOnay' => true,
    'UyelikSozlesmeOnay' => true,
    'VKayitDil' => 'tr',
];

$userSettings = [
    'AlisverissizOdemeGuncelle' => false,
    'CepTelefonuGuncelle' => false,
    'CinsiyetGuncelle' => false,
    'DogumTarihiGuncelle' => false,
    'IlGuncelle' => false,
    'IlceGuncelle' => false,
    'IsimGuncelle' => false,
    'KVKKSozlesmeOnayGuncelle' => false,
    'KapidaOdemeYasaklaGuncelle' => false,
    'KrediLimitiGuncelle' => false,
    'MailGuncelle' => false,
    'MailIzinGuncelle' => false,
    'MeslekGuncelle' => false,
    'MusteriKoduGuncelle' => false,
    'SifreGuncelle' => false,
    'SifreKaydetmeTuru' => null,
    'SmsIzinGuncelle' => false,
    'TelefonGuncelle' => false,
    'UyeSifreyiKendiOlustursun' => false,
    'UyelikSozlesmeOnayGuncelle' => false,
    'UyelikTarihiGuncelle' => false,
    'UyelikTuruGuncelle' => false,
];

$savedUserId = $userService->saveUser($newUserData, $userSettings);

if ($savedUserId) {
    echo "User saved successfully! User ID: $savedUserId\n";
} else {
    echo "Failed to save user.\n";
}

echo "\n=== List Users ===\n";

$filters = [
    'Aktif'                      => 1,
    'AlisverisYapti'             => -1,
    'BakiyeGetir'                => null,
    'Cinsiyet'                   => -1,
    'DogumTarihi1'               => null,
    'DogumTarihi2'               => null,
    'DuzenlemeTarihi1'           => null,
    'DuzenlemeTarihi2'           => null,
    'IlID'                       => 0,
    'IlceID'                     => 0,
    'IzinGuncellemeTarihi1'      => null,
    'IzinGuncellemeTarihi2'      => null,
    'IzinGuncellemeTarihiBas'    => null,
    'IzinGuncellemeTarihiGetir'  => null,
    'IzinGuncellemeTarihiSon'    => null,
    'Mail'                       => '',
    'MailIzin'                   => -1,
    'Onay'                       => null,
    'SmsIzin'                    => -1,
    'SonGirisTarihi1'            => null,
    'SonGirisTarihi2'            => null,
    'Telefon'                    => '',
    'TelefonEsit'                => '',
    'UyeID'                      => 0,
    'UyelikTarihi1'              => null,
    'UyelikTarihi2'              => null,
];

$pagination = [
    'BaslangicIndex'  => 0,
    'KayitSayisi'     => 5,
    'SiralamaDegeri'  => 'ID',
    'SiralamaYonu'    => 'DESC',
];

$users = $userService->getUsers($filters, $pagination);

foreach ($users as $user) {
    $id = $user->ID ?? '[No ID]';
    $aktif = $user->Aktif ?? '[No Aktif]';
    $telefon = $user->Telefon ?? '[No Telefon]';
    $cepTelefonu = $user->CepTelefonu ?? '[No CepTelefonu]';
    $isim = $user->Isim ?? '[No Isim]';
    $soyisim = $user->Soyisim ?? '[No Soyisim]';
    $mail = $user->Mail ?? '[No Mail]';
    $onay = $user->Onay ?? '[No Onay]';
    $sifre = $user->Sifre ?? '[No Sifre]';
    $vKayitDil = $user->VKayitDil ?? '[No VKayitDil]';
    $uyelikTipi = $user->UyelikTipi ?? '[No UyelikTipi]';
    $uyelikTipiID = $user->UyelikTipiID ?? '[No UyelikTipiID]';

    echo "\nID               : $id";
    echo "\nAktif            : $aktif";
    echo "\nTelefon          : $telefon";
    echo "\nCepTelefonu      : $cepTelefonu";
    echo "\nIsim             : $isim";
    echo "\nSoyisim          : $soyisim";
    echo "\nMail             : $mail";
    echo "\nOnay             : $onay";
    echo "\nSifre            : $sifre";
    echo "\nVKayitDil        : $vKayitDil";
    echo "\nUyelikTipi       : $uyelikTipi";
    echo "\nUyelikTipiID     : $uyelikTipiID";

    echo "\n\n---------------------------------------------\n";
}
