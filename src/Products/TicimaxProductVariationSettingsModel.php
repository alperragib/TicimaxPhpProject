<?php

namespace AlperRagib\Ticimax\Products;

class TicimaxProductVariationSettingsModel
{

	public $AktifGuncelle                      = true;
	public $AlisFiyatiGuncelle                 = true;
	public $BarkodGuncelle                     = true;
	public $EkSecenekGuncelle                  = true;
	public $EksiStokAdediGuncelle              = true;
	public $FiyatTipleriGuncelle               = true;
	public $GtipKoduGuncelle                   = true;
	public $IndirimliFiyatiGuncelle            = true;
	public $IscilikAgirlikGuncelle             = true;
	public $IscilikParaBirimiGuncelle          = true;
	public $KargoAgirligiGuncelle              = true;
	public $KargoAgirligiYurtDisiGuncelle      = true;
	public $KargoUcretiGuncelle                = true;
	public $KdvDahilGuncelle                   = true;
	public $KdvOraniGuncelle                   = true;
	public $KonsinyeUrunStokAdediGuncelle      = true;
	public $OncekiResimleriSil                 = true;
	public $ParaBirimiGuncelle                 = true;
	public $PiyasaFiyatiGuncelle               = true;
	public $ResimOlmayanlaraResimEkle          = true;
	public $SatisFiyatiGuncelle                = true;
	public $StokAdediGuncelle                  = true;
	public $StokKoduGuncelle                   = true;
	public $TahminiTeslimSuresiAyniGunGuncelle = true;
	public $TahminiTeslimSuresiGosterGuncelle  = true;
	public $TahminiTeslimSuresiGuncelle        = true;
	public $TahminiTeslimSuresiTarihGuncelle   = true;
	public $TedarikciKodunaGoreGuncelle        = true;
	public $TedarikciKomisyoniGuncelle         = true;
	public $UpdateKeyGuncelle                  = true;
	public $UrunAgirligiGuncelle               = true;
	public $UrunDerinlikGuncelle               = true;
	public $UrunGenislikGuncelle               = true;
	public $UrunResimGuncelle                  = true;
	public $UrunYukseklikGuncelle              = true;
	public $UyeAlimMaxGuncelle                 = true;
	public $UyeAlimMinGuncelle                 = true;
	public $UyeTipiFiyat1Guncelle              = true;
	public $UyeTipiFiyat2Guncelle              = true;
	public $UyeTipiFiyat3Guncelle              = true;
	public $UyeTipiFiyat4Guncelle              = true;
	public $UyeTipiFiyat5Guncelle              = true;
	public $UyeTipiFiyat6Guncelle              = true;
	public $UyeTipiFiyat7Guncelle              = true;
	public $UyeTipiFiyat8Guncelle              = true;
	public $UyeTipiFiyat9Guncelle              = true;
	public $UyeTipiFiyat10Guncelle             = true;
	public $UyeTipiFiyat11Guncelle             = true;
	public $UyeTipiFiyat12Guncelle             = true;
	public $UyeTipiFiyat13Guncelle             = true;
	public $UyeTipiFiyat14Guncelle             = true;
	public $UyeTipiFiyat15Guncelle             = true;
	public $UyeTipiFiyat16Guncelle             = true;
	public $UyeTipiFiyat17Guncelle             = true;
	public $UyeTipiFiyat18Guncelle             = true;
	public $UyeTipiFiyat19Guncelle             = true;
	public $UyeTipiFiyat20Guncelle             = true;

	public function set_aktif_guncelle(bool $AktifGuncelle): void
	{
		$this->AktifGuncelle = $AktifGuncelle;
	}

	public function is_aktif_guncelle(): bool
	{
		return $this->AktifGuncelle;
	}

	public function set_alis_fiyati_guncelle(bool $AlisFiyatiGuncelle): void
	{
		$this->AlisFiyatiGuncelle = $AlisFiyatiGuncelle;
	}

	public function is_alis_fiyati_guncelle(): bool
	{
		return $this->AlisFiyatiGuncelle;
	}

	public function set_barkod_guncelle(bool $BarkodGuncelle): void
	{
		$this->BarkodGuncelle = $BarkodGuncelle;
	}

