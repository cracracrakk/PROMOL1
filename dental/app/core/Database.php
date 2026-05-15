<?php
// Wrapper PDO con métodos estáticos sencillos
class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo) return self::$pdo;

        $cfg = cfg('db');
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $cfg['host'], $cfg['port'], $cfg['database'], $cfg['charset']
        );
        self::$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return self::$pdo;
    }

    public static function query(string $sql, array $bindings = []): array
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    public static function one(string $sql, array $bindings = []): ?array
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($bindings);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function value(string $sql, array $bindings = [])
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($bindings);
        $v = $stmt->fetchColumn();
        return $v === false ? null : $v;
    }

    public static function execute(string $sql, array $bindings = []): int
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    public static function insert(string $table, array $data): int
    {
        $cols = array_keys($data);
        $place = array_map(fn($c) => ':' . $c, $cols);
        $sql = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%s)',
            $table, implode('`,`', $cols), implode(',', $place)
        );
        $stmt = self::pdo()->prepare($sql);
        foreach ($data as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->execute();
        return (int)self::pdo()->lastInsertId();
    }

    public static function update(string $table, array $data, array $where): int
    {
        if (!$data) return 0;
        $set = [];
        foreach (array_keys($data) as $c) $set[] = "`$c` = :set_$c";
        $whereSql = [];
        foreach (array_keys($where) as $c) $whereSql[] = "`$c` = :w_$c";

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $table, implode(', ', $set), implode(' AND ', $whereSql)
        );
        $stmt = self::pdo()->prepare($sql);
        foreach ($data as $k => $v)  $stmt->bindValue(":set_$k", $v);
        foreach ($where as $k => $v) $stmt->bindValue(":w_$k",   $v);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public static function delete(string $table, array $where): int
    {
        $whereSql = [];
        foreach (array_keys($where) as $c) $whereSql[] = "`$c` = :w_$c";
        $sql = sprintf('DELETE FROM `%s` WHERE %s', $table, implode(' AND ', $whereSql));
        $stmt = self::pdo()->prepare($sql);
        foreach ($where as $k => $v) $stmt->bindValue(":w_$k", $v);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public static function transaction(callable $fn)
    {
        $pdo = self::pdo();
        $pdo->beginTransaction();
        try {
            $res = $fn($pdo);
            $pdo->commit();
            return $res;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
