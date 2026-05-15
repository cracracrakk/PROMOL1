<?php
abstract class Controller
{
    protected function view(string $template, array $data = [], ?string $layout = null): void
    {
        $data['_user'] = Auth::user();
        $data['_title'] = $data['_title'] ?? cfg('app.name');

        if ($layout === null) {
            $layout = match (true) {
                str_starts_with($template, 'admin/')   => 'admin',
                str_starts_with($template, 'landing/') => 'public',
                str_starts_with($template, 'portal/')  => 'portal',
                str_starts_with($template, 'public/')  => 'public',
                default => null,
            };
        }

        $file = dirname(__DIR__) . '/views/' . $template . '.php';
        if (!is_file($file)) {
            throw new RuntimeException("Vista no encontrada: $template");
        }

        extract($data, EXTR_SKIP);

        if ($layout) {
            ob_start();
            require $file;
            $__content = ob_get_clean();
            $layoutFile = dirname(__DIR__) . '/views/layouts/' . $layout . '.php';
            require $layoutFile;
        } else {
            require $file;
        }
    }

    protected function json($data, int $status = 200): void
    {
        json_response($data, $status);
    }

    protected function requireAuth(?array $roles = null): array
    {
        $u = Auth::user();
        if (!$u) {
            if ($this->wantsJson()) $this->json(['error' => 'No autenticado'], 401);
            redirect(url('/login'));
        }
        if ($roles && !in_array($u['role'], $roles, true)) {
            if ($this->wantsJson()) $this->json(['error' => 'Sin permisos'], 403);
            http_response_code(403);
            echo 'Sin permisos suficientes.';
            exit;
        }
        return $u;
    }

    protected function requireCsrf(): void
    {
        if (!csrf_check()) {
            if ($this->wantsJson()) $this->json(['error' => 'CSRF inválido'], 419);
            http_response_code(419);
            echo 'Token CSRF inválido.';
            exit;
        }
    }

    protected function wantsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return str_contains($accept, 'application/json')
            || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/');
    }

    protected function jsonBody(): array
    {
        $raw = file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
}
