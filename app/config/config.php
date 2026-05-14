<?php
// Cargador de variables de entorno desde .env
$envFile = dirname(__DIR__, 2) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        $v = trim($v, "\"'");
        $_ENV[$k] = $v;
        putenv("$k=$v");
    }
}

function env(string $key, $default = null) {
    $v = $_ENV[$key] ?? getenv($key);
    return ($v === false || $v === null || $v === '') ? $default : $v;
}

date_default_timezone_set(env('TIMEZONE', 'America/Tegucigalpa'));

return [
    'app' => [
        'name'   => env('APP_NAME', 'Spa Serenity'),
        'url'    => env('APP_URL', 'http://localhost:8000'),
        'env'    => env('APP_ENV', 'production'),
        'debug'  => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
        'locale' => env('LOCALE', 'es_HN'),
    ],
    'db' => [
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', '3306'),
        'name' => env('DB_NAME', 'spa_serenity'),
        'user' => env('DB_USER', 'root'),
        'pass' => env('DB_PASS', ''),
    ],
    'session' => [
        'name'     => env('SESSION_NAME', 'spa_session'),
        'lifetime' => (int) env('SESSION_LIFETIME', 7200),
    ],
    'currency' => [
        'code'     => env('CURRENCY', 'HNL'),
        'symbol'   => env('CURRENCY_SYMBOL', 'L.'),
        'fullname' => env('CURRENCY_CODE', 'Lempiras'),
    ],
    'tax' => [
        'country'  => env('TAX_COUNTRY', 'HN'),
        'rate'     => (float) env('TAX_RATE', 15),
        'name'     => env('TAX_NAME', 'ISV'),
    ],
    'sar' => [
        'rtn'              => env('SAR_RTN', ''),
        'business_name'    => env('SAR_BUSINESS_NAME', ''),
        'trade_name'       => env('SAR_TRADE_NAME', ''),
        'address'          => env('SAR_ADDRESS', ''),
        'phone'            => env('SAR_PHONE', ''),
        'email'            => env('SAR_EMAIL', ''),
        'regimen'          => env('SAR_REGIMEN', 'General'),
        'cai'              => env('SAR_CAI', ''),
        'punto_emision'    => env('SAR_PUNTO_EMISION', '001'),
        'establecimiento'  => env('SAR_ESTABLECIMIENTO', '000'),
        'tipo_documento'   => env('SAR_TIPO_DOCUMENTO', '01'),
        'rango_inicial'    => (int) env('SAR_RANGO_INICIAL', 1),
        'rango_final'      => (int) env('SAR_RANGO_FINAL', 10000),
        'resolucion'       => env('SAR_RESOLUCION', ''),
        'fecha_limite'     => env('SAR_FECHA_LIMITE', ''),
    ],
];
