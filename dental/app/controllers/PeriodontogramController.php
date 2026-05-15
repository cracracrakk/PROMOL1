<?php
class PeriodontogramController extends Controller
{
    public function index(string $patientId): void
    {
        $this->requireAuth();
        $patient = Patient::find((int)$patientId);
        if (!$patient) { http_response_code(404); return; }
        $this->view('admin/periodontogram/index', [
            '_title' => 'Periodontograma — ' . $patient['first_name'].' '.$patient['last_name'],
            'patient' => $patient,
            'exams'   => Periodontogram::listFor((int)$patientId),
        ]);
    }

    public function create(string $patientId): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->requireCsrf();
        // GET form, but no creation yet — we redirect to actually creating
        // To simplify, create immediately on GET so the user can fill teeth.
    }

    public function store(string $patientId): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->requireCsrf();
        $id = Periodontogram::create(
            (int)$patientId,
            (string)input('exam_date', date('Y-m-d')),
            Auth::id(),
            (string)input('notes', '')
        );
        Auth::log('create', 'periodontogram', $id);
        redirect(url('/admin/periodontograma/' . $id));
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $exam = Periodontogram::find((int)$id);
        if (!$exam) { http_response_code(404); return; }
        $patient = Patient::find((int)$exam['patient_id']);

        $this->view('admin/periodontogram/show', [
            '_title' => 'Periodontograma #' . $id,
            'patient' => $patient,
            'exam'    => $exam,
        ]);
    }

    public function updateTooth(string $id): void
    {
        $this->requireAuth(['admin','dentist']);
        $data = $this->jsonBody() ?: $_POST;
        $code = (string)($data['tooth_code'] ?? '');
        if (!$code) $this->json(['error' => 'Falta tooth_code'], 422);
        Periodontogram::upsertTooth((int)$id, $code, $data);
        $this->json(['ok' => true]);
    }
}
