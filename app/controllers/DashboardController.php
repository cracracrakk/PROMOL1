<?php
require_once dirname(__DIR__) . '/core/SarHelper.php';

class DashboardController {

    public function index(): void {
        Auth::requireLogin();

        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd   = date('Y-m-t');
        $lastMonth  = date('Y-m-01', strtotime('first day of last month'));
        $lastMonthEnd = date('Y-m-t', strtotime('last day of last month'));

        // KPIs
        $kpis = [
            'today_appointments' => Database::fetch("SELECT COUNT(*) c FROM appointments WHERE DATE(starts_at) = ?", [$today])['c'] ?? 0,
            'today_income'       => Database::fetch("SELECT COALESCE(SUM(total),0) s FROM invoices WHERE issue_date = ? AND status IN ('emitida','pagada')", [$today])['s'] ?? 0,
            'month_income'       => Database::fetch("SELECT COALESCE(SUM(total),0) s FROM invoices WHERE issue_date BETWEEN ? AND ? AND status IN ('emitida','pagada')", [$monthStart, $monthEnd])['s'] ?? 0,
            'last_month_income'  => Database::fetch("SELECT COALESCE(SUM(total),0) s FROM invoices WHERE issue_date BETWEEN ? AND ? AND status IN ('emitida','pagada')", [$lastMonth, $lastMonthEnd])['s'] ?? 0,
            'pending_appointments' => Database::fetch("SELECT COUNT(*) c FROM appointments WHERE status = 'pendiente'")['c'] ?? 0,
            'total_customers'    => Database::fetch("SELECT COUNT(*) c FROM customers")['c'] ?? 0,
            'low_stock'          => Database::fetch("SELECT COUNT(*) c FROM products WHERE stock <= stock_min AND active = 1")['c'] ?? 0,
            'unread_messages'    => Database::fetch("SELECT COUNT(*) c FROM contact_messages WHERE read_at IS NULL")['c'] ?? 0,
        ];

        // Crecimiento mensual
        $growth = 0;
        if ($kpis['last_month_income'] > 0) {
            $growth = (($kpis['month_income'] - $kpis['last_month_income']) / $kpis['last_month_income']) * 100;
        }

        // Citas próximas (hoy)
        $todayAppointments = Database::fetchAll(
            "SELECT a.*, c.first_name, c.last_name, c.phone, s.name AS service_name, u.name AS therapist_name
             FROM appointments a
             JOIN customers c ON c.id = a.customer_id
             JOIN services s ON s.id = a.service_id
             LEFT JOIN users u ON u.id = a.therapist_id
             WHERE DATE(a.starts_at) = ?
             ORDER BY a.starts_at ASC",
            [$today]
        );

        // Ingresos últimos 14 días (gráfica)
        $incomeChart = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $row = Database::fetch("SELECT COALESCE(SUM(total),0) s FROM invoices WHERE issue_date = ? AND status IN ('emitida','pagada')", [$d]);
            $incomeChart[] = ['date' => $d, 'total' => (float)$row['s']];
        }

        // Top servicios del mes
        $topServices = Database::fetchAll(
            "SELECT s.name, COUNT(a.id) AS count_apps, SUM(a.price) AS total
             FROM appointments a
             JOIN services s ON s.id = a.service_id
             WHERE a.starts_at BETWEEN ? AND ? AND a.status IN ('completada','confirmada')
             GROUP BY s.id, s.name
             ORDER BY count_apps DESC LIMIT 5",
            [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59']
        );

        // Stock bajo
        $lowStockProducts = Database::fetchAll(
            "SELECT * FROM products WHERE stock <= stock_min AND active = 1 ORDER BY stock ASC LIMIT 5"
        );

        // Alertas SAR
        $sarAlerts = [];
        try { $sarAlerts = SarHelper::getAlerts(); } catch (Throwable $e) {}

        view('admin/dashboard', compact(
            'kpis', 'growth', 'todayAppointments', 'incomeChart',
            'topServices', 'lowStockProducts', 'sarAlerts'
        ));
    }

    public function saveTheme(): void {
        $t = $_GET['t'] ?? 'light';
        $_SESSION['theme'] = ($t === 'dark') ? 'dark' : 'light';
        echo json_encode(['ok' => true]);
    }
}
