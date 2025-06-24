<?php

	use AlperRagib\Ticimax\Ticimax;
	use AlperRagib\Ticimax\Categories\TicimaxCategoryModel;

	require_once (__DIR__)."/vendor/autoload.php";

	$domain = "https://xxxxyyyyzzzz.com";
	$key    = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

	$ticimax = new Ticimax($domain, $key);

	$ticimax_categories = $ticimax->categories();

	$ticimax_category_model = new TicimaxCategoryModel();
	$ticimax_category_model->setCategoryId(1);//KATEGORİ ID Sİ

	$get_categories = $ticimax_categories->del_category($ticimax_category_model);
	print_r($get_categories);