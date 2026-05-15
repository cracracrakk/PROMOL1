<?php
class AppointmentController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $date = (string)input('date', date('Y-m-d'));
        $appointments = Appointment::listForRange($date . ' 00:00:00', $date . ' 23:59:59');
        $this->view('admin/appointments/index', [
            '_title' => 'Agenda',
            'date' => $date,
            'appointments' => $appointments,
            'dentists' => User::dentists(),
        ]);
    }

    public function calendar(): void
    {
        $this->requireAuth();
        $this->view('admin/appointments/calendar', [
            '_title'   => 'Calendario',
            'dentists' => User::dentists(),
            'rooms'    => Database::query('SELECT * FROM rooms WHERE is_active = 1'),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('admin/appointments/form', [
            '_title' => 'Nueva cita',
            'appointment' => null,
            'dentists'  => User::dentists(),
            'rooms'     => Database::query('SELECT * FROM rooms WHERE is_active = 1'),
            'treatments'=> Treatment::all(),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        $v = Validator::make($_POST, [
            'patient_id' => 'required|numeric',
            'dentist_id' => 'required|numeric',
            'starts_at'  => 'required',
            'ends_at'    => 'required',
        ]);
        if ($v->fails()) {
            flash('error', $v->firstError());
            redirect(url('/admin/citas/nueva'));
        }
        if (Appointment::hasConflict((int)$_POST['dentist_id'], $_POST['starts_at'], $_POST['ends_at'])) {
            flash('error', 'El odontólogo ya tiene una cita en ese horario.');
            redirect(url('/admin/citas/nueva'));
        }
        $data = $_POST;
        $data['created_by'] = Auth::id();
        $id = Appointment::create($data);
        Auth::log('create', 'appointment', $id);
        flash('success', 'Cita registrada.');
        redirect(url('/admin/citas?date=' . date('Y-m-d', strtotime($_POST['starts_at']))));
    }

    public function updateStatus(string $id): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        $status = (string)input('status');
        if (!in_array($status, ['scheduled','confirmed','in_progress','completed','cancelled','no_show'], true)) {
            $this->json(['error' => 'Estado inválido'], 422);
        }
        Appointment::updateStatus((int)$id, $status);
        Auth::log('status_change', 'appointment', $id, ['status' => $status]);
        if ($this->wantsJson()) $this->json(['ok' => true]);
        redirect(url('/admin/citas'));
    }

    public function destroy(string $id): void
    {
        $this->requireAuth(['admin','dentist','reception']);
        $this->requireCsrf();
        Appointment::delete((int)$id);
        Auth::log('delete', 'appointment', $id);
        flash('success', 'Cita eliminada.');
        redirect(url('/admin/citas'));
    }

    // -------- API --------

    public function apiList(): void
    {
        $this->requireAuth();
        $from = (string)input('from', date('Y-m-d 00:00:00'));
        $to   = (string)input('to',   date('Y-m-d 23:59:59', strtotime('+7 days')));
        $filters = [];
        if ($d = input('dentist_id')) $filters['dentist_id'] = (int)$d;
        if ($s = input('status'))     $filters['status']     = (string)$s;
        $this->json(Appointment::listForRange($from, $to, $filters));
    }

    public function apiStore(): void
    {
        $this->requireAuth();
        $data = $this->jsonBody() ?: $_POST;
        if (empty($data['patient_id']) || empty($data['dentist_id'])
            || empty($data['starts_at']) || empty($data['ends_at'])) {
            $this->json(['error' => 'Datos incompletos'], 422);
        }
        if (Appointment::hasConflict((int)$data['dentist_id'], $data['starts_at'], $data['ends_at'])) {
            $this->json(['error' => 'Conflicto de horario'], 409);
        }
        $data['created_by'] = Auth::id();
        $id = Appointment::create($data);
        $this->json(Appointment::find($id), 201);
    }
}
