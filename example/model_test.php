<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AlperRagib\Ticimax\Model\User\UserModel;
use AlperRagib\Ticimax\Model\Brand\BrandModel;
use AlperRagib\Ticimax\Model\Category\CategoryModel;

// Test UserModel
$userData = [
    'ID' => 1,
    'Email' => 'test@example.com',
    'Ad' => 'Test',
    'Soyad' => 'User'
];
$user = new UserModel($userData);
var_dump("User Test:");
var_dump($user->ID);
var_dump($user->Email);
var_dump($user->toArray());
echo "\n";

// Test BrandModel
$brandData = [
    'ID' => 1,
    'Tanim' => 'Test Brand',
    'Aciklama' => 'Test Description'
];
$brand = new BrandModel($brandData);
var_dump("Brand Test:");
var_dump($brand->ID);
var_dump($brand->Tanim);
var_dump($brand->toArray());
echo "\n";

// Test CategoryModel
$categoryData = [
    'ID' => 1,
    'Tanim' => 'Test Category',
    'ParentID' => 0
];
$category = new CategoryModel($categoryData);
var_dump("Category Test:");
var_dump($category->ID);
var_dump($category->Tanim);
var_dump($category->toArray()); 