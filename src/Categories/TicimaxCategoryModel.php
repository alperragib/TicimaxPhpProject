<?php

	namespace Hasokeyk\Ticimax\Categories;

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

		/**
		 * @return mixed
		 */
		public function getCategoryId(){
			return $this->category_id;
		}

		/**
		 * @param mixed $category_id
		 */
		public function setCategoryId($category_id): void{
			$this->category_id = $category_id;
		}

		/**
		 * @return mixed
		 */
		public function getCategoryName(){
			return $this->category_name;
		}

		/**
		 * @param mixed $category_name
		 */
		public function setCategoryName($category_name): void{
			$this->category_name = $category_name;
		}

		/**
		 * @return mixed
		 */
		public function getCategoryDescription(){
			return $this->category_description;
		}

		/**
		 * @param mixed $category_description
		 */
		public function setCategoryDescription($category_description): void{
			$this->category_description = $category_description;
		}

		/**
		 * @return mixed
		 */
		public function getCategoryParentId(){
			return $this->category_parent_id;
		}

		/**
		 * @param mixed $category_parent_id
		 */
		public function setCategoryParentId($category_parent_id): void{
			$this->category_parent_id = $category_parent_id;
		}

		/**
		 * @return mixed
		 */
		public function getCategoryStatus(){
			return $this->category_status;
		}

		/**
		 * @param mixed $category_status
		 */
		public function setCategoryStatus($category_status): void{
			$this->category_status = $category_status;
		}

		/**
		 * @return mixed
		 */
		public function getCategoryCode(){
			return $this->category_code;
		}

		/**
		 * @param mixed $category_code
		 */
		public function setCategoryCode($category_code): void{
			$this->category_code = $category_code;
		}

		/**
		 * @return mixed
		 */
		public function getCategorySeoPermalink(){
			return $this->category_seo_permalink;
		}

		/**
		 * @param mixed $category_seo_permalink
		 */
		public function setCategorySeoPermalink($category_seo_permalink): void{
			$this->category_seo_permalink = $category_seo_permalink;
		}

		/**
		 * @return mixed
		 */
		public function getCategorySeoKeyword(){
			return $this->category_seo_keyword;
		}

		/**
		 * @param mixed $category_seo_keyword
		 */
		public function setCategorySeoKeyword($category_seo_keyword): void{
			$this->category_seo_keyword = $category_seo_keyword;
		}

		/**
		 * @return mixed
		 */
		public function getCategorySeoDescription(){
			return $this->category_seo_description;
		}

		/**
		 * @param mixed $category_seo_description
		 */
		public function setCategorySeoDescription($category_seo_description): void{
			$this->category_seo_description = $category_seo_description;
		}

		/**
		 * @return mixed
		 */
		public function getCategorySeoTitle(){
			return $this->category_seo_title;
		}

		/**
		 * @param mixed $category_seo_title
		 */
		public function setCategorySeoTitle($category_seo_title): void{
			$this->category_seo_title = $category_seo_title;
		}

		/**
		 * @return mixed
		 */
		public function getCategorySort(){
			return $this->category_sort;
		}

		/**
		 * @param mixed $category_sort
		 */
		public function setCategorySort($category_sort): void{
			$this->category_sort = $category_sort;
		}

		public function to_array(){
			return [
				'ID'                 => $this->category_id,
				'PID'                => $this->category_parent_id,
				'Aktif'              => $this->category_status,
				'Tanim'              => $this->category_name,
				'Kod'                => $this->category_code,
				'SeoAnahtarKelime'   => $this->category_seo_keyword,
				'SeoSayfaBaslik'     => $this->category_seo_title,
				'SeoSayfaAciklama'   => $this->category_seo_description,
				'Icerik'             => $this->category_description,
				'Sira'               => $this->category_sort,
				'Url'                => $this->category_seo_permalink,
				'KategoriMenuGoster' => true
			];
		}

	}