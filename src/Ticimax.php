<?php

	namespace Hasokeyk\Ticimax;

	use SoapClient;
	use Hasokeyk\Ticimax\Products\TicimaxProducts;
	use Hasokeyk\Ticimax\Categories\TicimaxCategories;

	class Ticimax{

		public $main_domain = null;
		public $key         = null;
		public $ticimax     = null;
		public $categories  = null;
		public $products    = null;
		public $soap_client = null;

		function __construct($main_domain, $key){

			$this->main_domain = $main_domain;
			$this->key         = $key;
			$this->ticimax     = $this;

			$this->categories = $this->categories();
			$this->products   = $this->products();

		}

		function categories(){
			return new TicimaxCategories($this->ticimax);
		}

		function products(){
			return new TicimaxProducts($this->ticimax);
		}

		function soap_client($url = null){
			return $this->soap_client = new SoapClient($this->main_domain.$url, [
				'trace'      => 1,
				'exceptions' => true,
				'cache_wsdl' => WSDL_CACHE_NONE,
				'UyeKodu'    => $this->key
			]);
		}

	}