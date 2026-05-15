<?php
class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::user()) redirect(url('/admin'));
        $this->view('auth/login', ['_title' => 'Iniciar sesión']);
    }

    public function login(): void
    {
        $this->requireCsrf();
        $email = trim((string)input('email'));
        $pass  = (string)input('password');

        $user = Auth::attempt($email, $pass);
        if (!$user) {
            flash('error', 'Credenciales incorrectas.');
            redirect(url('/login'));
        }
        Auth::log('login', 'user', $user['id']);
        redirect(url('/admin'));
    }

    public function logout(): void
    {
        Auth::log('logout', 'user', Auth::id());
        Auth::logout();
        redirect(url('/login'));
    }
}
