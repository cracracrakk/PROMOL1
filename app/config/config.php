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

date_default_timezone_set(env('TIMEZONE', 'Europe/Madrid'));

return [
    'app' => [
        'name'  => env('APP_NAME', 'Spa Serenity'),
        'url'   => env('APP_URL', 'http://localhost:8000'),
        'env'   => env('APP_ENV', 'production'),
        'debug' => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
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
        'code'   => env('CURRENCY', 'EUR'),
        'symbol' => env('CURRENCY_SYMBOL', '€'),
    ],
    'tax_rate' => (float) env('TAX_RATE', 21),
];
