<?php

	use Hasokeyk\Ticimax\Ticimax;

	require_once (__DIR__)."/vendor/autoload.php";

	$domain = "https://xxxxyyyyzzzz.com";
	$key    = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

	$ticimax = new Ticimax($domain, $key);

	$ticimax_categories = $ticimax->categories();

	$get_categories = $ticimax_categories->get_categories();
	print_r($get_categories);