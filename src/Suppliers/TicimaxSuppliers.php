<?php

	namespace AlperRagib\Ticimax\Suppliers;

	use SoapFault;
	use AlperRagib\Ticimax\TicimaxRequest;

	class TicimaxSuppliers{

		public $api_url = "/Servis/UrunServis.svc?singleWsdl";

		private $ticimax_request;

		function __construct(TicimaxRequest $ticimax_request){
			$this->ticimax_request = $ticimax_request;
		}

		public function get_suppliers(){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$response = $client->__soapCall("SelectTedarikci", [
					[
						'UyeKodu'     => $this->ticimax_request->key,
						'tedarikciID' => 0,
						'kategoriID'  => 0,
					]
				]);
				return [
					'status'   => true,
					'data'     => $response->SelectTedarikciResult->Tedarikci ?? null,
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}catch(SoapFault $e){
				return [
					'status'   => false,
					'message'  => $e->getMessage(),
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}
		}

		public function get_supplier($supplier_id){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$response = $client->__soapCall("SelectTedarikci", [
					[
						'UyeKodu'     => $this->ticimax_request->key,
						'tedarikciID' => $supplier_id,
						'kategoriID'  => 0,
					]
				]);
				return [
					'status'   => true,
					'data'     => $response->SelectTedarikciResult->Tedarikci ?? null,
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}catch(SoapFault $e){
				return [
					'status'   => false,
					'message'  => $e->getMessage(),
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}
		}

		public function create_supplier(TicimaxSupplierModel $ticimax_supplier_model){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$ticimax_supplier = $ticimax_supplier_model->to_array();

				if(isset($ticimax_supplier['ID']) and $ticimax_supplier['ID'] != 0){
					return [
						'status'  => false,
					];
				}

				$ticimax_supplier['ID'] = 0;
				$response               = $client->__soapCall("SaveTedarikci", [
					[
						'UyeKodu'   => $this->ticimax_request->key,
						'tedarikci' => $ticimax_supplier
					]
				]);
				return [
					'status'   => true,
					'data'     => $response->SaveTedarikciResult ?? null,
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}catch(SoapFault $e){
				return [
					'status'   => false,
					'message'  => $e->getMessage(),
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}
		}

		public function update_supplier(TicimaxSupplierModel $ticimax_supplier_model){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$ticimax_supplier = $ticimax_supplier_model->to_array();

				if(isset($ticimax_supplier['ID']) and $ticimax_supplier['ID'] == 0){
					return [
						'status'  => false,
					];
				}

				$response = $client->__soapCall("SaveTedarikci", [
					[
						'UyeKodu'   => $this->ticimax_request->key,
						'tedarikci' => $ticimax_supplier
					]
				]);
				return [
					'status'   => true,
					'data'     => $response->SaveTedarikciResult ?? null,
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}catch(SoapFault $e){
				return [
					'status'   => false,
					'message'  => $e->getMessage(),
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}
		}

		public function del_supplier($supplier_id): array{
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$response = $client->__soapCall("DeleteTedarikci", [
					[
						'UyeKodu'     => $this->ticimax_request->key,
						'TedarikciID' => $supplier_id
					]
				]);
				return [
					'status'   => true,
					'data'     => $response->DeleteTedarikciResult ?? null,
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}catch(SoapFault $e){
				return [
					'status'   => false,
					'message'  => $e->getMessage(),
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}
		}
	}