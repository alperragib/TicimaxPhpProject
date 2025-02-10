<?php

	namespace Hasokeyk\Ticimax\Categories;

	use Hasokeyk\Ticimax\TicimaxHelpers;

	class TicimaxCategoryModel{

		public $category_id;
		public $category_name;
		public $category_description;
		public $category_parent_id;
		public $category_status;
		public $category_code;
		public $category_seo_permalink;
		public $category_seo_keyword;
		public $category_seo_description;
		public $category_seo_title;
		public $category_sort;
		public $category_show_menu;

		private $ticimax_helper;

		function __construct(){
			$this->ticimax_helper = new TicimaxHelpers();
		}

		private $request_params = [
			'category_name',
		];

		public function get_category_id(){
			return $this->category_id;
		}

		public function set_category_id($category_id){
			$this->category_id = $category_id;
		}

		public function get_category_name(){
			return $this->category_name;
		}

		public function set_category_name($category_name){
			$this->category_name = $category_name;
		}

		public function get_category_description(){
			return $this->category_description;
		}

		public function set_category_description($category_description){
			$this->category_description = $category_description;
		}

		public function get_category_parent_id(){
			return $this->category_parent_id;
		}

		public function set_category_parent_id($category_parent_id){
			$this->category_parent_id = $category_parent_id;
		}

		public function get_category_status(){
			return $this->category_status;
		}

		public function set_category_status(bool $category_status){
			$this->category_status = $category_status;
		}

		public function get_category_code(){
			return $this->category_code;
		}

		public function set_category_code($category_code){
			$this->category_code = $category_code;
		}

		public function get_category_seo_permalink(){
			return $this->category_seo_permalink;
		}

		public function set_category_seo_permalink($category_seo_permalink){
			$this->category_seo_permalink = $category_seo_permalink ?? $this->category_name;
		}

		public function get_category_seo_keyword(){
			return $this->category_seo_keyword;
		}

		public function set_category_seo_keyword($category_seo_keyword){
			$this->category_seo_keyword = $category_seo_keyword;
		}

		public function get_category_seo_description(){
			return $this->category_seo_description;
		}

		public function set_category_seo_description($category_seo_description){
			$this->category_seo_description = $category_seo_description;
		}

		public function get_category_seo_title(){
			return $this->category_seo_title;
		}

		public function set_category_seo_title($category_seo_title){
			$this->category_seo_title = $category_seo_title;
		}

		public function get_category_sort(){
			return $this->category_sort;
		}

		public function set_category_sort($category_sort){
			$this->category_sort = $category_sort;
		}

		public function get_category_show_menu(){
			return $this->category_show_menu;
		}

		public function set_category_show_menu($category_show_menu): void{
			$this->category_show_menu = $category_show_menu;
		}

		public function to_array(){

			$check = $this->ticimax_helper->check_request_params($this, $this->request_params);
			if(!$check){
				return false;
			}

			return [
				'ID'                 => $this->category_id ?? 0,
				'PID'                => $this->category_parent_id ?? 0,
				'Aktif'              => $this->category_status ?? true,
				'Tanim'              => $this->category_name,
				'Kod'                => $this->category_code ?? $this->category_name,
				'SeoAnahtarKelime'   => $this->category_seo_keyword,
				'SeoSayfaBaslik'     => $this->category_seo_title ?? $this->ticimax_helper->string_to_seo_title($this->category_name),
				'SeoSayfaAciklama'   => $this->category_seo_description,
				'Icerik'             => $this->category_description,
				'Sira'               => $this->category_sort ?? 0,
				'Url'                => $this->category_seo_permalink ?? $this->ticimax_helper->string_to_seo_url($this->category_name),
				'KategoriMenuGoster' => $this->category_show_menu ?? true,
			];
		}

	}