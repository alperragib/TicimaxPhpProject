<?php

	namespace Hasokeyk\Ticimax\Categories;

	use SoapFault;
	use Hasokeyk\Ticimax\TicimaxRequest;

	class TicimaxCategories{

		public $api_url = "/Servis/UrunServis.svc?singleWsdl";

		private $ticimax_request;

		function __construct(TicimaxRequest $ticimax_request){
			$this->ticimax_request = $ticimax_request;
		}

		public function get_categories(){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$response = $client->__soapCall("SelectKategori", [
					[
						'UyeKodu'    => $this->ticimax_request->key,
						'kategoriID' => 0
					]
				]);
				return (object)[
					'status'   => 'success',
					'data'     => isset($response->SelectKategoriResult->Kategori->ID) ? [$response->SelectKategoriResult->Kategori] : ($response->SelectKategoriResult->Kategori ?? null), //EĞER TEK VERİ VARSA DİREKT ERİŞİM VERİYOR DÖNGÜYE ALINCA HATA VERMEMESİ İÇİN TEK VERİ VARSA DİZİ İÇİNE OTOMATİK ALIYORUZ
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

		public function get_category($category_id){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$response = $client->__soapCall("SelectKategori", [
					[
						'UyeKodu'    => $this->ticimax_request->key,
						'kategoriID' => $category_id
					]
				]);
				return (object)[
					'status'   => 'success',
					'data'     => $response->SelectKategoriResult->Kategori ?? null,
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

		public function create_category(TicimaxCategoryModel $ticimax_category_model){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$ticimax_category = $ticimax_category_model->to_array();

				if(isset($ticimax_category['ID']) and $ticimax_category['ID'] != 0){
					return (object)[
						'status'  => 'danger',
						'message' => 'Yeni kategori oluşturmak için kategori ID 0 girilmeli'
					];
				}

				$ticimax_category['ID'] = 0;

				$response = $client->__soapCall("SaveKategori", [
					[
						'UyeKodu'  => $this->ticimax_request->key,
						'kategori' => $ticimax_category
					]
				]);
				return (object)[
					'status'   => 'success',
					'data'     => $response->SaveKategoriResult ?? null,
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

		public function update_category(TicimaxCategoryModel $ticimax_category_model){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$ticimax_category = $ticimax_category_model->to_array();
				if(isset($ticimax_category['ID']) and $ticimax_category['ID'] == 0){
					return (object)[
						'status'  => 'danger',
						'message' => 'Kategori güncellerken kategori ID 0 girilemez '
					];
				}

				$response = $client->__soapCall("SaveKategori", [
					[
						'UyeKodu'  => $this->ticimax_request->key,
						'kategori' => $ticimax_category
					]
				]);
				return (object)[
					'status'   => 'success',
					'data'     => $response->SaveKategoriResult ?? null,
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

		public function del_category($category_id): array{
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{
				$response = $client->__soapCall("DeleteKategori", [
					[
						'UyeKodu'    => $this->ticimax_request->key,
						'KategoriID' => $category_id
					]
				]);
				return (object)[
					'status'   => 'success',
					'data'     => $response->DeleteKategoriResult ?? null,
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