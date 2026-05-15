<?php
/** @var string $_title */
/** @var array  $_user */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$active = function (string $start) use ($path) {
    return str_starts_with($path, $start) ? 'active' : '';
};
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<meta name="theme-color" content="<?= e(setting('clinic_color_primary', '#0ea5e9')) ?>">
<link rel="manifest" href="<?= url('/manifest.json') ?>">
<link rel="icon" type="image/svg+xml" href="<?= url('/assets/img/icon.svg') ?>">
<title><?= e($_title) ?> — <?= e(setting('clinic_name', 'DentalCore')) ?></title>
<link rel="stylesheet" href="<?= url('/assets/css/app.css') ?>">
<style>:root { --primary: <?= e(setting('clinic_color_primary', '#0ea5e9')) ?>; --accent: <?= e(setting('clinic_color_accent', '#06b6d4')) ?>; }</style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <a href="<?= url('/admin') ?>" class="brand">
            <span class="brand-mark">D</span>
            DentalCore
        </a>
        <nav>
            <div class="nav-section">Operación</div>
            <a href="<?= url('/admin') ?>"           class="<?= $path === '/admin' || $path === '/admin/dashboard' ? 'active' : '' ?>"><span class="nav-ico">▤</span> Dashboard</a>
            <a href="<?= url('/admin/citas') ?>"     class="<?= $active('/admin/citas') ?>"><span class="nav-ico">▦</span> Agenda</a>
            <a href="<?= url('/admin/pacientes') ?>" class="<?= $active('/admin/pacientes') ?>"><span class="nav-ico">☺</span> Pacientes</a>
            <a href="<?= url('/admin/tareas') ?>"    class="<?= $active('/admin/tareas') ?>"><span class="nav-ico">✓</span> Tareas</a>

            <div class="nav-section">Finanzas</div>
            <a href="<?= url('/admin/facturas') ?>" class="<?= $active('/admin/facturas') ?>"><span class="nav-ico">▭</span> Facturas</a>
            <a href="<?= url('/admin/caja') ?>"     class="<?= $active('/admin/caja') ?>"><span class="nav-ico">$</span> Caja diaria</a>
            <a href="<?= url('/admin/reportes') ?>" class="<?= $active('/admin/reportes') ?>"><span class="nav-ico">▩</span> Reportes</a>

            <div class="nav-section">Catálogos</div>
            <a href="<?= url('/admin/tratamientos') ?>" class="<?= $active('/admin/tratamientos') ?>"><span class="nav-ico">＋</span> Tratamientos</a>
            <a href="<?= url('/admin/inventario') ?>"   class="<?= $active('/admin/inventario') ?>"><span class="nav-ico">▣</span> Inventario</a>

            <?php if (($_user['role'] ?? '') === 'admin'): ?>
            <div class="nav-section">Administración</div>
            <a href="<?= url('/admin/configuracion') ?>" class="<?= $active('/admin/configuracion') ?>"><span class="nav-ico">⚙</span> Configuración</a>
            <?php endif; ?>
        </nav>
    </aside>

    <div class="main">
        <header class="topbar">
            <h1><?= e($_title) ?></h1>
            <div class="user-chip">
                <span class="avatar"><?= e(strtoupper(mb_substr($_user['name'] ?? 'U', 0, 1))) ?></span>
                <div style="line-height:1.1;">
                    <div style="font-weight:600;font-size:0.85rem;"><?= e($_user['name'] ?? 'Usuario') ?></div>
                    <div style="font-size:0.7rem;color:var(--gray-500);"><?= e(ucfirst($_user['role'] ?? '')) ?></div>
                </div>
                <a href="<?= url('/logout') ?>" class="btn btn-sm btn-ghost" title="Salir">↪</a>
            </div>
        </header>

        <main class="content">
            <?php if ($msg = flash('success')): ?>
                <div class="alert alert-success"><?= e($msg) ?></div>
            <?php endif; ?>
            <?php if ($msg = flash('error')): ?>
                <div class="alert alert-error"><?= e($msg) ?></div>
            <?php endif; ?>

            <?= $__content ?? '' ?>
        </main>
    </div>
</div>
<script src="<?= url('/assets/js/app.js') ?>"></script>
<?php if (!empty($_scripts)) foreach ((array)$_scripts as $s): ?>
    <script src="<?= url($s) ?>"></script>
<?php endforeach; ?>
</body>
</html>
