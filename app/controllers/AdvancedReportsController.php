<?php
/**
 * Reportes avanzados de inteligencia de negocio:
 * - Mapa de calor de horas
 * - Tasa de retención 30/60/90 días
 * - Clientes en riesgo de fuga
 * - Forecast de ingresos
 * - Comparativa de terapeutas
 */
class AdvancedReportsController {
    public function __construct() { Auth::requireLogin(); }

    public function index(): void {
        view('admin/reportes/advanced');
    }

    /** Mapa de calor: ocupación por día de semana + hora */
    public function heatmap(): void {
        $rows = Database::fetchAll(
            "SELECT DAYOFWEEK(starts_at) AS dow, HOUR(starts_at) AS hr, COUNT(*) AS cnt
             FROM appointments
             WHERE starts_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
               AND status IN ('confirmada','completada')
             GROUP BY dow, hr"
        );
        $grid = [];
        for ($d = 1; $d <= 7; $d++) for ($h = 8; $h <= 21; $h++) $grid[$d][$h] = 0;
        foreach ($rows as $r) $grid[$r['dow']][$r['hr']] = (int)$r['cnt'];
        view('admin/reportes/heatmap', compact('grid'));
    }

    /** Tasa de retención (clientes que vuelven dentro de X días) */
    public function retention(): void {
        $sql = "SELECT
            (SELECT COUNT(DISTINCT customer_id) FROM appointments WHERE starts_at >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND starts_at < DATE_SUB(NOW(), INTERVAL 90 DAY)) AS base,
            (SELECT COUNT(DISTINCT a.customer_id) FROM appointments a WHERE a.starts_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND a.customer_id IN
                (SELECT customer_id FROM appointments WHERE starts_at >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND starts_at < DATE_SUB(NOW(), INTERVAL 90 DAY))
            ) AS r30,
            (SELECT COUNT(DISTINCT a.customer_id) FROM appointments a WHERE a.starts_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND a.customer_id IN
                (SELECT customer_id FROM appointments WHERE starts_at >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND starts_at < DATE_SUB(NOW(), INTERVAL 90 DAY))
            ) AS r60,
            (SELECT COUNT(DISTINCT a.customer_id) FROM appointments a WHERE a.starts_at >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND a.customer_id IN
                (SELECT customer_id FROM appointments WHERE starts_at >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND starts_at < DATE_SUB(NOW(), INTERVAL 90 DAY))
            ) AS r90
        ";
        $row = Database::fetch($sql) ?: ['base'=>0,'r30'=>0,'r60'=>0,'r90'=>0];
        view('admin/reportes/retention', compact('row'));
    }

    /** Clientes en riesgo de fuga (no vienen hace 60+ días) */
    public function atRisk(): void {
        $customers = Database::fetchAll(
            "SELECT c.*,
                (SELECT MAX(starts_at) FROM appointments WHERE customer_id = c.id) AS last_visit,
                (SELECT COUNT(*) FROM appointments WHERE customer_id = c.id) AS visits,
                (SELECT COALESCE(SUM(total),0) FROM invoices WHERE customer_id = c.id AND status IN ('emitida','pagada')) AS lifetime_value
             FROM customers c
             WHERE EXISTS (SELECT 1 FROM appointments WHERE customer_id = c.id)
               AND NOT EXISTS (SELECT 1 FROM appointments WHERE customer_id = c.id AND starts_at >= DATE_SUB(NOW(), INTERVAL 60 DAY))
             ORDER BY lifetime_value DESC LIMIT 100"
        );
        view('admin/reportes/at_risk', compact('customers'));
    }

    /** Forecast: ingresos previstos según citas confirmadas */
    public function forecast(): void {
        $rows = Database::fetchAll(
            "SELECT DATE(starts_at) AS day, SUM(price) AS expected
             FROM appointments
             WHERE status IN ('confirmada','pendiente') AND starts_at >= CURDATE() AND starts_at < DATE_ADD(CURDATE(), INTERVAL 30 DAY)
             GROUP BY DATE(starts_at) ORDER BY day"
        );
        view('admin/reportes/forecast', compact('rows'));
    }

    /** Comparativa de terapeutas */
    public function therapists(): void {
        $from = $_GET['from'] ?? date('Y-m-01');
        $to   = $_GET['to']   ?? date('Y-m-t');
        $data = Database::fetchAll(
            "SELECT u.id, u.name,
                COUNT(a.id) AS sessions,
                SUM(CASE WHEN a.status = 'completada' THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN a.status = 'no_show' THEN 1 ELSE 0 END) AS no_shows,
                SUM(CASE WHEN a.status = 'completada' THEN a.price ELSE 0 END) AS revenue,
                COUNT(DISTINCT a.customer_id) AS unique_customers,
                AVG(s.nps) AS avg_nps
             FROM users u
             LEFT JOIN appointments a ON a.therapist_id = u.id AND DATE(a.starts_at) BETWEEN ? AND ?
             LEFT JOIN surveys s ON s.appointment_id = a.id
             WHERE u.role = 'terapeuta' AND u.active = 1
             GROUP BY u.id, u.name
             ORDER BY revenue DESC", [$from, $to]
        );
        view('admin/reportes/therapists', compact('data','from','to'));
    }
}
