<?php
class TreatmentController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->view('admin/treatments/index', [
            '_title' => 'Catálogo de tratamientos',
            'treatments' => Treatment::all(false),
        ]);
    }

    public function form(?string $id = null): void
    {
        $this->requireAuth(['admin','dentist']);
        $treatment = $id ? Treatment::find((int)$id) : null;
        $this->view('admin/treatments/form', [
            '_title' => $treatment ? 'Editar tratamiento' : 'Nuevo tratamiento',
            'treatment' => $treatment,
        ]);
    }

    public function save(?string $id = null): void
    {
        $this->requireAuth(['admin','dentist']);
        $this->requireCsrf();
        $data = $_POST;
        $data['default_price']  = (float)($data['default_price'] ?? 0);
        $data['duration_min']   = (int)($data['duration_min'] ?? 30);
        $data['requires_tooth'] = !empty($data['requires_tooth']) ? 1 : 0;
        $data['is_active']      = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        if ($id) {
            Treatment::update((int)$id, $data);
            Auth::log('update', 'treatment', $id);
        } else {
            $id = Treatment::create($data);
            Auth::log('create', 'treatment', $id);
        }
        flash('success', 'Tratamiento guardado.');
        redirect(url('/admin/tratamientos'));
    }

    public function destroy(string $id): void
    {
        $this->requireAuth(['admin']);
        $this->requireCsrf();
        Treatment::delete((int)$id);
        Auth::log('delete', 'treatment', $id);
        flash('success', 'Tratamiento desactivado.');
        redirect(url('/admin/tratamientos'));
    }
}
