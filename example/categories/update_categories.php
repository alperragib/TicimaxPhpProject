<?php

	use AlperRagib\Ticimax\Ticimax;
	use AlperRagib\Ticimax\Categories\TicimaxCategoryModel;

	require_once (__DIR__)."/vendor/autoload.php";

	$domain = "https://xxxxyyyyzzzz.com";
	$key    = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

	$ticimax = new Ticimax($domain, $key);

	$ticimax_categories = $ticimax->categories();

	//ÖNCE KATEGORİ MODELİ OLUŞTURUYORUZ
	$ticimax_category = new TicimaxCategoryModel();
	$ticimax_category->setCategoryId(9999); //ID DE KATEGORİ VARSA GÜNCELLER
	$ticimax_category->setCategoryName("Kahve");
	$ticimax_category->setCategoryDescription("Kahve kategorisi");
	$ticimax_category->setCategoryParentId(0);
	$ticimax_category->setCategoryStatus(1);
	$ticimax_category->setCategoryCode("XXXX"); //KATEGORİ KODU NİYE VAR BİLMİYORUM
	$ticimax_category->setCategorySort(0); //SIRALAMA
	$ticimax_category->setCategorySeoTitle("Kahve");
	$ticimax_category->setCategorySeoDescription("Kahve kategorisi");
	$ticimax_category->setCategorySeoKeyword("Kahve");
	$ticimax_category->setCategorySeoPermalink("kahve-sayfasi");
	//ÖNCE KATEGORİ MODELİ OLUŞTURUYORUZ

	$get_categories = $ticimax_categories->update_category($ticimax_category);
	print_r($get_categories);