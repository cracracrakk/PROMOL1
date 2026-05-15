<?php /** @var string $_title */ ?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($_title) ?></title>
<meta name="description" content="DentalCore es el software todo-en-uno para clínicas dentales: pacientes, citas, odontograma digital y facturación.">
<link rel="stylesheet" href="<?= url('/assets/css/app.css') ?>">
<link rel="manifest" href="<?= url('/manifest.json') ?>">
<link rel="icon" type="image/svg+xml" href="<?= url('/assets/img/icon.svg') ?>">
<style>:root { --primary: <?= e(setting('clinic_color_primary', '#0ea5e9')) ?>; --accent: <?= e(setting('clinic_color_accent', '#06b6d4')) ?>; }</style>
</head>
<body class="landing">

<header class="landing-nav">
    <a href="<?= url('/') ?>" class="brand">
        <span class="brand-mark">D</span>
        <?= e(setting('clinic_name', 'DentalCore')) ?>
    </a>
    <nav>
        <a href="<?= url('/funciones') ?>">Funciones</a>
        <a href="<?= url('/precios') ?>">Precios</a>
        <a href="<?= url('/contacto') ?>">Contacto</a>
    </nav>
    <div class="flex gap-1">
        <a href="<?= url('/mi-cuenta') ?>" class="btn">Portal paciente</a>
        <a href="<?= url('/login') ?>" class="btn btn-primary">Acceso clínica</a>
    </div>
</header>

<?= $__content ?? '' ?>

<footer class="footer">
    <div class="footer-grid">
        <div>
            <div style="display:flex;gap:10px;align-items:center;color:#fff;margin-bottom:0.75rem;">
                <span class="brand-mark" style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;font-weight:800;">D</span>
                <strong><?= e(setting('clinic_name', 'DentalCore')) ?></strong>
            </div>
            <p style="color:#94a3b8;max-width:320px;">Software profesional para gestión integral de clínicas dentales.</p>
        </div>
        <div>
            <h4>Producto</h4>
            <a href="<?= url('/funciones') ?>">Funciones</a>
            <a href="<?= url('/precios') ?>">Precios</a>
            <a href="<?= url('/login') ?>">Iniciar sesión</a>
        </div>
        <div>
            <h4>Pacientes</h4>
            <a href="<?= url('/mi-cuenta') ?>">Portal del paciente</a>
            <a href="<?= url('/contacto') ?>">Contacto</a>
        </div>
        <div>
            <h4>Legal</h4>
            <a href="#">Términos</a>
            <a href="#">Privacidad</a>
        </div>
    </div>
    <div class="footer-bottom">© <?= date('Y') ?> <?= e(setting('clinic_name', 'DentalCore')) ?>. Todos los derechos reservados.</div>
</footer>
<script src="<?= url('/assets/js/app.js') ?>"></script>
</body>
</html>
