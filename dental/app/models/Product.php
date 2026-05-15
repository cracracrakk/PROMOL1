<?php
class Product
{
    public static function all(bool $onlyActive = true): array
    {
        $sql = 'SELECT * FROM products';
        if ($onlyActive) $sql .= ' WHERE is_active = 1';
        $sql .= ' ORDER BY name';
        return Database::query($sql);
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM products WHERE id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        $allowed = ['sku','name','category','unit','stock','min_stock','cost','supplier','notes','is_active'];
        return Database::insert('products', only($data, $allowed));
    }

    public static function update(int $id, array $data): int
    {
        $allowed = ['sku','name','category','unit','min_stock','cost','supplier','notes','is_active'];
        return Database::update('products', only($data, $allowed), ['id' => $id]);
    }

    public static function move(int $productId, string $type, float $qty, ?string $reason = null, ?int $userId = null): void
    {
        if (!in_array($type, ['in','out','adjust'], true)) {
            throw new InvalidArgumentException('Tipo de movimiento inválido');
        }
        Database::transaction(function () use ($productId, $type, $qty, $reason, $userId) {
            Database::insert('product_movements', [
                'product_id' => $productId,
                'type'       => $type,
                'quantity'   => $qty,
                'reason'     => $reason,
                'user_id'    => $userId,
            ]);
            $delta = $type === 'in' ? $qty : ($type === 'out' ? -$qty : 0);
            if ($type === 'adjust') {
                Database::update('products', ['stock' => $qty], ['id' => $productId]);
            } else {
                Database::execute('UPDATE products SET stock = stock + ? WHERE id = ?', [$delta, $productId]);
            }
        });
    }

    public static function lowStock(): array
    {
        return Database::query(
            'SELECT * FROM products
             WHERE is_active = 1 AND stock <= min_stock
             ORDER BY (stock - min_stock)'
        );
    }

    public static function movementsFor(int $productId, int $limit = 50): array
    {
        return Database::query(
            'SELECT m.*, u.name AS user_name
             FROM product_movements m LEFT JOIN users u ON u.id = m.user_id
             WHERE m.product_id = ? ORDER BY m.movement_date DESC LIMIT ' . (int)$limit,
            [$productId]
        );
    }
}
