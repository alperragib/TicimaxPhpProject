<?php

	namespace Hasokeyk\Ticimax\Products;

	class TicimaxProducts{

		public $api_url = "/Servis/UrunServis.svc?singleWsdl";

		private $ticimax;

		function __construct($ticimax){
			$this->ticimax = $ticimax;
		}

	}