	public function is_barkod_guncelle(): bool
	{
		return $this->BarkodGuncelle;
	}

	public function set_ek_secenek_guncelle(bool $EkSecenekGuncelle): void
	{
		$this->EkSecenekGuncelle = $EkSecenekGuncelle;
	}

	public function is_ek_secenek_guncelle(): bool
	{
		return $this->EkSecenekGuncelle;
	}

	public function set_eksi_stok_adedi_guncelle(bool $EksiStokAdediGuncelle): void
	{
		$this->EksiStokAdediGuncelle = $EksiStokAdediGuncelle;
	}

	public function is_eksi_stok_adedi_guncelle(): bool
	{
		return $this->EksiStokAdediGuncelle;
	}

	public function set_fiyat_tipleri_guncelle(bool $FiyatTipleriGuncelle): void
	{
		$this->FiyatTipleriGuncelle = $FiyatTipleriGuncelle;
	}

	public function is_fiyat_tipleri_guncelle(): bool
	{
		return $this->FiyatTipleriGuncelle;
	}

	public function set_gtip_kodu_guncelle(bool $GtipKoduGuncelle): void
	{
		$this->GtipKoduGuncelle = $GtipKoduGuncelle;
	}

	public function is_gtip_kodu_guncelle(): bool
	{
		return $this->GtipKoduGuncelle;
	}

	public function set_indirimli_fiyati_guncelle(bool $IndirimliFiyatiGuncelle): void
	{
		$this->IndirimliFiyatiGuncelle = $IndirimliFiyatiGuncelle;
	}

	public function is_indirimli_fiyati_guncelle(): bool
	{
		return $this->IndirimliFiyatiGuncelle;
	}

	public function set_iscilik_agirlik_guncelle(bool $IscilikAgirlikGuncelle): void
	{
		$this->IscilikAgirlikGuncelle = $IscilikAgirlikGuncelle;
	}

	public function is_iscilik_agirlik_guncelle(): bool
	{
		return $this->IscilikAgirlikGuncelle;
	}

	public function set_iscilik_para_birimi_guncelle(bool $IscilikParaBirimiGuncelle): void
	{
		$this->IscilikParaBirimiGuncelle = $IscilikParaBirimiGuncelle;
	}

	public function is_iscilik_para_birimi_guncelle(): bool
	{
		return $this->IscilikParaBirimiGuncelle;
	}

	public function set_kargo_agirligi_guncelle(bool $KargoAgirligiGuncelle): void
	{
		$this->KargoAgirligiGuncelle = $KargoAgirligiGuncelle;
	}

	public function is_kargo_agirligi_guncelle(): bool
	{
		return $this->KargoAgirligiGuncelle;
	}

	public function set_kargo_agirligi_yurt_disi_guncelle(bool $KargoAgirligiYurtDisiGuncelle): void
	{
		$this->KargoAgirligiYurtDisiGuncelle = $KargoAgirligiYurtDisiGuncelle;
	}

	public function is_kargo_agirligi_yurt_disi_guncelle(): bool
	{
		return $this->KargoAgirligiYurtDisiGuncelle;
	}

	public function set_kargo_ucreti_guncelle(bool $KargoUcretiGuncelle): void
	{
		$this->KargoUcretiGuncelle = $KargoUcretiGuncelle;
	}

	public function is_kargo_ucreti_guncelle(): bool
	{
		return $this->KargoUcretiGuncelle;
	}

	public function set_kdv_dahil_guncelle(bool $KdvDahilGuncelle): void
	{
		$this->KdvDahilGuncelle = $KdvDahilGuncelle;
	}

	public function is_kdv_dahil_guncelle(): bool
	{
		return $this->KdvDahilGuncelle;
	}

	public function set_kdv_orani_guncelle(bool $KdvOraniGuncelle): void
	{
		$this->KdvOraniGuncelle = $KdvOraniGuncelle;
	}

	public function is_kdv_orani_guncelle(): bool
	{
		return $this->KdvOraniGuncelle;
	}

