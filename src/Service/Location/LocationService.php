<?php

namespace TicimaxApi\Service\Location;

use TicimaxApi\Model\Location\CountryModel;
use TicimaxApi\Model\Location\CityModel;
use TicimaxApi\Model\Location\DistrictModel;
use TicimaxApi\Model\Response\ApiResponse;
use TicimaxApi\TicimaxHelpers;

class LocationService
{
    private $client;
    private $memberCode;

    public function __construct($client, $memberCode)
    {
        $this->client = $client;
        $this->memberCode = $memberCode;
    }

    /**
     * Get list of countries
     * 
     * @param int|null $countryId Filter by country ID
     * @param string|null $countryCode Filter by country code
     * @return ApiResponse
     */
    public function getCountries(?int $countryId = null, ?string $countryCode = null): ApiResponse
    {
        $request = [
            'FiltreUlkeID' => $countryId ?? -1,
            'FiltreUlkeKodu' => $countryCode ?? ''
        ];

        try {
            $response = $this->client->SelectUlkeler($this->memberCode, $request);
            
            if (!$response || !isset($response->SelectUlkelerResult)) {
                return new ApiResponse(false, 'No countries found', []);
            }

            $countries = [];
            foreach ($response->SelectUlkelerResult as $country) {
                $countries[] = new CountryModel(TicimaxHelpers::objectToArray($country));
            }

            return new ApiResponse(true, 'Countries retrieved successfully', $countries);
        } catch (\Exception $e) {
            return new ApiResponse(false, $e->getMessage(), []);
        }
    }

    /**
     * Get list of cities
     * 
     * @param int|null $cityId Filter by city ID
     * @param int|null $countryId Filter by country ID
     * @return ApiResponse
     */
    public function getCities(?int $cityId = null, ?int $countryId = null): ApiResponse
    {
        $request = [
            'FiltreIlID' => $cityId ?? -1,
            'FiltreUlkeID' => $countryId ?? -1
        ];

        try {
            $response = $this->client->SelectIller($this->memberCode, $request);
            
            if (!$response || !isset($response->SelectIllerResult)) {
                return new ApiResponse(false, 'No cities found', []);
            }

            $cities = [];
            foreach ($response->SelectIllerResult as $city) {
                $cities[] = new CityModel(TicimaxHelpers::objectToArray($city));
            }

            return new ApiResponse(true, 'Cities retrieved successfully', $cities);
        } catch (\Exception $e) {
            return new ApiResponse(false, $e->getMessage(), []);
        }
    }

    /**
     * Get list of districts
     * 
     * @param int|null $districtId Filter by district ID
     * @param int|null $cityId Filter by city ID
     * @return ApiResponse
     */
    public function getDistricts(?int $districtId = null, ?int $cityId = null): ApiResponse
    {
        $request = [
            'FiltreIlceID' => $districtId ?? -1,
            'FiltreIlID' => $cityId ?? -1
        ];

        try {
            $response = $this->client->SelectIlceler($this->memberCode, $request);
            
            if (!$response || !isset($response->SelectIlcelerResult)) {
                return new ApiResponse(false, 'No districts found', []);
            }

            $districts = [];
            foreach ($response->SelectIlcelerResult as $district) {
                $districts[] = new DistrictModel(TicimaxHelpers::objectToArray($district));
            }

            return new ApiResponse(true, 'Districts retrieved successfully', $districts);
        } catch (\Exception $e) {
            return new ApiResponse(false, $e->getMessage(), []);
        }
    }
} 