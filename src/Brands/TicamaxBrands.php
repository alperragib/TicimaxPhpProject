<?php

	namespace Hasokeyk\Ticimax\Brands;

	use SoapFault;

	class TicimaxBrands{

		public $api_url = "/Servis/UrunServis.svc?singleWsdl";

		private $ticimax;

		function __construct($ticimax){
			$this->ticimax = $ticimax;
		}

		public function get_brands(){
			try{
				$client   = $this->ticimax->soap_client($this->api_url);
				$response = $client->__soapCall("SelectKategori", [
					[
						'UyeKodu'    => $this->ticimax->key,
						'kategoriID' => 0
					]
				]);
				return [
					'status' => 'success',
					'data'   => $response->SelectKategoriResult ?? null
				];
			}catch(SoapFault $e){
				return [
					'status'  => 'danger',
					'message' => $e->getMessage()
				];
			}
		}
	}