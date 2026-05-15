<?php
class PortalController extends Controller
{
    public function login(): void
    {
        if (!empty($_SESSION['portal_patient_id'])) redirect(url('/mi-cuenta/inicio'));
        $this->view('portal/login', ['_title' => 'Portal del paciente']);
    }

    public function sendMagicLink(): void
    {
        $this->requireCsrf();
        $email = trim((string)input('email'));
        $patient = Database::one('SELECT * FROM patients WHERE email = ? AND is_active = 1', [$email]);
        if (!$patient) {
            // Por seguridad no revelamos si existe
            flash('success', 'Si el email está registrado, recibirás un enlace de acceso.');
            redirect(url('/mi-cuenta'));
        }
        $token = bin2hex(random_bytes(32));
        Database::update('patients', [
            'portal_token'         => $token,
            'portal_token_expires' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        ], ['id' => $patient['id']]);

        Mail::send(
            $email,
            'Acceso a tu portal de paciente',
            Mail::template('portal', [
                'patient_name' => $patient['first_name'],
                'token'        => $token,
            ]),
            (string)$patient['id']
        );

        flash('success', 'Si el email está registrado, recibirás un enlace de acceso.');
        redirect(url('/mi-cuenta'));
    }

    public function authenticate(string $token): void
    {
        $patient = Database::one(
            'SELECT * FROM patients WHERE portal_token = ? AND portal_token_expires > NOW()',
            [$token]
        );
        if (!$patient) {
            flash('error', 'El enlace expiró o no es válido.');
            redirect(url('/mi-cuenta'));
        }
        Database::update('patients', [
            'portal_token'         => null,
            'portal_token_expires' => null,
            'portal_last_login'    => now(),
        ], ['id' => $patient['id']]);
        $_SESSION['portal_patient_id'] = (int)$patient['id'];
        redirect(url('/mi-cuenta/inicio'));
    }

    private function requirePortalAuth(): array
    {
        $id = $_SESSION['portal_patient_id'] ?? null;
        if (!$id) redirect(url('/mi-cuenta'));
        $patient = Patient::find((int)$id);
        if (!$patient) { unset($_SESSION['portal_patient_id']); redirect(url('/mi-cuenta')); }
        return $patient;
    }

    public function dashboard(): void
    {
        $patient = $this->requirePortalAuth();
        $this->view('portal/dashboard', [
            '_title'   => 'Mi portal',
            'patient'  => $patient,
            'upcoming' => Appointment::upcomingFor((int)$patient['id']),
        ], 'portal');
    }

    public function appointments(): void
    {
        $patient = $this->requirePortalAuth();
        $this->view('portal/appointments', [
            '_title'   => 'Mis citas',
            'patient'  => $patient,
            'upcoming' => Appointment::upcomingFor((int)$patient['id']),
            'history'  => Appointment::historyFor((int)$patient['id']),
        ], 'portal');
    }

    public function treatments(): void
    {
        $patient = $this->requirePortalAuth();
        $notes = ClinicalNote::forPatient((int)$patient['id']);
        $this->view('portal/treatments', [
            '_title'  => 'Mis tratamientos',
            'patient' => $patient,
            'notes'   => $notes,
        ], 'portal');
    }

    public function invoices(): void
    {
        $patient = $this->requirePortalAuth();
        $result = Invoice::search(['patient_id' => (int)$patient['id']], 1, 50);
        $this->view('portal/invoices', [
            '_title' => 'Mis facturas',
            'patient' => $patient,
            'invoices' => $result['data'],
        ], 'portal');
    }

    public function logout(): void
    {
        unset($_SESSION['portal_patient_id']);
        redirect(url('/mi-cuenta'));
    }
}
