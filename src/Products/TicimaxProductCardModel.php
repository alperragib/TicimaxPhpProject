<?php

	namespace Hasokeyk\Ticimax\Products;

	class TicimaxProductCardModel{

		public $product_id;
		public $product_name;
		public $product_description;
		public $product_is_active;
		public $product_category_id;
		public $product_categories_ids = [];
		public $product_show_list;
		public $product_brand_id;
		public $product_supplier_id;
		public $product_supplier_code;
		public $product_variations;
		public $product_highlighting;
		public $product_images;
		public $product_unit_name;

		private $ukAyar = [
			'AciklamaGuncelle'                          => false,
			'AdwordsAciklamaGuncelle'                   => false,
			'AdwordsKategoriGuncelle'                   => false,
			'AdwordsTipGuncelle'                        => false,
			'AktifGuncelle'                             => false,
			'AktifPazaryeriListGuncelle'                => false,
			'AlanAdi'                                   => '',
			'AnaKategoriId'                             => 0,
			'AramaAnahtarKelimeGuncelle'                => false,
			'AsortiGrupGuncelle'                        => false,
			'Base64Resim'                               => false,
			'DegerTanim'                                => false,
			'EntegrasyonKodu'                           => false,
			'EtiketGuncelle'                            => false,
			'FBStoreGosterGuncelle'                     => false,
			'FirsatUrunuGuncelle'                       => false,
			'HediyeIpucuGosterGuncelle'                 => false,
			'IlgiliUrunResimGuncelle'                   => false,
			'KargoTipiGuncelle'                         => false,
			'KategoriGuncelle'                          => false,
			'ListedeGosterGuncelle'                     => false,
			'MaksTaksitSayisiGuncelle'                  => false,
			'MarkaGuncelle'                             => false,
			'MarketPlaceAktif2Guncelle'                 => false,
			'MarketPlaceAktif3Guncelle'                 => false,
			'MarketPlaceAktif4Guncelle'                 => false,
			'MarketPlaceAktif5Guncelle'                 => false,
			'MarketPlaceAktifGuncelle'                  => false,
			'MarketPlaceAyarGuncelle'                   => false,
			'MenseiUlkeGuncelle'                        => false,
			'MobilOzelAlanGuncelle'                     => false,
			'OnYaziGuncelle'                            => false,
			'OncekiKategoriEslestirmeleriniTemizle'     => false,
			'OncekiResimleriSil'                        => false,
			'OzelAlan1Guncelle'                         => false,
			'OzelAlan2Guncelle'                         => false,
			'OzelAlan3Guncelle'                         => false,
			'OzelAlan4Guncelle'                         => false,
			'OzelAlan5Guncelle'                         => false,
			'ParaPuanGuncelle'                          => false,
			'PuanKullanimiIptalAktifGuncelle'           => false,
			'RenkKoduGuncelle'                          => false,
			'ResimOlmayanlaraResimEkle'                 => false,
			'ResimleriIndirme'                          => false,
			'SatisBirimiGuncelle'                       => false,
			'SeoAnahtarKelimeGuncelle'                  => false,
			'SeoNoFollowGuncelle'                       => false,
			'SeoNoIndexGuncelle'                        => false,
			'SeoSayfaAciklamaGuncelle'                  => false,
			'SeoSayfaBaslikGuncelle'                    => false,
			'SepetteUcretsizKargoGuncelle'              => false,
			'TahminiTeslimSuresiGosterGuncelle'         => false,
			'TahminiTeslimSuresiGuncelle'               => false,
			'TahminiTeslimSuresiTarihGuncelle'          => false,
			'TedarikciGuncelle'                         => false,
			'TedarikciKodu2GoreGuncelle'                => false,
			'TedarikciKodunaGoreGuncelle'               => false,
			'TedarikciKomisyonGuncelle'                 => false,
			'TeknikDetayGuncelle'                       => false,
			'TumVaryasyonlarStokDusurGuncelle'          => false,
			'UcretsizKargoGuncelle'                     => false,
			'UrunAdediKademeDegerGuncelle'              => false,
			'UrunAdediMinimumDegerGuncelle'             => false,
			'UrunAdediOndalikliSayiGirilebilirGuncelle' => false,
			'UrunAdediVarsayilanDegerGuncelle'          => false,
			'UrunAdiGuncelle'                           => false,
			'UrunAdresiniElleOlustur'                   => false,
			'UrunKapasiteGuncelle'                      => false,
			'UrunKapidaOdemeYasakliGuncelle'            => false,
			'UrunResimGuncelle'                         => false,
			'UrunTipiGuncelle'                          => false,
			'UserAgent'                                 => false,
			'UyeAlimMaksGuncelle'                       => false,
			'UyeAlimMinGuncelle'                        => false,
			'VergiIstisnaKoduGuncelle'                  => false,
			'VitrinGuncelle'                            => false,
			'VitrinSiraSabitGuncelle'                   => false,
			'YayinTarihiGuncelle'                       => false,
			'YeniUrunGuncelle'                          => false,
		];

		private $requiredFields = [
			'setProductId'         => 'product_id',
			'setProductName'       => 'product_name',
			'setProductIsActive'   => 'product_is_active',
			'setProductCategoryId' => 'product_category_id',
			'setProductBrandId'    => 'product_brand_id',
			'setProductVariations' => 'product_variations',
		];
		private $calledGetters  = [];

		private function checkRequiredFields(){
			$missingFields = [];
			foreach($this->requiredFields as $method_name => $variation_name){
				if(!isset($this->$variation_name)){
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

		public function getProductHighlighting(){
			return $this->product_highlighting;
		}

		public function setProductHighlighting($product_highlighting){
			$this->product_highlighting = $product_highlighting;
			return $this;
		}

		public function getProductId(){
			return $this->product_id;
		}

		public function setProductId($product_id){
			$this->product_id = $product_id;
			return $this;
		}

		public function getProductName(){
			return $this->product_name;
		}

		public function setProductName($product_name){
			$this->product_name              = $product_name;
			$this->ukAyar['UrunAdiGuncelle'] = true;
			return $this;
		}

		public function getProductIsActive(){
			return $this->product_is_active;
		}

		public function setProductIsActive($product_is_active){
			$this->product_is_active       = $product_is_active;
			$this->ukAyar['AktifGuncelle'] = true;
			return $this;
		}

		public function getProductCategoryId(){
			return $this->product_category_id;
		}

		public function setProductCategoryId($product_category_id){
			$this->product_category_id     = $product_category_id;
			$this->ukAyar['AnaKategoriId'] = $product_category_id;
			return $this;
		}

		public function getProductCategoriesIds(): array{
			return $this->product_categories_ids;
		}

		public function setProductCategoriesIds(array $product_categories_ids): TicimaxProductCardModel{
			$this->product_categories_ids = $product_categories_ids;
			return $this;
		}

		public function getProductShowList(){
			return $this->product_show_list;
		}

		public function setProductShowList($product_show_list){
			$this->product_show_list               = $product_show_list;
			$this->ukAyar['ListedeGosterGuncelle'] = true;
			return $this;
		}

		public function getProductBrandId(){
			return $this->product_brand_id;
		}

		public function setProductBrandId($product_brand_id){
			$this->product_brand_id        = $product_brand_id;
			$this->ukAyar['MarkaGuncelle'] = true;
			return $this;
		}

		public function getProductSupplierId(){
			return $this->product_supplier_id;
		}

		public function setProductSupplierId($product_supplier_id){
			$this->product_supplier_id         = $product_supplier_id;
			$this->ukAyar['TedarikciGuncelle'] = true;
			return $this;
		}

		public function getProductSupplierCode(){
			return $this->product_supplier_code;
		}

		public function setProductSupplierCode($product_supplier_code){
			$this->product_supplier_code       = $product_supplier_code;
			$this->ukAyar['TedarikciGuncelle'] = true;
			return $this;
		}

		public function getProductVariations(){
			return $this->product_variations;
		}

		public function setProductVariations($product_variations){
			$this->product_variations = $product_variations;
			return $this;
		}

		public function getProductImages(){
			return $this->product_images;
		}

		public function setProductImages($product_images){
			$this->product_images              = $product_images;
			$this->ukAyar['UrunResimGuncelle'] = true;
			return $this;
		}

		public function getProductUnitName(){
			return $this->product_unit_name;
		}

		public function setProductUnitName($product_unit_name){
			$this->product_unit_name             = $product_unit_name;
			$this->ukAyar['SatisBirimiGuncelle'] = true;
			return $this;
		}

		public function getProductDesc(){
			return $this->product_description;
		}

		public function setProductDesc($product_description){
			$this->product_description        = $product_description;
			$this->ukAyar['AciklamaGuncelle'] = true;
			return $this;
		}

		public function productToArray(){

			$check = $this->checkRequiredFields();
			if(!$check){
				return false;
			}

			$variation_check = $this->getProductVariations()->checkRequiredFields();
			if(!$variation_check){
				return false;
			}

			return [
				'ID'            => $this->getProductID(),
				'UrunAdi'       => $this->getProductName(),
				'Aktif'         => $this->getProductIsActive(),
				'AnaKategoriID' => $this->getProductCategoryId(),
				'Kategoriler'   => $this->getProductCategoriesIds(),
				'ListedeGoster' => $this->getProductShowList(),
				'MarkaID'       => $this->getProductBrandId(),
				'Resimler'      => $this->getProductImages(),
				'SatisBirimi'   => $this->getProductUnitName(),
				'TedarikciID'   => $this->getProductSupplierId(),
				'TedarikciKodu' => $this->getProductSupplierCode(),
				'Varyasyonlar'  => [
					'Varyasyon' => $this->getProductVariations()->productVariationToArray(),
				],
				'Vitrin'        => $this->getProductHighlighting()
			];
		}


		public function ukAyarToArray(){
			return $this->ukAyar;
		}

	}