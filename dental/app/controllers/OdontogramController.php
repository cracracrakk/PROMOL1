<?php
class OdontogramController extends Controller
{
    public function show(string $patientId): void
    {
        $this->requireAuth();
        $patient = Patient::find((int)$patientId);
        if (!$patient) { http_response_code(404); echo 'No encontrado.'; return; }
        $this->view('admin/odontogram/show', [
            '_title'    => 'Odontograma — ' . $patient['first_name'] . ' ' . $patient['last_name'],
            'patient'   => $patient,
            'odontogram'=> Odontogram::forPatient((int)$patientId),
            'history'   => Odontogram::historyFor((int)$patientId, 30),
            'statuses'  => Odontogram::STATUSES,
        ]);
    }

    public function update(string $patientId): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->requireCsrf();
        $data = $this->jsonBody() ?: $_POST;
        $teeth = $data['teeth'] ?? [];
        if (!is_array($teeth) || !$teeth) {
            $this->json(['error' => 'Sin datos'], 422);
        }
        Odontogram::bulkUpdate((int)$patientId, $teeth, Auth::id());
        Auth::log('update', 'odontogram', $patientId, ['teeth_count' => count($teeth)]);
        $this->json(['ok' => true, 'odontogram' => Odontogram::forPatient((int)$patientId)]);
    }

    public function apiGet(string $patientId): void
    {
        $this->requireAuth();
        $this->json(Odontogram::forPatient((int)$patientId));
    }
}

class ClinicalController extends Controller
{
    public function notesIndex(string $patientId): void
    {
        $this->requireAuth();
        $patient = Patient::find((int)$patientId);
        if (!$patient) { http_response_code(404); return; }
        $this->view('admin/clinical/notes', [
            '_title'  => 'Historia clínica — ' . $patient['first_name'] . ' ' . $patient['last_name'],
            'patient' => $patient,
            'notes'   => ClinicalNote::forPatient((int)$patientId),
        ]);
    }

    public function notesStore(string $patientId): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->requireCsrf();
        $data = $_POST;
        $data['patient_id'] = (int)$patientId;
        $data['dentist_id'] = $data['dentist_id'] ?? Auth::id();
        $data['visit_date'] = $data['visit_date'] ?? date('Y-m-d');
        $id = ClinicalNote::create($data);
        Auth::log('create', 'clinical_note', $id);
        flash('success', 'Nota clínica añadida.');
        redirect(url('/admin/pacientes/' . $patientId . '/historia'));
    }
}
