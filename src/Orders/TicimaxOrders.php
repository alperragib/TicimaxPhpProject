<?php

	namespace Hasokeyk\Ticimax\Orders;

	use SoapFault;
	use Hasokeyk\Ticimax\TicimaxRequest;

	class TicimaxOrders{

		public $api_url = "/Servis/SiparisServis.svc?singleWsdl";

		private $ticimax_request;

		function __construct(TicimaxRequest $ticimax_request){
			$this->ticimax_request = $ticimax_request;
		}

		public function get_orders($filters = [], $pagination = []){

			$client = $this->ticimax_request->soap_client($this->api_url);
			try{

				// Varsayılan filtre ve sayfalama ayarları
				$defaultFilters    = [
					'EntegrasyonAktarildi' => -1,  // All
					'SiparisDurumu'        => -1,          // -1 Tümü
					'OdemeTipi'            => -1,             // All
					'OdemeDurumu'          => -1,           // All
					'SiparisKaynagi'       => '',
					'SiparisID'            => -1, //
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

				// Kullanıcıdan gelen parametrelerle birleştir
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
					'status'   => isset($response->SelectSiparisResult->WebSiparis) ? 'success' : 'danger',
					'data'     => isset($response->SelectSiparisResult->WebSiparis->ID) ? [$response->SelectSiparisResult->WebSiparis->ID] : ($response->SelectSiparisResult->WebSiparis ?? null), //EĞER TEK VERİ VARSA DİREKT ERİŞİM VERİYOR DÖNGÜYE ALINCA HATA VERMEMESİ İÇİN TEK VERİ VARSA DİZİ İÇİNE OTOMATİK ALIYORUZ
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];

			}catch(SoapFault $e){
				return (object)[
					'status'   => 'danger',
					'message'  => $e->getMessage(),
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}

		}

	}