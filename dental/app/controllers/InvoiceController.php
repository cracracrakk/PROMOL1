<?php
class InvoiceController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $filters = [
            'status'     => input('status'),
            'patient_id' => input('patient_id'),
            'from'       => input('from'),
            'to'         => input('to'),
        ];
        $page = max(1, (int)input('page', 1));
        $result = Invoice::search($filters, $page, 25);

        $this->view('admin/invoices/index', [
            '_title'  => 'Facturas',
            'filters' => $filters,
            'result'  => $result,
            'summary' => Invoice::summary($filters['from'] ?: date('Y-m-01'), $filters['to'] ?: date('Y-m-d')),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('admin/invoices/form', [
            '_title' => 'Nueva factura',
            'treatments' => Treatment::all(),
            'patient_id' => (int)input('patient_id', 0),
            'tax_rate' => (float)setting('tax_rate', 15),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        $patientId = (int)input('patient_id');
        if (!$patientId) {
            flash('error', 'Selecciona un paciente.');
            redirect(url('/admin/facturas/nueva'));
        }
        $items = [];
        $descriptions = $_POST['item_description'] ?? [];
        $qtys         = $_POST['item_qty']         ?? [];
        $prices       = $_POST['item_price']       ?? [];
        $treatments   = $_POST['item_treatment']   ?? [];
        $teeth        = $_POST['item_tooth']       ?? [];

        for ($i = 0; $i < count($descriptions); $i++) {
            $desc = trim((string)($descriptions[$i] ?? ''));
            $qty  = max(1, (int)($qtys[$i] ?? 1));
            $price= (float)($prices[$i] ?? 0);
            if ($desc === '' || $price <= 0) continue;
            $items[] = [
                'description'  => $desc,
                'quantity'     => $qty,
                'unit_price'   => $price,
                'line_total'   => $qty * $price,
                'treatment_id' => !empty($treatments[$i]) ? (int)$treatments[$i] : null,
                'tooth_code'   => !empty($teeth[$i]) ? $teeth[$i] : null,
            ];
        }
        if (!$items) {
            flash('error', 'Agrega al menos un ítem válido.');
            redirect(url('/admin/facturas/nueva?patient_id=' . $patientId));
        }

        $id = Invoice::create([
            'patient_id' => $patientId,
            'issue_date' => input('issue_date', date('Y-m-d')),
            'due_date'   => input('due_date'),
            'discount'   => (float)input('discount', 0),
            'tax_rate'   => (float)input('tax_rate', 0),
            'notes'      => (string)input('notes', ''),
            'created_by' => Auth::id(),
        ], $items);

        Auth::log('create', 'invoice', $id);
        flash('success', 'Factura creada.');
        redirect(url('/admin/facturas/' . $id));
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $invoice = Invoice::find((int)$id);
        if (!$invoice) { http_response_code(404); echo 'No encontrada.'; return; }
        $this->view('admin/invoices/show', [
            '_title' => 'Factura ' . $invoice['code'],
            'invoice' => $invoice,
        ]);
    }

    public function printable(string $id): void
    {
        $this->requireAuth();
        $invoice = Invoice::find((int)$id);
        if (!$invoice) { http_response_code(404); return; }
        $this->view('admin/invoices/print', ['invoice' => $invoice]);
    }

    public function updateStatus(string $id): void
    {
        $this->requireAuth(['admin','dentist','reception']);
        $this->requireCsrf();
        Invoice::updateStatus((int)$id, (string)input('status'));
        Auth::log('status_change', 'invoice', $id, ['status' => input('status')]);
        redirect(url('/admin/facturas/' . $id));
    }
}

class PaymentController extends Controller
{
    public function store(string $invoiceId): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        $amount = (float)input('amount', 0);
        if ($amount <= 0) {
            flash('error', 'El monto debe ser mayor a cero.');
            redirect(url('/admin/facturas/' . $invoiceId));
        }
        Payment::create([
            'invoice_id'  => (int)$invoiceId,
            'amount'      => $amount,
            'method'      => (string)input('method', 'cash'),
            'reference'   => (string)input('reference', ''),
            'received_by' => Auth::id(),
            'notes'       => (string)input('notes', ''),
            'paid_at'     => input('paid_at') ?: now(),
        ]);
        Auth::log('create', 'payment', $invoiceId, ['amount' => $amount]);
        flash('success', 'Pago registrado.');
        redirect(url('/admin/facturas/' . $invoiceId));
    }

    public function destroy(string $id): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->requireCsrf();
        Payment::delete((int)$id);
        Auth::log('delete', 'payment', $id);
        flash('success', 'Pago eliminado.');
        redirect($_SERVER['HTTP_REFERER'] ?? url('/admin/facturas'));
    }
}
