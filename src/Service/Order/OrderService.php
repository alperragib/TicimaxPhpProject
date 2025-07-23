<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Order;

use AlperRagib\Ticimax\Model\Order\OrderModel;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;
use AlperRagib\Ticimax\Model\Response\ApiResponse;

/**
 * Class OrderService
 * Handles order-related API operations.
 */
class OrderService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/SiparisServis.svc?singleWsdl";

    /**
     * Order data for SaveSiparis operation
     * @var array
     */
    public array $data = [];

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get orders
     * @param array $filters Filters
     * @param array $pagination Pagination (page and per_page can be used)
     *                         - Simple usage: ['page' => 1, 'per_page' => 50]
     *                         - Advanced: ['BaslangicIndex' => 0, 'KayitSayisi' => 50]
     * @return ApiResponse
     */
    public function getOrders(array $filters = [], array $pagination = []): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $defaultFilters = [
                'DurumTarihiBas'              => null,
                'DurumTarihiSon'              => null,
                'DuzenlemeTarihiBas'         => null,
                'DuzenlemeTarihiSon'         => null,
                'EFaturaURL'                 => null,
                'EntegrasyonAktarildi'       => -1,
                'EntegrasyonParams'          => [
                    'AlanDeger'             => '',
                    'Deger'                 => '',
                    'EntegrasyonKodu'      => '',
                    'EntegrasyonParamsAktif' => false,
                    'TabloAlan'            => '',
                    'Tanim'                => ''
                ],
                'FaturaNo'                   => '',
                'IptalEdilmisUrunler'        => true,
                'KampanyaGetir'              => false,
                'KargoEntegrasyonTakipDurumu' => null,
                'KargoFirmaID'               => -1,
                'OdemeDurumu'                => -1,
                'OdemeGetir'                 => null,
                'OdemeTamamlandi'            => null,
                'OdemeTipi'                  => -1,
                'PaketlemeDurumu'            => null,
                'PazaryeriIhracat'           => null,
                'SiparisDurumu'              => -1,
                'SiparisID'                  => -1,
                'SiparisKaynagi'             => '',
                'SiparisKodu'                => '',
                'SiparisNo'                  => '',
                'SiparisTarihiBas'           => null,
                'SiparisTarihiSon'           => null,
                'StrPaketlemeDurumu'         => '',
                'StrSiparisDurumu'           => '',
                'StrSiparisID'               => '',
                'TedarikciID'                => -1,  // -1 = all suppliers
                'TeslimatGunuBas'            => null,
                'TeslimatGunuSon'            => null,
                'TeslimatMagazaID'           => null,
                'UrunGetir'                  => null,
                'UyeID'                      => -1,
                'UyeTelefon'                 => '',
            ];

            // Simple pagination support: page and per_page can be used
            if (isset($pagination['page']) && isset($pagination['per_page'])) {
                $page = max(1, (int)$pagination['page']);
                $perPage = max(1, (int)$pagination['per_page']);
                $pagination['BaslangicIndex'] = ($page - 1) * $perPage;
                $pagination['KayitSayisi'] = $perPage;
                unset($pagination['page'], $pagination['per_page']);
            }

            $defaultPagination = [
                'BaslangicIndex'  => 0,
                'KayitSayisi'     => 20,
                'SiralamaDegeri'  => 'ID',
                'SiralamaYonu'    => 'DESC',
            ];

            $orderFiltre = array_merge($defaultFilters, $filters);
            $orderSayfalama = array_merge($defaultPagination, $pagination);
            
            $response = $client->__soapCall("SelectSiparis", [
                [
                    'UyeKodu' => $this->request->key,
                    'f'       => (object)$orderFiltre,
                    's'       => (object)$orderSayfalama,
                ]
            ]);
            
            $ordersArr = $response->SelectSiparisResult->WebSiparis ?? [];
            if (is_object($ordersArr)) {
                $ordersArr = [$ordersArr];
            }

            $orders = [];
            foreach ($ordersArr as $order) {
                $orders[] = new OrderModel($order);
            }

            return ApiResponse::success(
                $orders,
                'Orders retrieved successfully.'
            );

        } catch (SoapFault $e) {
            return ApiResponse::error(
                'Error retrieving orders: ' . $e->getMessage()
            );
        }
    }

    /**
     * Create a new order via the API.
     * @param OrderModel $order
     * @return ApiResponse
     */
    public function createOrder(OrderModel $order): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $params = [
                [
                    'UyeKodu' => $this->request->key,
                    'Siparis' => $order->toArray(),
                ]
            ];
            $response = $client->__soapCall("SaveSiparis", $params);
            $orderId = $response->SaveSiparisResult->ID ?? 0;
            
            if ($orderId > 0) {
                return ApiResponse::success(
                    ['orderId' => $orderId],
                    'Order created successfully.'
                );
            } else {
                return ApiResponse::error('Failed to create order.');
            }
        } catch (SoapFault $e) {
            return ApiResponse::error('Error creating order: ' . $e->getMessage());
        }
    }

    /**
     * Create new order
     * @param array $data Order data
     * @return ApiResponse
     */
    public function saveOrder(): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            // Creating required payment information for order (WebSiparisSaveOdeme)
            $odeme = [
                'BankaKomisyonu' => $this->data['Odeme']['BankaKomisyonu'] ?? 0.0,
                'HavaleHesapID' => $this->data['Odeme']['HavaleHesapID'] ?? null,
                'KapidaOdemeTutari' => $this->data['Odeme']['KapidaOdemeTutari'] ?? 0.0,
                'OdemeDurumu' => $this->data['Odeme']['OdemeDurumu'] ?? 1, // Required: Payment status
                'OdemeIndirimi' => $this->data['Odeme']['OdemeIndirimi'] ?? 0.0,
                'OdemeNotu' => $this->data['Odeme']['OdemeNotu'] ?? '',
                'OdemeSecenekID' => $this->data['Odeme']['OdemeSecenekID'] ?? 0, // Required: Defined payment option ID
                'OdemeTipi' => $this->data['Odeme']['OdemeTipi'] ?? 1, // Required: Payment type
                'TaksitSayisi' => $this->data['Odeme']['TaksitSayisi'] ?? 1,
                'Tarih' => $this->data['Odeme']['Tarih'] ?? date('c'),
                'Tutar' => $this->data['Odeme']['Tutar'] ?? 0.0 // Required
            ];

            // Sample structure for order products (List<WebSiparisSaveUrun>)
            $urunler = [];
            foreach ($this->data['Urunler'] ?? [] as $urun) {
                $urunler[] = [
                    'Adet' => $urun['Adet'] ?? 1, // Required
                    'KdvOrani' => $urun['KdvOrani'] ?? 18,
                    'KdvTutari' => $urun['KdvTutari'] ?? 0.0,
                    'Maliyet' => $urun['Maliyet'] ?? 0.0,
                    'Tutar' => $urun['Tutar'] ?? 0.0, // Required
                    'UrunID' => $urun['UrunID'] ?? 0 // Required
                ];
            }

            // Main order structure - WebSiparisSaveRequest
            $siparis = [
                // Required fields according to documentation
                'FaturaAdresId' => $this->data['FaturaAdresId'] ?? 0, // Required: Registered invoice address ID
                'KargoAdresId' => $this->data['KargoAdresId'] ?? 0,  // Required: Registered shipping address ID
                'KargoFirmaId' => $this->data['KargoFirmaId'] ?? 0,  // Required: Registered shipping company ID
                'UyeId' => $this->data['UyeId'] ?? 0,  // Required: Member ID
                'UrunTutari' => $this->data['UrunTutari'] ?? 0.0, // Required: Product total amount
                
                // Optional fields
                'IndirimTutari' => $this->data['IndirimTutari'] ?? 0.0,
                'KargoTutari' => $this->data['KargoTutari'] ?? 0.0,
                'ParaBirimi' => $this->data['ParaBirimi'] ?? 'TRY',
                'SiparisKaynagi' => $this->data['SiparisKaynagi'] ?? 'Web',
                'SiparisNotu' => $this->data['SiparisNotu'] ?? '',
                'UrunTutariKdv' => $this->data['UrunTutariKdv'] ?? 0.0,
                'TeslimatSaati' => $this->data['TeslimatSaati'] ?? '',
                'TeslimatTarihi' => $this->data['TeslimatTarihi'] ?? date('c'),
                
                // Required complex objects
                'Odeme' => $odeme,
                'Urunler' => $urunler
            ];

            // Validate required fields
            $requiredFields = ['FaturaAdresId', 'KargoAdresId', 'KargoFirmaId', 'UyeId', 'UrunTutari'];
            foreach ($requiredFields as $field) {
                if (empty($siparis[$field])) {
                    return ApiResponse::error("Required field missing: $field");
                }
            }

            // Making API call
            $response = $client->__soapCall("SaveSiparis", [
                [
                    'UyeKodu' => $this->request->key,
                    'siparis' => (object)$siparis
                ]
            ]);

            // Response validation - WebSiparisSaveResponse structure
            if (isset($response->SaveSiparisResult)) {
                $result = $response->SaveSiparisResult;
                
                if (isset($result->IsError) && $result->IsError === false) {
                    $orderDetails = $result->SiparisDetayi ?? null;
                    $orderId = $orderDetails->ID ?? null;
                    
                    return ApiResponse::success(
                        [
                            'orderId' => $orderId,
                            'orderDetails' => $orderDetails
                        ],
                        'Order created successfully.'
                    );
                }
                
                // Error handling
                $errorMessages = [];
                if (isset($result->Messages) && is_array($result->Messages)) {
                    foreach ($result->Messages as $message) {
                        if (isset($message->ErrorMessage)) {
                            $errorMessages[] = $message->ErrorMessage;
                        }
                    }
                }
                
                $errorMessage = !empty($errorMessages) 
                    ? implode('. ', $errorMessages)
                    : ($result->ErrorMessage ?? 'Unknown error');
                    
                error_log("Order save error: " . $errorMessage);
                return ApiResponse::error('Order save failed: ' . $errorMessage);
            }

            return ApiResponse::error('Invalid response format from API');

        } catch (SoapFault $e) {
            error_log("SOAP Error in SaveSiparis: " . $e->getMessage());
            return ApiResponse::error('SOAP Error: ' . $e->getMessage());
        }
    }

    /**
     * Get order payments
     * @param int $orderId Order ID
     * @param int $paymentId Payment ID (optional, 0 for all)
     * @param bool|null $isTransferred Filter by transfer status (optional)
     * @return ApiResponse
     */
    public function getOrderPayments(int $orderId, int $paymentId = 0, ?bool $isTransferred = null): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            $response = $client->__soapCall("SelectSiparisOdeme", [
                [
                    'UyeKodu' => $this->request->key,
                    'siparisId' => $orderId,
                    'odemeId' => $paymentId,
                    'isAktarildi' => $isTransferred
                ]
            ]);
            
            if (isset($response->SelectSiparisOdemeResult->WebSiparisOdeme)) {
                $odemeler = $response->SelectSiparisOdemeResult->WebSiparisOdeme;
                
                if (is_object($odemeler)) {
                    $odemeler = [$odemeler];
                }

                $payments = [];
                foreach ($odemeler as $odeme) {
                    $payments[] = [
                        'ID' => $odeme->ID ?? null,
                        'SiparisID' => $odeme->SiparisID ?? null,
                        'UyeID' => $odeme->UyeID ?? null,
                        'OdemeTipi' => $odeme->OdemeTipi ?? null,
                        'OdemeSecenekID' => $odeme->OdemeSecenekID ?? null,
                        'BankaKomisyonu' => $odeme->BankaKomisyonu ?? 0.0,
                        'HavaleBankaID' => $odeme->HavaleBankaID ?? null,
                        'HavaleHesapID' => $odeme->HavaleHesapID ?? null,
                        'KKOdemeBankaID' => $odeme->KKOdemeBankaID ?? null,
                        'KapidaOdemeTutari' => $odeme->KapidaOdemeTutari ?? 0.0,
                        'OdemeIndirimi' => $odeme->OdemeIndirimi ?? 0.0,
                        'OdemeNotu' => $odeme->OdemeNotu ?? '',
                        'Onaylandi' => $odeme->Onaylandi ?? null,
                        'PosReferansID' => $odeme->PosReferansID ?? '',
                        'TaksitSayisi' => $odeme->TaksitSayisi ?? 1,
                        'Tarih' => $odeme->Tarih ?? null,
                        'Tutar' => $odeme->Tutar ?? 0.0,
                        'CheckSum' => $odeme->CheckSum ?? ''
                    ];
                }

                return ApiResponse::success(
                    $payments,
                    'Payment information retrieved successfully.'
                );
            }

            return ApiResponse::success(
                [],
                'No payment information found.'
            );

        } catch (SoapFault $e) {
            return ApiResponse::error(
                'Error retrieving payment information: ' . $e->getMessage()
            );
        }
    }

    /**
     * Get order products
     * @param int $orderId Order ID
     * @param bool $includeCancelledProducts Include cancelled products (default false)
     * @return ApiResponse
     */
    public function getOrderProducts(int $orderId, bool $includeCancelledProducts = false): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            $response = $client->__soapCall("SelectSiparisUrun", [
                [
                    'UyeKodu' => $this->request->key,
                    'siparisId' => $orderId,
                    'iptalEdilmisUrunler' => $includeCancelledProducts
                ]
            ]);
            
            if (isset($response->SelectSiparisUrunResult->WebSiparisUrun)) {
                $urunler = $response->SelectSiparisUrunResult->WebSiparisUrun;
                
                // Convert single object to array if needed
                if (is_object($urunler)) {
                    $urunler = [$urunler];
                }

                $products = [];
                foreach ($urunler as $urun) {
                    $products[] = [
                        // Basic Info
                        'ID' => $urun->ID ?? null,
                        'SiparisID' => $urun->SiparisID ?? null,  // Fixed: SiparisId -> SiparisID
                        'UrunID' => $urun->UrunID ?? null,
                        'UrunKartiID' => $urun->UrunKartiID ?? null,
                        'UrunAdi' => $urun->UrunAdi ?? '',
                        'StokKodu' => $urun->StokKodu ?? '',
                        'Barkod' => $urun->Barkod ?? '',
                        
                        // Quantity & Pricing
                        'Adet' => $urun->Adet ?? 0.0,
                        'Tutar' => $urun->Tutar ?? 0.0,
                        'Maliyet' => $urun->Maliyet ?? 0.0,
                        'KdvOrani' => $urun->KdvOrani ?? 0.0,
                        'KdvTutari' => $urun->KdvTutari ?? 0.0,
                        'SatisBirimi' => $urun->SatisBirimi ?? '',
                        'UrunAgirligi' => $urun->UrunAgirligi ?? 0.0,
                        
                        // Status & Process
                        'Durum' => $urun->Durum ?? null,
                        'DurumAd' => $urun->DurumAd ?? '',
                        'IslemID' => $urun->IslemID ?? null,
                        'IslemAd' => $urun->IslemAd ?? '',
                        'IslemTarihi' => $urun->IslemTarihi ?? null,
                        
                        // Pricing Details
                        'SatisAniSatisFiyat' => $urun->SatisAniSatisFiyat ?? 0.0,
                        'SatisAniSatisFiyatKdv' => $urun->SatisAniSatisFiyatKdv ?? 0.0,
                        'SatisAniIndirimliFiyat' => $urun->SatisAniIndirimliFiyat ?? 0.0,
                        'SatisAniIndirimliFiyatKdv' => $urun->SatisAniIndirimliFiyatKdv ?? 0.0,
                        'FiyatIndirimOrani' => $urun->FiyatIndirimOrani ?? 0.0,
                        'FiyatIndirimi' => $urun->FiyatIndirimi ?? 0.0,
                        'ToplamIndirim' => $urun->ToplamIndirim ?? 0.0,
                        
                        // Campaign & Discounts
                        'KampanyaID' => $urun->KampanyaID ?? null,
                        'KampanyaIndirimOrani' => $urun->KampanyaIndirimOrani ?? 0.0,
                        'KampanyaIndirimTutari' => $urun->KampanyaIndirimTutari ?? 0.0,
                        'HediyeCekiIndirimi' => $urun->HediyeCekiIndirimi ?? 0.0,
                        
                        // Supplier Info
                        'TedarikciID' => $urun->TedarikciID ?? null,
                        'TedarikciKodu' => $urun->TedarikciKodu ?? '',
                        'TedarikciKodu2' => $urun->TedarikciKodu2 ?? '',
                        
                        // Store Info
                        'MagazaID' => $urun->MagazaID ?? null,
                        'MagazaKodu' => $urun->MagazaKodu ?? '',
                        'MagazaDurum' => $urun->MagazaDurum ?? null,
                        'MagazaAtamaTarihi' => $urun->MagazaAtamaTarihi ?? null,
                        'MagazaGonderimTarihi' => $urun->MagazaGonderimTarihi ?? null,
                        
                        // Shipping Info
                        'KargoFirmaID' => $urun->KargoFirmaID ?? null,
                        'KargoPaketID' => $urun->KargoPaketID ?? null,
                        'KargoTipi' => $urun->KargoTipi ?? null,
                        'KargoTutari' => $urun->KargoTutari ?? 0.0,
                        'KargoTakipNumarasi' => $urun->KargoTakipNumarasi ?? '',
                        'KargoTakipLink' => $urun->KargoTakipLink ?? '',
                        
                        // Return Info
                        'IadeNedenId' => $urun->IadeNedenId ?? null,
                        'IadeNeden' => $urun->IadeNeden ?? '',
                        
                        // Product Creation Info
                        'UrunOlusturmaGrupId' => $urun->UrunOlusturmaGrupId ?? null,
                        'UrunOlusturmaGrupAdi' => $urun->UrunOlusturmaGrupAdi ?? '',
                        'UrunOlusturmaKartId' => $urun->UrunOlusturmaKartId ?? null,
                        'UrunOlusturmaUrunAdi' => $urun->UrunOlusturmaUrunAdi ?? '',
                        'UrunOlusturmaGuid' => $urun->UrunOlusturmaGuid ?? '',
                        'UrunTipi' => $urun->UrunTipi ?? null,
                        
                        // Additional Info
                        'AsortiGrupId' => $urun->AsortiGrupId ?? null,
                        'EskiSiparisUrunId' => $urun->EskiSiparisUrunId ?? null,
                        'FormId' => $urun->FormId ?? null,
                        'FormIdList' => $urun->FormIdList ?? null,
                        'GtipKodu' => $urun->GtipKodu ?? '',
                        'IscilikAgirlik' => $urun->IscilikAgirlik ?? 0.0,
                        'IscilikParaBirimiID' => $urun->IscilikParaBirimiID ?? null,
                        'ResimYolu' => $urun->ResimYolu ?? '',
                        'MarketplaceParams' => $urun->MarketplaceParams ?? '',
                        
                        // Complex Objects (will be handled as-is)
                        'EkSecenekList' => $urun->EkSecenekList ?? null,
                        'Ozellestirme' => $urun->Ozellestirme ?? null
                    ];
                }

                return ApiResponse::success(
                    $products,
                    sprintf(
                        'Order products retrieved successfully. Total %d products found.',
                        count($products)
                    )
                );
            }

            return ApiResponse::success(
                [],
                'No products found in this order.'
            );

        } catch (SoapFault $e) {
            return ApiResponse::error(
                'Error retrieving order products: ' . $e->getMessage()
            );
        }
    }

    /**
     * Mark an order as transferred
     * 
     * @param int $orderId Order ID to be marked as transferred
     * @return ApiResponse
     */
    public function setOrderTransferred(int $orderId): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            $response = $client->__soapCall("SetSiparisAktarildi", [
                [
                    'UyeKodu' => $this->request->key,
                    'siparisId' => $orderId
                ]
            ]);
            
            // The API returns an empty response on success
            return ApiResponse::success(
                ['orderId' => $orderId, 'transferred' => true],
                'Order marked as transferred successfully.'
            );
        } catch (SoapFault $e) {
            return ApiResponse::error('Error marking order as transferred: ' . $e->getMessage());
        }
    }

    /**
     * Cancel the transferred status of an order
     * 
     * @param int $orderId Order ID to cancel the transferred status
     * @return ApiResponse
     */
    public function cancelOrderTransferred(int $orderId): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            $response = $client->__soapCall("SetSiparisAktarildiIptal", [
                [
                    'UyeKodu' => $this->request->key,
                    'siparisId' => $orderId
                ]
            ]);
            
            // The API returns an empty response on success
            return ApiResponse::success(
                ['orderId' => $orderId, 'transferred' => false],
                'Order transfer status cancelled successfully.'
            );
        } catch (SoapFault $e) {
            return ApiResponse::error('Error cancelling order transfer status: ' . $e->getMessage());
        }
    }
}
