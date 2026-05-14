<?php
/**
 * Portal del cliente - área privada sin contraseña.
 * Sistema "magic link": el cliente pide acceso con su email y recibe enlace seguro.
 */
class CustomerPortalController {

    public function login(): void {
        view('public/portal_login');
    }

    public function sendMagicLink(): void {
        csrf_verify();
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error','Email inválido.');
            redirect('/mi-cuenta');
        }
        $customer = Database::fetch('SELECT * FROM customers WHERE email = ?', [$email]);
        // Siempre mostramos el mismo mensaje (no revelar si existe)
        if ($customer) {
            $token = bin2hex(random_bytes(32));
            Database::update('customers', [
                'portal_token' => $token,
                'portal_token_expires' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
            ], 'id = :id', ['id' => $customer['id']]);

            $url   = url('/mi-cuenta/acceso/' . $token);
            $brand = e(brand_name());
            $name  = e($customer['first_name']);
            $body  = "
                <h2>Hola {$name},</h2>
                <p>Pulsa el botón para acceder a tu cuenta en <strong>{$brand}</strong>:</p>
                <p style=\"text-align:center;margin:30px 0;\">
                  <a href=\"{$url}\" style=\"background:#6b8a7a;color:#fff;padding:14px 32px;border-radius:30px;text-decoration:none;\">Acceder a mi cuenta</a>
                </p>
                <p style=\"font-size:12px;color:#7a8a82;\">Este enlace caduca en 30 minutos. Si no solicitaste este acceso, ignora el email.</p>
            ";
            Mail::send($email, "Tu enlace de acceso a {$brand}", $body);
        }
        flash('success','Si el email está registrado, recibirás un enlace de acceso.');
        redirect('/mi-cuenta');
    }

    public function authenticate(string $token): void {
        $customer = Database::fetch(
            'SELECT * FROM customers WHERE portal_token = ? AND portal_token_expires > NOW() LIMIT 1',
            [$token]
        );
        if (!$customer) {
            flash('error','Enlace inválido o caducado.');
            redirect('/mi-cuenta');
        }
        // Limpiar token (uso único)
        Database::update('customers', [
            'portal_token' => null,
            'portal_token_expires' => null,
        ], 'id = :id', ['id' => $customer['id']]);

        $_SESSION['customer'] = [
            'id'         => $customer['id'],
            'first_name' => $customer['first_name'],
            'last_name'  => $customer['last_name'],
            'email'      => $customer['email'],
        ];
        session_regenerate_id(true);
        redirect('/mi-cuenta/inicio');
    }

    public function logout(): void {
        unset($_SESSION['customer']);
        redirect('/');
    }

    public function dashboard(): void {
        $this->requireCustomer();
        $customerId = $_SESSION['customer']['id'];

        $upcoming = Database::fetchAll(
            "SELECT a.*, s.name AS service_name FROM appointments a
             JOIN services s ON s.id = a.service_id
             WHERE a.customer_id = ? AND a.starts_at >= NOW() AND a.status IN ('pendiente','confirmada')
             ORDER BY a.starts_at ASC", [$customerId]
        );
        $history = Database::fetchAll(
            "SELECT a.*, s.name AS service_name FROM appointments a
             JOIN services s ON s.id = a.service_id
             WHERE a.customer_id = ? AND a.starts_at < NOW()
             ORDER BY a.starts_at DESC LIMIT 20", [$customerId]
        );
        $packs = Database::fetchAll(
            "SELECT cp.*, sp.name AS pack_name FROM customer_packs cp
             JOIN service_packs sp ON sp.id = cp.pack_id
             WHERE cp.customer_id = ? AND cp.status = 'activo'", [$customerId]
        );
        $customer = Database::fetch('SELECT * FROM customers WHERE id = ?', [$customerId]);

        view('public/portal_dashboard', compact('upcoming','history','packs','customer'));
    }

    public function profile(): void {
        $this->requireCustomer();
        $customerId = $_SESSION['customer']['id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            Database::update('customers', [
                'first_name' => trim($_POST['first_name']),
                'last_name'  => trim($_POST['last_name'] ?? ''),
                'phone'      => trim($_POST['phone'] ?? ''),
                'birthdate'  => $_POST['birthdate'] ?: null,
                'address'    => trim($_POST['address'] ?? ''),
                'accepts_marketing' => isset($_POST['accepts_marketing']) ? 1 : 0,
            ], 'id = :id', ['id' => $customerId]);
            flash('success','Datos actualizados.');
            redirect('/mi-cuenta/perfil');
        }
        $customer = Database::fetch('SELECT * FROM customers WHERE id = ?', [$customerId]);
        view('public/portal_profile', compact('customer'));
    }

    private function requireCustomer(): void {
        if (empty($_SESSION['customer'])) {
            flash('error','Inicia sesión para acceder.');
            redirect('/mi-cuenta');
        }
    }
}
