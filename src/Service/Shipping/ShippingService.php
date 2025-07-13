<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Shipping;

use AlperRagib\Ticimax\Model\Shipping\ShippingCompanyModel;
use AlperRagib\Ticimax\Model\Response\ApiResponse;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class ShippingService
 * Handles shipping-related API operations
 */
class ShippingService
{
    private TicimaxRequest $request;

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get available shipping options for a given city and cart
     * 
     * @param int $cityId City ID from the cities table (e.g., 1 = Adana)
     * @param string $currency Currency code (e.g., 'TL')
     * @param object $cart Cart object (ServisSepet) - Required
     * @return ApiResponse
     */
    public function getShippingOptions(int $cityId, string $currency, object $cart): ApiResponse
    {
        try {
            $requestData = [
                'SehirId' => $cityId,
                'ParaBirimi' => $currency,
                'Sepet' => $cart
            ];

            // Use SiparisServis for shipping options (order-related operations)
            $response = $this->request->soap_client("/Servis/SiparisServis.svc?singleWsdl")->__soapCall("GetKargoSecenek", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$requestData
                ]
            ]);

            if (isset($response->GetKargoSecenekResult)) {
                $shippingCompanies = [];
                $result = $response->GetKargoSecenekResult;
                
                // Check if result is empty
                if (empty((array)$result)) {
                    return ApiResponse::success([], 'Bu istek için kargo seçeneği bulunmamaktadır.');
                }
                
                // Handle both single object and array responses
                if (is_object($result)) {
                    $companies = [$result];
                } elseif (is_array($result)) {
                    $companies = $result;
                } else {
                    return ApiResponse::error('Beklenmeyen yanıt formatı');
                }

                foreach ($companies as $company) {
                    $shippingCompanies[] = new ShippingCompanyModel($company);
                }

                return ApiResponse::success($shippingCompanies, 'Kargo seçenekleri başarıyla getirildi.');
            }

            return ApiResponse::error('Yanıtta kargo seçeneği bulunamadı.');

        } catch (SoapFault $e) {
            return ApiResponse::error('Kargo seçenekleri getirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Get list of shipping companies
     * 
     * @return ApiResponse
     */
    public function getShippingCompanies(): ApiResponse
    {
        $client = $this->request->soap_client("/Servis/CustomServis.svc?singleWsdl");
        
        try {
            $response = $client->__soapCall("SelectKargoFirmalari", [
                [
                    'UyeKodu' => $this->request->key
                ]
            ]);

            if (isset($response->SelectKargoFirmalariResult)) {
                $companies = [];
                $result = $response->SelectKargoFirmalariResult;
                
                // Handle KargoFirma structure
                if (isset($result->KargoFirma)) {
                    $kargoFirmalari = $result->KargoFirma;
                    
                    // Convert single object to array if needed
                    if (is_object($kargoFirmalari)) {
                        $kargoFirmalari = [$kargoFirmalari];
                    }

                    foreach ($kargoFirmalari as $company) {
                        // Map API fields to expected structure
                        $mappedCompany = [
                            'ID' => $company->ID ?? null,
                            'FirmaAdi' => $company->Tanim ?? null,
                            'FirmaKodu' => $company->FirmaKodu ?? null,
                            'Aktif' => $company->Aktif ?? true,
                            'Website' => $company->Website ?? null,
                            'TakipURL' => $company->TakipURL ?? null,
                            'EntegrasyonKodu' => $company->EntegrasyonKodu ?? null,
                            'Logo' => $company->Logo ?? null
                        ];
                        
                        $companies[] = new ShippingCompanyModel($mappedCompany);
                    }
                }

                return ApiResponse::success($companies, 'Kargo firmaları başarıyla getirildi.');
            }

            return ApiResponse::success([], 'Kargo firması bulunamadı.');

        } catch (SoapFault $e) {
            return ApiResponse::error('Kargo firmaları getirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }
} 