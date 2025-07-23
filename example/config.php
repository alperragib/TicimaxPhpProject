<?php
// Ticimax API config 

return [
    'mainDomain' => 'https://scentsan.com',
    'apiKey' => 'A6CZ675NOFUZ6UXP17F61CEQJNYF79',
];

// Set variables for use in test files - Ticimax constructor needs (domain, key)
$uyeKodu = $config['mainDomain']; // main_domain parameter
$kullaniciAdi = $config['apiKey']; // key parameter  
$sifre = ''; // Not used, keeping for compatibility

// Alternative: you can use these variables directly
$mainDomain = $config['mainDomain'];
$apiKey = $config['apiKey'];

// Return config for compatibility
return $config;

