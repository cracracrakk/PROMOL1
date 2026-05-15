<?php
class Setting
{
    public static function all(): array
    {
        $rows = Database::query('SELECT `key`,`value` FROM settings');
        $out = [];
        foreach ($rows as $r) $out[$r['key']] = $r['value'];
        return $out;
    }

    public static function set(string $key, $value): void
    {
        Database::execute(
            'INSERT INTO settings(`key`,`value`) VALUES(?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
            [$key, $value === null ? null : (string)$value]
        );
    }

    public static function saveMany(array $data): void
    {
        foreach ($data as $k => $v) self::set($k, $v);
    }
}
