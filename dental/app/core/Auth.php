<?php
class Auth
{
    public static function attempt(string $email, string $password): ?array
    {
        $user = Database::one(
            'SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1',
            [$email]
        );
        if (!$user) return null;
        if (!password_verify($password, $user['password_hash'])) return null;

        Database::execute('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$user['id']]);
        unset($user['password_hash']);
        $_SESSION['user'] = $user;
        return $user;
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    public static function log(string $action, string $entity, $entityId = null, array $payload = []): void
    {
        try {
            Database::insert('audit_log', [
                'user_id'   => self::id(),
                'action'    => $action,
                'entity'    => $entity,
                'entity_id' => $entityId !== null ? (string)$entityId : null,
                'payload'   => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'ip'        => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent'=> substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ]);
        } catch (Throwable $e) {}
    }
}
