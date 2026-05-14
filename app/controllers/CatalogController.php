<?php
/**
 * Controlador de catálogos auxiliares del sistema:
 * - Cabinas/salas
 * - Proveedores
 * - Categorías de servicios
 * - Categorías de productos
 * - Testimonios
 * - Galería
 * - Mensajes de contacto
 * - Códigos promocionales
 */
class CatalogController {
    public function __construct() { Auth::requireRole(['admin','gerente']); }

    // ========== CABINAS ==========
    public function rooms(): void {
        $rooms = Database::fetchAll('SELECT * FROM rooms ORDER BY name');
        view('admin/catalogos/rooms', compact('rooms'));
    }
    public function roomSave(): void {
        csrf_verify();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name'        => trim($_POST['name']),
            'room_type'   => trim($_POST['room_type'] ?? '') ?: null,
            'capacity'    => (int)($_POST['capacity'] ?? 1),
            'description' => trim($_POST['description'] ?? '') ?: null,
            'active'      => isset($_POST['active']) ? 1 : 0,
        ];
        if ($id) Database::update('rooms', $data, 'id = :id', ['id' => $id]);
        else     Database::insert('rooms', $data);
        flash('success','Cabina guardada.');
        redirect('/admin/catalogos/cabinas');
    }
    public function roomDelete($id): void {
        csrf_verify();
        Database::delete('rooms', 'id = ?', [(int)$id]);
        flash('success','Cabina eliminada.');
        redirect('/admin/catalogos/cabinas');
    }

    // ========== PROVEEDORES ==========
    public function suppliers(): void {
        $suppliers = Database::fetchAll('SELECT * FROM suppliers ORDER BY name');
        view('admin/catalogos/suppliers', compact('suppliers'));
    }
    public function supplierSave(): void {
        csrf_verify();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name'    => trim($_POST['name']),
            'contact' => trim($_POST['contact'] ?? '') ?: null,
            'phone'   => trim($_POST['phone'] ?? '') ?: null,
            'email'   => trim($_POST['email'] ?? '') ?: null,
            'notes'   => trim($_POST['notes'] ?? '') ?: null,
        ];
        if ($id) Database::update('suppliers', $data, 'id = :id', ['id' => $id]);
        else     Database::insert('suppliers', $data);
        flash('success','Proveedor guardado.');
        redirect('/admin/catalogos/proveedores');
    }
    public function supplierDelete($id): void {
        csrf_verify();
        Database::delete('suppliers', 'id = ?', [(int)$id]);
        flash('success','Proveedor eliminado.');
        redirect('/admin/catalogos/proveedores');
    }

    // ========== CATEGORÍAS SERVICIOS ==========
    public function serviceCategories(): void {
        $categories = Database::fetchAll('SELECT * FROM service_categories ORDER BY sort_order, name');
        view('admin/catalogos/service_categories', compact('categories'));
    }
    public function serviceCategorySave(): void {
        csrf_verify();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name'        => trim($_POST['name']),
            'slug'        => slugify($_POST['name']),
            'description' => trim($_POST['description'] ?? '') ?: null,
            'icon'        => trim($_POST['icon'] ?? '') ?: null,
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
            'active'      => isset($_POST['active']) ? 1 : 0,
        ];
        if ($id) Database::update('service_categories', $data, 'id = :id', ['id' => $id]);
        else     Database::insert('service_categories', $data);
        flash('success','Categoría guardada.');
        redirect('/admin/catalogos/categorias-servicios');
    }

    // ========== CATEGORÍAS PRODUCTOS ==========
    public function productCategories(): void {
        $categories = Database::fetchAll('SELECT * FROM product_categories ORDER BY name');
        view('admin/catalogos/product_categories', compact('categories'));
    }
    public function productCategorySave(): void {
        csrf_verify();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name'        => trim($_POST['name']),
            'description' => trim($_POST['description'] ?? '') ?: null,
        ];
        if ($id) Database::update('product_categories', $data, 'id = :id', ['id' => $id]);
        else     Database::insert('product_categories', $data);
        flash('success','Categoría guardada.');
        redirect('/admin/catalogos/categorias-productos');
    }

    // ========== TESTIMONIOS ==========
    public function testimonials(): void {
        $items = Database::fetchAll('SELECT * FROM testimonials ORDER BY id DESC');
        view('admin/catalogos/testimonials', compact('items'));
    }
    public function testimonialSave(): void {
        csrf_verify();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'author'    => trim($_POST['author']),
            'rating'    => max(1, min(5, (int)$_POST['rating'])),
            'comment'   => trim($_POST['comment']),
            'published' => isset($_POST['published']) ? 1 : 0,
        ];
        if ($id) Database::update('testimonials', $data, 'id = :id', ['id' => $id]);
        else     Database::insert('testimonials', $data);
        flash('success','Testimonio guardado.');
        redirect('/admin/catalogos/testimonios');
    }
    public function testimonialDelete($id): void {
        csrf_verify();
        Database::delete('testimonials', 'id = ?', [(int)$id]);
        flash('success','Testimonio eliminado.');
        redirect('/admin/catalogos/testimonios');
    }

    // ========== GALERÍA ==========
    public function gallery(): void {
        $items = Database::fetchAll('SELECT * FROM gallery ORDER BY sort_order, id DESC');
        view('admin/catalogos/gallery', compact('items'));
    }
    public function galleryUpload(): void {
        csrf_verify();
        if (empty($_FILES['image']['name'])) { flash('error','Selecciona una imagen.'); back(); }
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) { flash('error','Formato no permitido.'); back(); }
        $fname = 'gal-' . randomCode(8) . '.' . $ext;
        $dest = dirname(__DIR__, 2) . '/public/assets/uploads/' . $fname;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            flash('error','Error al subir.'); back();
        }
        Database::insert('gallery', [
            'image'      => $fname,
            'caption'    => trim($_POST['caption'] ?? '') ?: null,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'published'  => 1,
        ]);
        flash('success','Imagen subida.');
        redirect('/admin/catalogos/galeria');
    }
    public function galleryDelete($id): void {
        csrf_verify();
        $item = Database::fetch('SELECT * FROM gallery WHERE id = ?', [(int)$id]);
        if ($item) {
            @unlink(dirname(__DIR__, 2) . '/public/assets/uploads/' . $item['image']);
            Database::delete('gallery', 'id = ?', [(int)$id]);
        }
        flash('success','Imagen eliminada.');
        redirect('/admin/catalogos/galeria');
    }

    // ========== MENSAJES DE CONTACTO ==========
    public function messages(): void {
        $messages = Database::fetchAll('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 200');
        // Marcar como leídos
        Database::query('UPDATE contact_messages SET read_at = NOW() WHERE read_at IS NULL');
        view('admin/catalogos/messages', compact('messages'));
    }
    public function messageDelete($id): void {
        csrf_verify();
        Database::delete('contact_messages', 'id = ?', [(int)$id]);
        flash('success','Mensaje eliminado.');
        redirect('/admin/catalogos/mensajes');
    }

    // ========== CÓDIGOS PROMOCIONALES ==========
    public function promos(): void {
        $promos = Database::fetchAll('SELECT * FROM promo_codes ORDER BY active DESC, id DESC');
        view('admin/catalogos/promos', compact('promos'));
    }
    public function promoSave(): void {
        csrf_verify();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'code'           => strtoupper(trim($_POST['code'])),
            'description'    => trim($_POST['description'] ?? '') ?: null,
            'discount_type'  => $_POST['discount_type'] ?? 'percent',
            'discount_value' => (float)$_POST['discount_value'],
            'valid_from'     => !empty($_POST['valid_from']) ? $_POST['valid_from'] : null,
            'valid_until'    => !empty($_POST['valid_until']) ? $_POST['valid_until'] : null,
            'max_uses'       => !empty($_POST['max_uses']) ? (int)$_POST['max_uses'] : null,
            'active'         => isset($_POST['active']) ? 1 : 0,
        ];
        if ($id) Database::update('promo_codes', $data, 'id = :id', ['id' => $id]);
        else     Database::insert('promo_codes', $data);
        flash('success','Código guardado.');
        redirect('/admin/catalogos/promos');
    }
    public function promoDelete($id): void {
        csrf_verify();
        Database::delete('promo_codes', 'id = ?', [(int)$id]);
        flash('success','Código eliminado.');
        redirect('/admin/catalogos/promos');
    }
}
