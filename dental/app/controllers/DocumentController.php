<?php
class DocumentController extends Controller
{
    private const ALLOWED_MIME = [
        'image/jpeg','image/png','image/webp','image/gif','image/heic','image/heif',
        'application/pdf',
    ];
    private const MAX_BYTES = 10 * 1024 * 1024; // 10 MB

    public function index(string $patientId): void
    {
        $this->requireAuth();
        $patient = Patient::find((int)$patientId);
        if (!$patient) { http_response_code(404); return; }
        $type = (string)input('type', '');
        $this->view('admin/documents/index', [
            '_title'    => 'Documentos — ' . $patient['first_name'].' '.$patient['last_name'],
            'patient'   => $patient,
            'type'      => $type ?: null,
            'documents' => PatientDocument::listFor((int)$patientId, $type ?: null),
        ]);
    }

    public function upload(string $patientId): void
    {
        $this->requireAuth();
        $this->requireCsrf();
        $patient = Patient::find((int)$patientId);
        if (!$patient) { flash('error', 'Paciente no encontrado.'); redirect(url('/admin/pacientes')); }

        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'No se pudo subir el archivo.');
            redirect(url('/admin/pacientes/'.$patientId.'/documentos'));
        }
        $f = $_FILES['file'];
        if ($f['size'] > self::MAX_BYTES) {
            flash('error', 'Archivo demasiado grande (máx 10 MB).');
            redirect(url('/admin/pacientes/'.$patientId.'/documentos'));
        }
        $mime = mime_content_type($f['tmp_name']) ?: $f['type'];
        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            flash('error', 'Tipo de archivo no permitido.');
            redirect(url('/admin/pacientes/'.$patientId.'/documentos'));
        }

        $ext = pathinfo($f['name'], PATHINFO_EXTENSION) ?: 'bin';
        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'bin';
        $filename = sprintf('%d_%s.%s', $patientId, bin2hex(random_bytes(8)), $safeExt);
        $dir = dirname(__DIR__, 2) . '/storage/uploads/' . $patientId;
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $dest = $dir . '/' . $filename;
        if (!move_uploaded_file($f['tmp_name'], $dest)) {
            flash('error', 'Error guardando el archivo.');
            redirect(url('/admin/pacientes/'.$patientId.'/documentos'));
        }

        $id = PatientDocument::create([
            'patient_id'   => (int)$patientId,
            'type'         => (string)input('type', 'document'),
            'title'        => (string)input('title', basename($f['name'])),
            'description'  => (string)input('description', ''),
            'filename'     => $filename,
            'original_name'=> $f['name'],
            'mime'         => $mime,
            'size_bytes'   => (int)$f['size'],
            'tooth_code'   => input('tooth_code') ?: null,
            'taken_at'     => input('taken_at') ?: null,
            'uploaded_by'  => Auth::id(),
        ]);
        Auth::log('upload', 'patient_document', $id);
        flash('success', 'Documento subido.');
        redirect(url('/admin/pacientes/'.$patientId.'/documentos'));
    }

    public function view(string $id): void
    {
        $this->requireAuth();
        $doc = PatientDocument::find((int)$id);
        if (!$doc) { http_response_code(404); echo 'No encontrado.'; return; }
        $path = dirname(__DIR__, 2) . '/storage/uploads/' . $doc['patient_id'] . '/' . $doc['filename'];
        if (!is_file($path)) { http_response_code(404); echo 'Archivo no encontrado.'; return; }
        header('Content-Type: ' . $doc['mime']);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: inline; filename="' . basename($doc['original_name'] ?? $doc['filename']) . '"');
        readfile($path);
    }

    public function destroy(string $id): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->requireCsrf();
        $doc = PatientDocument::delete((int)$id);
        if ($doc) {
            $path = dirname(__DIR__, 2) . '/storage/uploads/' . $doc['patient_id'] . '/' . $doc['filename'];
            if (is_file($path)) @unlink($path);
            Auth::log('delete', 'patient_document', $id);
        }
        flash('success', 'Documento eliminado.');
        redirect($_SERVER['HTTP_REFERER'] ?? url('/admin/pacientes'));
    }
}
