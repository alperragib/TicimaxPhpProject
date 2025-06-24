<?php

	namespace AlperRagib\Ticimax\Brands;

	use SoapFault;
	use AlperRagib\Ticimax\TicimaxRequest;

	class TicimaxBrands{

		public $api_url = "/Servis/UrunServis.svc?singleWsdl";

		private $ticimax_request;

		function __construct(TicimaxRequest $ticimax_request){
			$this->ticimax_request = $ticimax_request;
		}

		public function get_brands(){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$response = $client->__soapCall("SelectMarka", [
					[
						'UyeKodu'    => $this->ticimax_request->key,
						'kategoriID' => 0
					]
				]);
				return (object)[
					'status'   => true,
					'data'     => isset($response->SelectMarkaResult->Marka->ID) ? [$response->SelectMarkaResult->Marka] : ($response->SelectMarkaResult->Marka ?? null), //EĞER TEK VERİ VARSA DİREKT ERİŞİM VERİYOR DÖNGÜYE ALINCA HATA VERMEMESİ İÇİN TEK VERİ VARSA DİZİ İÇİNE OTOMATİK ALIYORUZ
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

		public function get_brand($brand_id){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$response = $client->__soapCall("SelectMarka", [
					[
						'UyeKodu' => $this->ticimax_request->key,
						'markaID' => $brand_id
					]
				]);
				return (object)[
					'status'   => true,
					'data'     => $response->SelectMarkaResult->Marka ?? null,
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

		public function create_brand(TicimaxBrandModel $ticimax_brand_model){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$ticimax_brand = $ticimax_brand_model->to_array();

				if(isset($ticimax_brand['ID']) and $ticimax_brand['ID'] != 0){
					return [
						'status'  => false,
					];
				}

				$ticimax_brand['ID'] = 0;
				$response            = $client->__soapCall("SaveMarka", [
					[
						'UyeKodu' => $this->ticimax_request->key,
						'marka'   => $ticimax_brand
					]
				]);
				return (object)[
					'status'   => true,
					'data'     => $response->SaveMarkaResult ?? null,
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

		public function update_brand(TicimaxBrandModel $ticimax_brand_model){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$ticimax_brand = $ticimax_brand_model->to_array();

				if(isset($ticimax_brand['ID']) and $ticimax_brand['ID'] == 0){
					return [
						'status'  => false,
					];
				}

				$response = $client->__soapCall("SaveMarka", [
					[
						'UyeKodu' => $this->ticimax_request->key,
						'marka'   => $ticimax_brand
					]
				]);
				return (object)[
					'status'   => true,
					'data'     => $response->SaveMarkaResult ?? null,
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

		public function del_brand($brand_id){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$response = $client->__soapCall("DeleteMarka", [
					[
						'UyeKodu' => $this->ticimax_request->key,
						'MarkaID' => $brand_id
					]
				]);
				return (object)[
					'status'   => true,
					'data'     => $response->DeleteMarkaResult ?? null,
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