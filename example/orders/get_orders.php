<?php

	use Hasokeyk\Ticimax\Ticimax;

	require_once (__DIR__)."/vendor/autoload.php";

	$domain = "https://xxxxyyyyzzzz.com";
	$key    = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

	$ticimax = new Ticimax($domain, $key);

	$ticimax_orders = $ticimax->orders();

	$get_orders = $ticimax_orders->get_orders();
	var_dump($get_orders);