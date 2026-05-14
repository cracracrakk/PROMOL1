<?php
class AuthController {

    public function showLogin(): void {
        if (Auth::check()) redirect('/admin');
        view('auth/login');
    }

    public function login(): void {
        csrf_verify();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (Auth::isRateLimited($ip)) {
            flash('error', 'Demasiados intentos fallidos. Espera 15 minutos.');
            redirect('/login');
        }
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!Auth::attempt($email, $password)) {
            flash('error', 'Credenciales incorrectas o usuario inactivo.');
            rememberOld();
            redirect('/login');
        }
        if (Auth::isPendingTwoFactor()) {
            redirect('/login/2fa');
        }
        Database::update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => Auth::id()]);
        audit('login', 'user', Auth::id(), 'Inicio de sesión');
        forgetOld();
        redirect('/admin');
    }

    public function showTwoFactor(): void {
        if (!Auth::isPendingTwoFactor()) redirect('/login');
        view('auth/two_factor');
    }

    public function verifyTwoFactor(): void {
        csrf_verify();
        $code = trim($_POST['code'] ?? '');
        if (!Auth::verifyTwoFactor($code)) {
            flash('error','Código incorrecto.');
            redirect('/login/2fa');
        }
        Database::update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => Auth::id()]);
        audit('login_2fa', 'user', Auth::id(), 'Inicio de sesión con 2FA');
        redirect('/admin');
    }

    public function logout(): void {
        if (Auth::check()) audit('logout', 'user', Auth::id(), 'Cierre de sesión');
        Auth::logout();
        redirect('/login');
    }
}
