<?php
class InventoryController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->view('admin/inventory/index', [
            '_title'   => 'Inventario',
            'products' => Product::all(false),
            'low_stock'=> Product::lowStock(),
        ]);
    }

    public function form(?string $id = null): void
    {
        $this->requireAuth(['admin','dentist']);
        $product = $id ? Product::find((int)$id) : null;
        $this->view('admin/inventory/form', [
            '_title' => $product ? 'Editar producto' : 'Nuevo producto',
            'product' => $product,
            'movements' => $product ? Product::movementsFor((int)$id) : [],
        ]);
    }

    public function save(?string $id = null): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->requireCsrf();
        $data = $_POST;
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        if ($id) {
            Product::update((int)$id, $data);
            Auth::log('update', 'product', $id);
        } else {
            $id = Product::create($data);
            Auth::log('create', 'product', $id);
        }
        flash('success', 'Producto guardado.');
        redirect(url('/admin/inventario'));
    }

    public function move(string $id): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        $type = (string)input('type', 'in');
        $qty  = (float)input('quantity', 0);
        $reason = (string)input('reason', '');
        if ($qty <= 0 && $type !== 'adjust') {
            flash('error', 'Cantidad inválida.');
            redirect(url('/admin/inventario'));
        }
        Product::move((int)$id, $type, $qty, $reason, Auth::id());
        Auth::log('move', 'product', $id, ['type' => $type, 'qty' => $qty]);
        flash('success', 'Movimiento registrado.');
        redirect(url('/admin/inventario'));
    }
}
