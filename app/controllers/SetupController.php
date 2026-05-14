<?php
/**
 * Wizard de configuración inicial. Solo accesible si no hay
 * usuarios en la BD (instalación recién hecha).
 */
class SetupController {

    private function isFresh(): bool {
        try {
            $row = Database::fetch('SELECT COUNT(*) c FROM users');
            return (int)$row['c'] === 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function welcome(): void {
        if (!$this->isFresh()) redirect('/login');
        view('setup/welcome');
    }

    public function step1(): void {
        if (!$this->isFresh()) redirect('/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            setSetting('spa_name', trim($_POST['spa_name']));
            setSetting('spa_tagline', trim($_POST['spa_tagline'] ?? ''));
            setSetting('spa_phone', trim($_POST['spa_phone'] ?? ''));
            setSetting('spa_email', trim($_POST['spa_email'] ?? ''));
            setSetting('spa_address', trim($_POST['spa_address'] ?? ''));
            setSetting('color_primary', $_POST['color_primary'] ?? '#6b8a7a');
            setSetting('color_accent', $_POST['color_accent'] ?? '#c9a96e');
            redirect('/setup/paso-2');
        }
        view('setup/step1');
    }

    public function step2(): void {
        if (!$this->isFresh()) redirect('/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            setSetting('sar_business_name', trim($_POST['sar_business_name']));
            setSetting('sar_rtn', trim($_POST['sar_rtn']));
            setSetting('sar_address', trim($_POST['sar_address'] ?? ''));
            setSetting('sar_regimen', $_POST['sar_regimen'] ?? 'General');
            redirect('/setup/paso-3');
        }
        view('setup/step2');
    }

    public function step3(): void {
        if (!$this->isFresh()) redirect('/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $email    = trim($_POST['email']);
            $password = $_POST['password'];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
                flash('error','Datos inválidos.');
                redirect('/setup/paso-3');
            }
            Database::insert('users', [
                'name'     => trim($_POST['name']),
                'email'    => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role'     => 'admin',
                'active'   => 1,
            ]);
            flash('success','¡Configuración completada! Inicia sesión con tu cuenta de administrador.');
            redirect('/login');
        }
        view('setup/step3');
    }
}
