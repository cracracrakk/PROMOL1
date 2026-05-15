<?php
class Report
{
    public static function revenueByMonth(int $months = 12): array
    {
        return Database::query(
            "SELECT DATE_FORMAT(issue_date, '%Y-%m') AS month,
                    COUNT(*) AS invoices,
                    IFNULL(SUM(total),0)  AS billed,
                    IFNULL(SUM(paid),0)   AS collected
             FROM invoices
             WHERE status != 'cancelled'
               AND issue_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY DATE_FORMAT(issue_date, '%Y-%m')
             ORDER BY month", [$months]
        );
    }

    public static function revenueByDentist(string $from, string $to): array
    {
        return Database::query(
            "SELECT u.id, u.name,
                    COUNT(DISTINCT i.id) AS invoices,
                    IFNULL(SUM(i.total),0) AS billed,
                    IFNULL(SUM(i.paid),0)  AS collected
             FROM users u
             LEFT JOIN appointments a    ON a.dentist_id = u.id
             LEFT JOIN invoices i        ON i.patient_id = a.patient_id
                                         AND i.issue_date BETWEEN ? AND ?
                                         AND i.status != 'cancelled'
             WHERE u.role = 'dentist' AND u.is_active = 1
             GROUP BY u.id, u.name
             ORDER BY billed DESC",
            [$from, $to]
        );
    }

    public static function topTreatments(string $from, string $to, int $limit = 10): array
    {
        return Database::query(
            "SELECT t.name, COUNT(ii.id) AS n, IFNULL(SUM(ii.line_total),0) AS revenue
             FROM invoice_items ii
             JOIN invoices i ON i.id = ii.invoice_id
             LEFT JOIN treatments t ON t.id = ii.treatment_id
             WHERE i.issue_date BETWEEN ? AND ? AND i.status != 'cancelled'
               AND ii.treatment_id IS NOT NULL
             GROUP BY t.id, t.name
             ORDER BY revenue DESC
             LIMIT " . (int)$limit,
            [$from, $to]
        );
    }

    public static function ageingReceivables(): array
    {
        return Database::query(
            "SELECT i.id, i.code, i.issue_date, i.total, i.paid, (i.total - i.paid) AS balance,
                    CONCAT(p.first_name,' ',p.last_name) AS patient_name, p.code AS patient_code,
                    DATEDIFF(CURDATE(), i.issue_date) AS days_old,
                    CASE
                       WHEN DATEDIFF(CURDATE(), i.issue_date) <= 30 THEN '0-30'
                       WHEN DATEDIFF(CURDATE(), i.issue_date) <= 60 THEN '31-60'
                       WHEN DATEDIFF(CURDATE(), i.issue_date) <= 90 THEN '61-90'
                       ELSE '90+'
                    END AS bucket
             FROM invoices i
             JOIN patients p ON p.id = i.patient_id
             WHERE i.status IN ('issued','partial')
               AND (i.total - i.paid) > 0
             ORDER BY days_old DESC"
        );
    }

    public static function retentionStats(): array
    {
        return [
            'total_patients'    => (int)Database::value('SELECT COUNT(*) FROM patients WHERE is_active = 1'),
            'with_appointments' => (int)Database::value(
                'SELECT COUNT(DISTINCT patient_id) FROM appointments'
            ),
            'returning' => (int)Database::value(
                'SELECT COUNT(*) FROM (
                    SELECT patient_id, COUNT(*) AS n FROM appointments GROUP BY patient_id HAVING n >= 2
                 ) t'
            ),
            'inactive_6mo' => (int)Database::value(
                "SELECT COUNT(*) FROM patients p
                 WHERE p.is_active = 1
                   AND NOT EXISTS (
                     SELECT 1 FROM appointments a
                     WHERE a.patient_id = p.id
                       AND a.starts_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                   )"
            ),
        ];
    }

    public static function patientsForRecall(int $months = 6): array
    {
        return Database::query(
            "SELECT p.id, p.code, p.first_name, p.last_name, p.email, p.mobile, p.phone,
                    (SELECT MAX(a.starts_at) FROM appointments a
                     WHERE a.patient_id = p.id AND a.status = 'completed') AS last_visit
             FROM patients p
             WHERE p.is_active = 1
               AND EXISTS (
                 SELECT 1 FROM appointments a
                 WHERE a.patient_id = p.id AND a.status = 'completed'
               )
               AND NOT EXISTS (
                 SELECT 1 FROM appointments a
                 WHERE a.patient_id = p.id
                   AND a.starts_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
               )
             ORDER BY last_visit DESC
             LIMIT 100", [$months]
        );
    }
}
