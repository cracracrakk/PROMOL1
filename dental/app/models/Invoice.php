<?php
class Invoice
{
    public static function nextCode(): string
    {
        $last = (int)Database::value('SELECT IFNULL(MAX(id),0) FROM invoices');
        return next_code('F', $last);
    }

    public static function find(int $id): ?array
    {
        $inv = Database::one(
            'SELECT i.*,
                    CONCAT(p.first_name," ",p.last_name) AS patient_name,
                    p.code AS patient_code, p.document_number AS patient_doc,
                    p.address AS patient_address, p.email AS patient_email,
                    p.phone AS patient_phone
             FROM invoices i
             JOIN patients p ON p.id = i.patient_id
             WHERE i.id = ?', [$id]
        );
        if (!$inv) return null;
        $inv['items']    = Database::query('SELECT * FROM invoice_items WHERE invoice_id = ?', [$id]);
        $inv['payments'] = Database::query(
            'SELECT pm.*, u.name AS received_by_name
             FROM payments pm LEFT JOIN users u ON u.id = pm.received_by
             WHERE pm.invoice_id = ? ORDER BY pm.paid_at DESC', [$id]
        );
        return $inv;
    }

    public static function search(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $where = 'WHERE 1=1';
        $params = [];
        if (!empty($filters['status'])) {
            $where .= ' AND i.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['patient_id'])) {
            $where .= ' AND i.patient_id = ?';
            $params[] = $filters['patient_id'];
        }
        if (!empty($filters['from'])) {
            $where .= ' AND i.issue_date >= ?';
            $params[] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $where .= ' AND i.issue_date <= ?';
            $params[] = $filters['to'];
        }

        $rows = Database::query(
            "SELECT i.*, CONCAT(p.first_name,' ',p.last_name) AS patient_name, p.code AS patient_code
             FROM invoices i JOIN patients p ON p.id = i.patient_id
             $where ORDER BY i.issue_date DESC, i.id DESC
             LIMIT $perPage OFFSET $offset", $params
        );
        $total = (int)Database::value(
            "SELECT COUNT(*) FROM invoices i JOIN patients p ON p.id = i.patient_id $where",
            $params
        );

        return [
            'data' => $rows, 'total' => $total,
            'page' => $page, 'per_page' => $perPage,
            'last_page' => max(1, (int)ceil($total / $perPage)),
        ];
    }

    public static function create(array $data, array $items): int
    {
        return Database::transaction(function () use ($data, $items) {
            $subtotal = 0;
            foreach ($items as $it) $subtotal += (float)$it['line_total'];
            $discount = (float)($data['discount'] ?? 0);
            $taxRate  = (float)($data['tax_rate'] ?? 0);
            $base     = max(0, $subtotal - $discount);
            $tax      = round($base * $taxRate / 100, 2);
            $total    = round($base + $tax, 2);

            $data['code']       = $data['code'] ?? self::nextCode();
            $data['subtotal']   = $subtotal;
            $data['tax_amount'] = $tax;
            $data['total']      = $total;
            $data['paid']       = 0;
            $data['issue_date'] = $data['issue_date'] ?? date('Y-m-d');
            $data['status']     = $data['status'] ?? 'issued';

            $allowed = ['code','patient_id','treatment_plan_id','issue_date','due_date',
                        'subtotal','discount','tax_rate','tax_amount','total','status','notes','created_by'];
            $id = Database::insert('invoices', only($data, $allowed));

            foreach ($items as $it) {
                $it['invoice_id'] = $id;
                $itAllowed = ['invoice_id','treatment_id','description','tooth_code',
                              'quantity','unit_price','line_total'];
                Database::insert('invoice_items', only($it, $itAllowed));
            }
            return $id;
        });
    }

    public static function updateStatus(int $id, string $status): int
    {
        return Database::update('invoices', ['status' => $status], ['id' => $id]);
    }

    public static function recalcPaid(int $id): void
    {
        $paid = (float)Database::value(
            'SELECT IFNULL(SUM(amount),0) FROM payments WHERE invoice_id = ?', [$id]
        );
        $total = (float)Database::value('SELECT total FROM invoices WHERE id = ?', [$id]);
        $status = $paid <= 0 ? 'issued' : ($paid >= $total ? 'paid' : 'partial');
        Database::update('invoices', ['paid' => $paid, 'status' => $status], ['id' => $id]);
    }

    public static function summary(string $from, string $to): array
    {
        $row = Database::one(
            "SELECT COUNT(*) AS n,
                    IFNULL(SUM(total),0) AS billed,
                    IFNULL(SUM(paid),0)  AS collected,
                    IFNULL(SUM(total - paid),0) AS pending
             FROM invoices
             WHERE issue_date BETWEEN ? AND ? AND status != 'cancelled'",
            [$from, $to]
        );
        return $row ?: ['n' => 0, 'billed' => 0, 'collected' => 0, 'pending' => 0];
    }
}

class Payment
{
    public static function create(array $data): int
    {
        return Database::transaction(function () use ($data) {
            $allowed = ['invoice_id','paid_at','amount','method','reference','received_by','notes'];
            $id = Database::insert('payments', only($data, $allowed));
            Invoice::recalcPaid((int)$data['invoice_id']);
            return $id;
        });
    }

    public static function delete(int $id): void
    {
        $payment = Database::one('SELECT * FROM payments WHERE id = ?', [$id]);
        if (!$payment) return;
        Database::transaction(function () use ($payment, $id) {
            Database::delete('payments', ['id' => $id]);
            Invoice::recalcPaid((int)$payment['invoice_id']);
        });
    }

    public static function summary(string $from, string $to): array
    {
        return Database::query(
            "SELECT method, COUNT(*) AS n, IFNULL(SUM(amount),0) AS total
             FROM payments
             WHERE DATE(paid_at) BETWEEN ? AND ?
             GROUP BY method",
            [$from, $to]
        );
    }
}
