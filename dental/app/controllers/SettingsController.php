<?php
class SettingsController extends Controller
{
    public function index(): void
    {
        $this->requireAuth(['admin']);
        $this->view('admin/settings/index', [
            '_title'   => 'Configuración',
            'settings' => Setting::all(),
        ]);
    }

    public function save(): void
    {
        $this->requireAuth(['admin']);
        $this->requireCsrf();
        $keys = [
            'clinic_name','clinic_tagline','clinic_address','clinic_phone',
            'clinic_email','clinic_rtn','currency_symbol','currency_code',
            'tax_rate','working_hours_start','working_hours_end','working_days',
            'clinic_color_primary','clinic_color_accent','appointment_step',
            'reminder_hours_before','reminder_enabled','reminder_channel',
            'recall_months','survey_enabled','whatsapp_api_url','whatsapp_api_token',
        ];
        $data = [];
        foreach ($keys as $k) {
            if (array_key_exists($k, $_POST)) $data[$k] = (string)$_POST[$k];
        }
        Setting::saveMany($data);
        Auth::log('update', 'settings', null, array_keys($data));
        flash('success', 'Configuración guardada.');
        redirect(url('/admin/configuracion'));
    }

    public function users(): void
    {
        $this->requireAuth(['admin']);
        $this->view('admin/settings/users', [
            '_title' => 'Usuarios del sistema',
            'users'  => User::all(),
        ]);
    }

    public function userSave(): void
    {
        $this->requireAuth(['admin']);
        $this->requireCsrf();
        $id = (int)input('id', 0);
        $data = $_POST;
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        if ($id) {
            User::update($id, $data);
            Auth::log('update', 'user', $id);
        } else {
            $id = User::create($data);
            Auth::log('create', 'user', $id);
        }
        flash('success', 'Usuario guardado.');
        redirect(url('/admin/configuracion/usuarios'));
    }
}
