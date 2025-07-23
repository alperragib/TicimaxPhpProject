<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Cart;

use AlperRagib\Ticimax\Model\Cart\WebCartModel;
use AlperRagib\Ticimax\Model\Cart\WebCartProductModel;
use AlperRagib\Ticimax\Model\Response\ApiResponse;
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
     * @return ApiResponse
     */
    public function getCart(int $userId, ?int $cartId = null, int $campaignId = 0): ApiResponse
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
                
                // Pass raw product data (will be handled by BaseModel convertToArray)
                $urunlerRaw = [];
                if (isset($result->Urunler->ServisSepetUrun)) {
                    $urunlerData = $result->Urunler->ServisSepetUrun;
                    
                    // Convert single object to array if needed
                    if (is_object($urunlerData) && !is_array($urunlerData)) {
                        $urunlerRaw = [$urunlerData];
                    } else if (is_array($urunlerData)) {
                        $urunlerRaw = $urunlerData;
                    }
                }
                
                $cartData = [
                    'ID' => $result->SepetID ?? null,
                    'UyeID' => $result->OverrateSahipId ?? null,
                    'GenelToplam' => $result->GenelToplam ?? 0,
                    'ToplamKDV' => $result->ToplamKDV ?? 0,
                    'ToplamUrunAdedi' => $result->ToplamUrunAdedi ?? 0,
                    'SepetParaBirimiDilKodu' => $result->SepetParaBirimiDilKodu ?? null,
                    'Urunler' => $urunlerRaw,
                ];
                
                $cart = new WebCartModel($cartData);
                return ApiResponse::success($cart, 'Cart retrieved successfully.');
            }
            
            return ApiResponse::error('Cart not found.');
            
        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving cart: ' . $e->getMessage());
        }
    }



    /**
     * Create cart (CreateSepet).
     * @param int $userId User ID
     * @return ApiResponse
     */
    public function createCart(int $userId): ApiResponse
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
                
                // Use ServisSepet response directly (XML schema compliant)
                $servisSepet = [
                    'GenelKDVToplam' => $result->GenelKDVToplam ?? 0.0,
                    'GenelToplam' => $result->GenelToplam ?? 0.0,
                    'HediyeCekiKodu' => $result->HediyeCekiKodu ?? null,
                    'HediyeCekiTutari' => $result->HediyeCekiTutari ?? 0.0,
                    'HediyeCekiZubizuKampanyaId' => $result->HediyeCekiZubizuKampanyaId ?? 0,
                    'HediyePaketiTutari' => $result->HediyePaketiTutari ?? 0.0,
                    'HopiIndirimi' => $result->HopiIndirimi ?? 0.0,
                    'HopiParacikKullanimi' => $result->HopiParacikKullanimi ?? 0.0,
                    'IndirimlerToplami' => $result->IndirimlerToplami ?? 0.0,
                    'KampanyaID' => $result->KampanyaID ?? 0,
                    'KampanyaIndirimKDV' => $result->KampanyaIndirimKDV ?? 0.0,
                    'KampanyaIndirimTutari' => $result->KampanyaIndirimTutari ?? 0.0,
                    'KampanyasizUrunlerToplami' => $result->KampanyasizUrunlerToplami ?? 0.0,
                    'OverrateSahipId' => $result->OverrateSahipId ?? 0,
                    'SahipID' => $result->SahipID ?? null,
                    'SepetID' => $result->SepetID ?? 0,
                    'SepetParaBirimiDilKodu' => $result->SepetParaBirimiDilKodu ?? null,
                    'ToplamKDV' => $result->ToplamKDV ?? 0.0,
                    'ToplamTutar' => $result->ToplamTutar ?? 0.0,
                    'ToplamUrunAdedi' => $result->ToplamUrunAdedi ?? 0.0,
                    'UrunOzellestirmeFiyatlari' => $result->UrunOzellestirmeFiyatlari ?? 0.0,
                    'Urunler' => $result->Urunler ?? null
                ];
                
                return ApiResponse::success($servisSepet, 'Cart created successfully.');
            }
            
            return ApiResponse::error('Failed to create cart.');
            
        } catch (SoapFault $e) {
            return ApiResponse::error('Error creating cart: ' . $e->getMessage());
        }
    }

   

    /**
     * Select cart with filters
     * @param int $cartId Cart ID
     * @param int $userId User ID
     * @param string $startDate Start date
     * @param string $endDate End date
     * @param int|null $pageSize Page size (optional)
     * @param string|null $guidCartId GUID cart ID (optional)
     * @return ApiResponse
     */
    public function selectCart(
        int $cartId,
        int $userId,
        string $startDate,
        string $endDate,
        ?int $pageSize = null,
        ?string $guidCartId = null
    ): ApiResponse {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            // Convert dates to ISO 8601 format for SOAP
            $startDateFormatted = date('c', strtotime($startDate));
            $endDateFormatted = date('c', strtotime($endDate));

            $params = [
                'UyeKodu' => $this->request->key,
                'sepetId' => $cartId,
                'uyeId' => $userId,
                'BaslangicTarihi' => $startDate,
                'BitisTarihi' => $endDate
            ];

            // Add optional parameters
            if ($sayfaSayisi !== null) {
                $params['sayfaSayisi'] = $sayfaSayisi;
            }
            if ($guidCartId !== null) {
                $params['guidSepetId'] = $guidCartId;
            }

            $response = $client->__soapCall("SelectSepet", [$params]);
            
            $result = $response->SelectSepetResult ?? null;

            if ($result && isset($result->Sepetler)) {
                $sepetler = $result->Sepetler->WebSepet ?? [];
                if (is_object($sepetler)) {
                    $sepetler = [$sepetler];
                }
                
                $carts = [];
                foreach ($sepetler as $sepet) {
                    // Convert WebSepet structure to WebCartModel compatible format
                    $urunlerArray = [];
                    if (isset($sepet->Urunler->WebSepetUrun)) {
                        $webSepetUrun = $sepet->Urunler->WebSepetUrun;
                        
                        // Handle single product or array of products
                        if (is_object($webSepetUrun) && !is_array($webSepetUrun)) {
                            $urunlerArray = [$webSepetUrun];
                        } else if (is_array($webSepetUrun)) {
                            $urunlerArray = $webSepetUrun;
                        }
                    }
                    
                    // Calculate totals from products
                    $genelToplam = 0;
                    $toplamKDV = 0;
                    $toplamUrunAdedi = 0;
                    
                    foreach ($urunlerArray as $urun) {
                        $genelToplam += ($urun->UrunSepetFiyati ?? 0) * ($urun->Adet ?? 0);
                        $toplamKDV += ($urun->KDVTutari ?? 0) * ($urun->Adet ?? 0);
                        $toplamUrunAdedi += ($urun->Adet ?? 0);
                    }
                    
                    $cartData = [
                        'ID' => $sepet->ID ?? 0,
                        'GuidSepetID' => $sepet->GuidSepetID ?? null,
                        'UyeID' => $sepet->UyeID ?? null,
                        'UyeAdi' => $sepet->UyeAdi ?? null,
                        'UyeMail' => $sepet->UyeMail ?? null,
                        'SepetTarihi' => $sepet->SepetTarihi ?? null,
                        'GenelToplam' => $genelToplam,
                        'ToplamKDV' => $toplamKDV,
                        'ToplamUrunAdedi' => $toplamUrunAdedi,
                        'Urunler' => $urunlerArray,
                    ];
                    
                    $carts[] = new WebCartModel($cartData);
                }
                
                return ApiResponse::success([
                    'carts' => $carts,
                    'hasNext' => $result->Next ?? false
                ], 'Cart list retrieved successfully.');
            }

            return ApiResponse::success(
                [],
                'No carts found.'
            );

        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving cart list: ' . $e->getMessage());
        }
    }



    /**
     * Get web cart details using SelectWebSepet API call
     * According to WSDL specification
     * 
     * @param string|null $dil Language code (defaults to "TR" if empty)
     * @param string|null $paraBirimi Currency code  
     * @param int|null $sepetId Cart ID (optional)
     * @param int|null $uyeId User ID (optional)
     * @return ApiResponse
     */
    public function selectWebCart(
        ?string $dil = null,
        ?string $paraBirimi = null,
        ?int $sepetId = null,
        ?int $uyeId = null
    ): ApiResponse {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            // Build SelectWebSepetRequest according to WSDL
            $requestData = [
                'Dil' => $dil ?? '',  // Empty string defaults to "TR" on server side
                'ParaBirimi' => $paraBirimi,
                'SepetId' => $sepetId,
                'UyeId' => $uyeId
            ];

            $response = $client->__soapCall("SelectWebSepet", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$requestData
                ]
            ]);

            $result = $response->SelectWebSepetResult ?? null;

            if ($result && isset($result->Sepetler)) {
                $sepetler = $result->Sepetler->WebSepet ?? [];
                if (is_object($sepetler)) {
                    $sepetler = [$sepetler];
                }
                
                $carts = [];
                foreach ($sepetler as $sepet) {
                    $cartData = [
                        'ID' => $sepet->ID ?? null,
                        'UyeID' => $sepet->UyeID ?? null,
                        'UyeAdi' => $sepet->UyeAdi ?? null,
                        'UyeMail' => $sepet->UyeMail ?? null,
                        'SepetTarihi' => $sepet->SepetTarihi ?? null,
                        'Urunler' => []
                    ];

                    // Process products
                    if (isset($sepet->Urunler->WebSepetUrun)) {
                        $webSepetUrun = $sepet->Urunler->WebSepetUrun;
                        
                        // Handle single product or array of products
                        if (is_object($webSepetUrun) && !is_array($webSepetUrun)) {
                            $webSepetUrun = [$webSepetUrun];
                        }
                        
                        foreach ($webSepetUrun as $urun) {
                            $cartData['Urunler'][] = [
                                'ID' => $urun->ID ?? null,
                                'SepetID' => $urun->SepetID ?? null,
                                'UrunID' => $urun->UrunID ?? null,
                                'UrunKartiID' => $urun->UrunKartiID ?? null,
                                'UrunAdi' => $urun->UrunAdi ?? '',
                                'StokKodu' => $urun->StokKodu ?? '',
                                'SpotResim' => $urun->SpotResim ?? '',
                                'Adet' => $urun->Adet ?? 0.0,
                                'Desi' => $urun->Desi ?? 0.0,
                                'Fiyati' => $urun->Fiyati ?? 0.0,
                                'UrunSepetFiyati' => $urun->UrunSepetFiyati ?? 0.0,
                                'UrunSepetFiyatiKDV' => $urun->UrunSepetFiyatiKDV ?? 0.0,
                                'KDVOrani' => $urun->KDVOrani ?? 0.0,
                                'KDVTutari' => $urun->KDVTutari ?? 0.0,
                                'KargoUcreti' => $urun->KargoUcreti ?? 0.0,
                                'UcretsizKargo' => $urun->UcretsizKargo ?? false,
                                'ParaBirimi' => $urun->ParaBirimi ?? '',
                                'ParaBirimiDilKodu' => $urun->ParaBirimiDilKodu ?? ''
                            ];
                        }
                    }
                    
                    $carts[] = (object)$cartData;
                }

                return ApiResponse::success([
                    'carts' => $carts,
                    'hasNext' => $result->Next ?? false,
                    'totalCarts' => count($carts)
                ], 'Web cart information retrieved successfully.');
            }

            return ApiResponse::success([
                'carts' => [],
                'hasNext' => false,
                'totalCarts' => 0
            ], 'No web carts found.');

        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving web cart information: ' . $e->getMessage());
        }
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
     * @return ApiResponse
     */
    public function updateCart(int $cartId, int $cartProductId, int $productId, float $quantity = 1.0, bool $updateQuantity = false, bool $removeFromCart = false, int $campaignId = 0): ApiResponse
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

            // Fix: Send as object to ensure proper SOAP serialization
            $response = $client->__soapCall("UpdateSepet", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => (object)$requestData,  // Convert to object for proper SOAP serialization
                ]
            ]);

            if (isset($response->UpdateSepetResult)) {
                $result = $response->UpdateSepetResult;
                if (($result->IsError ?? false) === true) {
                    return ApiResponse::error($result->ErrorMessage ?? 'Unknown error occurred');
                }
                return ApiResponse::success($result, 'Cart updated successfully.');
            }
            
            return ApiResponse::error('Failed to update cart.');
            
        } catch (SoapFault $e) {
            return ApiResponse::error('Error updating cart: ' . $e->getMessage());
        }
    }

    /**
     * Get cart information using SelectSepet API call
     * According to WSDL specification
     * 
     * @param int $sepetId Cart ID (required)
     * @param int $uyeId User ID (required) 
     * @param string $baslangicTarihi Start date (required, format: Y-m-d H:i:s)
     * @param string $bitisTarihi End date (required, format: Y-m-d H:i:s)
     * @param int|null $sayfaSayisi Page size (optional)
     * @param string|null $guidSepetId GUID cart ID (optional)
     * @return ApiResponse
     */
    public function selectSepet(
        int $sepetId,
        int $uyeId,
        string $baslangicTarihi,
        string $bitisTarihi,
        ?int $sayfaSayisi = null,
        ?string $guidSepetId = null
    ): ApiResponse {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            // Convert dates to ISO 8601 format for SOAP
            $startDate = date('c', strtotime($baslangicTarihi));
            $endDate = date('c', strtotime($bitisTarihi));

            $params = [
                'UyeKodu' => $this->request->key,
                'sepetId' => $sepetId,
                'uyeId' => $uyeId,
                'BaslangicTarihi' => $startDate,
                'BitisTarihi' => $endDate
            ];

            // Add optional parameters
            if ($sayfaSayisi !== null) {
                $params['sayfaSayisi'] = $sayfaSayisi;
            }
            if ($guidSepetId !== null) {
                $params['guidSepetId'] = $guidSepetId;
            }

            $response = $client->__soapCall("SelectSepet", [$params]);
            
            $result = $response->SelectSepetResult ?? null;

            if ($result && isset($result->Sepetler)) {
                $sepetler = $result->Sepetler->WebSepet ?? [];
                if (is_object($sepetler)) {
                    $sepetler = [$sepetler];
                }
                
                $carts = [];
                foreach ($sepetler as $sepet) {
                    $cartData = [
                        'ID' => $sepet->ID ?? null,
                        'GuidSepetID' => $sepet->GuidSepetID ?? null,
                        'UyeID' => $sepet->UyeID ?? null,
                        'UyeAdi' => $sepet->UyeAdi ?? null,
                        'UyeMail' => $sepet->UyeMail ?? null,
                        'SepetTarihi' => $sepet->SepetTarihi ?? null,
                        'Urunler' => []
                    ];

                    // Process products
                    if (isset($sepet->Urunler->WebSepetUrun)) {
                        $webSepetUrun = $sepet->Urunler->WebSepetUrun;
                        
                        // Handle single product or array of products
                        if (is_object($webSepetUrun) && !is_array($webSepetUrun)) {
                            $webSepetUrun = [$webSepetUrun];
                        }
                        
                        foreach ($webSepetUrun as $urun) {
                            $cartData['Urunler'][] = [
                                'ID' => $urun->ID ?? null,
                                'SepetID' => $urun->SepetID ?? null,
                                'UrunID' => $urun->UrunID ?? null,
                                'UrunKartiID' => $urun->UrunKartiID ?? null,
                                'UrunAdi' => $urun->UrunAdi ?? '',
                                'StokKodu' => $urun->StokKodu ?? '',
                                'SpotResim' => $urun->SpotResim ?? '',
                                'Adet' => $urun->Adet ?? 0.0,
                                'UrunSepetFiyati' => $urun->UrunSepetFiyati ?? 0.0,
                                'UrunSepetFiyatiKDV' => $urun->UrunSepetFiyatiKDV ?? 0.0,
                                'KDVOrani' => $urun->KDVOrani ?? 0.0,
                                'KDVTutari' => $urun->KDVTutari ?? 0.0,
                                'KargoUcreti' => $urun->KargoUcreti ?? 0.0,
                                'UcretsizKargo' => $urun->UcretsizKargo ?? false,
                                'ParaBirimi' => $urun->ParaBirimi ?? '',
                                'ParaBirimiDilKodu' => $urun->ParaBirimiDilKodu ?? '',
                                'GuidSepetID' => $urun->GuidSepetID ?? null,
                                'GuidSepetUrunID' => $urun->GuidSepetUrunID ?? null
                            ];
                        }
                    }
                    
                    $carts[] = (object)$cartData;
                }

                return ApiResponse::success([
                    'carts' => $carts,
                    'hasNext' => $result->Next ?? false,
                    'totalCarts' => count($carts)
                ], 'Carts retrieved successfully.');
            }

            return ApiResponse::success([
                'carts' => [],
                'hasNext' => false,
                'totalCarts' => 0
            ], 'No carts found.');

        } catch (SoapFault $e) {
            return ApiResponse::error('Error retrieving carts: ' . $e->getMessage());
        }
    }
}