<?php
// Ticimax API config 

return [
    'mainDomain' => 'https://example.com',
    'apiKey' => 'xxxxxxxxxxxxxxxxxx',
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

