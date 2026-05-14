<?php
// Front controller - Sistema de Gestión de Spa
$config = require dirname(__DIR__) . '/app/config/config.php';
$GLOBALS['app_config'] = $config;

if ($config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

session_name($config['session']['name']);
session_set_cookie_params([
    'lifetime' => $config['session']['lifetime'],
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require dirname(__DIR__) . '/app/core/helpers.php';
require dirname(__DIR__) . '/app/core/Database.php';
require dirname(__DIR__) . '/app/core/Auth.php';
require dirname(__DIR__) . '/app/core/Router.php';
require dirname(__DIR__) . '/app/core/Mail.php';
require dirname(__DIR__) . '/app/core/SarHelper.php';

$router = new Router();

// ----- Rutas Públicas -----
$router->get ('/',               'HomeController@index');
$router->get ('/servicios',      'HomeController@services');
$router->get ('/sobre-nosotros', 'HomeController@about');
$router->get ('/contacto',       'HomeController@contact');
$router->post('/contacto',       'HomeController@submitContact');
$router->get ('/reservar',       'HomeController@booking');
$router->post('/reservar',       'HomeController@submitBooking');
$router->get ('/regalo',         'HomeController@giftCard');
$router->post('/regalo',         'HomeController@submitGiftCard');

// ----- Setup wizard (instalación) -----
$router->get ('/setup',         'SetupController@welcome');
$router->get ('/setup/paso-1',  'SetupController@step1');
$router->post('/setup/paso-1',  'SetupController@step1');
$router->get ('/setup/paso-2',  'SetupController@step2');
$router->post('/setup/paso-2',  'SetupController@step2');
$router->get ('/setup/paso-3',  'SetupController@step3');
$router->post('/setup/paso-3',  'SetupController@step3');

// ----- Auth -----
$router->get ('/login',     'AuthController@showLogin');
$router->post('/login',     'AuthController@login');
$router->get ('/login/2fa', 'AuthController@showTwoFactor');
$router->post('/login/2fa', 'AuthController@verifyTwoFactor');
$router->get ('/logout',    'AuthController@logout');

// ----- Tokens públicos de cita -----
$router->get ('/cita/confirmar/{token}', 'PublicAppointmentController@confirm');
$router->get ('/cita/cancelar/{token}',  'PublicAppointmentController@cancel');
$router->get ('/cita/encuesta/{token}',  'PublicAppointmentController@survey');
$router->post('/cita/encuesta/{token}',  'PublicAppointmentController@survey');

// ----- Portal del cliente -----
$router->get ('/mi-cuenta',                    'CustomerPortalController@login');
$router->post('/mi-cuenta/acceso',             'CustomerPortalController@sendMagicLink');
$router->get ('/mi-cuenta/acceso/{token}',     'CustomerPortalController@authenticate');
$router->get ('/mi-cuenta/inicio',             'CustomerPortalController@dashboard');
$router->get ('/mi-cuenta/perfil',             'CustomerPortalController@profile');
$router->post('/mi-cuenta/perfil',             'CustomerPortalController@profile');
$router->get ('/mi-cuenta/salir',              'CustomerPortalController@logout');

// ----- Dashboard -----
$router->get('/admin',           'DashboardController@index');
$router->get('/admin/dashboard', 'DashboardController@index');
$router->get('/admin/api/tema',  'DashboardController@saveTheme');

// ----- Citas -----
$router->get ('/admin/citas',                  'AppointmentController@index');
$router->get ('/admin/citas/agenda',           'AppointmentController@agenda');
$router->get ('/admin/citas/nueva',            'AppointmentController@create');
$router->post('/admin/citas/nueva',            'AppointmentController@store');
$router->get ('/admin/citas/{id}/editar',      'AppointmentController@edit');
$router->post('/admin/citas/{id}/editar',      'AppointmentController@update');
$router->post('/admin/citas/{id}/estado',      'AppointmentController@updateStatus');
$router->post('/admin/citas/{id}/eliminar',    'AppointmentController@destroy');
$router->get ('/admin/api/citas',              'AppointmentController@apiList');

// ----- Clientes -----
$router->get ('/admin/clientes',                 'CustomerController@index');
$router->get ('/admin/clientes/nuevo',           'CustomerController@create');
$router->post('/admin/clientes/nuevo',           'CustomerController@store');
$router->get ('/admin/clientes/{id}',            'CustomerController@show');
$router->get ('/admin/clientes/{id}/editar',     'CustomerController@edit');
$router->post('/admin/clientes/{id}/editar',     'CustomerController@update');
$router->post('/admin/clientes/{id}/eliminar',   'CustomerController@destroy');

// ----- Servicios -----
$router->get ('/admin/servicios',                'ServiceController@index');
$router->get ('/admin/servicios/nuevo',          'ServiceController@create');
$router->post('/admin/servicios/nuevo',          'ServiceController@store');
$router->get ('/admin/servicios/{id}/editar',    'ServiceController@edit');
$router->post('/admin/servicios/{id}/editar',    'ServiceController@update');
$router->post('/admin/servicios/{id}/eliminar',  'ServiceController@destroy');

// ----- Inventario -----
$router->get ('/admin/inventario',                'InventoryController@index');
$router->get ('/admin/inventario/nuevo',          'InventoryController@create');
$router->post('/admin/inventario/nuevo',          'InventoryController@store');
$router->get ('/admin/inventario/{id}/editar',    'InventoryController@edit');
$router->post('/admin/inventario/{id}/editar',    'InventoryController@update');
$router->post('/admin/inventario/{id}/movimiento','InventoryController@movement');
$router->post('/admin/inventario/{id}/eliminar',  'InventoryController@destroy');

// ----- Facturación -----
$router->get ('/admin/facturas',                 'InvoiceController@index');
$router->get ('/admin/facturas/nueva',           'InvoiceController@create');
$router->post('/admin/facturas/nueva',           'InvoiceController@store');
$router->get ('/admin/facturas/{id}',            'InvoiceController@show');
$router->post('/admin/facturas/{id}/estado',     'InvoiceController@updateStatus');
$router->get ('/admin/facturas/{id}/imprimir',   'InvoiceController@printable');

// ----- Bonos y tarjetas regalo -----
$router->get ('/admin/bonos',         'GiftController@index');
$router->post('/admin/bonos/regalo',  'GiftController@storeGift');
$router->post('/admin/bonos/bono',    'GiftController@storePack');
$router->post('/admin/bonos/asignar', 'GiftController@assignPack');

// ----- POS (caja rápida) -----
$router->get('/admin/pos', 'PosController@index');

// ----- Caja diaria -----
$router->get ('/admin/caja',        'CashController@index');
$router->post('/admin/caja/cierre', 'CashController@close');

// ----- Reportes básicos -----
$router->get('/admin/reportes',     'ReportController@index');
$router->get('/admin/reportes/sar', 'ReportController@exportSar');

// ----- Reportes avanzados -----
$router->get('/admin/reportes/avanzados', 'AdvancedReportsController@index');
$router->get('/admin/reportes/heatmap',   'AdvancedReportsController@heatmap');
$router->get('/admin/reportes/retencion', 'AdvancedReportsController@retention');
$router->get('/admin/reportes/en-riesgo', 'AdvancedReportsController@atRisk');
$router->get('/admin/reportes/forecast',  'AdvancedReportsController@forecast');
$router->get('/admin/reportes/terapeutas','AdvancedReportsController@therapists');

// ----- Catálogos -----
$router->get ('/admin/catalogos/cabinas',                    'CatalogController@rooms');
$router->post('/admin/catalogos/cabinas/guardar',            'CatalogController@roomSave');
$router->post('/admin/catalogos/cabinas/{id}/eliminar',      'CatalogController@roomDelete');
$router->get ('/admin/catalogos/proveedores',                'CatalogController@suppliers');
$router->post('/admin/catalogos/proveedores/guardar',        'CatalogController@supplierSave');
$router->post('/admin/catalogos/proveedores/{id}/eliminar',  'CatalogController@supplierDelete');
$router->get ('/admin/catalogos/categorias-servicios',       'CatalogController@serviceCategories');
$router->post('/admin/catalogos/categorias-servicios/guardar','CatalogController@serviceCategorySave');
$router->get ('/admin/catalogos/categorias-productos',       'CatalogController@productCategories');
$router->post('/admin/catalogos/categorias-productos/guardar','CatalogController@productCategorySave');
$router->get ('/admin/catalogos/testimonios',                'CatalogController@testimonials');
$router->post('/admin/catalogos/testimonios/guardar',        'CatalogController@testimonialSave');
$router->post('/admin/catalogos/testimonios/{id}/eliminar',  'CatalogController@testimonialDelete');
$router->get ('/admin/catalogos/galeria',                    'CatalogController@gallery');
$router->post('/admin/catalogos/galeria/subir',              'CatalogController@galleryUpload');
$router->post('/admin/catalogos/galeria/{id}/eliminar',      'CatalogController@galleryDelete');
$router->get ('/admin/catalogos/mensajes',                   'CatalogController@messages');
$router->post('/admin/catalogos/mensajes/{id}/eliminar',     'CatalogController@messageDelete');
$router->get ('/admin/catalogos/promos',                     'CatalogController@promos');
$router->post('/admin/catalogos/promos/guardar',             'CatalogController@promoSave');
$router->post('/admin/catalogos/promos/{id}/eliminar',       'CatalogController@promoDelete');

// ----- Newsletter -----
$router->get ('/admin/newsletter',         'NewsletterController@index');
$router->post('/admin/newsletter/enviar',  'NewsletterController@send');

// ----- Wallet -----
$router->post('/admin/wallet/recargar',    'WalletController@topup');

// ----- Import / Export -----
$router->get ('/admin/import-export',                       'ImportExportController@index');
$router->get ('/admin/import-export/clientes/exportar',     'ImportExportController@exportCustomers');
$router->post('/admin/import-export/clientes/importar',     'ImportExportController@importCustomers');
$router->get ('/admin/import-export/productos/exportar',    'ImportExportController@exportProducts');
$router->post('/admin/import-export/productos/importar',    'ImportExportController@importProducts');

// ----- Facturas PDF descarga -----
$router->get ('/admin/facturas/{id}/descargar', 'InvoiceController@download');

// ----- Sucursales -----
$router->get ('/admin/sistema/sucursales',                  'BranchController@index');
$router->post('/admin/sistema/sucursales/guardar',          'BranchController@save');
$router->post('/admin/sistema/sucursales/{id}/eliminar',    'BranchController@delete');

// ----- Sistema (solo admin) -----
$router->get ('/admin/sistema',                       'SystemController@index');
$router->post('/admin/sistema/guardar',               'SystemController@saveSettings');
$router->get ('/admin/sistema/usuarios',              'SystemController@users');
$router->get ('/admin/sistema/usuarios/nuevo',        'SystemController@userForm');
$router->get ('/admin/sistema/usuarios/{id}/editar',  'SystemController@userForm');
$router->post('/admin/sistema/usuarios/guardar',      'SystemController@userSave');
$router->post('/admin/sistema/usuarios/{id}/eliminar','SystemController@userDelete');
$router->get ('/admin/sistema/sar',                   'SystemController@sar');
$router->post('/admin/sistema/sar/guardar',           'SystemController@sarSave');
$router->get ('/admin/sistema/2fa',                   'SystemController@twoFactorSetup');
$router->post('/admin/sistema/2fa/verificar',         'SystemController@twoFactorVerify');
$router->post('/admin/sistema/2fa/desactivar',        'SystemController@twoFactorDisable');
$router->get ('/admin/sistema/auditoria',             'SystemController@audit');

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $ex) {
    http_response_code(500);
    if ($config['app']['debug']) {
        echo '<pre style="padding:20px;background:#fee;color:#900;font-family:monospace;">';
        echo 'Error: ' . htmlspecialchars($ex->getMessage()) . "\n\n";
        echo htmlspecialchars($ex->getTraceAsString());
        echo '</pre>';
    } else {
        require dirname(__DIR__) . '/app/views/errors/500.php';
    }
}
