<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Order;

use AlperRagib\Ticimax\Model\Order\OrderModel;
use AlperRagib\Ticimax\TicimaxRequest;
use SoapFault;

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
     * Fetch orders from the API.
     * @param array $filters
     * @param array $pagination
     * @return OrderModel[]
     */
    public function getOrders(array $filters = [], array $pagination = []): array
    {
        $client = $this->request->soap_client($this->apiUrl);
        $orders = [];
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
            foreach ($ordersArr as $order) {
                $orders[] = new OrderModel($order);
            }
        } catch (SoapFault $e) {
            // Handle error or log
        }
        return $orders;
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
            return $response->SaveSiparisResult->ID ?? 0;
        } catch (SoapFault $e) {
            // Handle error or log
            return false;
        }
    }
}
