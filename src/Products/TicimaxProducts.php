<?php

	namespace Hasokeyk\Ticimax\Products;

	use SoapFault;

	class TicimaxProducts{

		public $api_url = "/Servis/UrunServis.svc?singleWsdl";

		private $ticimax;

		function __construct($ticimax){
			$this->ticimax = $ticimax;
		}

		public function get_products($filters = [], $pagination = []){
			try{

				// Varsayılan filtre ve sayfalama ayarları
				$defaultFilters    = [
					'Aktif'       => -1,
					'Firsat'      => -1,
					'Indirimli'   => -1,
					'Vitrin'      => -1,
					'KategoriID'  => 0,
					'MarkaID'     => 0,
					'UrunKartiID' => 0,
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

				$client   = $this->ticimax->soap_client($this->api_url);
				$response = $client->__soapCall("SelectUrun", [
					[
						'UyeKodu' => $this->ticimax->key,
						'f'       => (object)$urun_filteleme,
						's'       => (object)$urun_sayfalama,
					]
				]);
				return [
					'status' => 'success',
					'data'   => $response->SelectUrunResult ?? null
				];
			}catch(SoapFault $e){
				return [
					'status'  => 'danger',
					'message' => $e->getMessage()
				];
			}
		}


		public function create_products($filters = [], $pagination = []){
			try{

				// Varsayılan filtre ve sayfalama ayarları
				$defaultFilters    = [
					'Aktif'       => -1,
					'Firsat'      => -1,
					'Indirimli'   => -1,
					'Vitrin'      => -1,
					'KategoriID'  => 0,
					'MarkaID'     => 0,
					'UrunKartiID' => 0,
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

				$client   = $this->ticimax->soap_client($this->api_url);
				$response = $client->__soapCall("SelectUrun", [
					[
						'UyeKodu' => $this->ticimax->key,
						'f'       => (object)$urun_filteleme,
						's'       => (object)$urun_sayfalama,
					]
				]);
				return [
					'status' => 'success',
					'data'   => $response->SelectUrunResult ?? null
				];
			}catch(SoapFault $e){
				return [
					'status'  => 'danger',
					'message' => $e->getMessage()
				];
			}
		}

	}