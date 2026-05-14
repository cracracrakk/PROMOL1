<?php
class AuthController {

    public function showLogin(): void {
        if (Auth::check()) redirect('/admin');
        view('auth/login');
    }

    public function login(): void {
        csrf_verify();
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!Auth::attempt($email, $password)) {
            flash('error', 'Credenciales incorrectas o usuario inactivo.');
            rememberOld();
            redirect('/login');
        }
        Database::update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => Auth::id()]);
        audit('login', 'user', Auth::id(), 'Inicio de sesión');
        forgetOld();
        redirect('/admin');
    }

    public function logout(): void {
        if (Auth::check()) audit('logout', 'user', Auth::id(), 'Cierre de sesión');
        Auth::logout();
        redirect('/login');
    }
}
