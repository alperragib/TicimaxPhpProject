<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Product;

use AlperRagib\Ticimax\Model\Product\ProductModel;
use AlperRagib\Ticimax\Model\Product\ProductVariationModel;
use AlperRagib\Ticimax\Model\Category\CategoryModel;
use AlperRagib\Ticimax\Model\Response\ApiResponse;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

/**
 * Class ProductService
 * Handles product-related API operations.
 */
class ProductService
{
    private TicimaxRequest $request;
    private string $apiUrl = "/Servis/UrunServis.svc?singleWsdl";

    public function __construct(TicimaxRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get payment options for a product variation
     * @param int $varyasyonId Variation ID to get payment options for
     * @return ApiResponse
     */
    public function SelectUrunOdemeSecenek(int $varyasyonId): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $response = $client->__soapCall("SelectUrunOdemeSecenek", [
                [
                    'UyeKodu' => $this->request->key,
                    'varyasyonId' => $varyasyonId
                ]
            ]);
            
            $odemeSecenekleri = $response->SelectUrunOdemeSecenekResult ?? [];
            if (is_object($odemeSecenekleri)) {
                $odemeSecenekleri = [$odemeSecenekleri];
            }

            $result = [];
            foreach ($odemeSecenekleri as $secenek) {
                $taksitler = [];
                
                // Taksit bilgilerini düzenle
                if (isset($secenek->Taksitler->UrunOdemeSecenekTaksit)) {
                    $taksitData = $secenek->Taksitler->UrunOdemeSecenekTaksit;
                    if (is_object($taksitData)) {
                        $taksitData = [$taksitData];
                    }
                    
                    foreach ($taksitData as $taksit) {
                        $taksitler[] = [
                            'taksitSayisi' => $taksit->TaksitSayisi ?? 0,
                            'taksitSayisiTanim' => $taksit->TaksitSayisiTanim ?? '',
                            'taksitTutari' => $taksit->TaksitTutari ?? 0.0,
                            'taksitTutariStr' => $taksit->TaksitTutariStr ?? '',
                            'toplamTutar' => $taksit->ToplamTutar ?? 0.0,
                            'toplamTutarStr' => $taksit->ToplamTutarStr ?? ''
                        ];
                    }
                }

                $result[] = [
                    'bankaAdi' => $secenek->BankaAdi ?? '',
                    'bankaId' => $secenek->BankaID ?? 0,
                    'taksitler' => $taksitler
                ];
            }

            return ApiResponse::success($result, 'Ödeme seçenekleri başarıyla getirildi.');
        } catch (SoapFault $e) {
            return ApiResponse::error('Ödeme seçenekleri getirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Fetch products from the API.
     * @param array $filters
     * @param array $pagination
     * @return ApiResponse
     */
    public function getProducts(array $filters = [], array $pagination = []): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        $products = [];
        try {
            $defaultFilters = [
                'Aktif'       => -1,  // int: -1=all, 0=inactive, 1=active
                'Firsat'      => -1,  // int: -1=all, 0=false, 1=true  
                'Indirimli'   => -1,  // int: -1=all, 0=false, 1=true
                'Vitrin'      => -1,  // int: -1=all, 0=false, 1=true
                'KategoriID'  => 0,   // int: 0=no filter
                'MarkaID'     => 0,   // int: 0=no filter
                'UrunKartiID' => 0,   // int: 0=no filter
            ];
            
            $defaultPagination = [
                'BaslangicIndex'            => 0,
                'KayitSayisi'               => 20,
                'KayitSayisinaGoreGetir'    => true,
                'SiralamaDegeri'            => 'ID',
                'SiralamaYonu'              => 'DESC',
            ];

            $urunFiltre = array_merge($defaultFilters, $filters);
            $urunSayfalama = array_merge($defaultPagination, $pagination);
            $response = $client->__soapCall("SelectUrun", [
                [
                    'UyeKodu' => $this->request->key,
                    'f'       => (object)$urunFiltre,
                    's'       => (object)$urunSayfalama,
                ]
            ]);
            $urunler = $response->SelectUrunResult->UrunKarti ?? [];
            if (is_object($urunler)) {
                $urunler = [$urunler];
            }
            foreach ($urunler as $urun) {
                $products[] = new ProductModel($urun);
            }
            
            return ApiResponse::success($products, 'Ürünler başarıyla getirildi.');
            
        } catch (SoapFault $e) {
            return ApiResponse::error('Ürünler getirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Get category information using SelectKategori SOAP method
     * @param int $kategoriID Category ID (0 returns all categories)
     * @param string $dil Language code (optional)
     * @param int|null $parentID Parent category ID (optional)
     * @return ApiResponse
     */
    public function SelectKategori(int $kategoriID = 0, string $dil = '', ?int $parentID = null): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        $categories = [];
        
        try {
            $response = $client->__soapCall("SelectKategori", [
                [
                    'UyeKodu'    => $this->request->key,
                    'kategoriID' => $kategoriID,
                    'dil'        => $dil,
                    'parentID'   => $parentID
                ]
            ]);
            
            $kategoriler = $response->SelectKategoriResult->Kategori ?? [];
            if (is_object($kategoriler)) {
                $kategoriler = [$kategoriler];
            }

            foreach ($kategoriler as $kategori) {
                $categories[] = new CategoryModel($kategori);
            }
            
            return ApiResponse::success($categories, 'Kategoriler başarıyla getirildi.');
            
        } catch (SoapFault $e) {
            return ApiResponse::error('Kategoriler getirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Get total count of products based on filters
     * @param array $filters Product filters
     * @return int Total number of products
     */
    public function SelectUrunCount(array $filters = []): int
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $defaultFilters = [
                'Aktif'                    => -1,   // -1: no filter, 0: false, 1: true
                'Firsat'                   => -1,   // -1: no filter, 0: false, 1: true
                'Indirimli'                => -1,   // -1: no filter, 0: false, 1: true
                'Vitrin'                   => -1,   // -1: no filter, 0: false, 1: true
                'KategoriID'               => 0,    // 0: no filter
                'MarkaID'                  => 0,    // 0: no filter
                'UrunKartiID'              => 0,    // 0: no filter
                'ToplamStokAdediBas'       => null, // Starting stock amount (double)
                'ToplamStokAdediSon'       => null, // Ending stock amount (double)
                'TedarikciID'              => 0,    // 0: no filter
            ];

            $urunFiltre = array_merge($defaultFilters, $filters);
            
            $response = $client->__soapCall("SelectUrunCount", [
                [
                    'UyeKodu' => $this->request->key,
                    'f'       => (object)$urunFiltre
                ]
            ]);
            
            return (int)($response->SelectUrunCountResult ?? 0);
        } catch (SoapFault $e) {
            // Handle error or log
            return 0;
        }
    }

    /**
     * Get product reviews
     * @param int $urunKartId Product ID to get reviews for
     * @return ApiResponse
     */
    public function GetProductReviews(int $urunKartId): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $response = $client->__soapCall("SelectUrunYorum", [
                [
                    'UyeKodu' => $this->request->key,
                    'urunKartId' => $urunKartId
                ]
            ]);
            
            $yorumlar = $response->SelectUrunYorumResult ?? [];
            if (is_object($yorumlar)) {
                $yorumlar = [$yorumlar];
            }

            $result = [];
            foreach ($yorumlar as $yorum) {
                $result[] = [
                    'id' => $yorum->ID ?? 0,
                    'urunKartiId' => $yorum->UrunKartiId ?? 0,
                    'uyeId' => $yorum->UyeID ?? 0,
                    'isim' => $yorum->Isim ?? '',
                    'mail' => $yorum->Mail ?? '',
                    'mesaj' => $yorum->Mesaj ?? '',
                    'urunAdi' => $yorum->UrunAdi ?? '',
                    'eklemeTarihi' => $yorum->EklemeTarihi ?? null
                ];
            }

            return ApiResponse::success($result, 'Ürün yorumları başarıyla getirildi.');
        } catch (SoapFault $e) {
            return ApiResponse::error('Ürün yorumları getirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Get product variations with filters and pagination
     * @param array $filters Variation filters
     * @param array $pagination Pagination settings
     * @return ProductVariationModel[] Array of variation models
     */
    public function GetProductVariations(array $filters = [], array $pagination = []): array
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $defaultFilters = [
                'Aktif'                    => -1,   // -1: all, 0: inactive, 1: active
                'Barkod'                   => '',   // barcode filter
                'StokKodu'                 => '',   // stock code filter
                'UrunID'                   => -1,   // product ID filter
                'UrunKartiID'              => -1,   // product card ID filter
                'StokGuncellemeTarihiBas'  => null, // stock update start date
                'StokGuncellemeTarihiSon'  => null  // stock update end date
            ];

            $defaultPagination = [
                'BaslangicIndex'         => 0,     // start index
                'KayitSayisi'            => 100,   // records per page
                'SiralamaDegeri'         => 'ID',  // sort field
                'SiralamaYonu'           => 'DESC' // sort direction
            ];

            $varyasyonFiltre = array_merge($defaultFilters, $filters);
            $urunSayfalama = array_merge($defaultPagination, $pagination);

            $response = $client->__soapCall("SelectVaryasyon", [
                [
                    'UyeKodu' => $this->request->key,
                    'f'       => (object)$varyasyonFiltre,
                    's'       => (object)$urunSayfalama
                ]
            ]);
            
            $variations = $response->SelectVaryasyonResult->Varyasyon ?? [];
            if (is_object($variations)) {
                $variations = [$variations];
            }

            // Convert each variation to ProductVariationModel
            return array_map(function($variation) {
                return new ProductVariationModel($variation);
            }, $variations);

        } catch (SoapFault $e) {
            // Handle error or log
            return [];
        }
    }

    /**
     * Get installment options for a given amount
     * @param float $amount Amount to calculate installments for
     * @param int $maxInstallments Maximum number of installments
     * @param string $currencyCode Currency code (e.g. 'TRY', 'USD')
     * @return array List of bank installment options
     */
    public function GetInstallmentOptions(float $amount, int $maxInstallments, string $currencyCode = 'TRY'): array
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $response = $client->__soapCall("GetTaksitSecenekleri", [
                [
                    'UyeKodu' => $this->request->key,
                    'Tutar' => $amount,
                    'MaximumTaksitSayisi' => $maxInstallments,
                    'DovizKodu' => $currencyCode
                ]
            ]);
            
            $bankalar = $response->GetTaksitSecenekleriResult->UrunOdemeSecenekBanka ?? [];
            if (is_object($bankalar)) {
                $bankalar = [$bankalar];
            }

            return $bankalar;
        } catch (SoapFault $e) {
            // Handle error or log
            return [];
        }
    }

    /**
     * Get stock information for a specific store
     * @param string $storeCode Store code to get stock information for
     * @return ApiResponse
     */
    public function GetStoreStock(string $storeCode): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $response = $client->__soapCall("SelectMagazaStok", [
                [
                    'UyeKodu' => $this->request->key,
                    'request' => [
                        'MagazaKodu' => $storeCode
                    ]
                ]
            ]);
            
            $result = $response->SelectMagazaStokResult ?? null;
            
            if ($result && !$result->IsError) {
                $stocks = $result->MagazaStokList->WebMagazaStok ?? [];
                if (is_object($stocks)) {
                    $stocks = [$stocks];
                }
                return ApiResponse::success($stocks, 'Mağaza stok bilgileri başarıyla getirildi.');
            }
            
            return ApiResponse::error($result->ErrorMessage ?? 'Bilinmeyen bir hata oluştu');
            
        } catch (SoapFault $e) {
            return ApiResponse::error('Mağaza stok bilgileri getirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Update stock quantities for product variations
     * @param array $variations Array of variations with ID and stock quantity
     *                         Each variation should have ['ID' => int, 'StokAdedi' => int]
     * @return ApiResponse
     */
    public function UpdateStockQuantity(array $variations): ApiResponse
    {
        $client = $this->request->soap_client($this->apiUrl);
        try {
            $response = $client->__soapCall("StokAdediGuncelle", [
                [
                    'UyeKodu' => $this->request->key,
                    'urunler' => $variations
                ]
            ]);
            
            $result = $response->StokAdediGuncelleResult ?? -1;
            
            if ($result >= 0) {
                return ApiResponse::success([
                        'updatedCount' => $result
                ], 'Stok miktarları başarıyla güncellendi.');
            }
            
            return ApiResponse::error('Stok güncellemesi başarısız oldu');
            
        } catch (SoapFault $e) {
            return ApiResponse::error('Stok güncellemesi sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }
    
}
