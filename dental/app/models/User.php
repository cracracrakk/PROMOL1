<?php
class User
{
    public static function all(): array
    {
        return Database::query(
            'SELECT id, name, email, role, specialty, license_number, phone, is_active, last_login_at
             FROM users ORDER BY name'
        );
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function dentists(): array
    {
        return Database::query(
            "SELECT id, name, specialty FROM users
             WHERE role = 'dentist' AND is_active = 1
             ORDER BY name"
        );
    }

    public static function create(array $data): int
    {
        $allowed = ['name','email','role','license_number','specialty','phone','is_active'];
        $clean = only($data, $allowed);
        $clean['password_hash'] = Auth::hash($data['password'] ?? 'changeme');
        return Database::insert('users', $clean);
    }

    public static function update(int $id, array $data): int
    {
        $allowed = ['name','email','role','license_number','specialty','phone','is_active'];
        $clean = only($data, $allowed);
        if (!empty($data['password'])) {
            $clean['password_hash'] = Auth::hash($data['password']);
        }
        return Database::update('users', $clean, ['id' => $id]);
    }
}