	public function set_konsinye_urun_stok_adedi_guncelle(bool $KonsinyeUrunStokAdediGuncelle): void
	{
		$this->KonsinyeUrunStokAdediGuncelle = $KonsinyeUrunStokAdediGuncelle;
	}

	public function is_konsinye_urun_stok_adedi_guncelle(): bool
	{
		return $this->KonsinyeUrunStokAdediGuncelle;
	}

	public function set_onceki_resimleri_sil(bool $OncekiResimleriSil): void
	{
		$this->OncekiResimleriSil = $OncekiResimleriSil;
	}

	public function is_onceki_resimleri_sil(): bool
	{
		return $this->OncekiResimleriSil;
	}

	public function set_para_birimi_guncelle(bool $ParaBirimiGuncelle): void
	{
		$this->ParaBirimiGuncelle = $ParaBirimiGuncelle;
	}

	public function is_para_birimi_guncelle(): bool
	{
		return $this->ParaBirimiGuncelle;
	}

	public function set_piyasa_fiyati_guncelle(bool $PiyasaFiyatiGuncelle): void
	{
		$this->PiyasaFiyatiGuncelle = $PiyasaFiyatiGuncelle;
	}

	public function is_piyasa_fiyati_guncelle(): bool
	{
		return $this->PiyasaFiyatiGuncelle;
	}

	public function set_resim_olmayanlara_resim_ekle(bool $ResimOlmayanlaraResimEkle): void
	{
		$this->ResimOlmayanlaraResimEkle = $ResimOlmayanlaraResimEkle;
	}

	public function is_resim_olmayanlara_resim_ekle(): bool
	{
		return $this->ResimOlmayanlaraResimEkle;
	}

	public function set_satis_fiyati_guncelle(bool $SatisFiyatiGuncelle): void
	{
		$this->SatisFiyatiGuncelle = $SatisFiyatiGuncelle;
	}

	public function is_satis_fiyati_guncelle(): bool
	{
		return $this->SatisFiyatiGuncelle;
	}

	public function set_stok_adedi_guncelle(bool $StokAdediGuncelle): void
	{
		$this->StokAdediGuncelle = $StokAdediGuncelle;
	}

	public function is_stok_adedi_guncelle(): bool
	{
		return $this->StokAdediGuncelle;
	}

	public function set_stok_kodu_guncelle(bool $StokKoduGuncelle): void
	{
		$this->StokKoduGuncelle = $StokKoduGuncelle;
	}

	public function is_stok_kodu_guncelle(): bool
	{
		return $this->StokKoduGuncelle;
	}

	public function set_tahmini_teslim_suresi_ayni_gun_guncelle(bool $TahminiTeslimSuresiAyniGunGuncelle): void
	{
		$this->TahminiTeslimSuresiAyniGunGuncelle = $TahminiTeslimSuresiAyniGunGuncelle;
	}

	public function is_tahmini_teslim_suresi_ayni_gun_guncelle(): bool
	{
		return $this->TahminiTeslimSuresiAyniGunGuncelle;
	}

	public function set_tahmini_teslim_suresi_goster_guncelle(bool $TahminiTeslimSuresiGosterGuncelle): void
	{
		$this->TahminiTeslimSuresiGosterGuncelle = $TahminiTeslimSuresiGosterGuncelle;
	}

	public function is_tahmini_teslim_suresi_goster_guncelle(): bool
	{
		return $this->TahminiTeslimSuresiGosterGuncelle;
	}

	public function set_tahmini_teslim_suresi_guncelle(bool $TahminiTeslimSuresiGuncelle): void
	{
		$this->TahminiTeslimSuresiGuncelle = $TahminiTeslimSuresiGuncelle;
	}

	public function is_tahmini_teslim_suresi_guncelle(): bool
	{
		return $this->TahminiTeslimSuresiGuncelle;
	}

	public function set_tahmini_teslim_suresi_tarih_guncelle(bool $TahminiTeslimSuresiTarihGuncelle): void
	{
		$this->TahminiTeslimSuresiTarihGuncelle = $TahminiTeslimSuresiTarihGuncelle;
	}

