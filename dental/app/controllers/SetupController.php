<?php
class SetupController extends Controller
{
    public function welcome(): void
    {
        if (setting('setup_completed') === '1' && !Auth::user()) {
            redirect(url('/login'));
        }
        $this->view('setup/welcome', ['_title' => 'Bienvenido a DentalCore']);
    }

    public function save(): void
    {
        $this->requireCsrf();
        $data = $_POST;

        // Si aún no hay usuario admin y se proporcionaron credenciales, crear
        if (!empty($data['admin_email']) && !empty($data['admin_password'])) {
            $exists = Database::value('SELECT 1 FROM users WHERE email = ?', [$data['admin_email']]);
            if (!$exists) {
                User::create([
                    'name'     => $data['admin_name'] ?? 'Administrador',
                    'email'    => $data['admin_email'],
                    'password' => $data['admin_password'],
                    'role'     => 'admin',
                    'is_active'=> 1,
                ]);
            }
        }

        Setting::saveMany([
            'clinic_name'           => $data['clinic_name'] ?? 'Mi Clínica Dental',
            'clinic_tagline'        => $data['clinic_tagline'] ?? '',
            'clinic_address'        => $data['clinic_address'] ?? '',
            'clinic_phone'          => $data['clinic_phone'] ?? '',
            'clinic_email'          => $data['clinic_email'] ?? '',
            'clinic_rtn'            => $data['clinic_rtn'] ?? '',
            'currency_symbol'       => $data['currency_symbol'] ?? 'L',
            'currency_code'         => $data['currency_code'] ?? 'HNL',
            'tax_rate'              => $data['tax_rate'] ?? '15',
            'working_hours_start'   => $data['working_hours_start'] ?? '08:00',
            'working_hours_end'     => $data['working_hours_end'] ?? '18:00',
            'clinic_color_primary'  => $data['clinic_color_primary'] ?? '#0ea5e9',
            'clinic_color_accent'   => $data['clinic_color_accent'] ?? '#06b6d4',
            'setup_completed'       => '1',
        ]);

        flash('success', 'Configuración inicial guardada. Ya puedes iniciar sesión.');
        redirect(url('/login'));
    }
}
