<?php
/**
 * Helpers globales del sistema.
 * Todo lo que dependa del negocio (nombre, logo, colores, RTN...) viene de la tabla
 * site_settings — el cliente lo configura desde el módulo "Sistema".
 */

function view(string $path, array $data = []): void {
    extract($data, EXTR_SKIP);
    $file = dirname(__DIR__) . '/views/' . $path . '.php';
    if (!file_exists($file)) {
        http_response_code(500);
        die("Vista no encontrada: $path");
    }
    require $file;
}

function partial(string $path, array $data = []): void {
    extract($data, EXTR_SKIP);
    $file = dirname(__DIR__) . '/views/' . $path . '.php';
    if (file_exists($file)) require $file;
}

function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function redirect(string $url): void { header("Location: $url"); exit; }

function back(): void {
    $ref = $_SERVER['HTTP_REFERER'] ?? '/';
    // Solo permitir redirects al mismo host (anti open redirect)
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $parsed = parse_url($ref);
    if (isset($parsed['host']) && $parsed['host'] !== $host) {
        $ref = '/';
    }
    redirect($ref);
}

function old(string $key, $default = ''): string {
    $v = $_SESSION['old'][$key] ?? $default;
    return e((string)$v);
}

function rememberOld(): void {
    $_SESSION['old'] = $_POST;
}

function forgetOld(): void { unset($_SESSION['old']); }

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

/**
 * Formatea moneda según configuración del cliente (símbolo en settings o .env).
 */
function money($amount): string {
    $symbol = setting('currency_symbol', $GLOBALS['app_config']['currency']['symbol'] ?? 'L.');
    return $symbol . ' ' . number_format((float)$amount, 2, '.', ',');
}

/**
 * Lee una configuración de la tabla site_settings con caché por request.
 */
function setting(string $key, $default = '') {
    static $cache = null;
    if ($cache === null) {
        try {
            $rows = Database::fetchAll('SELECT `key`, `value` FROM site_settings');
            $cache = [];
            foreach ($rows as $r) $cache[$r['key']] = $r['value'];
        } catch (Throwable $e) {
            $cache = [];
        }
    }
    return $cache[$key] ?? $default;
}

function setSetting(string $key, $value): void {
    Database::query(
        "INSERT INTO site_settings (`key`,`value`) VALUES (?,?)
         ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)",
        [$key, $value]
    );
}

function settingsAll(): array {
    $rows = Database::fetchAll('SELECT `key`, `value` FROM site_settings');
    $out = [];
    foreach ($rows as $r) $out[$r['key']] = $r['value'];
    return $out;
}

function url(string $path = ''): string {
    $base = setting('app_url', $GLOBALS['app_config']['app']['url'] ?? '');
    if (!$base) $base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string {
    return url('assets/' . ltrim($path, '/'));
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

function activeNav(string $segment): string {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    return strpos($uri, $segment) !== false ? 'active' : '';
}

function audit(string $action, string $entity = null, int $entityId = null, string $description = null): void {
    try {
        Database::insert('audit_log', [
            'user_id'    => Auth::id(),
            'action'     => $action,
            'entity'     => $entity,
            'entity_id'  => $entityId,
            'description'=> $description,
            'ip'         => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);
    } catch (Throwable $e) { /* silencioso */ }
}

function randomCode(int $length = 10): string {
    $chars = 'ABCDEFGHIJKLMNPQRSTUVWXYZ23456789';
    $out = '';
    for ($i = 0; $i < $length; $i++) $out .= $chars[random_int(0, strlen($chars) - 1)];
    return $out;
}

function brand_name(): string {
    return setting('spa_name', $GLOBALS['app_config']['app']['name'] ?? 'Sistema');
}

function brand_logo(): ?string {
    $logo = setting('spa_logo', '');
    if (!$logo) return null;
    $path = dirname(__DIR__, 2) . '/public/assets/uploads/' . $logo;
    return file_exists($path) ? asset('uploads/' . $logo) : null;
}

/**
 * Construye una URL de WhatsApp con mensaje precargado.
 * El cliente puede hacer click directamente para enviarle al spa.
 */
function whatsapp_link(string $phone, string $message = ''): string {
    $phone = preg_replace('/\D/', '', $phone);
    return 'https://wa.me/' . $phone . ($message ? '?text=' . urlencode($message) : '');
}

/**
 * Mensaje predefinido de confirmación de cita por WhatsApp.
 */
function whatsapp_appointment_link(array $appointment, array $customer, string $service): string {
    $phone = $customer['phone'] ?? '';
    $name  = trim($customer['first_name'] . ' ' . ($customer['last_name'] ?? ''));
    $date  = dt($appointment['starts_at'], 'd/m/Y');
    $time  = dt($appointment['starts_at'], 'H:i');
    $brand = brand_name();
    $msg   = "Hola {$name}, soy de {$brand}. Te confirmamos tu cita de {$service} para el {$date} a las {$time}. ¿Te queda bien?";
    return whatsapp_link($phone, $msg);
}
