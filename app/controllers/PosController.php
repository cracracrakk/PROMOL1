<?php
require_once dirname(__DIR__) . '/core/SarHelper.php';

/**
 * Punto de Venta (POS) - Caja rápida sin necesidad de cita previa.
 * Útil para clientes walk-in, venta de productos, propinas, etc.
 */
class PosController {
    public function __construct() { Auth::requireLogin(); }

    public function index(): void {
        $services  = Database::fetchAll("SELECT id, name, price, tax_rate, duration_minutes FROM services WHERE active = 1 ORDER BY sort_order, name");
        $products  = Database::fetchAll("SELECT id, name, sale_price AS price, tax_rate, stock FROM products WHERE active = 1 AND sellable = 1 ORDER BY name");
        $customers = Database::fetchAll("SELECT id, first_name, last_name, phone, tax_id FROM customers ORDER BY first_name LIMIT 500");
        view('admin/pos/index', compact('services','products','customers'));
    }
}
