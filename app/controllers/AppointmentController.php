<?php
class AppointmentController {

    public function __construct() { Auth::requireLogin(); }

    public function agenda(): void {
        $therapists = Database::fetchAll("SELECT id, name, color FROM users WHERE role IN ('terapeuta','admin') AND active = 1");
        view('admin/citas/agenda', compact('therapists'));
    }

    public function index(): void {
        $filter = $_GET['status'] ?? '';
        $where = $filter ? "WHERE a.status = " . Database::connect()->quote($filter) : '';
        $appointments = Database::fetchAll(
            "SELECT a.*, c.first_name, c.last_name, c.phone, s.name AS service_name, u.name AS therapist_name
             FROM appointments a
             JOIN customers c ON c.id = a.customer_id
             JOIN services s ON s.id = a.service_id
             LEFT JOIN users u ON u.id = a.therapist_id
             $where
             ORDER BY a.starts_at DESC LIMIT 200"
        );
        view('admin/citas/index', compact('appointments', 'filter'));
    }

    public function create(): void {
        $services   = Database::fetchAll("SELECT * FROM services WHERE active = 1 ORDER BY name");
        $customers  = Database::fetchAll("SELECT id, first_name, last_name, phone FROM customers ORDER BY first_name LIMIT 500");
        $therapists = Database::fetchAll("SELECT id, name FROM users WHERE role IN ('terapeuta','admin') AND active = 1");
        $rooms      = Database::fetchAll("SELECT * FROM rooms WHERE active = 1");
        view('admin/citas/form', compact('services', 'customers', 'therapists', 'rooms'));
    }

    public function store(): void {
        csrf_verify();
        $service = Database::fetch('SELECT * FROM services WHERE id = ?', [(int)$_POST['service_id']]);
        if (!$service) { flash('error','Servicio inválido.'); back(); }

        $startsAt = $_POST['date'] . ' ' . $_POST['time'] . ':00';
        $endsAt   = date('Y-m-d H:i:s', strtotime($startsAt) + ($service['duration_minutes'] * 60));

        $id = Database::insert('appointments', [
            'customer_id'  => (int)$_POST['customer_id'],
            'service_id'   => (int)$_POST['service_id'],
            'therapist_id' => !empty($_POST['therapist_id']) ? (int)$_POST['therapist_id'] : null,
            'room_id'      => !empty($_POST['room_id']) ? (int)$_POST['room_id'] : null,
            'starts_at'    => $startsAt,
            'ends_at'      => $endsAt,
            'price'        => $service['price'],
            'status'       => $_POST['status'] ?? 'confirmada',
            'source'       => 'admin',
            'notes'        => trim($_POST['notes'] ?? ''),
        ]);
        audit('create','appointment',$id);
        flash('success','Cita creada.');
        redirect('/admin/citas/agenda');
    }

    public function edit($id): void {
        $appointment = Database::fetch('SELECT * FROM appointments WHERE id = ?', [(int)$id]);
        if (!$appointment) { flash('error','Cita no encontrada.'); redirect('/admin/citas'); }
        $services   = Database::fetchAll("SELECT * FROM services WHERE active = 1 ORDER BY name");
        $customers  = Database::fetchAll("SELECT id, first_name, last_name, phone FROM customers ORDER BY first_name LIMIT 500");
        $therapists = Database::fetchAll("SELECT id, name FROM users WHERE role IN ('terapeuta','admin') AND active = 1");
        $rooms      = Database::fetchAll("SELECT * FROM rooms WHERE active = 1");
        view('admin/citas/form', compact('appointment','services','customers','therapists','rooms'));
    }

    public function update($id): void {
        csrf_verify();
        $id = (int)$id;
        $service = Database::fetch('SELECT * FROM services WHERE id = ?', [(int)$_POST['service_id']]);
        if (!$service) { flash('error','Servicio inválido.'); back(); }
        $startsAt = $_POST['date'] . ' ' . $_POST['time'] . ':00';
        $endsAt   = date('Y-m-d H:i:s', strtotime($startsAt) + ($service['duration_minutes'] * 60));

        Database::update('appointments', [
            'customer_id'  => (int)$_POST['customer_id'],
            'service_id'   => (int)$_POST['service_id'],
            'therapist_id' => !empty($_POST['therapist_id']) ? (int)$_POST['therapist_id'] : null,
            'room_id'      => !empty($_POST['room_id']) ? (int)$_POST['room_id'] : null,
            'starts_at'    => $startsAt,
            'ends_at'      => $endsAt,
            'price'        => $service['price'],
            'status'       => $_POST['status'],
            'notes'        => trim($_POST['notes'] ?? ''),
        ], 'id = :id', ['id' => $id]);
        audit('update','appointment',$id);
        flash('success','Cita actualizada.');
        redirect('/admin/citas/agenda');
    }

    public function updateStatus($id): void {
        csrf_verify();
        $id = (int)$id;
        $status = $_POST['status'] ?? 'confirmada';
        Database::update('appointments', ['status' => $status], 'id = :id', ['id' => $id]);
        // Si es no_show: incrementar contador
        if ($status === 'no_show') {
            Database::query('UPDATE customers SET no_show_count = no_show_count + 1 WHERE id = (SELECT customer_id FROM appointments WHERE id = ?)', [$id]);
        }
        audit('status','appointment',$id,$status);
        flash('success','Estado actualizado.');
        back();
    }

    public function destroy($id): void {
        csrf_verify();
        Database::delete('appointments', 'id = ?', [(int)$id]);
        audit('delete','appointment',(int)$id);
        flash('success','Cita eliminada.');
        redirect('/admin/citas/agenda');
    }

    /** Endpoint JSON usado por FullCalendar */
    public function apiList(): void {
        header('Content-Type: application/json');
        $start = $_GET['start'] ?? date('Y-m-d');
        $end   = $_GET['end']   ?? date('Y-m-d', strtotime('+30 days'));
        $rows = Database::fetchAll(
            "SELECT a.id, a.starts_at, a.ends_at, a.status,
                    CONCAT(c.first_name,' ',COALESCE(c.last_name,'')) AS customer,
                    s.name AS service, u.color AS color, u.name AS therapist
             FROM appointments a
             JOIN customers c ON c.id = a.customer_id
             JOIN services s ON s.id = a.service_id
             LEFT JOIN users u ON u.id = a.therapist_id
             WHERE a.starts_at BETWEEN ? AND ?",
            [$start, $end]
        );
        $events = array_map(function ($r) {
            $colors = [
                'pendiente'  => '#ffc107',
                'confirmada' => '#17a2b8',
                'en_curso'   => '#6c63ff',
                'completada' => '#28a745',
                'cancelada'  => '#dc3545',
                'no_show'    => '#6c757d',
            ];
            return [
                'id'    => $r['id'],
                'title' => trim($r['customer']) . ' · ' . $r['service'],
                'start' => $r['starts_at'],
                'end'   => $r['ends_at'],
                'color' => $colors[$r['status']] ?? '#6b8a7a',
                'extendedProps' => ['therapist' => $r['therapist'], 'status' => $r['status']],
            ];
        }, $rows);
        echo json_encode($events);
    }
}
