<?php

	namespace AlperRagib\Ticimax\Products;

	class TicimaxProductSettingsModel{

		public $AciklamaGuncelle                          = true;
		public $AdwordsAciklamaGuncelle                   = true;
		public $AdwordsKategoriGuncelle                   = true;
		public $AdwordsTipGuncelle                        = true;
		public $AktifGuncelle                             = true;
		public $AktifPazaryeriListGuncelle                = true;
		public $AlanAdi                                   = '';
		public $AnaKategoriId                             = 0;
		public $AramaAnahtarKelimeGuncelle                = true;
		public $AsortiGrupGuncelle                        = true;
		public $Base64Resim                               = true;
		public $DegerTanim                                = '';
		public $EntegrasyonKodu                           = '';
		public $EtiketGuncelle                            = true;
		public $FBStoreGosterGuncelle                     = true;
		public $FirsatUrunuGuncelle                       = true;
		public $HediyeIpucuGosterGuncelle                 = true;
		public $IlgiliUrunResimGuncelle                   = true;
		public $KargoTipiGuncelle                         = true;
		public $KategoriGuncelle                          = true;
		public $ListedeGosterGuncelle                     = true;
		public $MaksTaksitSayisiGuncelle                  = true;
		public $MarkaGuncelle                             = true;
		public $MarketPlaceAktif2Guncelle                 = true;
		public $MarketPlaceAktif3Guncelle                 = true;
		public $MarketPlaceAktif4Guncelle                 = true;
		public $MarketPlaceAktif5Guncelle                 = true;
		public $MarketPlaceAktifGuncelle                  = true;
		public $MarketPlaceAyarGuncelle                   = true;
		public $MenseiUlkeGuncelle                        = true;
		public $MobilOzelAlanGuncelle                     = true;
		public $OnYaziGuncelle                            = true;
		public $OncekiKategoriEslestirmeleriniTemizle     = true;
		public $OncekiResimleriSil                        = true;
		public $OzelAlan1Guncelle                         = true;
		public $OzelAlan2Guncelle                         = true;
		public $OzelAlan3Guncelle                         = true;
		public $OzelAlan4Guncelle                         = true;
		public $OzelAlan5Guncelle                         = true;
		public $ParaPuanGuncelle                          = true;
		public $PuanKullanimiIptalAktifGuncelle           = true;
		public $RenkKoduGuncelle                          = true;
		public $ResimOlmayanlaraResimEkle                 = true;
		public $ResimleriIndirme                          = true;
		public $SatisBirimiGuncelle                       = true;
		public $SeoAnahtarKelimeGuncelle                  = true;
		public $SeoNoFollowGuncelle                       = true;
		public $SeoNoIndexGuncelle                        = true;
		public $SeoSayfaAciklamaGuncelle                  = true;
		public $SeoSayfaBaslikGuncelle                    = true;
		public $SepetteUcretsizKargoGuncelle              = true;
		public $TahminiTeslimSuresiGosterGuncelle         = true;
		public $TahminiTeslimSuresiGuncelle               = true;
		public $TahminiTeslimSuresiTarihGuncelle          = true;
		public $TedarikciGuncelle                         = true;
		public $TedarikciKodu2GoreGuncelle                = true;
		public $TedarikciKodunaGoreGuncelle               = true;
		public $TedarikciKomisyonGuncelle                 = true;
		public $TeknikDetayGuncelle                       = true;
		public $TumVaryasyonlarStokDusurGuncelle          = true;
		public $UcretsizKargoGuncelle                     = true;
		public $UrunAdediKademeDegerGuncelle              = true;
		public $UrunAdediMinimumDegerGuncelle             = true;
		public $UrunAdediOndalikliSayiGirilebilirGuncelle = true;
		public $UrunAdediVarsayilanDegerGuncelle          = true;
		public $UrunAdiGuncelle                           = true;
		public $UrunAdresiniElleOlustur                   = true;
		public $UrunKapasiteGuncelle                      = true;
		public $UrunKapidaOdemeYasakliGuncelle            = true;
		public $UrunResimGuncelle                         = true;
		public $UrunTipiGuncelle                          = true;
		public $UserAgent                                 = '';
		public $UyeAlimMaksGuncelle                       = true;
		public $UyeAlimMinGuncelle                        = true;
		public $VergiIstisnaKoduGuncelle                  = true;
		public $VitrinGuncelle                            = true;
		public $VitrinSiraSabitGuncelle                   = true;
		public $YayinTarihiGuncelle                       = true;
		public $YeniUrunGuncelle                          = true;

		public function set_aciklama_guncelle(bool $AciklamaGuncelle): void{
			$this->AciklamaGuncelle = $AciklamaGuncelle;
		}

		public function get_aciklama_guncelle(): bool{
			return $this->AciklamaGuncelle;
		}

		public function set_adwords_aciklama_guncelle(bool $AdwordsAciklamaGuncelle): void{
			$this->AdwordsAciklamaGuncelle = $AdwordsAciklamaGuncelle;
		}

		public function get_adwords_aciklama_guncelle(): bool{
			return $this->AdwordsAciklamaGuncelle;
		}

		public function set_adwords_kategori_guncelle(bool $AdwordsKategoriGuncelle): void{
			$this->AdwordsKategoriGuncelle = $AdwordsKategoriGuncelle;
		}

		public function get_adwords_kategori_guncelle(): bool{
			return $this->AdwordsKategoriGuncelle;
		}

		public function set_adwords_tip_guncelle(bool $AdwordsTipGuncelle): void{
			$this->AdwordsTipGuncelle = $AdwordsTipGuncelle;
		}

		public function get_adwords_tip_guncelle(): bool{
			return $this->AdwordsTipGuncelle;
		}

		public function set_aktif_guncelle(bool $AktifGuncelle): void{
			$this->AktifGuncelle = $AktifGuncelle;
		}

		public function get_aktif_guncelle(): bool{
			return $this->AktifGuncelle;
		}

		public function set_aktif_pazaryeri_list_guncelle(bool $AktifPazaryeriListGuncelle): void{
			$this->AktifPazaryeriListGuncelle = $AktifPazaryeriListGuncelle;
		}

		public function get_aktif_pazaryeri_list_guncelle(): bool{
			return $this->AktifPazaryeriListGuncelle;
		}

		public function set_alan_adi(bool $AlanAdi): void{
			$this->AlanAdi = $AlanAdi;
		}

		public function get_alan_adi(): bool{
			return $this->AlanAdi;
		}

		public function set_ana_kategori_id(bool $AnaKategoriId): void{
			$this->AnaKategoriId = $AnaKategoriId;
		}

		public function get_ana_kategori_id(): bool{
			return $this->AnaKategoriId;
		}

		public function set_arama_anahtar_kelime_guncelle(bool $AramaAnahtarKelimeGuncelle): void{
			$this->AramaAnahtarKelimeGuncelle = $AramaAnahtarKelimeGuncelle;
		}

		public function get_arama_anahtar_kelime_guncelle(): bool{
			return $this->AramaAnahtarKelimeGuncelle;
		}

		public function set_asorti_grup_guncelle(bool $AsortiGrupGuncelle): void{
			$this->AsortiGrupGuncelle = $AsortiGrupGuncelle;
		}

		public function get_asorti_grup_guncelle(): bool{
			return $this->AsortiGrupGuncelle;
		}

		public function set_base_64_resim(bool $Base64Resim): void{
			$this->Base64Resim = $Base64Resim;
		}

		public function get_base_64_resim(): bool{
			return $this->Base64Resim;
		}

		public function set_deger_tanim(bool $DegerTanim): void{
			$this->DegerTanim = $DegerTanim;
		}

		public function get_deger_tanim(): bool{
			return $this->DegerTanim;
		}

		public function set_entegrasyon_kodu(bool $EntegrasyonKodu): void{
			$this->EntegrasyonKodu = $EntegrasyonKodu;
		}

		public function get_entegrasyon_kodu(): bool{
			return $this->EntegrasyonKodu;
		}

		public function set_etiket_guncelle(bool $EtiketGuncelle): void{
			$this->EtiketGuncelle = $EtiketGuncelle;
		}

		public function get_etiket_guncelle(): bool{
			return $this->EtiketGuncelle;
		}

		public function set_FB_store_goster_guncelle(bool $FBStoreGosterGuncelle): void{
			$this->FBStoreGosterGuncelle = $FBStoreGosterGuncelle;
		}

		public function get_FB_store_goster_guncelle(): bool{
			return $this->FBStoreGosterGuncelle;
		}

		public function set_firsat_urunu_guncelle(bool $FirsatUrunuGuncelle): void{
			$this->FirsatUrunuGuncelle = $FirsatUrunuGuncelle;
		}

		public function get_firsat_urunu_guncelle(): bool{
			return $this->FirsatUrunuGuncelle;
		}

		public function set_hediye_ipucu_goster_guncelle(bool $HediyeIpucuGosterGuncelle): void{
			$this->HediyeIpucuGosterGuncelle = $HediyeIpucuGosterGuncelle;
		}

		public function get_hediye_ipucu_goster_guncelle(): bool{
			return $this->HediyeIpucuGosterGuncelle;
		}

		public function set_ilgili_urun_resim_guncelle(bool $IlgiliUrunResimGuncelle): void{
			$this->IlgiliUrunResimGuncelle = $IlgiliUrunResimGuncelle;
		}

		public function get_ilgili_urun_resim_guncelle(): bool{
			return $this->IlgiliUrunResimGuncelle;
		}

		public function set_kargo_tipi_guncelle(bool $KargoTipiGuncelle): void{
			$this->KargoTipiGuncelle = $KargoTipiGuncelle;
		}

		public function get_kargo_tipi_guncelle(): bool{
			return $this->KargoTipiGuncelle;
		}

		public function set_kategori_guncelle(bool $KategoriGuncelle): void{
			$this->KategoriGuncelle = $KategoriGuncelle;
		}

		public function get_kategori_guncelle(): bool{
			return $this->KategoriGuncelle;
		}

		public function set_listede_goster_guncelle(bool $ListedeGosterGuncelle): void{
			$this->ListedeGosterGuncelle = $ListedeGosterGuncelle;
		}

		public function get_listede_goster_guncelle(): bool{
			return $this->ListedeGosterGuncelle;
		}

		public function set_maks_taksit_sayisi_guncelle(bool $MaksTaksitSayisiGuncelle): void{
			$this->MaksTaksitSayisiGuncelle = $MaksTaksitSayisiGuncelle;
		}

		public function get_maks_taksit_sayisi_guncelle(): bool{
			return $this->MaksTaksitSayisiGuncelle;
		}

		public function set_marka_guncelle(bool $MarkaGuncelle): void{
			$this->MarkaGuncelle = $MarkaGuncelle;
		}

		public function get_marka_guncelle(): bool{
			return $this->MarkaGuncelle;
		}

		public function set_market_place_aktif_2_guncelle(bool $MarketPlaceAktif2Guncelle): void{
			$this->MarketPlaceAktif2Guncelle = $MarketPlaceAktif2Guncelle;
		}

		public function get_market_place_aktif_2_guncelle(): bool{
			return $this->MarketPlaceAktif2Guncelle;
		}

		public function set_market_place_aktif_3_guncelle(bool $MarketPlaceAktif3Guncelle): void{
			$this->MarketPlaceAktif3Guncelle = $MarketPlaceAktif3Guncelle;
		}

		public function get_market_place_aktif_3_guncelle(): bool{
			return $this->MarketPlaceAktif3Guncelle;
		}

		public function set_market_place_aktif_4_guncelle(bool $MarketPlaceAktif4Guncelle): void{
			$this->MarketPlaceAktif4Guncelle = $MarketPlaceAktif4Guncelle;
		}

		public function get_market_place_aktif_4_guncelle(): bool{
			return $this->MarketPlaceAktif4Guncelle;
		}

		public function set_market_place_aktif_5_guncelle(bool $MarketPlaceAktif5Guncelle): void{
			$this->MarketPlaceAktif5Guncelle = $MarketPlaceAktif5Guncelle;
		}

		public function get_market_place_aktif_5_guncelle(): bool{
			return $this->MarketPlaceAktif5Guncelle;
		}

		public function set_market_place_aktif_guncelle(bool $MarketPlaceAktifGuncelle): void{
			$this->MarketPlaceAktifGuncelle = $MarketPlaceAktifGuncelle;
		}

		public function get_market_place_aktif_guncelle(): bool{
			return $this->MarketPlaceAktifGuncelle;
		}

		public function set_market_place_ayar_guncelle(bool $MarketPlaceAyarGuncelle): void{
			$this->MarketPlaceAyarGuncelle = $MarketPlaceAyarGuncelle;
		}

		public function get_market_place_ayar_guncelle(): bool{
			return $this->MarketPlaceAyarGuncelle;
		}

		public function set_mensei_ulke_guncelle(bool $MenseiUlkeGuncelle): void{
			$this->MenseiUlkeGuncelle = $MenseiUlkeGuncelle;
		}

		public function get_mensei_ulke_guncelle(): bool{
			return $this->MenseiUlkeGuncelle;
		}

		public function set_mobil_ozel_alan_guncelle(bool $MobilOzelAlanGuncelle): void{
			$this->MobilOzelAlanGuncelle = $MobilOzelAlanGuncelle;
		}

		public function get_mobil_ozel_alan_guncelle(): bool{
			return $this->MobilOzelAlanGuncelle;
		}

		public function set_on_yazi_guncelle(bool $OnYaziGuncelle): void{
			$this->OnYaziGuncelle = $OnYaziGuncelle;
		}

		public function get_on_yazi_guncelle(): bool{
			return $this->OnYaziGuncelle;
		}

		public function set_onceki_kategori_eslestirmelerini_temizle(bool $OncekiKategoriEslestirmeleriniTemizle): void{
			$this->OncekiKategoriEslestirmeleriniTemizle = $OncekiKategoriEslestirmeleriniTemizle;
		}

		public function get_onceki_kategori_eslestirmelerini_temizle(): bool{
			return $this->OncekiKategoriEslestirmeleriniTemizle;
		}

		public function set_onceki_resimleri_sil(bool $OncekiResimleriSil): void{
			$this->OncekiResimleriSil = $OncekiResimleriSil;
		}

		public function get_onceki_resimleri_sil(): bool{
			return $this->OncekiResimleriSil;
		}

		public function set_ozel_alan_1_guncelle(bool $OzelAlan1Guncelle): void{
			$this->OzelAlan1Guncelle = $OzelAlan1Guncelle;
		}

		public function get_ozel_alan_1_guncelle(): bool{
			return $this->OzelAlan1Guncelle;
		}

		public function set_ozel_alan_2_guncelle(bool $OzelAlan2Guncelle): void{
			$this->OzelAlan2Guncelle = $OzelAlan2Guncelle;
		}

		public function get_ozel_alan_2_guncelle(): bool{
			return $this->OzelAlan2Guncelle;
		}

		public function set_ozel_alan_3_guncelle(bool $OzelAlan3Guncelle): void{
			$this->OzelAlan3Guncelle = $OzelAlan3Guncelle;
		}

		public function get_ozel_alan_3_guncelle(): bool{
			return $this->OzelAlan3Guncelle;
		}

		public function set_ozel_alan_4_guncelle(bool $OzelAlan4Guncelle): void{
			$this->OzelAlan4Guncelle = $OzelAlan4Guncelle;
		}

		public function get_ozel_alan_4_guncelle(): bool{
			return $this->OzelAlan4Guncelle;
		}

		public function set_ozel_alan_5_guncelle(bool $OzelAlan5Guncelle): void{
			$this->OzelAlan5Guncelle = $OzelAlan5Guncelle;
		}

		public function get_ozel_alan_5_guncelle(): bool{
			return $this->OzelAlan5Guncelle;
		}

		public function set_para_puan_guncelle(bool $ParaPuanGuncelle): void{
			$this->ParaPuanGuncelle = $ParaPuanGuncelle;
		}

		public function get_para_puan_guncelle(): bool{
			return $this->ParaPuanGuncelle;
		}

		public function set_puan_kullanimi_iptal_aktif_guncelle(bool $PuanKullanimiIptalAktifGuncelle): void{
			$this->PuanKullanimiIptalAktifGuncelle = $PuanKullanimiIptalAktifGuncelle;
		}

		public function get_puan_kullanimi_iptal_aktif_guncelle(): bool{
			return $this->PuanKullanimiIptalAktifGuncelle;
		}

		public function set_renk_kodu_guncelle(bool $RenkKoduGuncelle): void{
			$this->RenkKoduGuncelle = $RenkKoduGuncelle;
		}

		public function get_renk_kodu_guncelle(): bool{
			return $this->RenkKoduGuncelle;
		}

		public function set_resim_olmayanlara_resim_ekle(bool $ResimOlmayanlaraResimEkle): void{
			$this->ResimOlmayanlaraResimEkle = $ResimOlmayanlaraResimEkle;
		}

		public function get_resim_olmayanlara_resim_ekle(): bool{
			return $this->ResimOlmayanlaraResimEkle;
		}

		public function set_resimleri_indirme(bool $ResimleriIndirme): void{
			$this->ResimleriIndirme = $ResimleriIndirme;
		}

		public function get_resimleri_indirme(): bool{
			return $this->ResimleriIndirme;
		}

		public function set_satget_birimi_guncelle(bool $SatisBirimiGuncelle): void{
			$this->SatisBirimiGuncelle = $SatisBirimiGuncelle;
		}

		public function get_satget_birimi_guncelle(): bool{
			return $this->SatisBirimiGuncelle;
		}

		public function set_seo_anahtar_kelime_guncelle(bool $SeoAnahtarKelimeGuncelle): void{
			$this->SeoAnahtarKelimeGuncelle = $SeoAnahtarKelimeGuncelle;
		}

		public function get_seo_anahtar_kelime_guncelle(): bool{
			return $this->SeoAnahtarKelimeGuncelle;
		}

		public function set_seo_no_follow_guncelle(bool $SeoNoFollowGuncelle): void{
			$this->SeoNoFollowGuncelle = $SeoNoFollowGuncelle;
		}

		public function get_seo_no_follow_guncelle(): bool{
			return $this->SeoNoFollowGuncelle;
		}

		public function set_seo_no_index_guncelle(bool $SeoNoIndexGuncelle): void{
			$this->SeoNoIndexGuncelle = $SeoNoIndexGuncelle;
		}

		public function get_seo_no_index_guncelle(): bool{
			return $this->SeoNoIndexGuncelle;
		}

		public function set_seo_sayfa_aciklama_guncelle(bool $SeoSayfaAciklamaGuncelle): void{
			$this->SeoSayfaAciklamaGuncelle = $SeoSayfaAciklamaGuncelle;
		}

		public function get_seo_sayfa_aciklama_guncelle(): bool{
			return $this->SeoSayfaAciklamaGuncelle;
		}

		public function set_seo_sayfa_baslik_guncelle(bool $SeoSayfaBaslikGuncelle): void{
			$this->SeoSayfaBaslikGuncelle = $SeoSayfaBaslikGuncelle;
		}

		public function get_seo_sayfa_baslik_guncelle(): bool{
			return $this->SeoSayfaBaslikGuncelle;
		}

		public function set_sepette_ucretsiz_kargo_guncelle(bool $SepetteUcretsizKargoGuncelle): void{
			$this->SepetteUcretsizKargoGuncelle = $SepetteUcretsizKargoGuncelle;
		}

		public function get_sepette_ucretsiz_kargo_guncelle(): bool{
			return $this->SepetteUcretsizKargoGuncelle;
		}

		public function set_tahmini_teslim_suresi_goster_guncelle(bool $TahminiTeslimSuresiGosterGuncelle): void{
			$this->TahminiTeslimSuresiGosterGuncelle = $TahminiTeslimSuresiGosterGuncelle;
		}

		public function get_tahmini_teslim_suresi_goster_guncelle(): bool{
			return $this->TahminiTeslimSuresiGosterGuncelle;
		}

		public function set_tahmini_teslim_suresi_guncelle(bool $TahminiTeslimSuresiGuncelle): void{
			$this->TahminiTeslimSuresiGuncelle = $TahminiTeslimSuresiGuncelle;
		}

		public function get_tahmini_teslim_suresi_guncelle(): bool{
			return $this->TahminiTeslimSuresiGuncelle;
		}

		public function set_tahmini_teslim_suresi_tarih_guncelle(bool $TahminiTeslimSuresiTarihGuncelle): void{
			$this->TahminiTeslimSuresiTarihGuncelle = $TahminiTeslimSuresiTarihGuncelle;
		}

		public function get_tahmini_teslim_suresi_tarih_guncelle(): bool{
			return $this->TahminiTeslimSuresiTarihGuncelle;
		}

		public function set_tedarikci_guncelle(bool $TedarikciGuncelle): void{
			$this->TedarikciGuncelle = $TedarikciGuncelle;
		}

		public function get_tedarikci_guncelle(): bool{
			return $this->TedarikciGuncelle;
		}

		public function set_tedarikci_kodu_2_gore_guncelle(bool $TedarikciKodu2GoreGuncelle): void{
			$this->TedarikciKodu2GoreGuncelle = $TedarikciKodu2GoreGuncelle;
		}

		public function get_tedarikci_kodu_2_gore_guncelle(): bool{
			return $this->TedarikciKodu2GoreGuncelle;
		}

		public function set_tedarikci_koduna_gore_guncelle(bool $TedarikciKodunaGoreGuncelle): void{
			$this->TedarikciKodunaGoreGuncelle = $TedarikciKodunaGoreGuncelle;
		}

		public function get_tedarikci_koduna_gore_guncelle(): bool{
			return $this->TedarikciKodunaGoreGuncelle;
		}

		public function set_tedarikci_komisyon_guncelle(bool $TedarikciKomisyonGuncelle): void{
			$this->TedarikciKomisyonGuncelle = $TedarikciKomisyonGuncelle;
		}

		public function get_tedarikci_komisyon_guncelle(): bool{
			return $this->TedarikciKomisyonGuncelle;
		}

		public function set_teknik_detay_guncelle(bool $TeknikDetayGuncelle): void{
			$this->TeknikDetayGuncelle = $TeknikDetayGuncelle;
		}

		public function get_teknik_detay_guncelle(): bool{
			return $this->TeknikDetayGuncelle;
		}

		public function set_tum_varyasyonlar_stok_dusur_guncelle(bool $TumVaryasyonlarStokDusurGuncelle): void{
			$this->TumVaryasyonlarStokDusurGuncelle = $TumVaryasyonlarStokDusurGuncelle;
		}

		public function get_tum_varyasyonlar_stok_dusur_guncelle(): bool{
			return $this->TumVaryasyonlarStokDusurGuncelle;
		}

		public function set_ucretsiz_kargo_guncelle(bool $UcretsizKargoGuncelle): void{
			$this->UcretsizKargoGuncelle = $UcretsizKargoGuncelle;
		}

		public function get_ucretsiz_kargo_guncelle(): bool{
			return $this->UcretsizKargoGuncelle;
		}

		public function set_urun_adedi_kademe_deger_guncelle(bool $UrunAdediKademeDegerGuncelle): void{
			$this->UrunAdediKademeDegerGuncelle = $UrunAdediKademeDegerGuncelle;
		}

		public function get_urun_adedi_kademe_deger_guncelle(): bool{
			return $this->UrunAdediKademeDegerGuncelle;
		}

		public function set_urun_adedi_minimum_deger_guncelle(bool $UrunAdediMinimumDegerGuncelle): void{
			$this->UrunAdediMinimumDegerGuncelle = $UrunAdediMinimumDegerGuncelle;
		}

		public function get_urun_adedi_minimum_deger_guncelle(): bool{
			return $this->UrunAdediMinimumDegerGuncelle;
		}

		public function set_urun_adedi_ondalikli_sayi_girilebilir_guncelle(bool $UrunAdediOndalikliSayiGirilebilirGuncelle): void{
			$this->UrunAdediOndalikliSayiGirilebilirGuncelle = $UrunAdediOndalikliSayiGirilebilirGuncelle;
		}

		public function get_urun_adedi_ondalikli_sayi_girilebilir_guncelle(): bool{
			return $this->UrunAdediOndalikliSayiGirilebilirGuncelle;
		}

		public function set_urun_adedi_varsayilan_deger_guncelle(bool $UrunAdediVarsayilanDegerGuncelle): void{
			$this->UrunAdediVarsayilanDegerGuncelle = $UrunAdediVarsayilanDegerGuncelle;
		}

		public function get_urun_adedi_varsayilan_deger_guncelle(): bool{
			return $this->UrunAdediVarsayilanDegerGuncelle;
		}

		public function set_urun_adi_guncelle(bool $UrunAdiGuncelle): void{
			$this->UrunAdiGuncelle = $UrunAdiGuncelle;
		}

		public function get_urun_adi_guncelle(): bool{
			return $this->UrunAdiGuncelle;
		}

		public function set_urun_adresini_elle_olustur(bool $UrunAdresiniElleOlustur): void{
			$this->UrunAdresiniElleOlustur = $UrunAdresiniElleOlustur;
		}

		public function get_urun_adresini_elle_olustur(): bool{
			return $this->UrunAdresiniElleOlustur;
		}

		public function set_urun_kapasite_guncelle(bool $UrunKapasiteGuncelle): void{
			$this->UrunKapasiteGuncelle = $UrunKapasiteGuncelle;
		}

		public function get_urun_kapasite_guncelle(): bool{
			return $this->UrunKapasiteGuncelle;
		}

		public function set_urun_kapida_odeme_yasakli_guncelle(bool $UrunKapidaOdemeYasakliGuncelle): void{
			$this->UrunKapidaOdemeYasakliGuncelle = $UrunKapidaOdemeYasakliGuncelle;
		}

		public function get_urun_kapida_odeme_yasakli_guncelle(): bool{
			return $this->UrunKapidaOdemeYasakliGuncelle;
		}

		public function set_urun_resim_guncelle(bool $UrunResimGuncelle): void{
			$this->UrunResimGuncelle = $UrunResimGuncelle;
		}

		public function get_urun_resim_guncelle(): bool{
			return $this->UrunResimGuncelle;
		}

		public function set_urun_tipi_guncelle(bool $UrunTipiGuncelle): void{
			$this->UrunTipiGuncelle = $UrunTipiGuncelle;
		}

		public function get_urun_tipi_guncelle(): bool{
			return $this->UrunTipiGuncelle;
		}

		public function set_user_agent(bool $UserAgent): void{
			$this->UserAgent = $UserAgent;
		}

		public function get_user_agent(): bool{
			return $this->UserAgent;
		}

		public function set_uye_alim_maks_guncelle(bool $UyeAlimMaksGuncelle): void{
			$this->UyeAlimMaksGuncelle = $UyeAlimMaksGuncelle;
		}

		public function get_uye_alim_maks_guncelle(): bool{
			return $this->UyeAlimMaksGuncelle;
		}

		public function set_uye_alim_min_guncelle(bool $UyeAlimMinGuncelle): void{
			$this->UyeAlimMinGuncelle = $UyeAlimMinGuncelle;
		}

		public function get_uye_alim_min_guncelle(): bool{
			return $this->UyeAlimMinGuncelle;
		}

		public function set_vergi_istisna_kodu_guncelle(bool $VergiIstisnaKoduGuncelle): void{
			$this->VergiIstisnaKoduGuncelle = $VergiIstisnaKoduGuncelle;
		}

		public function get_vergi_istisna_kodu_guncelle(): bool{
			return $this->VergiIstisnaKoduGuncelle;
		}

		public function set_vitrin_guncelle(bool $VitrinGuncelle): void{
			$this->VitrinGuncelle = $VitrinGuncelle;
		}

		public function get_vitrin_guncelle(): bool{
			return $this->VitrinGuncelle;
		}

		public function set_vitrin_sira_sabit_guncelle(bool $VitrinSiraSabitGuncelle): void{
			$this->VitrinSiraSabitGuncelle = $VitrinSiraSabitGuncelle;
		}

		public function get_vitrin_sira_sabit_guncelle(): bool{
			return $this->VitrinSiraSabitGuncelle;
		}

		public function set_yayin_tarihi_guncelle(bool $YayinTarihiGuncelle): void{
			$this->YayinTarihiGuncelle = $YayinTarihiGuncelle;
		}

		public function get_yayin_tarihi_guncelle(): bool{
			return $this->YayinTarihiGuncelle;
		}

		public function set_yeni_urun_guncelle(bool $YeniUrunGuncelle): void{
			$this->YeniUrunGuncelle = $YeniUrunGuncelle;
		}

		public function get_yeni_urun_guncelle(): bool{
			return $this->YeniUrunGuncelle;
		}

		public function to_array(): array{
			return [
//				'AciklamaGuncelle'                          => $this->AciklamaGuncelle,
//				'AdwordsAciklamaGuncelle'                   => $this->AdwordsAciklamaGuncelle,
//				'AdwordsKategoriGuncelle'                   => $this->AdwordsKategoriGuncelle,
//				'AdwordsTipGuncelle'                        => $this->AdwordsTipGuncelle,
				'AktifGuncelle'                             => $this->AktifGuncelle,
//				'AktifPazaryeriListGuncelle'                => $this->AktifPazaryeriListGuncelle,
//				'AlanAdi'                                   => $this->AlanAdi ?? '',
//				'AnaKategoriId'                             => $this->AnaKategoriId ?? 0,
//				'AramaAnahtarKelimeGuncelle'                => $this->AramaAnahtarKelimeGuncelle,
//				'AsortiGrupGuncelle'                        => $this->AsortiGrupGuncelle,
//				'Base64Resim'                               => $this->Base64Resim,
//				'DegerTanim'                                => $this->DegerTanim ?? '',
//				'EntegrasyonKodu'                           => $this->EntegrasyonKodu ?? '',
//				'EtiketGuncelle'                            => $this->EtiketGuncelle,
//				'FBStoreGosterGuncelle'                     => $this->FBStoreGosterGuncelle,
//				'FirsatUrunuGuncelle'                       => $this->FirsatUrunuGuncelle,
//				'HediyeIpucuGosterGuncelle'                 => $this->HediyeIpucuGosterGuncelle,
//				'IlgiliUrunResimGuncelle'                   => $this->IlgiliUrunResimGuncelle,
//				'KargoTipiGuncelle'                         => $this->KargoTipiGuncelle,
				'KategoriGuncelle'                          => $this->KategoriGuncelle,
//				'ListedeGosterGuncelle'                     => $this->ListedeGosterGuncelle,
//				'MaksTaksitSayisiGuncelle'                  => $this->MaksTaksitSayisiGuncelle,
				'MarkaGuncelle'                             => $this->MarkaGuncelle,
//				'MarketPlaceAktif2Guncelle'                 => $this->MarketPlaceAktif2Guncelle,
//				'MarketPlaceAktif3Guncelle'                 => $this->MarketPlaceAktif3Guncelle,
//				'MarketPlaceAktif4Guncelle'                 => $this->MarketPlaceAktif4Guncelle,
//				'MarketPlaceAktif5Guncelle'                 => $this->MarketPlaceAktif5Guncelle,
//				'MarketPlaceAktifGuncelle'                  => $this->MarketPlaceAktifGuncelle,
//				'MarketPlaceAyarGuncelle'                   => $this->MarketPlaceAyarGuncelle,
//				'MenseiUlkeGuncelle'                        => $this->MenseiUlkeGuncelle,
//				'MobilOzelAlanGuncelle'                     => $this->MobilOzelAlanGuncelle,
//				'OnYaziGuncelle'                            => $this->OnYaziGuncelle,
//				'OncekiKategoriEslestirmeleriniTemizle'     => $this->OncekiKategoriEslestirmeleriniTemizle,
//				'OncekiResimleriSil'                        => $this->OncekiResimleriSil,
//				'OzelAlan1Guncelle'                         => $this->OzelAlan1Guncelle,
//				'OzelAlan2Guncelle'                         => $this->OzelAlan2Guncelle,
//				'OzelAlan3Guncelle'                         => $this->OzelAlan3Guncelle,
//				'OzelAlan4Guncelle'                         => $this->OzelAlan4Guncelle,
//				'OzelAlan5Guncelle'                         => $this->OzelAlan5Guncelle,
//				'ParaPuanGuncelle'                          => $this->ParaPuanGuncelle,
//				'PuanKullanimiIptalAktifGuncelle'           => $this->PuanKullanimiIptalAktifGuncelle,
//				'RenkKoduGuncelle'                          => $this->RenkKoduGuncelle,
//				'ResimOlmayanlaraResimEkle'                 => $this->ResimOlmayanlaraResimEkle,
//				'ResimleriIndirme'                          => $this->ResimleriIndirme,
//				'SatisBirimiGuncelle'                       => $this->SatisBirimiGuncelle,
//				'SeoAnahtarKelimeGuncelle'                  => $this->SeoAnahtarKelimeGuncelle,
//				'SeoNoFollowGuncelle'                       => $this->SeoNoFollowGuncelle,
//				'SeoNoIndexGuncelle'                        => $this->SeoNoIndexGuncelle,
//				'SeoSayfaAciklamaGuncelle'                  => $this->SeoSayfaAciklamaGuncelle,
//				'SeoSayfaBaslikGuncelle'                    => $this->SeoSayfaBaslikGuncelle,
//				'SepetteUcretsizKargoGuncelle'              => $this->SepetteUcretsizKargoGuncelle,
//				'TahminiTeslimSuresiGosterGuncelle'         => $this->TahminiTeslimSuresiGosterGuncelle,
//				'TahminiTeslimSuresiGuncelle'               => $this->TahminiTeslimSuresiGuncelle,
//				'TahminiTeslimSuresiTarihGuncelle'          => $this->TahminiTeslimSuresiTarihGuncelle,
				'TedarikciGuncelle'                         => $this->TedarikciGuncelle,
//				'TedarikciKodu2GoreGuncelle'                => $this->TedarikciKodu2GoreGuncelle,
//				'TedarikciKodunaGoreGuncelle'               => $this->TedarikciKodunaGoreGuncelle,
//				'TedarikciKomisyonGuncelle'                 => $this->TedarikciKomisyonGuncelle,
//				'TeknikDetayGuncelle'                       => $this->TeknikDetayGuncelle,
//				'TumVaryasyonlarStokDusurGuncelle'          => $this->TumVaryasyonlarStokDusurGuncelle,
//				'UcretsizKargoGuncelle'                     => $this->UcretsizKargoGuncelle,
//				'UrunAdediKademeDegerGuncelle'              => $this->UrunAdediKademeDegerGuncelle,
//				'UrunAdediMinimumDegerGuncelle'             => $this->UrunAdediMinimumDegerGuncelle,
//				'UrunAdediOndalikliSayiGirilebilirGuncelle' => $this->UrunAdediOndalikliSayiGirilebilirGuncelle,
//				'UrunAdediVarsayilanDegerGuncelle'          => $this->UrunAdediVarsayilanDegerGuncelle,
				'UrunAdiGuncelle'                           => $this->UrunAdiGuncelle,
//				'UrunAdresiniElleOlustur'                   => $this->UrunAdresiniElleOlustur,
//				'UrunKapasiteGuncelle'                      => $this->UrunKapasiteGuncelle,
//				'UrunKapidaOdemeYasakliGuncelle'            => $this->UrunKapidaOdemeYasakliGuncelle,
//				'UrunResimGuncelle'                         => $this->UrunResimGuncelle,
//				'UrunTipiGuncelle'                          => $this->UrunTipiGuncelle,
//				'UserAgent'                                 => $this->UserAgent,
//				'UyeAlimMaksGuncelle'                       => $this->UyeAlimMaksGuncelle,
//				'UyeAlimMinGuncelle'                        => $this->UyeAlimMinGuncelle,
//				'VergiIstisnaKoduGuncelle'                  => $this->VergiIstisnaKoduGuncelle,
//				'VitrinGuncelle'                            => $this->VitrinGuncelle,
//				'VitrinSiraSabitGuncelle'                   => $this->VitrinSiraSabitGuncelle,
//				'YayinTarihiGuncelle'                       => $this->YayinTarihiGuncelle,
//				'YeniUrunGuncelle'                          => $this->YeniUrunGuncelle,
			];
		}

	}