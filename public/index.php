<?php
// Front controller - Spa Serenity
$config = require dirname(__DIR__) . '/app/config/config.php';
$GLOBALS['app_config'] = $config;

if ($config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

// Sesión
session_name($config['session']['name']);
session_set_cookie_params([
    'lifetime' => $config['session']['lifetime'],
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Núcleo
require dirname(__DIR__) . '/app/core/helpers.php';
require dirname(__DIR__) . '/app/core/Database.php';
require dirname(__DIR__) . '/app/core/Auth.php';
require dirname(__DIR__) . '/app/core/Router.php';

$router = new Router();

// ----- Rutas Públicas -----
$router->get('/',          'HomeController@index');
$router->get('/servicios', 'HomeController@services');
$router->get('/sobre-nosotros', 'HomeController@about');
$router->get('/contacto',  'HomeController@contact');
$router->post('/contacto', 'HomeController@submitContact');
$router->get('/reservar',  'HomeController@booking');
$router->post('/reservar', 'HomeController@submitBooking');

// ----- Autenticación -----
$router->get('/login',  'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// ----- Panel admin -----
$router->get('/admin',           'DashboardController@index');
$router->get('/admin/dashboard', 'DashboardController@index');

// Citas
$router->get('/admin/citas',            'AppointmentController@index');
$router->get('/admin/citas/agenda',     'AppointmentController@agenda');
$router->get('/admin/citas/nueva',      'AppointmentController@create');
$router->post('/admin/citas/nueva',     'AppointmentController@store');
$router->get('/admin/citas/{id}/editar','AppointmentController@edit');
$router->post('/admin/citas/{id}/editar','AppointmentController@update');
$router->post('/admin/citas/{id}/estado','AppointmentController@updateStatus');
$router->post('/admin/citas/{id}/eliminar','AppointmentController@destroy');

// Clientes
$router->get('/admin/clientes',           'CustomerController@index');
$router->get('/admin/clientes/nuevo',     'CustomerController@create');
$router->post('/admin/clientes/nuevo',    'CustomerController@store');
$router->get('/admin/clientes/{id}',      'CustomerController@show');
$router->get('/admin/clientes/{id}/editar','CustomerController@edit');
$router->post('/admin/clientes/{id}/editar','CustomerController@update');
$router->post('/admin/clientes/{id}/eliminar','CustomerController@destroy');

// Servicios
$router->get('/admin/servicios',           'ServiceController@index');
$router->get('/admin/servicios/nuevo',     'ServiceController@create');
$router->post('/admin/servicios/nuevo',    'ServiceController@store');
$router->get('/admin/servicios/{id}/editar','ServiceController@edit');
$router->post('/admin/servicios/{id}/editar','ServiceController@update');
$router->post('/admin/servicios/{id}/eliminar','ServiceController@destroy');

// Inventario
$router->get('/admin/inventario',          'InventoryController@index');
$router->get('/admin/inventario/nuevo',    'InventoryController@create');
$router->post('/admin/inventario/nuevo',   'InventoryController@store');
$router->get('/admin/inventario/{id}/editar','InventoryController@edit');
$router->post('/admin/inventario/{id}/editar','InventoryController@update');
$router->post('/admin/inventario/{id}/movimiento','InventoryController@movement');
$router->post('/admin/inventario/{id}/eliminar','InventoryController@destroy');

// Facturación
$router->get('/admin/facturas',           'InvoiceController@index');
$router->get('/admin/facturas/nueva',     'InvoiceController@create');
$router->post('/admin/facturas/nueva',    'InvoiceController@store');
$router->get('/admin/facturas/{id}',      'InvoiceController@show');
$router->post('/admin/facturas/{id}/estado','InvoiceController@updateStatus');
$router->get('/admin/facturas/{id}/imprimir','InvoiceController@printable');

// API ligera (JSON) para el calendario
$router->get('/admin/api/citas', 'AppointmentController@apiList');

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $ex) {
    http_response_code(500);
    if ($config['app']['debug']) {
        echo '<pre style="padding:20px;background:#fee;color:#900;font-family:monospace;">';
        echo 'Error: ' . e($ex->getMessage()) . "\n\n";
        echo e($ex->getTraceAsString());
        echo '</pre>';
    } else {
        require dirname(__DIR__) . '/app/views/errors/500.php';
    }
}