	public function is_tahmini_teslim_suresi_tarih_guncelle(): bool
	{
		return $this->TahminiTeslimSuresiTarihGuncelle;
	}

	public function set_tedarikci_koduna_gore_guncelle(bool $TedarikciKodunaGoreGuncelle): void
	{
		$this->TedarikciKodunaGoreGuncelle = $TedarikciKodunaGoreGuncelle;
	}

	public function is_tedarikci_koduna_gore_guncelle(): bool
	{
		return $this->TedarikciKodunaGoreGuncelle;
	}

	public function set_tedarikci_komisyoni_guncelle(bool $TedarikciKomisyoniGuncelle): void
	{
		$this->TedarikciKomisyoniGuncelle = $TedarikciKomisyoniGuncelle;
	}

	public function is_tedarikci_komisyoni_guncelle(): bool
	{
		return $this->TedarikciKomisyoniGuncelle;
	}

	public function set_update_key_guncelle(bool $UpdateKeyGuncelle): void
	{
		$this->UpdateKeyGuncelle = $UpdateKeyGuncelle;
	}

	public function is_update_key_guncelle(): bool
	{
		return $this->UpdateKeyGuncelle;
	}

	public function set_urun_agirligi_guncelle(bool $UrunAgirligiGuncelle): void
	{
		$this->UrunAgirligiGuncelle = $UrunAgirligiGuncelle;
	}

	public function is_urun_agirligi_guncelle(): bool
	{
		return $this->UrunAgirligiGuncelle;
	}

	public function set_urun_derinlik_guncelle(bool $UrunDerinlikGuncelle): void
	{
		$this->UrunDerinlikGuncelle = $UrunDerinlikGuncelle;
	}

	public function is_urun_derinlik_guncelle(): bool
	{
		return $this->UrunDerinlikGuncelle;
	}

	public function set_urun_genislik_guncelle(bool $UrunGenislikGuncelle): void
	{
		$this->UrunGenislikGuncelle = $UrunGenislikGuncelle;
	}

	public function is_urun_genislik_guncelle(): bool
	{
		return $this->UrunGenislikGuncelle;
	}

	public function set_urun_resim_guncelle(bool $UrunResimGuncelle): void
	{
		$this->UrunResimGuncelle = $UrunResimGuncelle;
	}

	public function is_urun_resim_guncelle(): bool
	{
		return $this->UrunResimGuncelle;
	}

	public function set_urun_yukseklik_guncelle(bool $UrunYukseklikGuncelle): void
	{
		$this->UrunYukseklikGuncelle = $UrunYukseklikGuncelle;
	}

	public function is_urun_yukseklik_guncelle(): bool
	{
		return $this->UrunYukseklikGuncelle;
	}

	public function set_uye_alim_max_guncelle(bool $UyeAlimMaxGuncelle): void
	{
		$this->UyeAlimMaxGuncelle = $UyeAlimMaxGuncelle;
	}

	public function is_uye_alim_max_guncelle(): bool
	{
		return $this->UyeAlimMaxGuncelle;
	}

	public function set_uye_alim_min_guncelle(bool $UyeAlimMinGuncelle): void
	{
		$this->UyeAlimMinGuncelle = $UyeAlimMinGuncelle;
	}

	public function is_uye_alim_min_guncelle(): bool
	{
		return $this->UyeAlimMinGuncelle;
	}

	public function set_uye_tipi_fiyat_10_guncelle(bool $UyeTipiFiyat10Guncelle): void
	{
		$this->UyeTipiFiyat10Guncelle = $UyeTipiFiyat10Guncelle;
	}

	public function is_uye_tipi_fiyat_10_guncelle(): bool
	{
		return $this->UyeTipiFiyat10Guncelle;
	}

	public function set_uye_tipi_fiyat_11_guncelle(bool $UyeTipiFiyat11Guncelle): void
	{
		$this->UyeTipiFiyat11Guncelle = $UyeTipiFiyat11Guncelle;
	}

	public function is_uye_tipi_fiyat_11_guncelle(): bool
	{
		return $this->UyeTipiFiyat11Guncelle;
	}

