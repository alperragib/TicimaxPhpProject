<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\FavouriteProduct;

use AlperRagib\Ticimax\Model\FavouriteProduct\FavouriteProductModel;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class FavouriteProductService
 * Handles FavouriteProduct-related API operations.
 */
class FavouriteProductService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/CustomServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch FavouriteProducts from the API.
     * @param array $parameters
     * @return FavouriteProductModel[]
     */
    public function getFavouriteProducts(array $parameters = []): array
    {
        $client = $this->request->soap_client($this->apiUrl);
        $favouriteProducts = [];
        try {
            $defaultParameters = [
                'BaslangicTarihi'       => null,
                'BitisTarihi'           => null,
                'KayitSayisi'           => 20,
                'SayfaNo'               => 1,
                'UyeID'                 => 0,
            ];

            $parameters = array_merge($defaultParameters, $parameters);

            $response = $client->__soapCall("GetFavoriUrunler", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$parameters,
                ]
            ]);

            $result = $response->GetFavoriUrunlerResult->Urunler ?? [];


            if (isset($result->WebFavoriUrunler) && $result->WebFavoriUrunler !== null) {
                if (is_array($result->WebFavoriUrunler)) {
                    foreach ($result->WebFavoriUrunler as $favoriUrun) {
                        $favouriteProducts[] = new FavouriteProductModel($favoriUrun);
                    }
                } else {
                    $favouriteProducts[] = new FavouriteProductModel($result->WebFavoriUrunler);
                }
            }
        } catch (SoapFault $e) {
            // Handle error or log
        }
        return $favouriteProducts;
    }

    /**
     * Add favourite product.
     * @param int $userId User ID
     * @param int $productCardId Product card ID
     * @param float $quantity Quantity (default: 1.0)
     * @return array Returns response with IsError and ErrorMessage
     */
    public function addFavouriteProduct(int $userId, int $productCardId, float $quantity = 1.0): array
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            $requestData = [
                'UyeID' => $userId,
                'UrunKartiID' => $productCardId,
                'Adet' => $quantity,
            ];

            $response = $client->__soapCall("AddFavoriUrun", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$requestData,
                ]
            ]);

            print_r($response);

            if (isset($response->AddFavoriUrunResult)) {
                $result = $response->AddFavoriUrunResult;
                return [
                    'IsError' => $result->IsError ?? true,
                    'ErrorMessage' => $result->ErrorMessage ?? 'Unknown error occurred'
                ];
            }
        } catch (SoapFault $e) {
            // Handle error or log
        }
        
        return [
            'IsError' => true,
            'ErrorMessage' => 'Failed to add favourite product'
        ];
    }

    /**
     * Remove favourite product.
     * @param int $userId User ID
     * @param int $favouriteProductId Favourite product ID
     * @return array Returns response with IsError and ErrorMessage
     */
    public function removeFavouriteProduct(int $userId, int $favouriteProductId): array
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            $requestData = [
                'UyeID' => $userId,
                'FavoriUrunID' => $favouriteProductId,
            ];

            $response = $client->__soapCall("RemoveFavoriUrun", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$requestData,
                ]
            ]);

            print_r($response);

            if (isset($response->RemoveFavoriUrunResult)) {
                $result = $response->RemoveFavoriUrunResult;
                return [
                    'IsError' => $result->IsError ?? true,
                    'ErrorMessage' => $result->ErrorMessage ?? 'Unknown error occurred'
                ];
            }
        } catch (SoapFault $e) {
            // Handle error or log
        }
        
        return [
            'IsError' => true,
            'ErrorMessage' => 'Failed to remove favourite product'
        ];
    }

}
