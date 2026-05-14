<?php
class ReportController {
    public function __construct() { Auth::requireLogin(); }

    public function index(): void {
        $from = $_GET['from'] ?? date('Y-m-01');
        $to   = $_GET['to']   ?? date('Y-m-t');

        // Resumen ventas
        $summary = Database::fetch(
            "SELECT COUNT(*) AS num, SUM(subtotal) AS sub, SUM(importe_gravado_15) AS g15,
                    SUM(importe_gravado_18) AS g18, SUM(importe_exento) AS ex,
                    SUM(isv_15) AS isv15, SUM(isv_18) AS isv18,
                    SUM(discount) AS disc, SUM(total) AS total
             FROM invoices WHERE issue_date BETWEEN ? AND ? AND status IN ('emitida','pagada')",
            [$from, $to]
        );

        // Detalle (libro de ventas)
        $invoices = Database::fetchAll(
            "SELECT * FROM invoices WHERE issue_date BETWEEN ? AND ? ORDER BY issue_date, number",
            [$from, $to]
        );

        // Por servicio
        $byService = Database::fetchAll(
            "SELECT s.name, COUNT(*) AS cant, SUM(ii.total) AS total
             FROM invoice_items ii JOIN services s ON s.id = ii.service_id
             JOIN invoices i ON i.id = ii.invoice_id
             WHERE i.issue_date BETWEEN ? AND ? AND i.status IN ('emitida','pagada')
             GROUP BY s.id, s.name ORDER BY total DESC LIMIT 20",
            [$from, $to]
        );

        // Por terapeuta (comisiones)
        $byTherapist = Database::fetchAll(
            "SELECT u.name, COUNT(a.id) AS cant, SUM(a.price) AS total
             FROM appointments a JOIN users u ON u.id = a.therapist_id
             WHERE DATE(a.starts_at) BETWEEN ? AND ? AND a.status = 'completada'
             GROUP BY u.id, u.name ORDER BY total DESC",
            [$from, $to]
        );

        view('admin/reportes/index', compact('from','to','summary','invoices','byService','byTherapist'));
    }

    /** Libro de ventas SAR - CSV exportable */
    public function exportSar(): void {
        Auth::requireLogin();
        $from = $_GET['from'] ?? date('Y-m-01');
        $to   = $_GET['to']   ?? date('Y-m-t');
        $invoices = Database::fetchAll(
            "SELECT * FROM invoices WHERE issue_date BETWEEN ? AND ? ORDER BY issue_date, number",
            [$from, $to]
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="libro_ventas_'.$from.'_a_'.$to.'.csv"');
        $out = fopen('php://output', 'w');
        // BOM UTF-8
        fputs($out, "\xEF\xBB\xBF");
        fputcsv($out, [
            'Fecha','Número','Tipo','CAI','RTN Cliente','Nombre Cliente',
            'Importe Exento','Importe Gravado 15%','Importe Gravado 18%',
            'ISV 15%','ISV 18%','Descuento','Total','Estado','Forma de pago'
        ], ';');
        foreach ($invoices as $i) {
            fputcsv($out, [
                $i['issue_date'], $i['number'], $i['document_type'], $i['cai'],
                $i['customer_rtn'], $i['customer_name'],
                $i['importe_exento'], $i['importe_gravado_15'], $i['importe_gravado_18'],
                $i['isv_15'], $i['isv_18'], $i['discount'], $i['total'],
                $i['status'], $i['payment_method']
            ], ';');
        }
        fclose($out);
        exit;
    }
}
