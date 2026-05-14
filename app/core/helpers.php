<?php
function view(string $path, array $data = []): void {
    extract($data, EXTR_SKIP);
    $file = dirname(__DIR__) . '/views/' . $path . '.php';
    if (!file_exists($file)) {
        http_response_code(500);
        die("Vista no encontrada: $path");
    }
    require $file;
}

function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function redirect(string $url): void { header("Location: $url"); exit; }

function old(string $key, $default = ''): string {
    $v = $_SESSION['old'][$key] ?? $default;
    return e((string)$v);
}

function flash(string $type, ?string $message = null) {
    if ($message === null) {
        $m = $_SESSION["flash_$type"] ?? null;
        unset($_SESSION["flash_$type"]);
        return $m;
    }
    $_SESSION["flash_$type"] = $message;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
}

function csrf_verify(): void {
    $t = $_POST['_csrf'] ?? '';
    if (!hash_equals(csrf_token(), $t)) {
        http_response_code(419);
        die('Token CSRF inválido. Recarga la página e inténtalo de nuevo.');
    }
}

function money($amount): string {
    $cfg = $GLOBALS['app_config'] ?? require dirname(__DIR__) . '/config/config.php';
    return number_format((float)$amount, 2, ',', '.') . ' ' . $cfg['currency']['symbol'];
}

function setting(string $key, $default = '') {
    static $cache = null;
    if ($cache === null) {
        $rows = Database::fetchAll('SELECT `key`, `value` FROM site_settings');
        $cache = [];
        foreach ($rows as $r) $cache[$r['key']] = $r['value'];
    }
    return $cache[$key] ?? $default;
}

function url(string $path = ''): string {
    $cfg = $GLOBALS['app_config'] ?? require dirname(__DIR__) . '/config/config.php';
    return rtrim($cfg['app']['url'], '/') . '/' . ltrim($path, '/');
}

function input(string $key, $default = null) {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function dt(?string $datetime, string $format = 'd/m/Y H:i'): string {
    if (!$datetime) return '';
    return date($format, strtotime($datetime));
}

function slugify(string $s): string {
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    $s = preg_replace('~[^\pL\d]+~u', '-', $s);
    $s = trim($s, '-');
    return strtolower($s ?: 'item');
}
