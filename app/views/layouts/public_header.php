<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? brand_name()) ?> · <?= e(setting('spa_tagline', 'Bienestar y armonía')) ?></title>
    <meta name="description" content="<?= e($pageDescription ?? setting('spa_description', '')) ?>">
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="manifest" href="<?= url('/manifest.json') ?>">
    <meta name="theme-color" content="<?= e(setting('color_primary', '#6b8a7a')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        :root {
            --brand: <?= e(setting('color_primary', '#6b8a7a')) ?>;
            --accent: <?= e(setting('color_accent', '#c9a96e')) ?>;
        }
    </style>
</head>
<body class="public-body">
<header class="site-header">
    <div class="container nav">
        <a href="<?= url('/') ?>" class="logo">
            <?php if (brand_logo()): ?>
                <img src="<?= brand_logo() ?>" alt="<?= e(brand_name()) ?>" style="height:42px;">
            <?php else: ?>
                <?= e(brand_name()) ?><span>.</span>
            <?php endif; ?>
        </a>
        <nav>
            <ul class="nav-links">
                <li><a href="<?= url('/') ?>">Inicio</a></li>
                <li><a href="<?= url('/servicios') ?>">Servicios</a></li>
                <li><a href="<?= url('/sobre-nosotros') ?>">Nosotros</a></li>
                <li><a href="<?= url('/regalo') ?>">Tarjeta Regalo</a></li>
                <li><a href="<?= url('/contacto') ?>">Contacto</a></li>
                <li><a href="<?= url('/reservar') ?>" class="btn btn-sm">Reservar</a></li>
            </ul>
            <button class="nav-toggle" aria-label="Menú">&#9776;</button>
        </nav>
    </div>
</header>
<main>
<?php if ($msg = flash('success')): ?>
    <div class="container"><div class="alert alert-success"><?= e($msg) ?></div></div>
<?php endif; ?>
<?php if ($msg = flash('error')): ?>
    <div class="container"><div class="alert alert-error"><?= e($msg) ?></div></div>
<?php endif; ?>
