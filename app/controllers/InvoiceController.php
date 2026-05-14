<?php
require_once dirname(__DIR__) . '/core/SarHelper.php';

class InvoiceController {
    public function __construct() { Auth::requireLogin(); }

    public function index(): void {
        $type = $_GET['type'] ?? '';
        $where = $type ? "WHERE i.document_type = " . Database::connect()->quote($type) : '';
        $invoices = Database::fetchAll(
            "SELECT i.*, c.first_name, c.last_name FROM invoices i
             LEFT JOIN customers c ON c.id = i.customer_id
             $where
             ORDER BY i.issue_date DESC, i.id DESC LIMIT 200"
        );
        view('admin/facturas/index', compact('invoices', 'type'));
    }

    public function create(): void {
        $customers = Database::fetchAll("SELECT id, first_name, last_name, tax_id, phone FROM customers ORDER BY first_name LIMIT 500");
        $services  = Database::fetchAll("SELECT * FROM services WHERE active = 1 ORDER BY name");
        $products  = Database::fetchAll("SELECT * FROM products WHERE active = 1 AND sellable = 1 ORDER BY name");
        $appointmentId = (int)($_GET['appointment'] ?? 0);
        $appointment = $appointmentId ? Database::fetch(
            "SELECT a.*, s.name AS service_name, s.price AS service_price, s.tax_rate AS service_tax,
                    c.first_name, c.last_name, c.tax_id, c.address, c.id AS cid
             FROM appointments a JOIN services s ON s.id = a.service_id JOIN customers c ON c.id = a.customer_id
             WHERE a.id = ?", [$appointmentId]
        ) : null;
        view('admin/facturas/form', compact('customers','services','products','appointment'));
    }

