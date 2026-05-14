<?php
class CashController {
    public function __construct() { Auth::requireLogin(); }

    public function index(): void {
        $date = $_GET['date'] ?? date('Y-m-d');

        $byMethod = Database::fetchAll(
            "SELECT payment_method, COUNT(*) AS n, SUM(total) AS s
             FROM invoices WHERE issue_date = ? AND status IN ('emitida','pagada')
             GROUP BY payment_method", [$date]
        );
        $methods = [];
        foreach ($byMethod as $r) $methods[$r['payment_method']] = $r;

        $expectedCash  = $methods['efectivo']['s'] ?? 0;
        $expectedCard  = $methods['tarjeta']['s'] ?? 0;
        $expectedOther = array_sum(array_map(fn($r) => $r['payment_method'] !== 'efectivo' && $r['payment_method'] !== 'tarjeta' ? $r['s'] : 0, $byMethod));

        $invoices = Database::fetchAll(
            "SELECT * FROM invoices WHERE issue_date = ? AND status IN ('emitida','pagada') ORDER BY id DESC", [$date]
        );

        $closing = Database::fetch('SELECT * FROM cash_closings WHERE closing_date = ?', [$date]);

        view('admin/caja/index', compact('date','methods','expectedCash','expectedCard','expectedOther','invoices','closing'));
    }

    public function close(): void {
        csrf_verify();
        $date = $_POST['date'] ?? date('Y-m-d');
        $countedCash = (float)$_POST['counted_cash'];
        $expectedCash = (float)$_POST['expected_cash'];
        $expectedCard = (float)$_POST['expected_card'];
        $expectedOther = (float)$_POST['expected_other'];
        $difference = $countedCash - $expectedCash;

        $existing = Database::fetch('SELECT id FROM cash_closings WHERE closing_date = ?', [$date]);
        $data = [
            'closing_date'   => $date,
            'expected_cash'  => $expectedCash,
            'counted_cash'   => $countedCash,
            'expected_card'  => $expectedCard,
            'expected_other' => $expectedOther,
            'difference'     => $difference,
            'notes'          => trim($_POST['notes'] ?? ''),
            'user_id'        => Auth::id(),
        ];
        if ($existing) {
            Database::update('cash_closings', $data, 'id = :id', ['id' => $existing['id']]);
        } else {
            Database::insert('cash_closings', $data);
        }
        audit('cash_closing','cash',null,$date);
        flash('success','Cierre de caja registrado.');
        redirect('/admin/caja?date=' . $date);
    }
}
