<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\FavouriteProduct;

use AlperRagib\Ticimax\Model\FavouriteProduct\FavouriteProductModel;
use AlperRagib\Ticimax\Model\Response\ApiResponse;
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
     * @return ApiResponse
     */
    public function getFavouriteProducts(array $parameters = []): ApiResponse
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

            // Check for API error first
            if (isset($response->GetFavoriUrunlerResult->IsError) && $response->GetFavoriUrunlerResult->IsError) {
                return ApiResponse::error($response->GetFavoriUrunlerResult->ErrorMessage ?? 'Bilinmeyen bir hata oluştu');
            }

            $result = $response->GetFavoriUrunlerResult->Urunler ?? [];

            // Handle the Urunler array structure
            if (!empty($result)) {
                // If result is an array, process each WebFavoriUrunler
                if (is_array($result)) {
                    foreach ($result as $favoriUrun) {
                        $favouriteProducts[] = new FavouriteProductModel($favoriUrun);
                    }
                } else {
                    // Single WebFavoriUrunler object
                    $favouriteProducts[] = new FavouriteProductModel($result);
                }
            }
            
            return ApiResponse::success($favouriteProducts, 'Favori ürünler başarıyla getirildi.');
            
        } catch (SoapFault $e) {
            return ApiResponse::error('Favori ürünler getirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Add favourite product.
     * @param int $userId User ID
     * @param int $productCardId Product card ID
     * @param float $quantity Quantity (default: 1.0)
     * @return ApiResponse
     */
    public function addFavouriteProduct(int $userId, int $productCardId, float $quantity = 1.0): ApiResponse
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
                
                // Debug: Response detaylarını logla
                error_log("AddFavoriUrun Response - IsError: " . json_encode($result->IsError ?? null) . 
                         ", ErrorMessage: " . json_encode($result->ErrorMessage ?? null));
                
                // IsError kontrolü - WSDL'e göre boolean field
                $isError = $result->IsError ?? false; // Default false (başarılı kabul et)
                
                if ($isError === true) {
                    return ApiResponse::error($result->ErrorMessage ?? 'Bilinmeyen bir hata oluştu');
                }
                
                return ApiResponse::success($result, 'Favori ürün başarıyla eklendi.');
            }
            
            return ApiResponse::error('AddFavoriUrunResult bulunamadı - API yanıt yapısı beklenenden farklı.');
            
        } catch (SoapFault $e) {
            error_log("AddFavoriUrun SoapFault: " . $e->getMessage());
            return ApiResponse::error('Favori ürün eklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove favourite product.
     * @param int $userId User ID
     * @param int $favouriteProductId Favourite product ID
     * @return ApiResponse
     */
    public function removeFavouriteProduct(int $userId, int $favouriteProductId): ApiResponse
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
                if ($result->IsError ?? true) {
                    return ApiResponse::error($result->ErrorMessage ?? 'Bilinmeyen bir hata oluştu');
                }
                return ApiResponse::success($result, 'Favori ürün başarıyla silindi.');
            }
            
            return ApiResponse::error('Favori ürün silinemedi.');
            
        } catch (SoapFault $e) {
            return ApiResponse::error('Favori ürün silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

}