	public function set_uye_tipi_fiyat_12_guncelle(bool $UyeTipiFiyat12Guncelle): void
	{
		$this->UyeTipiFiyat12Guncelle = $UyeTipiFiyat12Guncelle;
	}

	public function is_uye_tipi_fiyat_12_guncelle(): bool
	{
		return $this->UyeTipiFiyat12Guncelle;
	}

	public function set_uye_tipi_fiyat_13_guncelle(bool $UyeTipiFiyat13Guncelle): void
	{
		$this->UyeTipiFiyat13Guncelle = $UyeTipiFiyat13Guncelle;
	}

	public function is_uye_tipi_fiyat_13_guncelle(): bool
	{
		return $this->UyeTipiFiyat13Guncelle;
	}

	public function set_uye_tipi_fiyat_14_guncelle(bool $UyeTipiFiyat14Guncelle): void
	{
		$this->UyeTipiFiyat14Guncelle = $UyeTipiFiyat14Guncelle;
	}

	public function is_uye_tipi_fiyat_14_guncelle(): bool
	{
		return $this->UyeTipiFiyat14Guncelle;
	}

	public function set_uye_tipi_fiyat_15_guncelle(bool $UyeTipiFiyat15Guncelle): void
	{
		$this->UyeTipiFiyat15Guncelle = $UyeTipiFiyat15Guncelle;
	}

	public function is_uye_tipi_fiyat_15_guncelle(): bool
	{
		return $this->UyeTipiFiyat15Guncelle;
	}

	public function set_uye_tipi_fiyat_16_guncelle(bool $UyeTipiFiyat16Guncelle): void
	{
		$this->UyeTipiFiyat16Guncelle = $UyeTipiFiyat16Guncelle;
	}

	public function is_uye_tipi_fiyat_16_guncelle(): bool
	{
		return $this->UyeTipiFiyat16Guncelle;
	}

	public function set_uye_tipi_fiyat_17_guncelle(bool $UyeTipiFiyat17Guncelle): void
	{
		$this->UyeTipiFiyat17Guncelle = $UyeTipiFiyat17Guncelle;
	}

	public function is_uye_tipi_fiyat_17_guncelle(): bool
	{
		return $this->UyeTipiFiyat17Guncelle;
	}

	public function set_uye_tipi_fiyat_18_guncelle(bool $UyeTipiFiyat18Guncelle): void
	{
		$this->UyeTipiFiyat18Guncelle = $UyeTipiFiyat18Guncelle;
	}

	public function is_uye_tipi_fiyat_18_guncelle(): bool
	{
		return $this->UyeTipiFiyat18Guncelle;
	}

	public function set_uye_tipi_fiyat_19_guncelle(bool $UyeTipiFiyat19Guncelle): void
	{
		$this->UyeTipiFiyat19Guncelle = $UyeTipiFiyat19Guncelle;
	}

	public function is_uye_tipi_fiyat_19_guncelle(): bool
	{
		return $this->UyeTipiFiyat19Guncelle;
	}

	public function set_uye_tipi_fiyat_1_guncelle(bool $UyeTipiFiyat1Guncelle): void
	{
		$this->UyeTipiFiyat1Guncelle = $UyeTipiFiyat1Guncelle;
	}

	public function is_uye_tipi_fiyat_1_guncelle(): bool
	{
		return $this->UyeTipiFiyat1Guncelle;
	}

	public function set_uye_tipi_fiyat_20_guncelle(bool $UyeTipiFiyat20Guncelle): void
	{
		$this->UyeTipiFiyat20Guncelle = $UyeTipiFiyat20Guncelle;
	}

	public function is_uye_tipi_fiyat_20_guncelle(): bool
	{
		return $this->UyeTipiFiyat20Guncelle;
	}

	public function set_uye_tipi_fiyat_2_guncelle(bool $UyeTipiFiyat2Guncelle): void
	{
		$this->UyeTipiFiyat2Guncelle = $UyeTipiFiyat2Guncelle;
	}

	public function is_uye_tipi_fiyat_2_guncelle(): bool
	{
		return $this->UyeTipiFiyat2Guncelle;
	}

