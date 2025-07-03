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
            return [
                'IsError' => true,
                'ErrorMessage' => 'Error retrieving cart information: ' . $e->getMessage(),
                'Data' => null
            ];
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
     * @return array Returns ['success' => bool, 'message' => string, 'data' => array]
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
                if ($result->IsError ?? true) {
                    return [
                        'success' => false,
                        'message' => $result->ErrorMessage ?? 'Unknown error occurred',
                        'data' => []
                    ];
                }
                return [
                    'success' => true,
                    'message' => 'Cart updated successfully',
                    'data' => []
                ];
            }
        } catch (SoapFault $e) {
            return [
                'success' => false,
                'message' => 'Failed to update cart: ' . $e->getMessage(),
                'data' => []
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to update cart',
            'data' => []
        ];
    }

    /**
     * Sepet listesini getirir
     * 
     * @param int|null $sepetId Sepet ID (-1 için filtreleme yapılmaz)
     * @param int|null $uyeId Üye ID (-1 için filtreleme yapılmaz)
     * @param string|null $baslangicTarihi Başlangıç tarihi (Y-m-d formatında)
     * @param string|null $bitisTarihi Bitiş tarihi (Y-m-d formatında)
     * @param int|null $sayfaSayisi Sayfa sayısı
     * @param string|null $guidSepetId GUID sepet ID
     * @return array Returns ['success' => bool, 'message' => string, 'data' => array]
     */
    public function selectSepet(
        ?int $sepetId = -1,
        ?int $uyeId = -1,
        ?string $baslangicTarihi = null,
        ?string $bitisTarihi = null,
        ?int $sayfaSayisi = null,
        ?string $guidSepetId = null
    ): array {
        try {
            // Tarihleri kontrol et ve varsayılan değerleri ayarla
            $baslangicTarihi = $baslangicTarihi ? date('Y-m-d', strtotime($baslangicTarihi)) : date('Y-m-d', strtotime('-30 days'));
            $bitisTarihi = $bitisTarihi ? date('Y-m-d', strtotime($bitisTarihi)) : date('Y-m-d');

            $params = [
                'UyeKodu' => $this->request->key,
                'sepetId' => $sepetId,
                'uyeId' => $uyeId,
                'BaslangicTarihi' => $baslangicTarihi,
                'BitisTarihi' => $bitisTarihi,
                'sayfaSayisi' => $sayfaSayisi,
                'guidSepetId' => $guidSepetId
            ];

            $response = $this->request->soap_client($this->apiUrl)->__soapCall("SelectSepet", [$params]);
            $result = $response->SelectSepetResult ?? null;

            if ($result && isset($result->Sepetler) && is_array($result->Sepetler)) {
                return [
                    'success' => true,
                    'message' => 'Cart list retrieved successfully',
                    'data' => $result->Sepetler
                ];
            }

            return [
                'success' => true,
                'message' => 'No carts found',
                'data' => []
            ];

        } catch (SoapFault $e) {
            return [
                'success' => false,
                'message' => 'Error retrieving cart list: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get web cart details using SelectWebSepet API call
     * 
     * @param string|null $dil Language code (defaults to "TR" if empty)
     * @param string|null $paraBirimi Currency code
     * @param int|null $sepetId Cart ID
     * @param int|null $uyeId User ID
     * @return array Returns ['success' => bool, 'message' => string, 'data' => array]
     */
    public function selectWebSepet(
        ?string $dil = null,
        ?string $paraBirimi = null,
        ?int $sepetId = null,
        ?int $uyeId = null
    ): array {
        try {
            $requestData = [
                'Dil' => $dil ?? '',
                'ParaBirimi' => $paraBirimi ?? 'TL',
                'SepetId' => $sepetId,
                'UyeId' => $uyeId
            ];

            $response = $this->request->soap_client($this->apiUrl)->__soapCall("SelectWebSepet", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$requestData
                ]
            ]);

            if (isset($response->SelectWebSepetResult) && isset($response->SelectWebSepetResult->Sepetler)) {
                return [
                    'success' => true,
                    'message' => 'Cart information retrieved successfully',
                    'data' => $response->SelectWebSepetResult->Sepetler
                ];
            }

            return [
                'success' => true,
                'message' => 'No carts found',
                'data' => []
            ];

        } catch (SoapFault $e) {
            return [
                'success' => false,
                'message' => 'Error retrieving web cart information: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }
}
