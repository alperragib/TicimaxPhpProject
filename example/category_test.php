<?php
require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Ticimax;

$config = require __DIR__ . '/config.php';

$ticimax = new Ticimax($config['mainDomain'], $config['apiKey']);
$categoryService = $ticimax->categoryService();

$categoryId = 0;
$language = 'TR';
$parentId = null;

$response = $categoryService->getCategories( $categoryId, $language,  $parentId );
print_r($response);
