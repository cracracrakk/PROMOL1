<?php
class PatientController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $q    = (string)input('q', '');
        $page = max(1, (int)input('page', 1));
        $result = Patient::search($q, $page, 20);

        $this->view('admin/patients/index', [
            '_title' => 'Pacientes',
            'q' => $q, 'result' => $result,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('admin/patients/form', [
            '_title' => 'Nuevo paciente',
            'patient' => null,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        $v = Validator::make($_POST, [
            'first_name' => 'required|max:80',
            'last_name'  => 'required|max:80',
            'email'      => 'email',
            'birth_date' => 'date',
        ]);
        if ($v->fails()) {
            flash('error', $v->firstError());
            redirect(url('/admin/pacientes/nuevo'));
        }
        $data = $_POST;
        $data['created_by'] = Auth::id();
        $id = Patient::create($data);
        Patient::saveMedicalHistory($id, $_POST);
        Auth::log('create', 'patient', $id);
        flash('success', 'Paciente registrado correctamente.');
        redirect(url('/admin/pacientes/' . $id));
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $patient = Patient::findWithHistory((int)$id);
        if (!$patient) { http_response_code(404); echo 'No encontrado.'; return; }

        $this->view('admin/patients/show', [
            '_title' => $patient['first_name'] . ' ' . $patient['last_name'],
            'patient'  => $patient,
            'upcoming' => Appointment::upcomingFor((int)$id),
            'history'  => Appointment::historyFor((int)$id),
            'notes'    => ClinicalNote::forPatient((int)$id),
            'plans'    => TreatmentPlan::forPatient((int)$id),
            'invoices' => Invoice::search(['patient_id' => (int)$id], 1, 10)['data'],
        ]);
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $patient = Patient::findWithHistory((int)$id);
        if (!$patient) { http_response_code(404); echo 'No encontrado.'; return; }
        $this->view('admin/patients/form', [
            '_title' => 'Editar paciente',
            'patient' => $patient,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        Patient::update((int)$id, $_POST);
        Patient::saveMedicalHistory((int)$id, $_POST);
        Auth::log('update', 'patient', $id);
        flash('success', 'Paciente actualizado.');
        redirect(url('/admin/pacientes/' . $id));
    }

    public function destroy(string $id): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->requireCsrf();
        Patient::delete((int)$id);
        Auth::log('delete', 'patient', $id);
        flash('success', 'Paciente desactivado.');
        redirect(url('/admin/pacientes'));
    }

    // -------- API endpoints --------

    public function apiList(): void
    {
        $this->requireAuth();
        $this->json(Patient::search((string)input('q', ''), (int)input('page', 1), (int)input('per_page', 20)));
    }

    public function apiShow(string $id): void
    {
        $this->requireAuth();
        $p = Patient::findWithHistory((int)$id);
        if (!$p) $this->json(['error' => 'No encontrado'], 404);
        $this->json($p);
    }
}
