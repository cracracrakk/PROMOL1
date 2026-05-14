<?php
class BranchController {
    public function __construct() { Auth::requireRole(['admin']); }

    public function index(): void {
        $branches = Database::fetchAll('SELECT * FROM branches ORDER BY name');
        view('admin/sistema/branches', compact('branches'));
    }

    public function save(): void {
        csrf_verify();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name'           => trim($_POST['name']),
            'address'        => trim($_POST['address'] ?? ''),
            'phone'          => trim($_POST['phone'] ?? ''),
            'email'          => trim($_POST['email'] ?? ''),
            'sar_cai_prefix' => trim($_POST['sar_cai_prefix'] ?? ''),
            'timezone'       => $_POST['timezone'] ?? 'America/Tegucigalpa',
            'active'         => isset($_POST['active']) ? 1 : 0,
        ];
        if ($id) Database::update('branches', $data, 'id = :id', ['id' => $id]);
        else     Database::insert('branches', $data);
        flash('success','Sucursal guardada.');
        redirect('/admin/sistema/sucursales');
    }

    public function delete($id): void {
        csrf_verify();
        Database::delete('branches', 'id = ?', [(int)$id]);
        flash('success','Sucursal eliminada.');
        redirect('/admin/sistema/sucursales');
    }
}
