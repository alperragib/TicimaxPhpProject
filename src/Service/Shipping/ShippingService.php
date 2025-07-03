<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Shipping;

use AlperRagib\Ticimax\Model\Shipping\ShippingCompanyModel;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class ShippingService
 * Handles shipping-related API operations
 */
class ShippingService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/SiparisServis.svc?singleWsdl";

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
     * @return array Returns ['IsError' => bool, 'ErrorMessage' => string, 'Data' => ShippingCompanyModel[]]
     */
    public function getShippingOptions(int $cityId, string $currency, object $cart): array
    {
        try {
            $requestData = [
                'SehirId' => $cityId,
                'ParaBirimi' => $currency,
                'Sepet' => $cart
            ];

            $response = $this->request->soap_client($this->apiUrl)->__soapCall("GetKargoSecenek", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$requestData
                ]
            ]);

            // Debug response
            echo "Raw SOAP Response:\n";
            print_r($response);
            echo "\n";

            if (isset($response->GetKargoSecenekResult)) {
                $shippingCompanies = [];
                $result = $response->GetKargoSecenekResult;
                
                // Handle both single object and array responses
                if (is_object($result)) {
                    $companies = [$result];
                } elseif (is_array($result)) {
                    $companies = $result;
                } else {
                    return [
                        'IsError' => true,
                        'ErrorMessage' => 'Unexpected response format',
                        'Data' => []
                    ];
                }

                foreach ($companies as $company) {
                    $shippingCompanies[] = new ShippingCompanyModel($company);
                }

                return [
                    'IsError' => false,
                    'ErrorMessage' => '',
                    'Data' => $shippingCompanies
                ];
            }

            return [
                'IsError' => true,
                'ErrorMessage' => 'No shipping options found in response',
                'Data' => []
            ];

        } catch (SoapFault $e) {
            return [
                'IsError' => true,
                'ErrorMessage' => 'Error retrieving shipping options: ' . $e->getMessage(),
                'Data' => []
            ];
        }
    }

    /**
     * Get list of shipping companies
     * 
     * @return array Returns ['success' => bool, 'message' => string, 'data' => array]
     */
    public function getShippingCompanies(): array
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

                // Convert single object to array if needed
                if (is_object($result)) {
                    $result = [$result];
                }

                foreach ($result as $company) {
                    $companies[] = new ShippingCompanyModel($company);
                }

                return [
                    'success' => true,
                    'message' => 'Shipping companies retrieved successfully',
                    'data' => $companies
                ];
            }

            return [
                'success' => true,
                'message' => 'No shipping companies found',
                'data' => []
            ];

        } catch (SoapFault $e) {
            return [
                'success' => false,
                'message' => 'Error retrieving shipping companies: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }
} 