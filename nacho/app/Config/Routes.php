<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Root route redirects to login
$routes->get('/', 'Home::index');

// Debug route (remove in production)
$routes->get('debug/session', 'Debug::session');

// Authentication routes
$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::attemptLogin');
    $routes->get('logout', 'Auth::logout');
    $routes->get('register', 'Auth::register', ['filter' => 'role:admin']);
    $routes->post('register', 'Auth::attemptRegister', ['filter' => 'role:admin']);
});

// Dashboard (requires authentication)
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Products module (requires authentication)
$routes->group('products', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Products::index');
    $routes->get('create', 'Products::create');
    $routes->post('store', 'Products::store');
    $routes->get('edit/(:num)', 'Products::edit/$1');
    $routes->post('update/(:num)', 'Products::update/$1');
    $routes->get('delete/(:num)', 'Products::delete/$1');
});

// Product Stock module (requires authentication)
$routes->group('product-stock', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'ProductStock::index');
    $routes->get('warehouse/(:num)', 'ProductStock::warehouse/$1');
});

// Categories module (requires authentication)
$routes->group('categories', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Categories::index');
    $routes->get('create', 'Categories::create');
    $routes->post('store', 'Categories::store');
    $routes->get('edit/(:num)', 'Categories::edit/$1');
    $routes->post('update/(:num)', 'Categories::update/$1');
    $routes->get('delete/(:num)', 'Categories::delete/$1');
});

// Customers module (requires authentication)
$routes->group('customers', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Customers::index');
    $routes->get('create', 'Customers::create');
    $routes->post('store', 'Customers::store');
    $routes->get('edit/(:num)', 'Customers::edit/$1');
    $routes->post('update/(:num)', 'Customers::update/$1');
    $routes->get('delete/(:num)', 'Customers::delete/$1');
    $routes->get('account/(:num)', 'Customers::account/$1');
    $routes->post('ajax-store', 'Customers::ajaxStore');
});

// Suppliers module (requires authentication)
$routes->group('suppliers', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Suppliers::index');
    $routes->get('create', 'Suppliers::create');
    $routes->post('store', 'Suppliers::store');
    $routes->get('edit/(:num)', 'Suppliers::edit/$1');
    $routes->post('update/(:num)', 'Suppliers::update/$1');
    $routes->get('delete/(:num)', 'Suppliers::delete/$1');
    $routes->get('account/(:num)', 'Suppliers::account/$1');
});

// Sales module (requires authentication)
$routes->group('sales', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Sales::index');
    $routes->get('create', 'Sales::create');
    $routes->post('store', 'Sales::store');
    $routes->get('view/(:num)', 'Sales::view/$1');
    $routes->get('delete/(:num)', 'Sales::delete/$1', ['filter' => 'role:admin']);
    $routes->post('validate-auth', 'Sales::validateAuth');
    $routes->get('search-products', 'Sales::searchProducts');
});

// Purchases module (requires authentication)
$routes->group('purchases', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Purchases::index');
    $routes->get('create', 'Purchases::create');
    $routes->post('store', 'Purchases::store');
    $routes->get('view/(:num)', 'Purchases::view/$1');
    $routes->get('delete/(:num)', 'Purchases::delete/$1', ['filter' => 'role:admin']);
});

// Collections module (requires authentication)
$routes->group('collections', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Collections::index');
    $routes->get('create/(:num)', 'Collections::create/$1');
    $routes->post('store', 'Collections::store');
    $routes->get('history/(:num)', 'Collections::history/$1');
});

// Payments module (requires authentication)
$routes->group('payments', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Payments::index');
    $routes->get('create/(:num)', 'Payments::create/$1');
    $routes->post('store', 'Payments::store');
    $routes->get('history/(:num)', 'Payments::history/$1');
});

// Inventory Adjustments module (requires authentication)
$routes->group('inventory-adjustments', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'InventoryAdjustments::index');
    $routes->get('create', 'InventoryAdjustments::create');
    $routes->post('store', 'InventoryAdjustments::store');
    $routes->get('view/(:num)', 'InventoryAdjustments::view/$1');
    $routes->get('history/(:num)', 'InventoryAdjustments::history/$1');
    $routes->get('getStock', 'InventoryAdjustments::getStock');
});

// Expenses module (requires authentication)
$routes->group('expenses', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Expenses::index');
    $routes->get('create', 'Expenses::create');
    $routes->post('store', 'Expenses::store');
    $routes->get('edit/(:num)', 'Expenses::edit/$1');
    $routes->post('update/(:num)', 'Expenses::update/$1');
    $routes->get('delete/(:num)', 'Expenses::delete/$1', ['filter' => 'role:admin']);
    $routes->get('report', 'Expenses::report');
});

// Profile module (requires authentication)
$routes->group('profile', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Profile::index');
    $routes->post('update', 'Profile::update');
});

// Settings module (admin only)
$routes->group('settings', ['filter' => 'role:admin'], function($routes) {
    $routes->get('/', 'Settings::index');
    $routes->post('update', 'Settings::update');
});

// Roles module (requires authentication)
$routes->group('roles', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Roles::index');
    $routes->get('create', 'Roles::create');
    $routes->post('store', 'Roles::store');
    $routes->get('edit/(:num)', 'Roles::edit/$1');
    $routes->post('update/(:num)', 'Roles::update/$1');
    $routes->get('delete/(:num)', 'Roles::delete/$1');
});


