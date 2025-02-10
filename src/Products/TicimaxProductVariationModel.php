<?php

	namespace Hasokeyk\Ticimax\Products;

	use Hasokeyk\Ticimax\TicimaxHelpers;

	class TicimaxProductVariationModel{

		private $product_variation_id;
		private $product_variation_is_active;
		private $product_variation_money_unit_id;
		private $product_variation_sale_price;
		private $product_variation_stock_quantity;
		private $product_variation_stock_code;
		private $product_variation_barcode;

		public $vAyar = [
			"AktifGuncelle"                      => false,
			"AlisFiyatiGuncelle"                 => false,
			"BarkodGuncelle"                     => false,
			"EkSecenekGuncelle"                  => false,
			"EksiStokAdediGuncelle"              => false,
			"FiyatTipleriGuncelle"               => false,
			"GtipKoduGuncelle"                   => false,
			"IndirimliFiyatiGuncelle"            => false,
			"IscilikAgirlikGuncelle"             => false,
			"IscilikParaBirimiGuncelle"          => false,
			"KargoAgirligiGuncelle"              => false,
			"KargoAgirligiYurtDisiGuncelle"      => false,
			"KargoUcretiGuncelle"                => false,
			"KdvDahilGuncelle"                   => false,
			"KdvOraniGuncelle"                   => false,
			"KonsinyeUrunStokAdediGuncelle"      => false,
			"OncekiResimleriSil"                 => false,
			"ParaBirimiGuncelle"                 => false,
			"PiyasaFiyatiGuncelle"               => false,
			"ResimOlmayanlaraResimEkle"          => false,
			"SatisFiyatiGuncelle"                => false,
			"StokAdediGuncelle"                  => false,
			"StokKoduGuncelle"                   => false,
			"TahminiTeslimSuresiAyniGunGuncelle" => false,
			"TahminiTeslimSuresiGosterGuncelle"  => false,
			"TahminiTeslimSuresiGuncelle"        => false,
			"TahminiTeslimSuresiTarihGuncelle"   => false,
			"TedarikciKodunaGoreGuncelle"        => false,
			"TedarikciKomisyoniGuncelle"         => false,
			"UpdateKeyGuncelle"                  => false,
			"UrunAgirligiGuncelle"               => false,
			"UrunDerinlikGuncelle"               => false,
			"UrunGenislikGuncelle"               => false,
			"UrunResimGuncelle"                  => false,
			"UrunYukseklikGuncelle"              => false,
			"UyeAlimMaxGuncelle"                 => false,
			"UyeAlimMinGuncelle"                 => false,
			"UyeTipiFiyat10Guncelle"             => false,
			"UyeTipiFiyat11Guncelle"             => false,
			"UyeTipiFiyat12Guncelle"             => false,
			"UyeTipiFiyat13Guncelle"             => false,
			"UyeTipiFiyat14Guncelle"             => false,
			"UyeTipiFiyat15Guncelle"             => false,
			"UyeTipiFiyat16Guncelle"             => false,
			"UyeTipiFiyat17Guncelle"             => false,
			"UyeTipiFiyat18Guncelle"             => false,
			"UyeTipiFiyat19Guncelle"             => false,
			"UyeTipiFiyat1Guncelle"              => false,
			"UyeTipiFiyat20Guncelle"             => false,
			"UyeTipiFiyat2Guncelle"              => false,
			"UyeTipiFiyat3Guncelle"              => false,
			"UyeTipiFiyat4Guncelle"              => false,
			"UyeTipiFiyat5Guncelle"              => false,
			"UyeTipiFiyat6Guncelle"              => false,
			"UyeTipiFiyat7Guncelle"              => false,
			"UyeTipiFiyat8Guncelle"              => false,
			"UyeTipiFiyat9Guncelle"              => false,
		];

		private $request_params = [
			'product_variation_money_unit_id',
			'product_variation_sale_price',
			'product_variation_stock_code',
//			'product_variation_stock_quantity',
		];

		private $ticimax_helper;

		function __construct(){
			$this->ticimax_helper = new TicimaxHelpers();
		}

		public function get_product_variation_id(){
			return $this->product_variation_id;
		}

		public function set_product_variation_id($product_variation_id): void{
			$this->product_variation_id = $product_variation_id;
		}

		public function get_product_variation_is_active(){
			return $this->product_variation_is_active;
		}

		public function set_product_variation_is_active($product_variation_is_active): void{
			$this->product_variation_is_active = $product_variation_is_active;
			$this->vAyar['AktifGuncelle']      = true;
		}

		public function get_product_variation_money_unit_id(){
			return $this->product_variation_money_unit_id;
		}

		public function set_product_variation_money_unit_id($product_variation_money_unit_id): void{
			$this->product_variation_money_unit_id = $product_variation_money_unit_id;
			$this->vAyar['ParaBirimiGuncelle']     = true;
		}

		public function get_product_variation_sale_price(){
			return $this->product_variation_sale_price;
		}

		public function set_product_variation_sale_price($product_variation_sale_price): void{
			$this->product_variation_sale_price = $product_variation_sale_price;
			$this->vAyar['SatisFiyatiGuncelle'] = true;
		}

		public function get_product_variation_stock_quantity(){
			return $this->product_variation_stock_quantity;
		}

		public function set_product_variation_stock_quantity($product_variation_stock_quantity): void{
			$this->product_variation_stock_quantity = $product_variation_stock_quantity;
			$this->vAyar['StokAdediGuncelle']       = true;
		}

		public function get_product_variation_stock_code(){
			return $this->product_variation_stock_code;
		}

		public function set_product_variation_stock_code($product_variation_stock_code): void{
			$this->product_variation_stock_code = $product_variation_stock_code;
			$this->vAyar['StokKoduGuncelle']    = true;
		}

		public function get_product_variation_barcode(){
			return $this->product_variation_barcode;
		}

		public function set_product_variation_barcode($product_variation_barcode): void{
			$this->product_variation_barcode = $product_variation_barcode;
		}

		public function product_variation_to_array(){

			$check = $this->ticimax_helper->check_request_params($this, $this->request_params);
			if(!$check){
				return false;
			}

			return [
				'ID'           => $this->product_variation_id ?? 0,
				'Aktif'        => $this->product_variation_is_active ?? true,
				'ParaBirimiID' => $this->product_variation_money_unit_id,
				'StokAdedi'    => $this->product_variation_stock_quantity,
				'StokKodu'     => $this->product_variation_stock_code,
				'SatisFiyati'  => $this->product_variation_sale_price,
				'Barkod'       => $this->product_variation_barcode,
			];
		}

		public function v_ayar_to_array(){
			return $this->vAyar;
		}

	}