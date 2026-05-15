<?php
// Helpers globales

function cfg(string $key, $default = null) {
    $parts = explode('.', $key);
    $cur = $GLOBALS['app_config'] ?? [];
    foreach ($parts as $p) {
        if (!is_array($cur) || !array_key_exists($p, $cur)) return $default;
        $cur = $cur[$p];
    }
    return $cur;
}

function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string {
    $base = rtrim((string)cfg('app.url', ''), '/');
    return $base . '/' . ltrim($path, '/');
}

function redirect(string $path, int $code = 302): void {
    header('Location: ' . $path, true, $code);
    exit;
}

function json_response($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function input(string $key, $default = null) {
    $src = array_merge($_GET, $_POST);
    return $src[$key] ?? $default;
}

function flash(string $type, ?string $msg = null) {
    if ($msg === null) {
        $msg = $_SESSION['_flash'][$type] ?? null;
        unset($_SESSION['_flash'][$type]);
        return $msg;
    }
    $_SESSION['_flash'][$type] = $msg;
}

function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): bool {
    $t = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return is_string($t) && hash_equals($_SESSION['_csrf'] ?? '', $t);
}

function money(float $amount): string {
    return cfg('app.currency_symbol', 'L') . ' ' . number_format($amount, 2);
}

function format_date(?string $date, string $fmt = 'd/m/Y'): string {
    if (!$date) return '';
    $ts = strtotime($date);
    return $ts ? date($fmt, $ts) : '';
}

function age_from(?string $birth): ?int {
    if (!$birth) return null;
    $d = date_create($birth);
    if (!$d) return null;
    return (int)date_diff($d, date_create('now'))->y;
}

function next_code(string $prefix, ?int $lastId = null): string {
    $n = ($lastId ?? 0) + 1;
    return sprintf('%s-%06d', $prefix, $n);
}

function now(): string {
    return date('Y-m-d H:i:s');
}

function only(array $src, array $keys): array {
    $out = [];
    foreach ($keys as $k) {
        if (array_key_exists($k, $src)) $out[$k] = $src[$k];
    }
    return $out;
}

function setting(string $key, $default = null) {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            $rows = Database::query('SELECT `key`,`value` FROM settings');
            foreach ($rows as $r) $cache[$r['key']] = $r['value'];
        } catch (Throwable $e) {}
    }
    return $cache[$key] ?? $default;
}

function teeth_permanent(): array {
    // FDI: 18..11, 21..28 (superior); 48..41, 31..38 (inferior)
    return [
        ['18','17','16','15','14','13','12','11','21','22','23','24','25','26','27','28'],
        ['48','47','46','45','44','43','42','41','31','32','33','34','35','36','37','38'],
    ];
}

function teeth_deciduous(): array {
    return [
        ['55','54','53','52','51','61','62','63','64','65'],
        ['85','84','83','82','81','71','72','73','74','75'],
    ];
}
