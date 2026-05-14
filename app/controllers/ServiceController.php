<?php
class ServiceController {
    public function __construct() { Auth::requireLogin(); }

    public function index(): void {
        $services = Database::fetchAll(
            "SELECT s.*, c.name AS category_name FROM services s
             LEFT JOIN service_categories c ON c.id = s.category_id
             ORDER BY s.sort_order, s.name"
        );
        $categories = Database::fetchAll("SELECT * FROM service_categories ORDER BY sort_order, name");
        view('admin/servicios/index', compact('services','categories'));
    }

    public function create(): void {
        $categories = Database::fetchAll("SELECT * FROM service_categories ORDER BY name");
        view('admin/servicios/form', compact('categories'));
    }

    public function store(): void {
        csrf_verify();
        $id = Database::insert('services', $this->payload());
        audit('create','service',$id);
        flash('success','Servicio creado.');
        redirect('/admin/servicios');
    }

    public function edit($id): void {
        $service = Database::fetch('SELECT * FROM services WHERE id = ?', [(int)$id]);
        if (!$service) { flash('error','Servicio no encontrado.'); redirect('/admin/servicios'); }
        $categories = Database::fetchAll("SELECT * FROM service_categories ORDER BY name");
        view('admin/servicios/form', compact('service','categories'));
    }

    public function update($id): void {
        csrf_verify();
        Database::update('services', $this->payload(), 'id = :id', ['id' => (int)$id]);
        audit('update','service',(int)$id);
        flash('success','Servicio actualizado.');
        redirect('/admin/servicios');
    }

    public function destroy($id): void {
        csrf_verify();
        Database::update('services', ['active' => 0], 'id = :id', ['id' => (int)$id]);
        audit('deactivate','service',(int)$id);
        flash('success','Servicio desactivado.');
        redirect('/admin/servicios');
    }

    private function payload(): array {
        return [
            'category_id'        => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'name'               => trim($_POST['name']),
            'description'        => trim($_POST['description'] ?? ''),
            'duration_minutes'   => (int)$_POST['duration_minutes'],
            'buffer_minutes'     => (int)($_POST['buffer_minutes'] ?? 15),
            'price'              => (float)$_POST['price'],
            'cost'               => (float)($_POST['cost'] ?? 0),
            'tax_rate'           => (float)($_POST['tax_rate'] ?? 15),
            'requires_room_type' => trim($_POST['requires_room_type'] ?? '') ?: null,
            'bookable_online'    => isset($_POST['bookable_online']) ? 1 : 0,
            'active'             => isset($_POST['active']) ? 1 : 0,
            'sort_order'         => (int)($_POST['sort_order'] ?? 0),
        ];
    }
}