	public function set_uye_tipi_fiyat_3_guncelle(bool $UyeTipiFiyat3Guncelle): void
	{
		$this->UyeTipiFiyat3Guncelle = $UyeTipiFiyat3Guncelle;
	}

	public function is_uye_tipi_fiyat_3_guncelle(): bool
	{
		return $this->UyeTipiFiyat3Guncelle;
	}

	public function set_uye_tipi_fiyat_4_guncelle(bool $UyeTipiFiyat4Guncelle): void
	{
		$this->UyeTipiFiyat4Guncelle = $UyeTipiFiyat4Guncelle;
	}

	public function is_uye_tipi_fiyat_4_guncelle(): bool
	{
		return $this->UyeTipiFiyat4Guncelle;
	}

	public function set_uye_tipi_fiyat_5_guncelle(bool $UyeTipiFiyat5Guncelle): void
	{
		$this->UyeTipiFiyat5Guncelle = $UyeTipiFiyat5Guncelle;
	}

	public function is_uye_tipi_fiyat_5_guncelle(): bool
	{
		return $this->UyeTipiFiyat5Guncelle;
	}

	public function set_uye_tipi_fiyat_6_guncelle(bool $UyeTipiFiyat6Guncelle): void
	{
		$this->UyeTipiFiyat6Guncelle = $UyeTipiFiyat6Guncelle;
	}

	public function is_uye_tipi_fiyat_6_guncelle(): bool
	{
		return $this->UyeTipiFiyat6Guncelle;
	}

	public function set_uye_tipi_fiyat_7_guncelle(bool $UyeTipiFiyat7Guncelle): void
	{
		$this->UyeTipiFiyat7Guncelle = $UyeTipiFiyat7Guncelle;
	}

	public function is_uye_tipi_fiyat_7_guncelle(): bool
	{
		return $this->UyeTipiFiyat7Guncelle;
	}

	public function set_uye_tipi_fiyat_8_guncelle(bool $UyeTipiFiyat8Guncelle): void
	{
		$this->UyeTipiFiyat8Guncelle = $UyeTipiFiyat8Guncelle;
	}

	public function is_uye_tipi_fiyat_8_guncelle(): bool
	{
		return $this->UyeTipiFiyat8Guncelle;
	}

	public function set_uye_tipi_fiyat_9_guncelle(bool $UyeTipiFiyat9Guncelle): void
	{
		$this->UyeTipiFiyat9Guncelle = $UyeTipiFiyat9Guncelle;
	}

	public function is_uye_tipi_fiyat_9_guncelle(): bool
	{
		return $this->UyeTipiFiyat9Guncelle;
	}

