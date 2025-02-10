<?php

	use Hasokeyk\Ticimax\Ticimax;
	use Hasokeyk\Ticimax\Products\TicimaxProductVariationModel;
	use Hasokeyk\Ticimax\Products\TicimaxProductCardModel;

	require_once (__DIR__)."/vendor/autoload.php";

	$domain = "https://xxxxyyyyzzzz.com";
	$key    = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

	$ticimax = new Ticimax($domain, $key);

	$ticimax_products = $ticimax->products();

	$ticimax_product_card = new TicimaxProductCardModel();
	$ticimax_product_card->setProductID(0);
	$ticimax_product_card->setProductName('Hayatı Kodla');
	$ticimax_product_card->setProductIsActive(true);
	$ticimax_product_card->setProductCategoryId(2);
	$ticimax_product_card->setProductBrandId(3);
	$ticimax_product_card->setProductSupplierId(2);

	$ticimax_product_variation = new TicimaxProductVariationModel();
	$ticimax_product_variation->setProductVariationIsActive(1);
	$ticimax_product_variation->setProductVariationMoneyUnitId(1);
	$ticimax_product_variation->set_product_variation_sale_price(1000);

	$ticimax_product_card->setProductVariations($ticimax_product_variation);

	$get_products = $ticimax_products->create_products($ticimax_product_card);
	var_dump($get_products);