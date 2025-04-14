<?php

	namespace Hasokeyk\Ticimax\Products;

	use SoapFault;
	use Hasokeyk\Ticimax\TicimaxRequest;

	class TicimaxProduct{

		public $api_url = "/Servis/UrunServis.svc?singleWsdl";

		private $ticimax_request;

		function __construct(TicimaxRequest $ticimax_request){
			$this->ticimax_request = $ticimax_request;
		}

		public function get_products($filters = [], $pagination = []){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{

				// Varsayılan filtre ve sayfalama ayarları
				$defaultFilters    = [
					'Aktif'       => -1,
					'Firsat'      => -1,
					'Indirimli'   => -1,
					'Vitrin'      => -1,
					'KategoriID'  => 0,
					'MarkaID'     => 0,
					'UrunKartiID' => 0,
				];
				$defaultPagination = [
					'BaslangicIndex' => 0,
					'KayitSayisi'    => 100,
					'SiralamaDegeri' => 'ID',
					'SiralamaYonu'   => 'DESC',
				];

				// Kullanıcıdan gelen parametrelerle birleştir
				$urun_filteleme = array_merge($defaultFilters, $filters);
				$urun_sayfalama = array_merge($defaultPagination, $pagination);

				$response = $client->__soapCall("SelectUrun", [
					[
						'UyeKodu' => $this->ticimax_request->key,
						'f'       => (object)$urun_filteleme,
						's'       => (object)$urun_sayfalama,
					]
				]);

				$total_product = $client->__soapCall("SelectUrunCount", [
					[
						'UyeKodu' => $this->ticimax_request->key,
						'f'       => (object)$urun_filteleme,
					]
				]);

				return (object)[
					'status'        => isset($response->SelectUrunResult->UrunKarti) ? 'success' : 'danger',
					'data'          => isset($response->SelectUrunResult->UrunKarti->ID) ? [$response->SelectUrunResult->UrunKarti] : ($response->SelectUrunResult->UrunKarti ?? null), //EĞER TEK VERİ VARSA DİREKT ERİŞİM VERİYOR DÖNGÜYE ALINCA HATA VERMEMESİ İÇİN TEK VERİ VARSA DİZİ İÇİNE OTOMATİK ALIYORUZ
					'total_product' => $total_product->SelectUrunCountResult ?? 0,
					'request'       => $client->__getLastRequest(),
					'response'      => $client->__getLastResponse(),
				];

			}catch(SoapFault $e){
				return (object)[
					'status'   => 'danger',
					'message'  => $e->getMessage(),
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}
		}

		public function create_products($ticimax_product_cards, TicimaxProductSettingsModel $ticimax_product_settings = null, TicimaxProductVariationSettingsModel $ticimax_product_variation_settings = null){

			if(!is_array($ticimax_product_cards)){
				return [
					'status'  => 'danger',
					'message' => 'Gönderilen ürün değeri dizi olmalıdır'
				];
			}

			$client = $this->ticimax_request->soap_client($this->api_url);
			try{

				$ticimax_product_cards_array = [];
				foreach($ticimax_product_cards as $ticimax_product_card){
					$get_array = $ticimax_product_card->to_array();
					if(is_array($get_array)){
						$ticimax_product_cards_array['UrunKarti'][] = $get_array;
					}
					else{
						return false;
					}
				}

				$params = [
					[
						'UyeKodu'      => $this->ticimax_request->key,
						'urunKartlari' => $ticimax_product_cards_array,
						'ukAyar'       => ($ticimax_product_settings ?? (new TicimaxProductSettingsModel()))->to_array(),
						'vAyar'        => ($ticimax_product_variation_settings ?? (new TicimaxProductVariationSettingsModel()))->to_array(),
					]
				];

				$response = $client->__soapCall("SaveUrun", $params);
				return (object)[
					'status'   => 'success',
					'data'     => isset($response->urunKartlari->UrunKarti->ID) ? (object)['urunKartlari' => (object)['UrunKarti' => [$response->urunKartlari->UrunKarti]]] : ($response ?? null), //EĞER TEK VERİ VARSA DİREKT ERİŞİM VERİYOR DÖNGÜYE ALINCA HATA VERMEMESİ İÇİN TEK VERİ VARSA DİZİ İÇİNE OTOMATİK ALIYORUZ
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}catch(SoapFault $e){
				return (object)[
					'status'   => 'danger',
					'message'  => $e->getMessage(),
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}
		}

		public function update_main_products(TicimaxProduct $ticimax_product_card){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{

				$ticimax_product_card_array          = $ticimax_product_card->product_to_array();
				$ticimax_product_card_settings_array = $ticimax_product_card->uk_ayar_to_array();

				if(isset($ticimax_product_card_array['ID']) and $ticimax_product_card_array['ID'] == 0){
					return (object)[
						'status'  => 'danger',
						'message' => 'Ürün güncellerken ürün ID 0 girilemez'
					];
				}

				if(!is_array($ticimax_product_card_array)){
					return false;
				}

				$params = [
					[
						'UyeKodu'       => $this->ticimax_request->key,
						'urunKarti'     => $ticimax_product_card_array,
						'urunKartiAyar' => $ticimax_product_card_settings_array,
					]
				];

				$response = $client->__soapCall("UrunKartiGuncelle", $params);
				return (object)[
					'status'   => 'success',
					'data'     => $response ?? null,
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}catch(SoapFault $e){
				return (object)[
					'status'   => 'danger',
					'message'  => $e->getMessage(),
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}
		}

		public function update_variation_products(TicimaxProductVariationModel $ticimax_product_card){
			$client = $this->ticimax_request->soap_client($this->api_url);
			try{

				$ticimax_product_variation_array          = $ticimax_product_card->product_variation_to_array();
				$ticimax_product_variation_settings_array = $ticimax_product_card->v_ayar_to_array();

				if(isset($ticimax_product_variation_array['ID']) and $ticimax_product_variation_array['ID'] == 0){
					return (object)[
						'status'  => 'danger',
						'message' => 'Ürün güncellerken ürün ID 0 girilemez '
					];
				}

				if(!is_array($ticimax_product_variation_array)){
					return false;
				}

				$params = [
					[
						'UyeKodu' => $this->ticimax_request->key,
						'urun'    => $ticimax_product_variation_array,
						'ayar'    => $ticimax_product_variation_settings_array,
					]
				];

				$response = $client->__soapCall("VaryasyonGuncelle", $params);
				return (object)[
					'status'   => 'success',
					'data'     => $response ?? null,
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}catch(SoapFault $e){
				return (object)[
					'status'   => 'danger',
					'message'  => $e->getMessage(),
					'request'  => $client->__getLastRequest(),
					'response' => $client->__getLastResponse(),
				];
			}
		}

	}