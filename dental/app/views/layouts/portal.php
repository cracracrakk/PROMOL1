<?php /** @var array $patient */ ?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($_title) ?> — Portal</title>
<link rel="manifest" href="<?= url('/manifest.json') ?>">
<link rel="stylesheet" href="<?= url('/assets/css/app.css') ?>">
<style>:root { --primary: <?= e(setting('clinic_color_primary', '#0ea5e9')) ?>; }</style>
</head>
<body style="background:var(--gray-50);">
<header style="background:#fff;border-bottom:1px solid var(--gray-200);padding:1rem 1.5rem;display:flex;justify-content:space-between;align-items:center;">
    <div style="display:flex;gap:10px;align-items:center;">
        <span style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;">D</span>
        <strong><?= e(setting('clinic_name', 'DentalCore')) ?></strong>
    </div>
    <div style="display:flex;gap:1rem;align-items:center;">
        <span class="text-muted"><?= e($patient['first_name'].' '.$patient['last_name']) ?></span>
        <a href="<?= url('/mi-cuenta/salir') ?>" class="btn btn-sm">Salir</a>
    </div>
</header>
<nav style="background:#fff;border-bottom:1px solid var(--gray-200);padding:0.5rem 1.5rem;display:flex;gap:0.5rem;">
    <a href="<?= url('/mi-cuenta/inicio') ?>" class="btn btn-sm">Inicio</a>
    <a href="<?= url('/mi-cuenta/citas') ?>" class="btn btn-sm">Mis citas</a>
    <a href="<?= url('/mi-cuenta/tratamientos') ?>" class="btn btn-sm">Tratamientos</a>
    <a href="<?= url('/mi-cuenta/facturas') ?>" class="btn btn-sm">Facturas</a>
</nav>
<main style="max-width:1000px;margin:1.5rem auto;padding:0 1rem;">
    <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>
    <?= $__content ?? '' ?>
</main>
<script src="<?= url('/assets/js/app.js') ?>"></script>
</body></html>
