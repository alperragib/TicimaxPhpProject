<?php

	namespace Hasokeyk\Ticimax\Categories;

	use SoapFault;

	class TicimaxCategories{

		public $api_url = "/Servis/UrunServis.svc?singleWsdl";

		private $ticimax;

		function __construct($ticimax){
			$this->ticimax = $ticimax;
		}

		public function get_categories(){
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

		public function create_category(TicimaxCategoryModel $ticimax_category_model){
			try{
				$client           = $this->ticimax->soap_client($this->api_url);
				$ticimax_category = $ticimax_category_model->to_array();
				$response         = $client->__soapCall("SaveKategori", [
					[
						'UyeKodu'  => $this->ticimax->key,
						'kategori' => $ticimax_category
					]
				]);
				return [
					'status' => 'success',
					'data'   => $response->SaveKategoriResult ?? null
				];
			}catch(SoapFault $e){
				return [
					'status'  => 'danger',
					'message' => $e->getMessage()
				];
			}
		}

		public function del_category(TicimaxCategoryModel $ticimax_category_model): array{
			try{
				$client              = $this->ticimax->soap_client($this->api_url);
				$ticimax_category_id = $ticimax_category_model->getCategoryId();
				$response            = $client->__soapCall("DeleteKategori", [
					[
						'UyeKodu'    => $this->ticimax->key,
						'KategoriID' => $ticimax_category_id
					]
				]);
				return [
					'status' => 'success',
					'data'   => $response->DeleteKategoriResult ?? null
				];
			}catch(SoapFault $e){
				return [
					'status'  => 'danger',
					'message' => $e->getMessage()
				];
			}
		}

	}