    public function store(): void {
        csrf_verify();

        $docType = $_POST['document_type'] ?? 'factura';
        $items = $_POST['items'] ?? [];
        if (empty($items)) { flash('error','Añade al menos una línea.'); back(); }

        $cleanItems = [];
        foreach ($items as $it) {
            if (empty($it['description']) || empty($it['quantity']) || !isset($it['unit_price'])) continue;
            $cleanItems[] = [
                'item_type'   => $it['item_type'] ?? 'servicio',
                'service_id'  => !empty($it['service_id']) ? (int)$it['service_id'] : null,
                'product_id'  => !empty($it['product_id']) ? (int)$it['product_id'] : null,
                'description' => trim($it['description']),
                'quantity'    => (float)$it['quantity'],
                'unit_price'  => (float)$it['unit_price'],
                'tax_rate'    => (float)($it['tax_rate'] ?? 15),
            ];
        }
        if (empty($cleanItems)) { flash('error','Líneas inválidas.'); back(); }

        $discount = (float)($_POST['discount'] ?? 0);
        $totals = SarHelper::calculateTotals($cleanItems, $discount);

        // Asignar número correlativo SAR
        try {
            $next = SarHelper::nextInvoiceNumber($docType);
        } catch (Throwable $e) {
            flash('error','SAR: ' . $e->getMessage());
            back();
        }

        $totalLetras = SarHelper::numberToWords($totals['total'], 'LEMPIRAS');

        $customer = !empty($_POST['customer_id']) ? Database::fetch('SELECT * FROM customers WHERE id = ?', [(int)$_POST['customer_id']]) : null;

        $invoiceData = [
            'document_type'         => $docType,
            'number'                => $next['number'],
            'sar_authorization_id'  => $next['auth']['id'],
            'cai'                   => $next['auth']['cai'],
            'fecha_limite_emision'  => $next['auth']['fecha_limite'],
            'rango_autorizado'      => SarHelper::formatRange($next['auth']),
            'related_invoice_id'    => !empty($_POST['related_invoice_id']) ? (int)$_POST['related_invoice_id'] : null,
            'customer_id'           => $customer['id'] ?? null,
            'customer_rtn'          => $customer['tax_id'] ?? trim($_POST['customer_rtn'] ?? ''),
            'customer_name'         => $customer ? trim($customer['first_name'].' '.$customer['last_name']) : trim($_POST['customer_name'] ?? 'Consumidor final'),
            'customer_address'      => $customer['address'] ?? trim($_POST['customer_address'] ?? ''),
            'appointment_id'        => !empty($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : null,
            'user_id'               => Auth::id(),
            'issue_date'            => date('Y-m-d'),
            'importe_exento'        => $totals['importe_exento'],
            'importe_exonerado'     => 0,
            'importe_gravado_15'    => $totals['importe_gravado_15'],
            'importe_gravado_18'    => $totals['importe_gravado_18'],
            'isv_15'                => $totals['isv_15'],
            'isv_18'                => $totals['isv_18'],
            'subtotal'              => $totals['subtotal'],
            'discount'              => $totals['discount'],
            'discount_reason'       => trim($_POST['discount_reason'] ?? '') ?: null,
            'tax_amount'            => $totals['tax_amount'],
            'total'                 => $totals['total'],
            'total_letras'          => $totalLetras,
            'tip'                   => (float)($_POST['tip'] ?? 0),
            'payment_method'        => $_POST['payment_method'] ?? 'efectivo',
            'payment_reference'     => trim($_POST['payment_reference'] ?? '') ?: null,
            'gift_card_code'        => trim($_POST['gift_card_code'] ?? '') ?: null,
            'promo_code'            => trim($_POST['promo_code'] ?? '') ?: null,
            'status'                => 'pagada',
            'notes'                 => trim($_POST['notes'] ?? '') ?: null,
        ];

        $invoiceId = Database::insert('invoices', $invoiceData);
        foreach ($cleanItems as $it) {
            $it['invoice_id'] = $invoiceId;
            $it['total'] = round($it['quantity'] * $it['unit_price'] * (1 + $it['tax_rate']/100), 2);
            Database::insert('invoice_items', $it);
            // Si es producto, descontar stock
            if ($it['item_type'] === 'producto' && $it['product_id']) {
                Database::query('UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?', [(int)$it['quantity'], $it['product_id']]);
                Database::insert('stock_movements', [
                    'product_id' => $it['product_id'], 'type' => 'venta',
                    'quantity' => (int)$it['quantity'], 'invoice_id' => $invoiceId,
                    'user_id' => Auth::id(),
                ]);
            }
        }

        audit('create','invoice',$invoiceId,$next['number']);
        flash('success', ucfirst($docType) . ' ' . $next['number'] . ' creada.');
        redirect('/admin/facturas/' . $invoiceId);
    }

    public function show($id): void {
        $invoice = Database::fetch('SELECT * FROM invoices WHERE id = ?', [(int)$id]);
        if (!$invoice) { flash('error','Documento no encontrado.'); redirect('/admin/facturas'); }
        $items = Database::fetchAll('SELECT * FROM invoice_items WHERE invoice_id = ?', [(int)$id]);
        view('admin/facturas/show', compact('invoice','items'));
    }

    public function printable($id): void {
        $invoice = Database::fetch('SELECT * FROM invoices WHERE id = ?', [(int)$id]);
        if (!$invoice) { flash('error','Documento no encontrado.'); redirect('/admin/facturas'); }
        $items = Database::fetchAll('SELECT * FROM invoice_items WHERE invoice_id = ?', [(int)$id]);
        view('admin/facturas/print', compact('invoice','items'));
    }

    /**
     * Descarga como HTML servible que el navegador puede convertir a PDF.
     * Para PDF binario nativo se requiere mPDF/FPDF; este endpoint optimiza
     * el HTML para "Guardar como PDF" del navegador.
     */
    public function download($id): void {
        $invoice = Database::fetch('SELECT * FROM invoices WHERE id = ?', [(int)$id]);
        if (!$invoice) { flash('error','Documento no encontrado.'); redirect('/admin/facturas'); }
        $items = Database::fetchAll('SELECT * FROM invoice_items WHERE invoice_id = ?', [(int)$id]);
        // Header de descarga
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: inline; filename="factura-' . $invoice['number'] . '.html"');
        view('admin/facturas/print', compact('invoice','items'));
    }

    public function updateStatus($id): void {
        csrf_verify();
        $status = $_POST['status'] ?? 'pagada';
        Database::update('invoices', ['status' => $status], 'id = :id', ['id' => (int)$id]);
        audit('status','invoice',(int)$id,$status);
        flash('success','Estado actualizado.');
        back();
    }
}
