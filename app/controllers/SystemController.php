<?php
class SystemController {
    public function __construct() { Auth::requireRole(['admin']); }

    /** Configuración general (identidad, colores, contacto, SAR) */
    public function index(): void {
        $settings = settingsAll();
        view('admin/sistema/index', compact('settings'));
    }

    public function saveSettings(): void {
        csrf_verify();
        $fields = [
            'spa_name','spa_tagline','spa_description','spa_address','spa_phone','spa_email',
            'spa_whatsapp','spa_instagram','spa_hours',
            'hero_title','hero_subtitle','about_subtitle','about_text',
            'color_primary','color_primary_dark','color_accent',
            'currency_symbol','app_url',
            'sar_business_name','sar_trade_name','sar_rtn','sar_address','sar_phone','sar_email','sar_regimen','sar_resolucion',
        ];
        foreach ($fields as $f) {
            if (isset($_POST[$f])) setSetting($f, trim($_POST[$f]));
        }
        // Logo
        if (!empty($_FILES['logo']['name'])) {
            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $fname = 'logo-' . randomCode(6) . '.' . $ext;
            $dest = dirname(__DIR__, 2) . '/public/assets/uploads/' . $fname;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest)) {
                setSetting('spa_logo', $fname);
            }
        }
        audit('update','settings');
        flash('success','Configuración guardada.');
        redirect('/admin/sistema');
    }

    // ===== USUARIOS =====
    public function users(): void {
        $users = Database::fetchAll("SELECT * FROM users ORDER BY name");
        view('admin/sistema/users', compact('users'));
    }

    public function userForm($id = null): void {
        $user = $id ? Database::fetch('SELECT * FROM users WHERE id = ?', [(int)$id]) : null;
        view('admin/sistema/user_form', compact('user'));
    }

    public function userSave(): void {
        csrf_verify();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name'   => trim($_POST['name']),
            'email'  => trim($_POST['email']),
            'role'   => $_POST['role'],
            'phone'  => trim($_POST['phone'] ?? '') ?: null,
            'bio'    => trim($_POST['bio'] ?? '') ?: null,
            'color'  => $_POST['color'] ?? '#6b8a7a',
            'active' => isset($_POST['active']) ? 1 : 0,
        ];
        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }
        if ($id) {
            Database::update('users', $data, 'id = :id', ['id' => $id]);
            audit('update','user',$id);
            flash('success','Usuario actualizado.');
        } else {
            if (empty($data['password'] ?? '')) {
                $data['password'] = password_hash('changeme123', PASSWORD_BCRYPT);
            }
            $newId = Database::insert('users', $data);
            audit('create','user',$newId);
            flash('success','Usuario creado. Contraseña por defecto: changeme123');
        }
        redirect('/admin/sistema/usuarios');
    }

    public function userDelete($id): void {
        csrf_verify();
        if ((int)$id === Auth::id()) {
            flash('error','No puedes eliminarte a ti mismo.');
            back();
        }
        Database::update('users', ['active' => 0], 'id = :id', ['id' => (int)$id]);
        audit('deactivate','user',(int)$id);
        flash('success','Usuario desactivado.');
        redirect('/admin/sistema/usuarios');
    }

    // ===== SAR / CAI =====
    public function sar(): void {
        $auths = Database::fetchAll("SELECT * FROM sar_authorizations ORDER BY active DESC, created_at DESC");
        view('admin/sistema/sar', compact('auths'));
    }

    public function sarSave(): void {
        csrf_verify();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'document_type'   => $_POST['document_type'],
            'cai'             => trim($_POST['cai']),
            'resolucion'      => trim($_POST['resolucion'] ?? ''),
            'establecimiento' => trim($_POST['establecimiento']),
            'punto_emision'   => trim($_POST['punto_emision']),
            'tipo_documento'  => trim($_POST['tipo_documento']),
            'rango_inicial'   => (int)$_POST['rango_inicial'],
            'rango_final'     => (int)$_POST['rango_final'],
            'next_number'     => (int)($_POST['next_number'] ?? $_POST['rango_inicial']),
            'fecha_limite'    => $_POST['fecha_limite'],
            'active'          => isset($_POST['active']) ? 1 : 0,
        ];
        if ($id) {
            Database::update('sar_authorizations', $data, 'id = :id', ['id' => $id]);
        } else {
            Database::insert('sar_authorizations', $data);
        }
        audit('save','sar_authorization', $id ?: null);
        flash('success','Autorización CAI guardada.');
        redirect('/admin/sistema/sar');
    }

    // ===== AUDITORIA =====
    public function audit(): void {
        $logs = Database::fetchAll(
            "SELECT a.*, u.name AS user_name FROM audit_log a LEFT JOIN users u ON u.id = a.user_id
             ORDER BY a.created_at DESC LIMIT 300"
        );
        view('admin/sistema/audit', compact('logs'));
    }
}
