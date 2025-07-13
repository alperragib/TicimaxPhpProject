<?php

namespace AlperRagib\Ticimax\Service\Location;

use AlperRagib\Ticimax\Model\Location\CountryModel;
use AlperRagib\Ticimax\Model\Location\CityModel;
use AlperRagib\Ticimax\Model\Location\DistrictModel;
use AlperRagib\Ticimax\Model\Response\ApiResponse;
use AlperRagib\Ticimax\TicimaxHelpers;
use AlperRagib\Ticimax\TicimaxRequest;

class LocationService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/CustomServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
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
        $client = $this->request->soap_client($this->apiUrl);
        
        $request = [
            'FiltreUlkeID' => $countryId ?? -1,
            'FiltreUlkeKodu' => $countryCode ?? ''
        ];

        try {
            $response = $client->__soapCall("SelectUlkeler", [
                [
                    'UyeKodu' => $this->request->key,
                    'SelectRequest' => (object)$request,
                ]
            ]);

            if (!$response || !isset($response->SelectUlkelerResult)) {
                return new ApiResponse(false, 'No countries found', []);
            }

            $countries = [];
            $ulkeler = $response->SelectUlkelerResult->KargoUlke ?? [];
            if (is_object($ulkeler)) {
                $ulkeler = [$ulkeler];
            }
            
            foreach ($ulkeler as $country) {
                $countries[] = new CountryModel($country);
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
        $client = $this->request->soap_client($this->apiUrl);
        
        $request = [
            'FiltreIlID' => $cityId ?? -1,
            'FiltreUlkeID' => $countryId ?? -1
        ];

        try {
            $response = $client->__soapCall("SelectIller", [
                [
                    'UyeKodu' => $this->request->key,
                    'SelectRequest' => (object)$request,
                ]
            ]);
            
            if (!$response || !isset($response->SelectIllerResult)) {
                return new ApiResponse(false, 'No cities found', []);
            }

            $cities = [];
            $iller = $response->SelectIllerResult->KargoIl ?? [];
            if (is_object($iller)) {
                $iller = [$iller];
            }
            
            foreach ($iller as $city) {
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
        $client = $this->request->soap_client($this->apiUrl);
        
        $request = [
            'FiltreIlceID' => $districtId ?? -1,
            'FiltreIlID' => $cityId ?? -1
        ];

        try {
            $response = $client->__soapCall("SelectIlceler", [
                [
                    'UyeKodu' => $this->request->key,
                    'SelectRequest' => (object)$request,
                ]
            ]);
            
            if (!$response || !isset($response->SelectIlcelerResult)) {
                return new ApiResponse(false, 'No districts found', []);
            }

            $districts = [];
            $ilceler = $response->SelectIlcelerResult->KargoIlce ?? [];
            if (is_object($ilceler)) {
                $ilceler = [$ilceler];
            }
            
            foreach ($ilceler as $district) {
                $districts[] = new DistrictModel(TicimaxHelpers::objectToArray($district));
            }

            return new ApiResponse(true, 'Districts retrieved successfully', $districts);
        } catch (\Exception $e) {
            return new ApiResponse(false, $e->getMessage(), []);
        }
    }

    /**
     * Get shipping companies
     * 
     * @return ApiResponse
     */
    public function getShippingCompanies(): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);

        try {
            $response = $client->__soapCall("SelectKargoFirmalari", [
                [
                    'UyeKodu' => $this->request->key,
                ]
            ]);

            if (!$response || !isset($response->SelectKargoFirmalariResult)) {
                return new ApiResponse(false, 'No shipping companies found', []);
            }

            $companies = [];
            $kargoFirmalari = $response->SelectKargoFirmalariResult->KargoFirma ?? [];
            if (is_object($kargoFirmalari)) {
                $kargoFirmalari = [$kargoFirmalari];
            }
            
            foreach ($kargoFirmalari as $company) {
                $companies[] = [
                    'ID' => $company->ID ?? null,
                    'FirmaAdi' => $company->FirmaAdi ?? null,
                    'FirmaKodu' => $company->FirmaKodu ?? null,
                    'Aktif' => $company->Aktif ?? false,
                    'Website' => $company->Website ?? null,
                    'TakipURL' => $company->TakipURL ?? null
                ];
            }

            return new ApiResponse(true, 'Shipping companies retrieved successfully', $companies);
        } catch (\Exception $e) {
            return new ApiResponse(false, $e->getMessage(), []);
        }
    }
    
} 