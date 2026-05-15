<?php
class ReportController extends Controller
{
    public function index(): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->view('admin/reports/index', [
            '_title' => 'Reportes',
            'retention' => Report::retentionStats(),
        ]);
    }

    public function revenue(): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->view('admin/reports/revenue', [
            '_title' => 'Ingresos',
            'data' => Report::revenueByMonth(12),
        ]);
    }

    public function byDentist(): void
    {
        $this->requireAuth(['admin','dentist']);
        $from = (string)input('from', date('Y-m-01'));
        $to   = (string)input('to', date('Y-m-d'));
        $this->view('admin/reports/by_dentist', [
            '_title' => 'Ingresos por odontólogo',
            'data'   => Report::revenueByDentist($from, $to),
            'from'   => $from, 'to' => $to,
        ]);
    }

    public function treatments(): void
    {
        $this->requireAuth(['admin','dentist']);
        $from = (string)input('from', date('Y-m-01'));
        $to   = (string)input('to', date('Y-m-d'));
        $this->view('admin/reports/treatments', [
            '_title' => 'Tratamientos top',
            'data'   => Report::topTreatments($from, $to),
            'from'   => $from, 'to' => $to,
        ]);
    }

    public function ageing(): void
    {
        $this->requireAuth(['admin','dentist','reception']);
        $rows = Report::ageingReceivables();
        $buckets = ['0-30'=>0,'31-60'=>0,'61-90'=>0,'90+'=>0];
        foreach ($rows as $r) $buckets[$r['bucket']] += (float)$r['balance'];
        $this->view('admin/reports/ageing', [
            '_title'  => 'Cuentas por cobrar',
            'rows'    => $rows,
            'buckets' => $buckets,
        ]);
    }

    public function retention(): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->view('admin/reports/retention', [
            '_title' => 'Retención de pacientes',
            'data'   => Report::retentionStats(),
        ]);
    }

    public function recall(): void
    {
        $this->requireAuth(['admin','dentist','reception']);
        $months = (int)input('months', (int)setting('recall_months', 6));
        $this->view('admin/reports/recall', [
            '_title' => 'Pacientes para recall',
            'data'   => Report::patientsForRecall($months),
            'months' => $months,
        ]);
    }
}
