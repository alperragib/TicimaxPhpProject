<?php

	namespace AlperRagib\Ticimax\Suppliers;

	use AlperRagib\Ticimax\TicimaxHelpers;

	class TicimaxSupplierModel{

		private $supplier_status;
		private $supplier_id;
		private $supplier_mail;
		private $supplier_note;
		private $supplier_name;

		private $ticimax_helper;

		function __construct(){
			$this->ticimax_helper = new TicimaxHelpers();
		}

		private $request_params = [
			'supplier_name',
		];

		public function get_supplier_name(){
			return $this->supplier_name;
		}

		public function set_supplier_name($supplier_name): void{
			$this->supplier_name = $supplier_name;
		}

		public function get_supplier_note(){
			return $this->supplier_note;
		}

		public function set_supplier_note($supplier_note): void{
			$this->supplier_note = $supplier_note;
		}

		public function get_supplier_mail(){
			return $this->supplier_mail;
		}

		public function set_supplier_mail($supplier_mail): void{
			$this->supplier_mail = $supplier_mail;
		}

		public function get_supplier_id(){
			return $this->supplier_id;
		}

		public function set_supplier_id($supplier_id): void{
			$this->supplier_id = $supplier_id;
		}

		public function get_supplier_status(){
			return $this->supplier_status;
		}

		public function set_supplier_status($supplier_status): void{
			$this->supplier_status = $supplier_status;
		}

		public function to_array(){

			$check = $this->ticimax_helper->check_request_params($this, $this->request_params);
			if(!$check){
				return false;
			}

			return [
				'ID'         => $this->supplier_id ?? 0,
				'Aktif'      => $this->supplier_status ?? true,
				'Mail'       => $this->supplier_mail,
				'Not'        => $this->supplier_note,
				'Tanim'      => $this->supplier_name,
				'Breadcrumb' => 222,
			];
		}

	}