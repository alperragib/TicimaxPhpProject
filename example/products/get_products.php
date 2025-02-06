<?php

	use Hasokeyk\Ticimax\Ticimax;

	require_once (__DIR__)."/vendor/autoload.php";

	$domain = "https://xxxxyyyyzzzz.com";
	$key    = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

	$ticimax = new Ticimax($domain, $key);

	$ticimax_products = $ticimax->products();

	$get_products = $ticimax_products->get_products();
	var_dump($get_products);