<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TicimaxApi\Ticimax;
use TicimaxApi\Service\Location\LocationService;

// Initialize the API client
$client = new Ticimax([
    'username' => 'YOUR_USERNAME',
    'password' => 'YOUR_PASSWORD',
    'url' => 'YOUR_SERVICE_URL'
]);

// Get the location service
$locationService = new LocationService($client->getClient(), $client->getMemberCode());

// Get all countries
$countries = $locationService->getCountries();
if ($countries->isSuccess()) {
    foreach ($countries->getData() as $country) {
        echo sprintf("Country: %s (ID: %d)\n", $country->getName(), $country->getId());
    }
}

// Get cities for a specific country (e.g., country ID 1)
$cities = $locationService->getCities(null, 1);
if ($cities->isSuccess()) {
    foreach ($cities->getData() as $city) {
        echo sprintf("City: %s (ID: %d, Country ID: %d)\n", 
            $city->getName(), 
            $city->getId(),
            $city->getCountryId()
        );
    }
}

// Get districts for a specific city (e.g., city ID 1)
$districts = $locationService->getDistricts(null, 1);
if ($districts->isSuccess()) {
    foreach ($districts->getData() as $district) {
        echo sprintf("District: %s (ID: %d, City ID: %d)\n", 
            $district->getName(), 
            $district->getId(),
            $district->getCityId()
        );
    }
} 