<?php

	namespace Hasokeyk\Ticimax\Categories;

	class TicimaxCategories{

		public $api_url = "/Servis/UrunServis.svc?singleWsdl";

		private $ticimax;

		function __construct($ticimax){
			$this->ticimax = $ticimax;
		}

		public function get_categories(){
			$client   = $this->ticimax->soap_client($this->api_url);
			$response = $client->__soapCall("SelectKategori", [
				[
					'UyeKodu'    => $this->ticimax->key,
					'kategoriID' => 0
				]
			]);
			return $response->SelectKategoriResult ?? null;
		}

		public function create_categories(TicimaxCategoryModel $ticimax_category_model){
			$client = $this->ticimax->soap_client($this->api_url);
			$ticimax_category = $ticimax_category_model->to_array();
			$response = $client->__soapCall("SaveKategori", [
				[
					'UyeKodu'  => $this->ticimax->key,
					'kategori' => $ticimax_category
				]
			]);
			return $response->SaveKategoriResult ?? null;
		}

	}