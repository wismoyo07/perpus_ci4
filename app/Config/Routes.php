<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');


// Template Notika
// LOGIN
// Default
$routes->get('auth/wizard', 'Auth::wizard');

$routes->get('/', 'Auth::login');

$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::processLogin');

$routes->get('auth/register', 'Auth::register');
$routes->post('auth/register', 'Auth::processRegister');

// FORCE CHANGE PASSWORD (harus login dulu)
$routes->get('force-change-password', 'Auth::forceChange', [
    'filter' => 'auth'
]);
$routes->post('force-change-password', 'Auth::processForceChange', [
    'filter' => 'auth'
]);

// LOGOUT
$routes->get('logout', 'Auth::logout', [
    'filter' => 'auth'
]);

// DASHBOARD UMUM
$routes->get('dashboard', 'Dashboard::index', [
    'filter' => 'auth'
]);

// ROLE BASED DASHBOARD
$routes->get('admin/dashboard', 'Dashboard::admin', [
    'filter' => ['auth','role:admin']
]);

$routes->get('staff/dashboard', 'Dashboard::staff', [
    'filter' => ['auth','role:staff']
]);

$routes->get('member/dashboard', 'Dashboard::member', [
    'filter' => ['auth','role:member']
]);

$routes->group('books', ['filter' => 'role:admin,staff'], function($routes) {
    $routes->get('/', 'Books::index');
});

/*
|--------------------------------------------------------------------------
| BOOKS ROUTES
|--------------------------------------------------------------------------
| Hanya admin & staff
*/

$routes->group('books', ['filter' => 'auth', 'role:admin,staff'], function($routes) {

    $routes->get('/', 'Books::index');
    $routes->get('create', 'Books::create');
    $routes->post('store', 'Books::store');
    $routes->get('edit/(:num)', 'Books::edit/$1');
    $routes->post('update/(:num)', 'Books::update/$1');
    $routes->post('delete/(:num)', 'Books::delete/$1');
    $routes->post('archive/(:num)', 'Books::archive/$1');
    $routes->post('restore/(:num)', 'Books::restore/$1');

});


/*
|--------------------------------------------------------------------------
| TRANSACTIONS ROUTES
|--------------------------------------------------------------------------
| Route Bersama admin & staff
*/

$routes->group('lost-books', [
    'filter' => ['auth', 'role:staff,admin']
], function($routes) {

    $routes->get('/', 'Transactions::lostIndex');
    $routes->post('settle/(:num)', 'Transactions::settleLost/$1');

});

/* Admin Only */
$routes->get('admin/transactions', 'Transactions::adminIndex', [
    'filter' => ['auth', 'role:admin']
]);


/* STAFF Only */
$routes->group('transactions', [
    'filter' => ['auth', 'role:staff']
], function($routes) {

    $routes->get('/', 'Transactions::index');
    $routes->get('create', 'Transactions::create');
    $routes->post('store', 'Transactions::store');
    $routes->get('return/(:num)', 'Transactions::returnForm/$1');
    $routes->post('return/(:num)', 'Transactions::returnProcess/$1');
});


// Routes untuk member
$routes->group('member', ['filter' => 'auth'], function($routes){
    $routes->get('dashboard', 'Dashboard::member', ['filter'=>'role:member']);
});

$routes->group('member', [
    'filter' => ['auth', 'role:member']
], function($routes) {

    $routes->get('books', 'Books::listMember');

});

$routes->group('member', [
    'filter' => ['auth', 'role:member']
], function($routes) {

    $routes->get('transactions', 'Member::transactions');

});


// MEMBER CRUD USER MANAGEMENT
$routes->group('users', [
    'filter' => ['auth','role:admin,staff']
], function($routes){

    $routes->get('/', 'Users::index');
    $routes->get('create', 'Users::create');
    $routes->post('store', 'Users::store');
    $routes->get('edit/(:num)', 'Users::edit/$1');
    $routes->post('update/(:num)', 'Users::update/$1');

    $routes->post('activate/(:num)', 'Users::activate/$1');
    $routes->post('archive/(:num)', 'Users::archive/$1');

    $routes->post('reset-password/(:num)', 'Users::resetPassword/$1');
});

$routes->post('users/delete/(:num)', 'Users::delete/$1', [
    'filter' => ['auth','role:admin']
]);

$routes->post('users/validate/(:num)', 
    'Users::validateUser/$1',
    ['filter' => ['auth','role:admin,staff']]
);

$routes->post('users/bulk-action', 'Users::bulkAction', [
    'filter' => ['auth','role:admin,staff']
]);


// STAFF MANAGEMENT
$routes->group('staff', [
    'filter' => ['auth','role:admin']
], function($routes){

    $routes->get('/', 'Users::staffIndex');

});

// RESET PASSWORD STAFF SIDE
$routes->post('users/reset-password/(:num)', 'Users::resetPassword', [
    'filter' => ['auth','role:staff']
]);

// ROUTE LOG ACTIVITY
$routes->get('activity-logs', 'ActivityLogs::index', [
    'filter' => ['auth','role:admin']
]);