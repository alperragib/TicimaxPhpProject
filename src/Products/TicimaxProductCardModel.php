<?php

	namespace Hasokeyk\Ticimax\Products;

	use Hasokeyk\Ticimax\TicimaxHelpers;

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

		private $request_params = [
			'product_name',
			'product_is_active',
			'product_category_id',
			'product_brand_id',
			'product_variations',
		];

		private $ticimax_helper;

		function __construct(){
			$this->ticimax_helper = new TicimaxHelpers();
		}

		public function get_product_id(){
			return $this->product_id;
		}

		public function set_product_id($product_id): void{
			$this->product_id = $product_id;
		}

		public function get_product_name(){
			return $this->product_name;
		}

		public function set_product_name($product_name): void{
			$this->product_name              = $product_name;
			$this->ukAyar['UrunAdiGuncelle'] = true;
		}

		public function get_product_description(){
			return $this->product_description;
		}

		public function set_product_description($product_description): void{
			$this->product_description        = $product_description;
			$this->ukAyar['AciklamaGuncelle'] = true;
		}

		public function get_product_is_active(){
			return $this->product_is_active;
		}

		public function set_product_is_active($product_is_active): void{
			$this->product_is_active       = $product_is_active;
			$this->ukAyar['AktifGuncelle'] = true;
		}

		public function get_product_category_id(){
			return $this->product_category_id;
		}

		public function set_product_category_id($product_category_id): void{
			$this->product_category_id        = $product_category_id;
			$this->ukAyar['KategoriGuncelle'] = true;
		}

		public function get_product_categories_ids(): array{
			return $this->product_categories_ids;
		}

		public function set_product_categories_ids(array $product_categories_ids): void{
			$this->product_categories_ids = $product_categories_ids;
		}

		public function get_product_show_list(){
			return $this->product_show_list;
		}

		public function set_product_show_list($product_show_list): void{
			$this->product_show_list               = $product_show_list;
			$this->ukAyar['ListedeGosterGuncelle'] = true;
		}

		public function get_product_brand_id(){
			return $this->product_brand_id;
		}

		public function set_product_brand_id($product_brand_id): void{
			$this->product_brand_id        = $product_brand_id;
			$this->ukAyar['MarkaGuncelle'] = true;
		}

		public function get_product_supplier_id(){
			return $this->product_supplier_id;
		}

		public function set_product_supplier_id($product_supplier_id): void{
			$this->product_supplier_id         = $product_supplier_id;
			$this->ukAyar['TedarikciGuncelle'] = true;
		}

		public function get_product_supplier_code(){
			return $this->product_supplier_code;
		}

		public function set_product_supplier_code($product_supplier_code): void{
			$this->product_supplier_code                = $product_supplier_code;
			$this->ukAyar['TedarikciKodu2GoreGuncelle'] = true;
		}

		public function get_product_variations(){
			return $this->product_variations;
		}

		public function set_product_variations($product_variations): void{
			$this->product_variations = $product_variations;
		}

		public function get_product_highlighting(){
			return $this->product_highlighting;
		}

		public function set_product_highlighting($product_highlighting): void{
			$this->product_highlighting     = $product_highlighting;
			$this->ukAyar['VitrinGuncelle'] = true;
		}

		public function get_product_images(){
			return $this->product_images;
		}

		public function set_product_images($product_images): void{
			$this->product_images              = $product_images;
			$this->ukAyar['UrunResimGuncelle'] = true;
		}

		public function get_product_unit_name(){
			return $this->product_unit_name;
		}

		public function set_product_unit_name($product_unit_name): void{
			$this->product_unit_name             = $product_unit_name;
			$this->ukAyar['SatisBirimiGuncelle'] = true;
		}

		public function product_to_array(){

			$check = $this->ticimax_helper->check_request_params($this, $this->request_params);
			if(!$check){
				return false;
			}

			$product_array = [
				'ID'            => $this->product_id ?? 0,
				'UrunAdi'       => $this->product_name,
				'Aktif'         => $this->product_is_active ?? true,
				'AnaKategoriID' => $this->product_category_id,
				'Kategoriler'   => $this->product_categories_ids,
				'ListedeGoster' => $this->product_show_list ?? true,
				'MarkaID'       => $this->product_brand_id,
				'Resimler'      => $this->product_images,
				'SatisBirimi'   => $this->product_unit_name,
				'TedarikciID'   => $this->product_supplier_id,
				'TedarikciKodu' => $this->product_supplier_code,
				'Vitrin'        => $this->product_highlighting ?? true
			];

			if(is_object($this->get_product_variations())){
				$product_array['Varyasyonlar']['Varyasyon'] = ($this->product_variations->product_variation_to_array() ?? null);
			}

			return $product_array;
		}

		public function uk_ayar_to_array(){
			return $this->ukAyar;
		}

	}