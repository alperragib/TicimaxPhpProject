<?php

	namespace AlperRagib\Ticimax\Orders;

	use SoapFault;
	use AlperRagib\Ticimax\TicimaxRequest;

	class TicimaxOrders{

		public $api_url = "/Servis/SiparisServis.svc?singleWsdl";

		private $ticimax_request;

		function __construct(TicimaxRequest $ticimax_request){
			$this->ticimax_request = $ticimax_request;
		}

		public function get_orders($filters = [], $pagination = []){

			$client = $this->ticimax_request->soap_client($this->api_url);
			try{

				$defaultFilters    = [
					'EntegrasyonAktarildi' => -1,
					'SiparisDurumu'        => -1,
					'OdemeTipi'            => -1,
					'OdemeDurumu'          => -1,
					'SiparisKaynagi'       => '',
					'SiparisID'            => -1,
					'SiparisNo'            => '',
					'FaturaNo'             => '',
					'UyeID'                => -1,
					'UyeTelefon'           => '',
					'TedarikciID'          => -1,
					'IptalEdilmisUrunler'  => true,
					'WebSipariFiltre'      => [

					]
				];
				$defaultPagination = [
					'BaslangicIndex' => 0,
					'KayitSayisi'    => 100,
					'SiralamaDegeri' => 'ID',
					'SiralamaYonu'   => 'DESC',
				];

				$urun_filteleme = array_merge($defaultFilters, $filters);
				$urun_sayfalama = array_merge($defaultPagination, $pagination);

				$response = $client->__soapCall("SelectSiparis", [
					[
						'UyeKodu' => $this->ticimax_request->key,
						'f'       => (object)$urun_filteleme,
						's'       => (object)$urun_sayfalama,
					]
				]);

				return (object)[
					'status'   => isset($response->SelectSiparisResult->WebSiparis) ? true : false,
					'data'     => isset($response->SelectSiparisResult->WebSiparis->ID) ? [$response->SelectSiparisResult->WebSiparis->ID] : ($response->SelectSiparisResult->WebSiparis ?? null), //EĞER TEK VERİ VARSA DİREKT ERİŞİM VERİYOR DÖNGÜYE ALINCA HATA VERMEMESİ İÇİN TEK VERİ VARSA DİZİ İÇİNE OTOMATİK ALIYORUZ
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];

			}catch(SoapFault $e){
				return (object)[
					'status'   => false,
					'message'  => $e->getMessage(),
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}

		}

	}