<?php
class Auth {
    public const MAX_ATTEMPTS = 5;
    public const ATTEMPT_WINDOW = 900; // 15 min

    public static function attempt(string $email, string $password): bool {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        if (self::isRateLimited($ip)) {
            return false;
        }

        $user = Database::fetch('SELECT * FROM users WHERE email = ? AND active = 1', [$email]);
        if (!$user || !password_verify($password, $user['password'])) {
            self::logAttempt($email, $ip, false);
            return false;
        }

        self::logAttempt($email, $ip, true);

        // Si tiene 2FA activado, no logueamos todavía; guardamos id pendiente
        if (!empty($user['two_factor_secret'])) {
            $_SESSION['pending_2fa_user'] = (int)$user['id'];
            return true;
        }

        unset($user['password']);
        $_SESSION['user'] = $user;
        $_SESSION['logged_in_at'] = time();
        session_regenerate_id(true);
        return true;
    }

    public static function verifyTwoFactor(string $code): bool {
        $userId = $_SESSION['pending_2fa_user'] ?? null;
        if (!$userId) return false;
        $user = Database::fetch('SELECT * FROM users WHERE id = ?', [$userId]);
        if (!$user || empty($user['two_factor_secret'])) return false;

        require_once __DIR__ . '/Totp.php';
        if (!Totp::verify($user['two_factor_secret'], $code)) return false;

        unset($_SESSION['pending_2fa_user']);
        unset($user['password']);
        $_SESSION['user'] = $user;
        $_SESSION['logged_in_at'] = time();
        session_regenerate_id(true);
        return true;
    }

    public static function isPendingTwoFactor(): bool {
        return !empty($_SESSION['pending_2fa_user']);
    }

    public static function isRateLimited(string $ip): bool {
        $row = Database::fetch(
            'SELECT COUNT(*) AS c FROM login_attempts
             WHERE ip = ? AND success = 0 AND created_at >= DATE_SUB(NOW(), INTERVAL ? SECOND)',
            [$ip, self::ATTEMPT_WINDOW]
        );
        return (int)$row['c'] >= self::MAX_ATTEMPTS;
    }

    private static function logAttempt(string $email, string $ip, bool $success): void {
        try {
            Database::insert('login_attempts', [
                'email' => $email, 'ip' => $ip, 'success' => $success ? 1 : 0,
            ]);
        } catch (Throwable $e) { /* silencioso */ }
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool { return isset($_SESSION['user']); }

    public static function user(): ?array { return $_SESSION['user'] ?? null; }

    public static function id(): ?int { return $_SESSION['user']['id'] ?? null; }

    public static function role(): ?string { return $_SESSION['user']['role'] ?? null; }

    public static function requireLogin(): void {
        if (!self::check()) {
            $_SESSION['flash_error'] = 'Debes iniciar sesión.';
            header('Location: /login');
            exit;
        }
    }

    public static function requireRole(array $roles): void {
        self::requireLogin();
        if (!in_array(self::role(), $roles, true)) {
            http_response_code(403);
            die('No tienes permisos para acceder a esta sección.');
        }
    }
}
