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

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Siparişleri getir
     * @param array $filters Filtreler
     * @param array $pagination Sayfalama
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
                'TedarikciID'                => -1,
                'TeslimatGunuBas'            => null,
                'TeslimatGunuSon'            => null,
                'TeslimatMagazaID'           => null,
                'UrunGetir'                  => null,
                'UyeID'                      => -1,
                'UyeTelefon'                 => '',
            ];

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
                'Siparişler başarıyla getirildi.'
            );

        } catch (SoapFault $e) {
            return ApiResponse::error(
                'Siparişler getirilirken bir hata oluştu: ' . $e->getMessage()
            );
        }
    }

    /**
     * Create a new order via the API.
     * @param OrderModel $order
     * @return bool
     */
    public function createOrder(OrderModel $order): bool
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
            return $response->SaveSiparisResult->ID ??  0;
        } catch (SoapFault $e) {
            // Handle error or log
            return false;
        }
    }

    /**
     * Yeni sipariş oluştur
     * @param array $data Sipariş verileri
     * @return ApiResponse
     */
    public function saveOrder(): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            // Sipariş için gerekli ödeme bilgisi oluşturma
            $odeme = [
                'BankaKomisyonu' => 0.0,
                'HavaleHesapID' => null,
                'KapidaOdemeTutari' => 0.0,
                'OdemeDurumu' => 1, // Ödeme durumu (örn: 1 = Ödendi)
                'OdemeIndirimi' => 0.0,
                'OdemeNotu' => '',
                'OdemeSecenekID' => 0, // Tanımlı ödeme seçenek ID'si
                'OdemeTipi' => 1, // Ödeme tipi (örn: 1 = Kredi Kartı)
                'TaksitSayisi' => 1,
                'Tarih' => date('c'), // ISO 8601 format
                'Tutar' => 0.0
            ];

            // Sipariş ürünleri için örnek yapı
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

            // Ana sipariş yapısı
            $siparis = [
                'FaturaAdresId' => $this->data['FaturaAdresId'] ?? 0,
                'IndirimTutari' => $this->data['IndirimTutari'] ?? 0.0,
                'KargoAdresId' => $this->data['KargoAdresId'] ?? 0,
                'KargoFirmaId' => $this->data['KargoFirmaId'] ?? 0,
                'KargoTutari' => $this->data['KargoTutari'] ?? 0.0,
                'Odeme' => $odeme,
                'ParaBirimi' => $this->data['ParaBirimi'] ?? 'TL',
                'SiparisKaynagi' => $this->data['SiparisKaynagi'] ?? 'Web',
                'SiparisNotu' => $this->data['SiparisNotu'] ?? '',
                'Urunler' => $urunler,
                'UrunTutari' => $this->data['UrunTutari'] ?? 0.0,
                'UrunTutariKdv' => $this->data['UrunTutariKdv'] ?? 0.0,
                'UyeId' => $this->data['UyeId'] ?? 0,
                'TeslimatSaati' => $this->data['TeslimatSaati'] ?? '',
                'TeslimatTarihi' => $this->data['TeslimatTarihi'] ?? date('c')
            ];

            // API çağrısı yapma
            $response = $client->__soapCall("SaveSiparis", [
                [
                    'UyeKodu' => $this->request->key,
                    'siparis' => $siparis
                ]
            ]);

            // Yanıt kontrolü
            if (isset($response->SaveSiparisResult)) {
                $result = $response->SaveSiparisResult;
                if (isset($result->IsError) && $result->IsError === false) {
                    return ApiResponse::success(
                        ['orderId' => $result->ID ?? null],
                        'Sipariş başarıyla oluşturuldu.'
                    );
                }
                // Hata durumunda log tutulabilir
                error_log("Sipariş kaydetme hatası: " . ($result->ErrorMessage ?? 'Bilinmeyen hata'));
            }

        } catch (SoapFault $e) {
            // SOAP hatası durumunda log tutulabilir
            error_log("SOAP Hatası: " . $e->getMessage());
        }
        return ApiResponse::error('Beklenmeyen bir hata oluştu.');
    }

    /**
     * Sipariş ödemelerini getir
     * @param int $siparisId Siparişin ID'si
     * @param int $odemeId Ödeme ID'si (opsiyonel, varsayılan 0)
     * @param bool|null $isAktarildi Aktarılma durumu (opsiyonel)
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
                    'Ödeme bilgileri başarıyla getirildi.'
                );
            }

            return ApiResponse::success(
                [],
                'Ödeme bilgisi bulunamadı.'
            );

        } catch (SoapFault $e) {
            return ApiResponse::error(
                'Ödeme bilgileri getirilirken bir hata oluştu: ' . $e->getMessage()
            );
        }
    }

    /**
     * Sipariş ürünlerini getir
     * @param int $siparisId Siparişin ID'si
     * @param bool $iptalEdilmisUrunler İptal edilmiş ürünler getirilsin mi?
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
                
                // Tek ürün gelirse array'e çevir
                if (is_object($urunler)) {
                    $urunler = [$urunler];
                }

                $products = [];
                foreach ($urunler as $urun) {
                    $products[] = [
                        'ID' => $urun->ID ?? null,
                        'SiparisId' => $urun->SiparisId ?? null,
                        'Adet' => $urun->Adet ?? 0.0,
                        'Barkod' => $urun->Barkod ?? '',
                        'Durum' => $urun->Durum ?? null,
                        'DurumAd' => $urun->DurumAd ?? '',
                        'IslemAd' => $urun->IslemAd ?? '',
                        'IslemID' => $urun->IslemID ?? null,
                        'KampanyaID' => $urun->KampanyaID ?? null,
                        'KampanyaIndirimTutari' => $urun->KampanyaIndirimTutari ?? 0.0,
                        'KdvOrani' => $urun->KdvOrani ?? 0,
                        'KdvTutari' => $urun->KdvTutari ?? 0.0,
                        'MagazaAtamaTarihi' => $urun->MagazaAtamaTarihi ?? null,
                        'MagazaDurum' => $urun->MagazaDurum ?? null,
                        'MagazaGonderimTarihi' => $urun->MagazaGonderimTarihi ?? null,
                        'MagazaID' => $urun->MagazaID ?? null,
                        'MagazaKodu' => $urun->MagazaKodu ?? '',
                        'Maliyet' => $urun->Maliyet ?? 0.0,
                        'StokKodu' => $urun->StokKodu ?? '',
                        'TedarikciID' => $urun->TedarikciID ?? null,
                        'TedarikciKodu' => $urun->TedarikciKodu ?? '',
                        'TedarikciKodu2' => $urun->TedarikciKodu2 ?? '',
                        'Tutar' => $urun->Tutar ?? 0.0,
                        'UrunAdi' => $urun->UrunAdi ?? '',
                        'UrunID' => $urun->UrunID ?? null,
                        'UrunKartiID' => $urun->UrunKartiID ?? null
                    ];
                }

                return ApiResponse::success(
                    $products,
                    sprintf(
                        'Sipariş ürünleri başarıyla getirildi. Toplam %d ürün bulundu.',
                        count($products)
                    )
                );
            }

            return ApiResponse::success(
                [],
                'Bu siparişte ürün bulunamadı.'
            );

        } catch (SoapFault $e) {
            return ApiResponse::error(
                'Sipariş ürünleri getirilirken bir hata oluştu: ' . $e->getMessage()
            );
        }
    }

    /**
     * Mark an order as transferred
     * 
     * @param int $orderId Order ID to be marked as transferred
     * @return bool Returns true if successful
     */
    public function setOrderTransferred(int $orderId): bool
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
            return true;
        } catch (SoapFault $e) {
            // Handle error or log
            return false;
        }
    }

    /**
     * Cancel the transferred status of an order
     * 
     * @param int $orderId Order ID to cancel the transferred status
     * @return bool Returns true if successful
     */
    public function cancelOrderTransferred(int $orderId): bool
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
            return true;
        } catch (SoapFault $e) {
            // Handle error or log
            return false;
        }
    }
}
