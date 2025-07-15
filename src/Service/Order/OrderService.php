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
                'TedarikciID'                => -1,  // -1 = tüm tedarikçiler
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
            // Creating required payment information for order
            $odeme = [
                'BankaKomisyonu' => 0.0,
                'HavaleHesapID' => null,
                'KapidaOdemeTutari' => 0.0,
                'OdemeDurumu' => 1, // Payment status (e.g.: 1 = Paid)
                'OdemeIndirimi' => 0.0,
                'OdemeNotu' => '',
                'OdemeSecenekID' => 0, // Defined payment option ID
                'OdemeTipi' => 1, // Payment type (e.g.: 1 = Credit Card)
                'TaksitSayisi' => 1,
                'Tarih' => date('c'), // ISO 8601 format
                'Tutar' => 0.0
            ];

            // Sample structure for order products
            $urunler = [];
            foreach ($this->data['Urunler'] ?? [] as $urun) {
                $urunler[] = [
                    'Adet' => $urun['Adet'] ?? 1,
                    'KdvOrani' => $urun['KdvOrani'] ?? 18,
                    'KdvTutari' => $urun['KdvTutari'] ?? 0.0,
                    'Maliyet' => $urun['Maliyet'] ?? 0.0,
                    'Tutar' => $urun['Tutar'] ?? 0.0,
                    'UrunID' => $urun['UrunID'] ?? 0
                ];
            }

            // Main order structure - WSDL WebSiparisSaveRequest yapısına uygun (CORRECTED FIELD NAMES)
            $siparis = [
                // ZORUNLU - Kullanıcı Bilgileri (WSDL: UyeID, UyeAdi, Mail)
                'UyeID' => $this->data['UyeID'] ?? $this->data['UyeId'] ?? 0,  // CORRECTED: UyeID (capital D)
                'UyeAdi' => $this->data['UyeAdi'] ?? 'Test User',
                'UyeSoyadi' => $this->data['UyeSoyadi'] ?? 'Test',  // ADDED: UyeSoyadi required
                'Mail' => $this->data['Mail'] ?? $this->data['UyeMail'] ?? 'test@example.com',  // CORRECTED: Mail not UyeMail
                
                // ZORUNLU - Adres Bilgileri (WSDL: TeslimatAdresi, FaturaAdresi objects)
                'TeslimatAdresi' => [  // CORRECTED: TeslimatAdresi (with 'i')
                    'Adres' => $this->data['TeslimatAdresi']['Adres'] ?? $this->data['TeslimatAdres']['Adres'] ?? 'Test Teslimat Adresi',
                    'Il' => $this->data['TeslimatAdresi']['Il'] ?? $this->data['TeslimatAdres']['Il'] ?? 'İstanbul',
                    'Ilce' => $this->data['TeslimatAdresi']['Ilce'] ?? $this->data['TeslimatAdres']['Ilce'] ?? 'Beyoğlu',
                    'PostaKodu' => $this->data['TeslimatAdresi']['PostaKodu'] ?? $this->data['TeslimatAdres']['PostaKodu'] ?? '34000',
                    'AliciAdi' => $this->data['TeslimatAdresi']['AliciAdi'] ?? $this->data['TeslimatAdres']['AliciAdi'] ?? 'Test User',
                    'AliciTelefon' => $this->data['TeslimatAdresi']['AliciTelefon'] ?? $this->data['TeslimatAdres']['AliciTelefon'] ?? '0555 123 45 67',
                    'Ulke' => $this->data['TeslimatAdresi']['Ulke'] ?? $this->data['TeslimatAdres']['Ulke'] ?? 'Türkiye'
                ],
                'FaturaAdresi' => [  // CORRECTED: FaturaAdresi (with 'i')
                    'Adres' => $this->data['FaturaAdresi']['Adres'] ?? $this->data['FaturaAdres']['Adres'] ?? 'Test Fatura Adresi',
                    'Il' => $this->data['FaturaAdresi']['Il'] ?? $this->data['FaturaAdres']['Il'] ?? 'İstanbul',
                    'Ilce' => $this->data['FaturaAdresi']['Ilce'] ?? $this->data['FaturaAdres']['Ilce'] ?? 'Beyoğlu',
                    'PostaKodu' => $this->data['FaturaAdresi']['PostaKodu'] ?? $this->data['FaturaAdres']['PostaKodu'] ?? '34000',
                    'AdSoyad' => $this->data['FaturaAdresi']['AdSoyad'] ?? $this->data['FaturaAdres']['AdSoyad'] ?? 'Test User',
                    'FirmaAdi' => $this->data['FaturaAdresi']['FirmaAdi'] ?? $this->data['FaturaAdres']['FirmaAdi'] ?? '',
                    'VergiDairesi' => $this->data['FaturaAdresi']['VergiDairesi'] ?? $this->data['FaturaAdres']['VergiDairesi'] ?? '',
                    'VergiNo' => $this->data['FaturaAdresi']['VergiNo'] ?? $this->data['FaturaAdres']['VergiNo'] ?? '',
                    'isKurumsal' => $this->data['FaturaAdresi']['isKurumsal'] ?? $this->data['FaturaAdres']['isKurumsal'] ?? false,
                    'Ulke' => $this->data['FaturaAdresi']['Ulke'] ?? $this->data['FaturaAdres']['Ulke'] ?? 'Türkiye'
                ],
                
                // Finansal Bilgiler
                'IndirimTutari' => $this->data['IndirimTutari'] ?? 0.0,
                'KargoTutari' => $this->data['KargoTutari'] ?? 0.0,
                'ToplamTutar' => $this->data['ToplamTutar'] ?? 0.0,  // ADDED: ToplamTutar required
                'ToplamKdv' => $this->data['ToplamKdv'] ?? $this->data['UrunTutariKdv'] ?? 0.0,  // CORRECTED: ToplamKdv
                
                // Kargo ve Para Birimi (WSDL FIELD NAMES)
                'KargoFirmaID' => $this->data['KargoFirmaID'] ?? $this->data['KargoFirmaId'] ?? 2,  // CORRECTED: KargoFirmaID (capital ID)
                'ParaBirimi' => $this->data['ParaBirimi'] ?? 'TRY',
                
                // Sipariş Detayları
                'SiparisKaynagi' => $this->data['SiparisKaynagi'] ?? 'Web',
                'SiparisNotu' => $this->data['SiparisNotu'] ?? '',
                'TeslimatSaati' => $this->data['TeslimatSaati'] ?? '',
                'TeslimatTarihi' => $this->data['TeslimatTarihi'] ?? date('c'),  // FIXED: TeslimatTarihi not TeslimatGunu
                
                // Ürünler ve Ödeme
                'Urunler' => $urunler,
                'Odemeler' => [$odeme] // Array olarak gönder
            ];

            // Making API call - WSDL'ye göre siparis object olmalı
            $response = $client->__soapCall("SaveSiparis", [
                [
                    'UyeKodu' => $this->request->key,
                    'siparis' => (object)$siparis  // Object olarak gönder - WSDL gereksinimi
                ]
            ]);

            // Response validation - WSDL WebSiparisSaveResponse yapısına göre
            if (isset($response->SaveSiparisResult)) {
                $result = $response->SaveSiparisResult;
                
                // WebSiparisSaveResponse extends WebServisResponse
                // IsError field'ı WebServisResponse'dan geliyor
                if (isset($result->IsError) && $result->IsError === false) {
                    // Başarılı - SiparisDetayi WebSiparis object'i içerir
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
                
                // Hata durumu - Messages array'i kontrol et
                $errorMessages = [];
                if (isset($result->Messages)) {
                    $messages = is_array($result->Messages) ? $result->Messages : [$result->Messages];
                    foreach ($messages as $message) {
                        if (isset($message->ErrorMessage)) {
                            $errorMessages[] = $message->ErrorMessage;
                        }
                    }
                }
                
                $fullErrorMessage = !empty($errorMessages) 
                    ? implode('. ', $errorMessages)
                    : ($result->ErrorMessage ?? 'Unknown error');
                    
                error_log("Order save error: " . $fullErrorMessage);
                return ApiResponse::error('Order save failed: ' . $fullErrorMessage);
            }

        } catch (SoapFault $e) {
            // SOAP error can be logged if needed
            error_log("SOAP Error: " . $e->getMessage());
        }
        return ApiResponse::error('An unexpected error occurred.');
    }

    /**
     * Get order payments
     * @param int $siparisId Order ID
     * @param int $odemeId Payment ID (optional, default 0)
     * @param bool|null $isAktarildi Transfer status (optional)
     * @return ApiResponse
     */
    public function getOrderPayments(int $siparisId, int $odemeId = 0, ?bool $isAktarildi = null): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            $response = $client->__soapCall("SelectSiparisOdeme", [
                [
                    'UyeKodu' => $this->request->key,
                    'siparisId' => $siparisId,
                    'odemeId' => $odemeId,
                    'isAktarildi' => $isAktarildi
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
     * @param int $siparisId Order ID
     * @param bool $iptalEdilmisUrunler Should cancelled products be included?
     * @return ApiResponse
     */
    public function getOrderProducts(int $siparisId, bool $iptalEdilmisUrunler = false): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        
        try {
            $response = $client->__soapCall("SelectSiparisUrun", [
                [
                    'UyeKodu' => $this->request->key,
                    'siparisId' => $siparisId,
                    'iptalEdilmisUrunler' => $iptalEdilmisUrunler
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
