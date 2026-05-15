<?php
// DentalCore - Front controller
$config = require dirname(__DIR__) . '/app/config/config.php';
$GLOBALS['app_config'] = $config;

if ($config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

date_default_timezone_set($config['app']['timezone'] ?? 'UTC');

session_name($config['session']['name']);
session_set_cookie_params([
    'lifetime' => $config['session']['lifetime'],
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$base = dirname(__DIR__);
require $base . '/app/core/helpers.php';
require $base . '/app/core/Database.php';
require $base . '/app/core/Auth.php';
require $base . '/app/core/Validator.php';
require $base . '/app/core/Controller.php';
require $base . '/app/core/Router.php';

// Autoload modelos
spl_autoload_register(function ($cls) use ($base) {
    foreach (['models', 'core'] as $dir) {
        $f = $base . '/app/' . $dir . '/' . $cls . '.php';
        if (is_file($f)) { require $f; return; }
    }
});

$r = new Router();

// ---- Landing pública ----
$r->get ('/',              'HomeController@landing');
$r->get ('/funciones',     'HomeController@features');
$r->get ('/precios',       'HomeController@pricing');
$r->get ('/contacto',      'HomeController@contact');
$r->post('/contacto',      'HomeController@submitContact');

// ---- Auth ----
$r->get ('/login',  'AuthController@showLogin');
$r->post('/login',  'AuthController@login');
$r->get ('/logout', 'AuthController@logout');

// ---- Panel ----
$r->get('/admin',           'DashboardController@index');
$r->get('/admin/dashboard', 'DashboardController@index');

// Pacientes
$r->get ('/admin/pacientes',                  'PatientController@index');
$r->get ('/admin/pacientes/nuevo',            'PatientController@create');
$r->post('/admin/pacientes/nuevo',            'PatientController@store');
$r->get ('/admin/pacientes/{id}',             'PatientController@show');
$r->get ('/admin/pacientes/{id}/editar',      'PatientController@edit');
$r->post('/admin/pacientes/{id}/editar',      'PatientController@update');
$r->post('/admin/pacientes/{id}/eliminar',    'PatientController@destroy');

// Odontograma + historia clínica
$r->get ('/admin/pacientes/{id}/odontograma', 'OdontogramController@show');
$r->post('/admin/pacientes/{id}/odontograma', 'OdontogramController@update');
$r->get ('/admin/pacientes/{id}/historia',    'ClinicalController@notesIndex');
$r->post('/admin/pacientes/{id}/historia',    'ClinicalController@notesStore');

// Citas
$r->get ('/admin/citas',                'AppointmentController@index');
$r->get ('/admin/citas/calendario',     'AppointmentController@calendar');
$r->get ('/admin/citas/nueva',          'AppointmentController@create');
$r->post('/admin/citas/nueva',          'AppointmentController@store');
$r->post('/admin/citas/{id}/estado',    'AppointmentController@updateStatus');
$r->post('/admin/citas/{id}/eliminar',  'AppointmentController@destroy');

// Tratamientos
$r->get ('/admin/tratamientos',                 'TreatmentController@index');
$r->get ('/admin/tratamientos/nuevo',           'TreatmentController@form');
$r->post('/admin/tratamientos/nuevo',           'TreatmentController@save');
$r->get ('/admin/tratamientos/{id}/editar',     'TreatmentController@form');
$r->post('/admin/tratamientos/{id}/editar',     'TreatmentController@save');
$r->post('/admin/tratamientos/{id}/eliminar',   'TreatmentController@destroy');

// Facturas
$r->get ('/admin/facturas',                'InvoiceController@index');
$r->get ('/admin/facturas/nueva',          'InvoiceController@create');
$r->post('/admin/facturas/nueva',          'InvoiceController@store');
$r->get ('/admin/facturas/{id}',           'InvoiceController@show');
$r->get ('/admin/facturas/{id}/imprimir',  'InvoiceController@printable');
$r->post('/admin/facturas/{id}/estado',    'InvoiceController@updateStatus');

// Pagos
$r->post('/admin/facturas/{id}/pago',      'PaymentController@store');
$r->post('/admin/pagos/{id}/eliminar',     'PaymentController@destroy');

// ---- API JSON ----
$r->get ('/api/patients',                  'PatientController@apiList');
$r->get ('/api/patients/{id}',             'PatientController@apiShow');
$r->get ('/api/patients/{id}/odontogram',  'OdontogramController@apiGet');
$r->post('/api/patients/{id}/odontogram',  'OdontogramController@update');
$r->get ('/api/appointments',              'AppointmentController@apiList');
$r->post('/api/appointments',              'AppointmentController@apiStore');

try {
    $r->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    http_response_code(500);
    if ($config['app']['debug']) {
        echo '<pre style="padding:20px;background:#fee;color:#900;font-family:monospace;">';
        echo 'Error: ' . htmlspecialchars($e->getMessage()) . "\n\n";
        echo htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
    } else {
        $f = dirname(__DIR__) . '/app/views/errors/500.php';
        if (is_file($f)) require $f;
        else echo 'Error interno del servidor.';
    }
}
