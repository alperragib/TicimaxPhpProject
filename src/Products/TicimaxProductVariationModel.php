<?php

	namespace Hasokeyk\Ticimax\Products;

	class TicimaxProductVariationModel{

		private $product_variation_is_active;
		private $product_variation_money_unit_id;
		private $product_variation_sale_price;

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

		private $requiredFields = [
			'setProductVariationIsActive'    => 'product_variation_is_active',
			'setProductVariationMoneyUnitId' => 'product_variation_money_unit_id',
			'setProductVariationSalePrice'   => 'product_variation_sale_price',
		];
		private $calledGetters  = [];

		public function checkRequiredFields(){
			$missingFields = [];
			foreach($this->requiredFields as $method_name => $variation_name){
				if(strlen($this->$variation_name) <= 0){
					$missingFields[] = $method_name;
				}
			}

			if(!empty($missingFields)){
				$missingFieldsString = implode(', ', $missingFields);
				trigger_error("The following getter methods were not called (and are required): ".$missingFieldsString, E_USER_WARNING);
				return false;
			}

			return true;
		}

		public function getProductVariationIsActive(){
			return $this->product_variation_is_active;
		}

		public function setProductVariationIsActive($product_variation_is_active){
			$this->product_variation_is_active = $product_variation_is_active;
			$this->vAyar['AktifGuncelle'] = true;
			return $this;
		}

		public function getProductVariationMoneyUnitId(){
			return $this->product_variation_money_unit_id;
		}

		public function setProductVariationMoneyUnitId($product_variation_money_unit_id){
			$this->product_variation_money_unit_id = $product_variation_money_unit_id;
			$this->vAyar['ParaBirimiGuncelle'] = true;
			return $this;
		}

		public function getProductVariationSalePrice(){
			return $this->product_variation_sale_price;
		}

		public function setProductVariationSalePrice($product_variation_sale_price){
			$this->product_variation_sale_price = $product_variation_sale_price;
			$this->vAyar['SatisFiyatiGuncelle'] = true;
			return $this;
		}

		public function productVariationToArray(){

			$check = $this->checkRequiredFields();
			if(!$check){
				return false;
			}

			return [
				'ID'           => 0,
				'Aktif'        => $this->getProductVariationIsActive(),
				'ParaBirimiID' => $this->getProductVariationMoneyUnitId(),
				'SatisFiyati'  => $this->getProductVariationSalePrice(),
			];
		}

	}