	public function to_array(): array
	{
		return [
			'AktifGuncelle'                      => $this->AktifGuncelle,
			'AlisFiyatiGuncelle'                 => $this->AlisFiyatiGuncelle,
			'BarkodGuncelle'                     => $this->BarkodGuncelle,
			'EkSecenekGuncelle'                  => $this->EkSecenekGuncelle,
			'EksiStokAdediGuncelle'              => $this->EksiStokAdediGuncelle,
			'FiyatTipleriGuncelle'               => $this->FiyatTipleriGuncelle,
			'GtipKoduGuncelle'                   => $this->GtipKoduGuncelle,
			'IndirimliFiyatiGuncelle'            => $this->IndirimliFiyatiGuncelle,
			'IscilikAgirlikGuncelle'             => $this->IscilikAgirlikGuncelle,
			'IscilikParaBirimiGuncelle'          => $this->IscilikParaBirimiGuncelle,
			'KargoAgirligiGuncelle'              => $this->KargoAgirligiGuncelle,
			'KargoAgirligiYurtDisiGuncelle'      => $this->KargoAgirligiYurtDisiGuncelle,
			'KargoUcretiGuncelle'                => $this->KargoUcretiGuncelle,
			'KdvDahilGuncelle'                   => $this->KdvDahilGuncelle,
			'KdvOraniGuncelle'                   => $this->KdvOraniGuncelle,
			'KonsinyeUrunStokAdediGuncelle'      => $this->KonsinyeUrunStokAdediGuncelle,
			'OncekiResimleriSil'                 => $this->OncekiResimleriSil,
			'ParaBirimiGuncelle'                 => $this->ParaBirimiGuncelle,
			'PiyasaFiyatiGuncelle'               => $this->PiyasaFiyatiGuncelle,
			'ResimOlmayanlaraResimEkle'          => $this->ResimOlmayanlaraResimEkle,
			'SatisFiyatiGuncelle'                => $this->SatisFiyatiGuncelle,
			'StokAdediGuncelle'                  => $this->StokAdediGuncelle,
			'StokKoduGuncelle'                   => $this->StokKoduGuncelle,
			'TahminiTeslimSuresiAyniGunGuncelle' => $this->TahminiTeslimSuresiAyniGunGuncelle,
			'TahminiTeslimSuresiGosterGuncelle'  => $this->TahminiTeslimSuresiGosterGuncelle,
			'TahminiTeslimSuresiGuncelle'        => $this->TahminiTeslimSuresiGuncelle,
			'TahminiTeslimSuresiTarihGuncelle'   => $this->TahminiTeslimSuresiTarihGuncelle,
			'TedarikciKodunaGoreGuncelle'        => $this->TedarikciKodunaGoreGuncelle,
			'TedarikciKomisyoniGuncelle'         => $this->TedarikciKomisyoniGuncelle,
			'UpdateKeyGuncelle'                  => $this->UpdateKeyGuncelle,
			'UrunAgirligiGuncelle'               => $this->UrunAgirligiGuncelle,
			'UrunDerinlikGuncelle'               => $this->UrunDerinlikGuncelle,
			'UrunGenislikGuncelle'               => $this->UrunGenislikGuncelle,
			'UrunResimGuncelle'                  => $this->UrunResimGuncelle,
			'UrunYukseklikGuncelle'              => $this->UrunYukseklikGuncelle,
			'UyeAlimMaxGuncelle'                 => $this->UyeAlimMaxGuncelle,
			'UyeAlimMinGuncelle'                 => $this->UyeAlimMinGuncelle,
			'UyeTipiFiyat1Guncelle'              => $this->UyeTipiFiyat1Guncelle,
			'UyeTipiFiyat2Guncelle'              => $this->UyeTipiFiyat2Guncelle,
			'UyeTipiFiyat3Guncelle'              => $this->UyeTipiFiyat3Guncelle,
			'UyeTipiFiyat4Guncelle'              => $this->UyeTipiFiyat4Guncelle,
			'UyeTipiFiyat5Guncelle'              => $this->UyeTipiFiyat5Guncelle,
			'UyeTipiFiyat6Guncelle'              => $this->UyeTipiFiyat6Guncelle,
			'UyeTipiFiyat7Guncelle'              => $this->UyeTipiFiyat7Guncelle,
			'UyeTipiFiyat8Guncelle'              => $this->UyeTipiFiyat8Guncelle,
			'UyeTipiFiyat9Guncelle'              => $this->UyeTipiFiyat9Guncelle,
			'UyeTipiFiyat10Guncelle'             => $this->UyeTipiFiyat10Guncelle,
			'UyeTipiFiyat11Guncelle'             => $this->UyeTipiFiyat11Guncelle,
			'UyeTipiFiyat12Guncelle'             => $this->UyeTipiFiyat12Guncelle,
			'UyeTipiFiyat13Guncelle'             => $this->UyeTipiFiyat13Guncelle,
			'UyeTipiFiyat14Guncelle'             => $this->UyeTipiFiyat14Guncelle,
			'UyeTipiFiyat15Guncelle'             => $this->UyeTipiFiyat15Guncelle,
			'UyeTipiFiyat16Guncelle'             => $this->UyeTipiFiyat16Guncelle,
			'UyeTipiFiyat17Guncelle'             => $this->UyeTipiFiyat17Guncelle,
			'UyeTipiFiyat18Guncelle'             => $this->UyeTipiFiyat18Guncelle,
			'UyeTipiFiyat19Guncelle'             => $this->UyeTipiFiyat19Guncelle,
			'UyeTipiFiyat20Guncelle'             => $this->UyeTipiFiyat20Guncelle,
		];
	}
}
