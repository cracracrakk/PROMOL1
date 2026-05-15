<?php
// DentalCore - Configuración global
// Lee variables de entorno desde .env (opcional)

$envFile = dirname(__DIR__, 2) . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        $v = trim($v, "\"' \t");
        if ($k !== '' && !isset($_ENV[$k])) {
            $_ENV[$k] = $v;
            putenv("$k=$v");
        }
    }
}

$env = static function (string $key, $default = null) {
    $v = $_ENV[$key] ?? getenv($key);
    if ($v === false || $v === null || $v === '') return $default;
    return match (strtolower((string)$v)) {
        'true', '1', 'yes', 'on'   => true,
        'false', '0', 'no', 'off'  => false,
        default => $v,
    };
};

return [
    'app' => [
        'name'     => $env('APP_NAME', 'DentalCore'),
        'env'      => $env('APP_ENV', 'production'),
        'debug'    => (bool)$env('APP_DEBUG', false),
        'url'      => $env('APP_URL', 'http://localhost'),
        'timezone' => $env('APP_TIMEZONE', 'America/Tegucigalpa'),
    ],
    'db' => [
        'host'     => $env('DB_HOST', '127.0.0.1'),
        'port'     => (int)$env('DB_PORT', 3306),
        'database' => $env('DB_DATABASE', 'dental_core'),
        'username' => $env('DB_USERNAME', 'root'),
        'password' => $env('DB_PASSWORD', ''),
        'charset'  => 'utf8mb4',
    ],
    'session' => [
        'name'     => $env('SESSION_NAME', 'DENTALSESS'),
        'lifetime' => (int)$env('SESSION_LIFETIME', 7200),
    ],
    'mail' => [
        'from_address' => $env('MAIL_FROM_ADDRESS', 'no-reply@dental.local'),
        'from_name'    => $env('MAIL_FROM_NAME', 'DentalCore'),
    ],
];
