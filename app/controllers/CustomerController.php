<?php
class CustomerController {
    public function __construct() { Auth::requireLogin(); }

    public function index(): void {
        $q = trim($_GET['q'] ?? '');
        $params = [];
        $where = '';
        if ($q !== '') {
            $where = "WHERE first_name LIKE :q OR last_name LIKE :q OR email LIKE :q OR phone LIKE :q OR tax_id LIKE :q";
            $params['q'] = "%$q%";
        }
        $customers = Database::fetchAll(
            "SELECT * FROM customers $where ORDER BY first_name LIMIT 500", $params
        );
        view('admin/clientes/index', compact('customers', 'q'));
    }

    public function create(): void  { view('admin/clientes/form'); }

    public function store(): void {
        csrf_verify();
        $data = $this->payload();
        if (empty($data['first_name'])) { flash('error','Nombre obligatorio.'); back(); }
        $data['gdpr_consent_at'] = date('Y-m-d H:i:s');
        $data['referral_code'] = 'REF-' . randomCode(6);
        $id = Database::insert('customers', $data);
        audit('create','customer',$id);
        flash('success','Cliente creado.');
        redirect('/admin/clientes/' . $id);
    }

    public function show($id): void {
        $customer = Database::fetch('SELECT * FROM customers WHERE id = ?', [(int)$id]);
        if (!$customer) { flash('error','Cliente no encontrado.'); redirect('/admin/clientes'); }
        $appointments = Database::fetchAll(
            "SELECT a.*, s.name AS service_name FROM appointments a JOIN services s ON s.id = a.service_id
             WHERE a.customer_id = ? ORDER BY a.starts_at DESC LIMIT 50",
            [(int)$id]
        );
        $invoices = Database::fetchAll(
            "SELECT * FROM invoices WHERE customer_id = ? ORDER BY issue_date DESC LIMIT 50",
            [(int)$id]
        );
        $totalSpent = Database::fetch(
            "SELECT COALESCE(SUM(total),0) s FROM invoices WHERE customer_id = ? AND status IN ('emitida','pagada')",
            [(int)$id]
        )['s'] ?? 0;
        view('admin/clientes/show', compact('customer','appointments','invoices','totalSpent'));
    }

    public function edit($id): void {
        $customer = Database::fetch('SELECT * FROM customers WHERE id = ?', [(int)$id]);
        if (!$customer) { flash('error','Cliente no encontrado.'); redirect('/admin/clientes'); }
        view('admin/clientes/form', compact('customer'));
    }

    public function update($id): void {
        csrf_verify();
        $data = $this->payload();
        Database::update('customers', $data, 'id = :id', ['id' => (int)$id]);
        audit('update','customer',(int)$id);
        flash('success','Cliente actualizado.');
        redirect('/admin/clientes/' . (int)$id);
    }

    public function destroy($id): void {
        csrf_verify();
        Database::delete('customers', 'id = ?', [(int)$id]);
        audit('delete','customer',(int)$id);
        flash('success','Cliente eliminado.');
        redirect('/admin/clientes');
    }

    private function payload(): array {
        return [
            'first_name'         => trim($_POST['first_name'] ?? ''),
            'last_name'          => trim($_POST['last_name'] ?? ''),
            'email'              => trim($_POST['email'] ?? '') ?: null,
            'phone'              => trim($_POST['phone'] ?? ''),
            'birthdate'          => !empty($_POST['birthdate']) ? $_POST['birthdate'] : null,
            'gender'             => $_POST['gender'] ?? null,
            'tax_id'             => trim($_POST['tax_id'] ?? '') ?: null,
            'is_company'         => isset($_POST['is_company']) ? 1 : 0,
            'address'            => trim($_POST['address'] ?? '') ?: null,
            'city'               => trim($_POST['city'] ?? '') ?: null,
            'postal_code'        => trim($_POST['postal_code'] ?? '') ?: null,
            'allergies'          => trim($_POST['allergies'] ?? '') ?: null,
            'medical_conditions' => trim($_POST['medical_conditions'] ?? '') ?: null,
            'medications'        => trim($_POST['medications'] ?? '') ?: null,
            'pregnant'           => isset($_POST['pregnant']) ? 1 : 0,
            'pregnancy_weeks'    => !empty($_POST['pregnancy_weeks']) ? (int)$_POST['pregnancy_weeks'] : null,
            'preferences'        => trim($_POST['preferences'] ?? '') ?: null,
            'vip'                => isset($_POST['vip']) ? 1 : 0,
            'accepts_marketing'  => isset($_POST['accepts_marketing']) ? 1 : 0,
            'notes'              => trim($_POST['notes'] ?? '') ?: null,
        ];
    }
}
