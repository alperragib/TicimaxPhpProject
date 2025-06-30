<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Cart;

use AlperRagib\Ticimax\Model\Cart\WebCartModel;
use AlperRagib\Ticimax\Model\Cart\WebCartProductModel;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class CartService
 * Handles cart-related API operations.
 */
class CartService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/SiparisServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get cart (GetSepet).
     * @param int $userId User ID
     * @param int|null $cartId Cart ID (optional, null if not provided)
     * @param int $campaignId Campaign ID (optional)
     * @return WebCartModel|null Returns cart data or null on error
     */
    public function getSepet(int $userId, ?int $cartId = null, int $campaignId = 0): ?WebCartModel
    {
        $client = $this->request->soap_client($this->apiUrl);

        try {
            $requestData = [
                'KampanyaID' => $campaignId,
                'SepetID' => $cartId,
                'UyeID' => $userId,
            ];

            $response = $client->__soapCall("GetSepet", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$requestData,
                ]
            ]);

            if (isset($response->GetSepetResult)) {
                $result = $response->GetSepetResult;
                
                // Convert products to WebCartProductModel objects
                $urunler = [];
                if (isset($result->Urunler) && $result->Urunler !== null) {
                    $urunlerData = $result->Urunler;
                    
                    if (is_object($urunlerData)) {
                        $urunlerData = [$urunlerData];
                    }
                    
                    if (is_array($urunlerData)) {
                        foreach ($urunlerData as $urun) {
                            if (isset($urun) && $urun !== null) {
                                $urunler[] = new WebCartProductModel($urun);
                            }
                        }
                    }
                }
                
                $cartData = [
                    'ID' => $result->SepetID ?? null,
                    'UyeID' => $result->OverrateSahipId ?? null,
                    'GenelToplam' => $result->GenelToplam ?? 0,
                    'ToplamKDV' => $result->ToplamKDV ?? 0,
                    'ToplamUrunAdedi' => $result->ToplamUrunAdedi ?? 0,
                    'SepetParaBirimiDilKodu' => $result->SepetParaBirimiDilKodu ?? null,
                    'Urunler' => $urunler,
                ];
                
                return new WebCartModel($cartData);
            }
        } catch (SoapFault $e) {
            // Handle error or log
        }

        return null;
    }

    /**
     * Create cart (CreateSepet).
     * @param int $userId User ID
     * @return WebCartModel|null Returns created cart data or null on error
     */
    public function createSepet(int $userId): ?WebCartModel
    {
        $client = $this->request->soap_client($this->apiUrl);

        try {
            $requestData = [
                'UyeID' => $userId,
            ];

            $response = $client->__soapCall("CreateSepet", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$requestData,
                ]
            ]);

            if (isset($response->CreateSepetResult)) {
                $result = $response->CreateSepetResult;
                
                // Convert products to WebCartProductModel objects
                $urunler = [];
                if (isset($result->Urunler) && $result->Urunler !== null) {
                    $urunlerData = $result->Urunler;
                    
                    if (is_object($urunlerData)) {
                        $urunlerData = [$urunlerData];
                    }
                    
                    if (is_array($urunlerData)) {
                        foreach ($urunlerData as $urun) {
                            if (isset($urun) && $urun !== null) {
                                $urunler[] = new WebCartProductModel($urun);
                            }
                        }
                    }
                }
                
                $cartData = [
                    'ID' => $result->SepetID ?? null,
                    'UyeID' => $result->OverrateSahipId ?? null,
                    'GenelToplam' => $result->GenelToplam ?? 0,
                    'ToplamKDV' => $result->ToplamKDV ?? 0,
                    'ToplamUrunAdedi' => $result->ToplamUrunAdedi ?? 0,
                    'SepetParaBirimiDilKodu' => $result->SepetParaBirimiDilKodu ?? null,
                    'Urunler' => $urunler,
                ];
                
                return new WebCartModel($cartData);
            }
        } catch (SoapFault $e) {
            // Handle error or log
        }

        return null;
    }

    /**
     * Update cart item.
     * @param int $cartId Cart ID
     * @param int $cartProductId Cart product ID
     * @param int $productId Product ID
     * @param float $quantity Quantity (optional, for quantity updates)
     * @param bool $updateQuantity Whether to update quantity (default: false)
     * @param bool $removeFromCart Whether to remove from cart (default: false)
     * @param int $campaignId Campaign ID (optional)
     * @return array Returns response with IsError and ErrorMessage
     */
    public function updateCart(int $cartId, int $cartProductId, int $productId, float $quantity = 1.0, bool $updateQuantity = false, bool $removeFromCart = false, int $campaignId = 0): array
    {
        $client = $this->request->soap_client($this->apiUrl);

        try {
            $requestData = [
                'SepetID' => $cartId,
                'SepetUrunID' => $cartProductId,
                'UrunID' => $productId,
                'Adet' => $quantity,
                'AdetGuncelle' => $updateQuantity,
                'SepettenCikar' => $removeFromCart,
                'KampanyaID' => $campaignId,
            ];

            $response = $client->__soapCall("UpdateSepet", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$requestData,
                ]
            ]);

            if (isset($response->UpdateSepetResult)) {
                $result = $response->UpdateSepetResult;
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
            'ErrorMessage' => 'Failed to update cart'
        ];
    }
}
