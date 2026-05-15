<?php
class HomeController extends Controller
{
    public function landing(): void
    {
        $this->view('landing/index', [
            '_title' => 'DentalCore — Software para clínicas dentales modernas',
        ]);
    }

    public function pricing(): void
    {
        $this->view('landing/pricing', ['_title' => 'Planes y precios — DentalCore']);
    }

    public function features(): void
    {
        $this->view('landing/features', ['_title' => 'Funcionalidades — DentalCore']);
    }

    public function contact(): void
    {
        $this->view('landing/contact', ['_title' => 'Contacto — DentalCore']);
    }

    public function submitContact(): void
    {
        $this->requireCsrf();
        $v = Validator::make($_POST, [
            'name'    => 'required|max:120',
            'email'   => 'required|email',
            'message' => 'required|min:10',
        ]);
        if ($v->fails()) {
            flash('error', $v->firstError());
            redirect(url('/contacto'));
        }
        Database::insert('settings', [
            'key' => 'contact_msg_' . time(),
            'value' => json_encode(only($_POST, ['name','email','phone','clinic','message']), JSON_UNESCAPED_UNICODE),
        ]);
        flash('success', '¡Gracias! Te contactaremos pronto.');
        redirect(url('/contacto'));
    }
}
