<?php
// Load CodeIgniter
require '../app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

use App\Models\ProductModel;
use App\Models\CategoryModel;

$productModel = new ProductModel();
$categoryModel = new CategoryModel();

echo "<h1>Debug Products</h1>";

echo "<h2>All Products (Raw)</h2>";
$allProducts = $productModel->findAll();
echo "<pre>";
print_r($allProducts);
echo "</pre>";

echo "<h2>All Categories</h2>";
$allCategories = $categoryModel->findAll();
echo "<pre>";
print_r($allCategories);
echo "</pre>";

echo "<h2>Products with Category (Join)</h2>";
$joinedProducts = $productModel->getProductsWithCategory();
echo "<pre>";
print_r($joinedProducts);
echo "</pre>";
