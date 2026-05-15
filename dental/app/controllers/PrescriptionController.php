<?php
class PrescriptionController extends Controller
{
    public function index(string $patientId): void
    {
        $this->requireAuth();
        $patient = Patient::find((int)$patientId);
        if (!$patient) { http_response_code(404); return; }
        $this->view('admin/prescriptions/index', [
            '_title' => 'Recetas — ' . $patient['first_name'].' '.$patient['last_name'],
            'patient' => $patient,
            'list'    => Prescription::listFor((int)$patientId),
        ]);
    }

    public function create(string $patientId): void
    {
        $this->requireAuth(['admin','dentist']);
        $patient = Patient::find((int)$patientId);
        if (!$patient) { http_response_code(404); return; }
        $this->view('admin/prescriptions/form', [
            '_title' => 'Nueva receta',
            'patient' => $patient,
        ]);
    }

    public function store(string $patientId): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->requireCsrf();
        $items = [];
        $drugs = $_POST['drug'] ?? [];
        $doses = $_POST['dosage'] ?? [];
        $freqs = $_POST['frequency'] ?? [];
        $durs  = $_POST['duration'] ?? [];
        $instr = $_POST['instructions'] ?? [];
        for ($i = 0; $i < count($drugs); $i++) {
            if (empty($drugs[$i])) continue;
            $items[] = [
                'drug'         => $drugs[$i],
                'dosage'       => $doses[$i] ?? null,
                'frequency'    => $freqs[$i] ?? null,
                'duration'     => $durs[$i] ?? null,
                'instructions' => $instr[$i] ?? null,
            ];
        }
        if (!$items) {
            flash('error', 'Agrega al menos un medicamento.');
            redirect(url('/admin/pacientes/'.$patientId.'/recetas/nueva'));
        }
        $id = Prescription::create([
            'patient_id' => (int)$patientId,
            'dentist_id' => Auth::id(),
            'diagnosis'  => (string)input('diagnosis', ''),
            'notes'      => (string)input('notes', ''),
        ], $items);
        Auth::log('create', 'prescription', $id);
        flash('success', 'Receta creada.');
        redirect(url('/admin/recetas/'.$id));
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $rx = Prescription::find((int)$id);
        if (!$rx) { http_response_code(404); return; }
        $this->view('admin/prescriptions/show', [
            '_title' => 'Receta ' . $rx['code'],
            'rx' => $rx,
        ]);
    }

    public function printable(string $id): void
    {
        $this->requireAuth();
        $rx = Prescription::find((int)$id);
        if (!$rx) { http_response_code(404); return; }
        $this->view('admin/prescriptions/print', ['rx' => $rx]);
    }
}
