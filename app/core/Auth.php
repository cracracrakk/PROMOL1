<?php
class Auth {
    public static function attempt(string $email, string $password): bool {
        $user = Database::fetch('SELECT * FROM users WHERE email = ? AND active = 1', [$email]);
        if (!$user) return false;
        if (!password_verify($password, $user['password'])) return false;

        unset($user['password']);
        $_SESSION['user'] = $user;
        $_SESSION['logged_in_at'] = time();
        session_regenerate_id(true);
        return true;
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
