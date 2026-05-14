<?php
class GiftController {
    public function __construct() { Auth::requireLogin(); }

    public function index(): void {
        $giftCards = Database::fetchAll(
            "SELECT * FROM gift_cards ORDER BY purchased_at DESC LIMIT 200"
        );
        $packs = Database::fetchAll(
            "SELECT sp.*, COUNT(cp.id) AS sold
             FROM service_packs sp LEFT JOIN customer_packs cp ON cp.pack_id = sp.id
             GROUP BY sp.id ORDER BY sp.name"
        );
        $customerPacks = Database::fetchAll(
            "SELECT cp.*, c.first_name, c.last_name, sp.name AS pack_name
             FROM customer_packs cp
             JOIN customers c ON c.id = cp.customer_id
             JOIN service_packs sp ON sp.id = cp.pack_id
             ORDER BY cp.purchased_at DESC LIMIT 100"
        );
        view('admin/bonos/index', compact('giftCards','packs','customerPacks'));
    }

    public function storeGift(): void {
        csrf_verify();
        Database::insert('gift_cards', [
            'code'            => 'GC-' . randomCode(8),
            'initial_amount'  => (float)$_POST['amount'],
            'balance'         => (float)$_POST['amount'],
            'buyer_name'      => trim($_POST['buyer_name']),
            'buyer_email'     => trim($_POST['buyer_email'] ?? ''),
            'recipient_name'  => trim($_POST['recipient_name'] ?? ''),
            'recipient_email' => trim($_POST['recipient_email'] ?? ''),
            'message'         => trim($_POST['message'] ?? ''),
            'purchased_at'    => date('Y-m-d H:i:s'),
            'expires_at'      => date('Y-m-d H:i:s', strtotime('+1 year')),
            'status'          => 'activa',
        ]);
        flash('success','Tarjeta regalo emitida.');
        redirect('/admin/bonos');
    }

    public function storePack(): void {
        csrf_verify();
        Database::insert('service_packs', [
            'name'           => trim($_POST['name']),
            'description'    => trim($_POST['description'] ?? ''),
            'service_id'     => !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null,
            'sessions_total' => (int)$_POST['sessions_total'],
            'price'          => (float)$_POST['price'],
            'valid_months'   => (int)($_POST['valid_months'] ?? 6),
            'active'         => 1,
        ]);
        flash('success','Bono creado.');
        redirect('/admin/bonos');
    }

    public function assignPack(): void {
        csrf_verify();
        $pack = Database::fetch('SELECT * FROM service_packs WHERE id = ?', [(int)$_POST['pack_id']]);
        Database::insert('customer_packs', [
            'customer_id'    => (int)$_POST['customer_id'],
            'pack_id'        => $pack['id'],
            'code'           => 'BN-' . randomCode(8),
            'sessions_total' => $pack['sessions_total'],
            'purchased_at'   => date('Y-m-d H:i:s'),
            'expires_at'     => date('Y-m-d H:i:s', strtotime("+{$pack['valid_months']} months")),
            'price_paid'     => $pack['price'],
            'status'         => 'activo',
        ]);
        flash('success','Bono asignado.');
        redirect('/admin/bonos');
    }
}
