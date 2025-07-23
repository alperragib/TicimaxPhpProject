<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Supplier;

use AlperRagib\Ticimax\Model\Supplier\SupplierModel;
use AlperRagib\Ticimax\Model\Response\ApiResponse;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class SupplierService
 * Handles supplier-related API operations.
 */
class SupplierService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/UrunServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch suppliers from the API.
     * @param int $supplierId (optional) Specific supplier ID, defaults to null for all suppliers
     * @return ApiResponse
     */
    public function getSuppliers(?int $supplierId = null): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        $suppliers = [];
        try {
            $params = [
                'UyeKodu'     => $this->request->key,
                'tedarikciID' => $supplierId,
            ];

            $response = $client->__soapCall("SelectTedarikci", [
                'parameters' => $params
            ]);

            $supArr = $response->SelectTedarikciResult->Tedarikci ?? [];

            if (is_object($supArr)) {
                $supArr = [$supArr];
            }
            foreach ($supArr as $sup) {
                $suppliers[] = new SupplierModel($sup);
            }
            
            return ApiResponse::success(
                $suppliers,
                'Suppliers retrieved successfully.'
            );
        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving suppliers: ' . $e->getMessage());
        }
    }
}
