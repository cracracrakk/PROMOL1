<?php
class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $from = date('Y-m-01');
        $to   = date('Y-m-d');

        $data = [
            '_title'     => 'Panel principal',
            'patients'   => Patient::stats(),
            'today'      => Appointment::todaySummary(),
            'invoices'   => Invoice::summary($from, $to),
            'upcoming'   => Appointment::listForRange(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59', strtotime('+7 days'))),
            'recent_patients' => Database::query(
                'SELECT id, code, first_name, last_name, created_at
                 FROM patients ORDER BY created_at DESC LIMIT 8'
            ),
            'payments_today' => (float)Database::value(
                "SELECT IFNULL(SUM(amount),0) FROM payments WHERE DATE(paid_at) = CURDATE()"
            ),
        ];
        $this->view('admin/dashboard', $data);
    }
}
