<?php
class HomeController {

    public function index(): void {
        $featuredServices = Database::fetchAll(
            "SELECT * FROM services WHERE active = 1 AND bookable_online = 1
             ORDER BY sort_order ASC LIMIT 6"
        );
        $testimonials = Database::fetchAll(
            "SELECT * FROM testimonials WHERE published = 1 ORDER BY id DESC LIMIT 4"
        );
        view('public/home', compact('featuredServices', 'testimonials'));
    }

    public function services(): void {
        $categories  = Database::fetchAll(
            "SELECT * FROM service_categories WHERE active = 1 ORDER BY sort_order ASC"
        );
        $services    = Database::fetchAll(
            "SELECT * FROM services WHERE active = 1 ORDER BY sort_order ASC"
        );
        $servicesByCat = [];
        foreach ($services as $s) $servicesByCat[$s['category_id']][] = $s;
        view('public/services', compact('categories', 'servicesByCat'));
    }

    public function about(): void {
        $team = Database::fetchAll(
            "SELECT id, name, role, photo, bio, color FROM users
             WHERE active = 1 AND role IN ('terapeuta','admin','gerente') ORDER BY id ASC"
        );
        view('public/about', compact('team'));
    }

    public function contact(): void {
        view('public/contact');
    }

    public function submitContact(): void {
        csrf_verify();
        $name    = trim($_POST['name']    ?? '');
        $email   = trim($_POST['email']   ?? '');
        $phone   = trim($_POST['phone']   ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Por favor, completa los campos obligatorios correctamente.');
            rememberOld();
            redirect('/contacto');
        }

        Database::insert('contact_messages', compact('name','email','phone','message'));
        forgetOld();
        flash('success', 'Mensaje recibido. Te contactaremos lo antes posible.');
        redirect('/contacto');
    }

    public function booking(): void {
        $services = Database::fetchAll(
            "SELECT * FROM services WHERE active = 1 AND bookable_online = 1 ORDER BY sort_order ASC"
        );
        view('public/booking', compact('services'));
    }

    public function submitBooking(): void {
        csrf_verify();
        $required = ['first_name','email','phone','service_id','date','time'];
        foreach ($required as $r) {
            if (empty($_POST[$r])) {
                flash('error', 'Por favor, completa todos los campos obligatorios.');
                rememberOld();
                redirect('/reservar');
            }
        }
        if (empty($_POST['gdpr'])) {
            flash('error', 'Debes aceptar la política de privacidad.');
            rememberOld();
            redirect('/reservar');
        }

        $service = Database::fetch('SELECT * FROM services WHERE id = ? AND active = 1', [(int)$_POST['service_id']]);
        if (!$service) {
            flash('error', 'Servicio no disponible.');
            redirect('/reservar');
        }

        // Crear / encontrar cliente
        $email = trim($_POST['email']);
        $existing = Database::fetch('SELECT id FROM customers WHERE email = ? LIMIT 1', [$email]);
        if ($existing) {
            $customerId = (int)$existing['id'];
        } else {
            $customerId = Database::insert('customers', [
                'first_name'        => trim($_POST['first_name']),
                'last_name'         => trim($_POST['last_name'] ?? ''),
                'email'             => $email,
                'phone'             => trim($_POST['phone']),
                'accepts_marketing' => 1,
                'gdpr_consent_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        $startsAt = $_POST['date'] . ' ' . $_POST['time'] . ':00';
        $endsAt   = date('Y-m-d H:i:s', strtotime($startsAt) + ($service['duration_minutes'] * 60));

        $appointmentId = Database::insert('appointments', [
            'customer_id'  => $customerId,
            'service_id'   => $service['id'],
            'starts_at'    => $startsAt,
            'ends_at'      => $endsAt,
            'price'        => $service['price'],
            'status'       => 'pendiente',
            'source'       => 'web',
            'notes'        => trim($_POST['notes'] ?? ''),
            'confirmation_token' => bin2hex(random_bytes(16)),
        ]);

        // Email de confirmación al cliente
        $appointment = Database::fetch('SELECT * FROM appointments WHERE id = ?', [$appointmentId]);
        $customer    = Database::fetch('SELECT * FROM customers WHERE id = ?', [$customerId]);
        Mail::sendBookingPending($appointment, $customer, $service);

        forgetOld();
        flash('success', '¡Solicitud recibida! Te enviamos un email de confirmación.');
        redirect('/reservar');
    }

    public function giftCard(): void {
        view('public/giftcard');
    }

    public function submitGiftCard(): void {
        csrf_verify();
        $amount      = (float)($_POST['amount'] ?? 0);
        $buyerName   = trim($_POST['buyer_name'] ?? '');
        $buyerEmail  = trim($_POST['buyer_email'] ?? '');

        if ($amount <= 0 || $buyerName === '' || !filter_var($buyerEmail, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Datos incompletos o incorrectos.');
            rememberOld();
            redirect('/regalo');
        }

        $cardId = Database::insert('gift_cards', [
            'code'             => 'GC-' . randomCode(8),
            'initial_amount'   => $amount,
            'balance'          => $amount,
            'buyer_name'       => $buyerName,
            'buyer_email'      => $buyerEmail,
            'recipient_name'   => trim($_POST['recipient_name'] ?? ''),
            'recipient_email'  => trim($_POST['recipient_email'] ?? ''),
            'message'          => trim($_POST['message'] ?? ''),
            'purchased_at'     => date('Y-m-d H:i:s'),
            'expires_at'       => date('Y-m-d H:i:s', strtotime('+1 year')),
            'status'           => 'activa',
        ]);

        // Email al comprador (notificación de solicitud)
        Mail::send($buyerEmail, 'Tu solicitud de tarjeta regalo - ' . brand_name(),
            '<h2>Solicitud recibida</h2><p>Hemos recibido tu solicitud de tarjeta regalo por ' . money($amount) . '. Te contactaremos en breve para coordinar el pago.</p>');

        forgetOld();
        flash('success', '¡Solicitud recibida! Te contactaremos para coordinar el pago.');
        redirect('/regalo');
    }
}
