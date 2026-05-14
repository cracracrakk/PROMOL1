<?php
/**
 * Importación y exportación de clientes y productos vía CSV.
 */
class ImportExportController {
    public function __construct() { Auth::requireRole(['admin','gerente']); }

    public function index(): void {
        view('admin/import_export');
    }

    public function exportCustomers(): void {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="clientes_'.date('Y-m-d').'.csv"');
        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF");
        fputcsv($out, ['nombre','apellidos','email','telefono','rtn','fecha_nacimiento','direccion','ciudad','vip','marketing','notas'], ';');
        $rows = Database::fetchAll('SELECT * FROM customers ORDER BY id');
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['first_name'], $r['last_name'], $r['email'], $r['phone'], $r['tax_id'],
                $r['birthdate'], $r['address'], $r['city'],
                $r['vip'] ? 'si' : 'no', $r['accepts_marketing'] ? 'si' : 'no', $r['notes'],
            ], ';');
        }
        fclose($out); exit;
    }

    public function exportProducts(): void {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="productos_'.date('Y-m-d').'.csv"');
        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF");
        fputcsv($out, ['sku','nombre','descripcion','precio_coste','precio_venta','isv','stock','stock_min','unidad'], ';');
        $rows = Database::fetchAll('SELECT * FROM products ORDER BY id');
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['sku'], $r['name'], $r['description'],
                $r['cost_price'], $r['sale_price'], $r['tax_rate'],
                $r['stock'], $r['stock_min'], $r['unit'],
            ], ';');
        }
        fclose($out); exit;
    }

    public function importCustomers(): void {
        csrf_verify();
        if (empty($_FILES['csv']['tmp_name'])) { flash('error','Selecciona un archivo.'); back(); }
        $handle = fopen($_FILES['csv']['tmp_name'], 'r');
        $header = fgetcsv($handle, 0, ';');
        $imported = 0; $skipped = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $data = array_combine($header, $row);
            if (empty($data['nombre'])) { $skipped++; continue; }

            $email = $data['email'] ?? '';
            $existing = $email ? Database::fetch('SELECT id FROM customers WHERE email = ?', [$email]) : null;
            if ($existing) { $skipped++; continue; }

            Database::insert('customers', [
                'first_name'        => $data['nombre'],
                'last_name'         => $data['apellidos'] ?? '',
                'email'             => $email ?: null,
                'phone'             => $data['telefono'] ?? '',
                'tax_id'            => $data['rtn'] ?? null,
                'birthdate'         => !empty($data['fecha_nacimiento']) ? $data['fecha_nacimiento'] : null,
                'address'           => $data['direccion'] ?? null,
                'city'              => $data['ciudad'] ?? null,
                'vip'               => (strtolower($data['vip'] ?? '') === 'si') ? 1 : 0,
                'accepts_marketing' => (strtolower($data['marketing'] ?? 'si') === 'si') ? 1 : 0,
                'notes'             => $data['notas'] ?? null,
            ]);
            $imported++;
        }
        fclose($handle);
        flash('success', "Importados: {$imported}. Omitidos: {$skipped}.");
        redirect('/admin/import-export');
    }

    public function importProducts(): void {
        csrf_verify();
        if (empty($_FILES['csv']['tmp_name'])) { flash('error','Selecciona un archivo.'); back(); }
        $handle = fopen($_FILES['csv']['tmp_name'], 'r');
        $header = fgetcsv($handle, 0, ';');
        $imported = 0; $skipped = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $data = array_combine($header, $row);
            if (empty($data['nombre']) || empty($data['sku'])) { $skipped++; continue; }
            $existing = Database::fetch('SELECT id FROM products WHERE sku = ?', [$data['sku']]);
            if ($existing) { $skipped++; continue; }

            Database::insert('products', [
                'sku'        => $data['sku'],
                'name'       => $data['nombre'],
                'description'=> $data['descripcion'] ?? null,
                'cost_price' => (float)($data['precio_coste'] ?? 0),
                'sale_price' => (float)($data['precio_venta'] ?? 0),
                'tax_rate'   => (float)($data['isv'] ?? 15),
                'stock'      => (int)($data['stock'] ?? 0),
                'stock_min'  => (int)($data['stock_min'] ?? 5),
                'unit'       => $data['unidad'] ?? 'ud',
                'active'     => 1, 'sellable' => 1,
            ]);
            $imported++;
        }
        fclose($handle);
        flash('success', "Importados: {$imported}. Omitidos: {$skipped}.");
        redirect('/admin/import-export');
    }
}
