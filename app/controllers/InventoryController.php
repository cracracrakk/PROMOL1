<?php
class InventoryController {
    public function __construct() { Auth::requireLogin(); }

    public function index(): void {
        $q = trim($_GET['q'] ?? '');
        $where = ''; $params = [];
        if ($q !== '') {
            $where = "WHERE p.name LIKE :q OR p.sku LIKE :q OR p.barcode LIKE :q";
            $params['q'] = "%$q%";
        }
        $products = Database::fetchAll(
            "SELECT p.*, c.name AS category_name, s.name AS supplier_name
             FROM products p
             LEFT JOIN product_categories c ON c.id = p.category_id
             LEFT JOIN suppliers s ON s.id = p.supplier_id
             $where
             ORDER BY p.name", $params
        );
        view('admin/inventario/index', compact('products','q'));
    }

    public function create(): void {
        $categories = Database::fetchAll("SELECT * FROM product_categories ORDER BY name");
        $suppliers  = Database::fetchAll("SELECT * FROM suppliers ORDER BY name");
        view('admin/inventario/form', compact('categories','suppliers'));
    }

    public function store(): void {
        csrf_verify();
        $data = $this->payload();
        if (empty($data['sku'])) $data['sku'] = 'SKU-' . randomCode(6);
        $id = Database::insert('products', $data);
        if ($data['stock'] > 0) {
            Database::insert('stock_movements', [
                'product_id' => $id, 'type' => 'entrada', 'quantity' => $data['stock'],
                'cost_price' => $data['cost_price'], 'reason' => 'Stock inicial',
                'user_id' => Auth::id(),
            ]);
        }
        audit('create','product',$id);
        flash('success','Producto creado.');
        redirect('/admin/inventario');
    }

    public function edit($id): void {
        $product = Database::fetch('SELECT * FROM products WHERE id = ?', [(int)$id]);
        if (!$product) { flash('error','Producto no encontrado.'); redirect('/admin/inventario'); }
        $categories = Database::fetchAll("SELECT * FROM product_categories ORDER BY name");
        $suppliers  = Database::fetchAll("SELECT * FROM suppliers ORDER BY name");
        $movements  = Database::fetchAll(
            "SELECT m.*, u.name AS user_name FROM stock_movements m LEFT JOIN users u ON u.id = m.user_id
             WHERE m.product_id = ? ORDER BY m.created_at DESC LIMIT 30",
            [(int)$id]
        );
        view('admin/inventario/form', compact('product','categories','suppliers','movements'));
    }

    public function update($id): void {
        csrf_verify();
        $data = $this->payload();
        unset($data['stock']); // stock se modifica con movimientos
        Database::update('products', $data, 'id = :id', ['id' => (int)$id]);
        audit('update','product',(int)$id);
        flash('success','Producto actualizado.');
        redirect('/admin/inventario');
    }

    public function movement($id): void {
        csrf_verify();
        $allowed = ['entrada','salida','ajuste','venta','consumo'];
        $type = $_POST['type'] ?? '';
        if (!in_array($type, $allowed, true)) { flash('error','Tipo de movimiento inválido.'); back(); }
        $qty  = (int)$_POST['quantity'];
        $reason = trim($_POST['reason'] ?? '');
        if ($qty <= 0) { flash('error','Cantidad inválida.'); back(); }

        $product = Database::fetch('SELECT * FROM products WHERE id = ?', [(int)$id]);
        $newStock = (int)$product['stock'];
        if ($type === 'entrada') $newStock += $qty;
        elseif (in_array($type, ['salida','consumo','venta'])) $newStock = max(0, $newStock - $qty);
        elseif ($type === 'ajuste') $newStock = $qty;

        Database::update('products', ['stock' => $newStock], 'id = :id', ['id' => (int)$id]);
        Database::insert('stock_movements', [
            'product_id' => (int)$id, 'type' => $type, 'quantity' => $qty,
            'reason' => $reason, 'user_id' => Auth::id(),
        ]);
        audit('stock_movement','product',(int)$id, "$type $qty");
        flash('success','Movimiento registrado.');
        back();
    }

    public function destroy($id): void {
        csrf_verify();
        Database::update('products', ['active' => 0], 'id = :id', ['id' => (int)$id]);
        audit('deactivate','product',(int)$id);
        flash('success','Producto desactivado.');
        redirect('/admin/inventario');
    }

    private function payload(): array {
        return [
            'category_id'    => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'supplier_id'    => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
            'sku'            => trim($_POST['sku']),
            'barcode'        => trim($_POST['barcode'] ?? '') ?: null,
            'name'           => trim($_POST['name']),
            'description'    => trim($_POST['description'] ?? ''),
            'cost_price'     => (float)$_POST['cost_price'],
            'sale_price'     => (float)$_POST['sale_price'],
            'tax_rate'       => (float)($_POST['tax_rate'] ?? 15),
            'stock'          => (int)($_POST['stock'] ?? 0),
            'stock_min'      => (int)$_POST['stock_min'],
            'unit'           => trim($_POST['unit'] ?? 'ud'),
            'is_consumable'  => isset($_POST['is_consumable']) ? 1 : 0,
            'sellable'       => isset($_POST['sellable']) ? 1 : 0,
            'active'         => isset($_POST['active']) ? 1 : 0,
        ];
    }
}
