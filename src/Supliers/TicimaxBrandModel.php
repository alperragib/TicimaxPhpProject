<?php

	namespace Hasokeyk\Ticimax\Brands;

	use Hasokeyk\Ticimax\TicimaxHelpers;

	class TicimaxBrandModel{

		private $brand_status;
		private $brand_id;
		private $brand_breadcrumb;
		private $brand_name;
		private $brand_description;
		private $brand_seo_title;
		private $brand_seo_url;

		private $ticimax_helper;

		function __construct(){
			$this->ticimax_helper = new TicimaxHelpers();
		}

		private $request_params = [
			'brand_name',
		];

		public function get_brand_status(){
			return $this->brand_status;
		}

		public function set_brand_status($brand_status){
			$this->brand_status = $brand_status;
		}

		public function get_brand_id(){
			return $this->brand_id;
		}

		public function set_brand_id($brand_id){
			$this->brand_id = $brand_id;
		}

		public function get_brand_name(){
			return $this->brand_name;
		}

		public function set_brand_name($brand_name){
			$this->brand_name = $brand_name;
		}

		public function get_brand_description(){
			return $this->brand_description;
		}

		public function set_brand_description($brand_description){
			$this->brand_description = $brand_description;
		}

		public function get_brand_seo_title(){
			return $this->brand_seo_title;
		}

		public function set_brand_seo_title($brand_seo_title){
			$this->brand_seo_title = $brand_seo_title;
		}

		public function get_brand_seo_url(){
			return $this->brand_seo_url;
		}

		public function set_brand_seo_url($brand_seo_url){
			$this->brand_seo_url = $brand_seo_url;
		}

		public function get_brand_breadcrumb(){
			return $this->brand_breadcrumb;
		}

		public function set_brand_breadcrumb($brand_breadcrumb){
			$this->brand_breadcrumb = $brand_breadcrumb;
		}

		public function to_array(){

			$check = $this->ticimax_helper->check_request_params($this, $this->request_params);
			if(!$check){
				return false;
			}

			return [
				'ID'             => $this->brand_id ?? 0,
				'Aktif'          => $this->brand_status ?? true,
				'Tanim'          => $this->brand_name,
				'Breadcrumb'     => $this->brand_breadcrumb ?? $this->brand_name,
				'SeoSayfaBaslik' => $this->brand_seo_title ?? $this->ticimax_helper->string_to_seo_title($this->brand_name),
				'Url'            => $this->brand_seo_url ?? $this->ticimax_helper->string_to_seo_url($this->brand_name),
			];
		}

	}