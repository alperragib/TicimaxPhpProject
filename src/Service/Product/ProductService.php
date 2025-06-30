<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Product;

use AlperRagib\Ticimax\Model\Product\ProductModel;
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
     * Fetch products from the API.
     * @param array $filters
     * @param array $pagination
     * @return ProductModel[]
     */
    public function getProducts(array $filters = [], array $pagination = []): array
    {
        $client = $this->request->soap_client($this->apiUrl);
        $products = [];
        try {
            $defaultFilters = [
                'Aktif'       => -1,
                'Firsat'      => -1,
                'Indirimli'   => -1,
                'Vitrin'      => -1,
                'KategoriID'  => 0,
                'MarkaID'     => 0,
                'UrunKartiID' => 0,
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
            foreach (
                $urunler as $urun
            ) {
                $products[] = new ProductModel($urun);
            }
        } catch (SoapFault $e) {
            // Handle error or log
        }
        return $products;
    }
}
