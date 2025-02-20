<?php

	namespace Hasokeyk\Ticimax;

	use Hasokeyk\Ticimax\Brands\TicimaxBrands;
	use Hasokeyk\Ticimax\Products\TicimaxProduct;
	use Hasokeyk\Ticimax\Suppliers\TicimaxSuppliers;
	use Hasokeyk\Ticimax\Categories\TicimaxCategories;

	class Ticimax{

		public $main_domain = null;
		public $key         = null;
		public $ticimax     = null;

		public $request    = null;
		public $categories = null;
		public $products   = null;
		public $brands     = null;
		public $suppliers  = null;

		function __construct($main_domain, $key){

			$this->main_domain = $main_domain;
			$this->key         = $key;
			$this->ticimax     = $this;
			$this->request     = $this->request();

			$this->categories = $this->categories();
			$this->products   = $this->products();
			$this->brands     = $this->brands();
			$this->suppliers  = $this->suppliers();

		}

		function request(){
			return new TicimaxRequest($this->main_domain, $this->key);
		}

		function categories(){
			return new TicimaxCategories($this->request);
		}

		function products(){
			return new TicimaxProduct($this->request);
		}

		function brands(){
			return new TicimaxBrands($this->request);
		}

		function suppliers(){
			return new TicimaxSuppliers($this->request);
		}

	}