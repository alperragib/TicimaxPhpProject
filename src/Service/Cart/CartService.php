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
    public function getSepet(int $userId, ?int $cartId = null, int $campaignId = 0): ApiResponse
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
                
                // Raw ürün datası geç (BaseModel convertToArray ile handle edilecek)
                $urunlerRaw = [];
                if (isset($result->Urunler->ServisSepetUrun)) {
                    $urunlerData = $result->Urunler->ServisSepetUrun;
                    
                    // Tek ürün object ise array'e çevir
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
                return ApiResponse::success($cart, 'Sepet başarıyla getirildi.');
            }
            
            return ApiResponse::error('Sepet bulunamadı.');
            
        } catch (SoapFault $e) {
            return ApiResponse::error('Sepet getirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Create cart (CreateSepet).
     * @param int $userId User ID
     * @return ApiResponse
     */
    public function createSepet(int $userId): ApiResponse
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
                
                // ServisSepet response'unu direkt kullan (XML şemaya uygun)
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
                
                return ApiResponse::success($servisSepet, 'Sepet başarıyla oluşturuldu.');
            }
            
            return ApiResponse::error('Sepet oluşturulamadı.');
            
        } catch (SoapFault $e) {
            return ApiResponse::error('Sepet oluşturulurken bir hata oluştu: ' . $e->getMessage());
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

            // Fix: Object casting sorunu - manuel object creation
            $requestObject = new \stdClass();
            $requestObject->SepetID = $requestData['SepetID'];
            $requestObject->SepetUrunID = $requestData['SepetUrunID'];
            $requestObject->UrunID = $requestData['UrunID'];
            $requestObject->Adet = $requestData['Adet'];
            $requestObject->AdetGuncelle = $requestData['AdetGuncelle'];
            $requestObject->SepettenCikar = $requestData['SepettenCikar'];
            $requestObject->KampanyaID = $requestData['KampanyaID'];
            
            $response = $client->__soapCall("UpdateSepet", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => $requestObject,
                ]
            ]);

            if (isset($response->UpdateSepetResult)) {
                $result = $response->UpdateSepetResult;
                if (($result->IsError ?? false) === true) {
                    return ApiResponse::error($result->ErrorMessage ?? 'Bilinmeyen bir hata oluştu');
                }
                return ApiResponse::success($result, 'Sepet başarıyla güncellendi.');
            }
            
            return ApiResponse::error('Sepet güncellenemedi.');
            
        } catch (SoapFault $e) {
            return ApiResponse::error('Sepet güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
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
        ?int $sepetId = null,
        ?int $uyeId = null,
        ?string $baslangicTarihi = null,
        ?string $bitisTarihi = null,
        ?int $sayfaSayisi = null,
        ?string $guidSepetId = null
    ): ApiResponse {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            // DateTime formatını kontrol et (XML şema dateTime istiyor)
            $startDate = null;
            $endDate = null;
            
            if ($baslangicTarihi) {
                $startDate = date('c', strtotime($baslangicTarihi)); // ISO 8601 format
            }
            if ($bitisTarihi) {
                $endDate = date('c', strtotime($bitisTarihi)); // ISO 8601 format  
            }

            $response = $client->__soapCall("SelectSepet", [
                [
                    'UyeKodu' => $this->request->key,
                    'sepetId' => $sepetId,
                    'uyeId' => $uyeId,
                    'BaslangicTarihi' => $startDate,
                    'BitisTarihi' => $endDate,
                    'sayfaSayisi' => $sayfaSayisi,
                    'guidSepetId' => $guidSepetId
                ]
            ]);
            
            $result = $response->SelectSepetResult ?? null;

            if ($result && isset($result->Sepetler)) {
                $sepetler = $result->Sepetler->WebSepet ?? [];
                if (is_object($sepetler)) {
                    $sepetler = [$sepetler];
                }
                
                $carts = [];
                foreach ($sepetler as $sepet) {
                    $carts[] = new WebCartModel($sepet);
                }
                
                return ApiResponse::success([
                    'carts' => $carts,
                    'hasNext' => $result->Next ?? false
                ], 'Sepet listesi başarıyla getirildi.');
            }

            return ApiResponse::success(
                [],
               
            );

        } catch (SoapFault $e) {
            return ApiResponse::error('Sepet listesi getirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Get web cart details using SelectWebSepet API call
     * 
     * @param string|null $dil Language code (defaults to "TR" if empty)
     * @param string|null $paraBirimi Currency code
     * @param int|null $sepetId Cart ID
     * @param int|null $uyeId User ID
     * @return ApiResponse
     */
    public function selectWebSepet(
        ?string $dil = null,
        ?string $paraBirimi = null,
        ?int $sepetId = null,
        ?int $uyeId = null
    ): ApiResponse {
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
                return ApiResponse::success($response->SelectWebSepetResult->Sepetler, 'Sepet bilgileri başarıyla getirildi.');
            }

            return ApiResponse::success([], 'Sepet bulunamadı.');

        } catch (SoapFault $e) {
            return ApiResponse::error('Web sepet bilgileri getirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
