<?php
class CashSession
{
    public static function current(): ?array
    {
        return Database::one(
            'SELECT cs.*, u.name AS opened_by_name
             FROM cash_sessions cs JOIN users u ON u.id = cs.opened_by
             WHERE cs.closed_at IS NULL
             ORDER BY cs.opened_at DESC LIMIT 1'
        );
    }

    public static function find(int $id): ?array
    {
        return Database::one(
            'SELECT cs.*, u1.name AS opened_by_name, u2.name AS closed_by_name
             FROM cash_sessions cs
             JOIN users u1 ON u1.id = cs.opened_by
             LEFT JOIN users u2 ON u2.id = cs.closed_by
             WHERE cs.id = ?', [$id]
        );
    }

    public static function open(int $userId, float $openingAmount = 0, ?string $notes = null): int
    {
        if (self::current()) {
            throw new RuntimeException('Ya hay una caja abierta. Ciérrala antes de abrir otra.');
        }
        return Database::insert('cash_sessions', [
            'opened_at'      => now(),
            'opening_amount' => $openingAmount,
            'opened_by'      => $userId,
            'notes'          => $notes,
        ]);
    }

    public static function expectedCash(int $sessionId): float
    {
        $session = self::find($sessionId);
        if (!$session) return 0;

        $opening = (float)$session['opening_amount'];
        $income  = (float)Database::value(
            "SELECT IFNULL(SUM(amount),0) FROM payments
             WHERE method = 'cash' AND paid_at BETWEEN ? AND IFNULL(?, NOW())",
            [$session['opened_at'], $session['closed_at']]
        );
        $expenses = (float)Database::value(
            "SELECT IFNULL(SUM(amount),0) FROM expenses
             WHERE method = 'cash' AND cash_session_id = ?",
            [$sessionId]
        );
        return $opening + $income - $expenses;
    }

    public static function close(int $sessionId, int $userId, float $closingAmount, ?string $notes = null): array
    {
        $expected = self::expectedCash($sessionId);
        $diff = round($closingAmount - $expected, 2);
        Database::update('cash_sessions', [
            'closed_at'      => now(),
            'closing_amount' => $closingAmount,
            'expected_cash'  => $expected,
            'difference'     => $diff,
            'notes'          => $notes,
            'closed_by'      => $userId,
        ], ['id' => $sessionId]);
        return ['expected' => $expected, 'closing' => $closingAmount, 'difference' => $diff];
    }

    public static function history(int $limit = 30): array
    {
        return Database::query(
            'SELECT cs.*, u1.name AS opened_by_name, u2.name AS closed_by_name
             FROM cash_sessions cs
             JOIN users u1 ON u1.id = cs.opened_by
             LEFT JOIN users u2 ON u2.id = cs.closed_by
             ORDER BY cs.opened_at DESC LIMIT ' . (int)$limit
        );
    }

    public static function movementsFor(int $sessionId): array
    {
        $session = self::find($sessionId);
        if (!$session) return [];

        $payments = Database::query(
            "SELECT pm.id, pm.paid_at AS at, pm.amount, pm.method, pm.reference, 'payment' AS kind,
                    CONCAT('Factura ', i.code, ' - ', p.first_name, ' ', p.last_name) AS description
             FROM payments pm
             JOIN invoices i ON i.id = pm.invoice_id
             JOIN patients p ON p.id = i.patient_id
             WHERE pm.paid_at BETWEEN ? AND IFNULL(?, NOW())
             ORDER BY pm.paid_at",
            [$session['opened_at'], $session['closed_at']]
        );
        $expenses = Database::query(
            "SELECT id, created_at AS at, amount, method, '' AS reference, 'expense' AS kind, description
             FROM expenses WHERE cash_session_id = ? ORDER BY created_at",
            [$sessionId]
        );
        return ['payments' => $payments, 'expenses' => $expenses];
    }
}

class Expense
{
    public static function create(array $data): int
    {
        $allowed = ['expense_date','category','description','amount','method',
                    'cash_session_id','receipt_doc','created_by'];
        return Database::insert('expenses', only($data, $allowed));
    }

    public static function recent(int $days = 30): array
    {
        return Database::query(
            "SELECT e.*, u.name AS created_by_name
             FROM expenses e LEFT JOIN users u ON u.id = e.created_by
             WHERE e.expense_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             ORDER BY e.expense_date DESC, e.id DESC", [$days]
        );
    }

    public static function summary(string $from, string $to): array
    {
        return Database::query(
            'SELECT category, COUNT(*) AS n, IFNULL(SUM(amount),0) AS total
             FROM expenses WHERE expense_date BETWEEN ? AND ?
             GROUP BY category ORDER BY total DESC',
            [$from, $to]
        );
    }
}
