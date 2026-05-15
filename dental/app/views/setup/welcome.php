<?php
$layout = false; // standalone
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Configuración inicial — DentalCore</title>
<link rel="stylesheet" href="<?= url('/assets/css/app.css') ?>">
</head>
<body style="background:linear-gradient(135deg,#0ea5e9,#06b6d4);padding:2rem;">
<div style="max-width:680px;margin:0 auto;background:#fff;border-radius:12px;padding:2.5rem;box-shadow:0 10px 30px rgba(0,0,0,0.15);">
    <h1 style="margin:0 0 0.5rem;">¡Bienvenido a DentalCore! 🦷</h1>
    <p class="text-muted mb-3">Vamos a configurar tu clínica en unos pocos pasos.</p>

    <form method="post" action="<?= url('/setup') ?>">
        <?= csrf_field() ?>
        <h3>1. Datos de tu clínica</h3>
        <div class="grid grid-2">
            <div class="form-group"><label>Nombre de la clínica *</label>
                <input type="text" name="clinic_name" required></div>
            <div class="form-group"><label>Slogan</label>
                <input type="text" name="clinic_tagline"></div>
            <div class="form-group" style="grid-column:span 2;"><label>Dirección</label>
                <input type="text" name="clinic_address"></div>
            <div class="form-group"><label>Teléfono</label>
                <input type="tel" name="clinic_phone"></div>
            <div class="form-group"><label>Email</label>
                <input type="email" name="clinic_email"></div>
            <div class="form-group"><label>RTN / NIT</label>
                <input type="text" name="clinic_rtn"></div>
        </div>

        <h3 class="mt-3">2. Monedas e impuestos</h3>
        <div class="grid grid-3">
            <div class="form-group"><label>Símbolo</label>
                <input type="text" name="currency_symbol" value="L"></div>
            <div class="form-group"><label>Código</label>
                <input type="text" name="currency_code" value="HNL"></div>
            <div class="form-group"><label>Impuesto (%)</label>
                <input type="number" step="0.01" name="tax_rate" value="15"></div>
        </div>

        <h3 class="mt-3">3. Horarios de atención</h3>
        <div class="grid grid-2">
            <div class="form-group"><label>Apertura</label>
                <input type="time" name="working_hours_start" value="08:00"></div>
            <div class="form-group"><label>Cierre</label>
                <input type="time" name="working_hours_end" value="18:00"></div>
        </div>

        <h3 class="mt-3">4. Branding</h3>
        <div class="grid grid-2">
            <div class="form-group"><label>Color primario</label>
                <input type="color" name="clinic_color_primary" value="#0ea5e9"></div>
            <div class="form-group"><label>Color de acento</label>
                <input type="color" name="clinic_color_accent" value="#06b6d4"></div>
        </div>

        <h3 class="mt-3">5. Cuenta de administrador</h3>
        <div class="grid grid-2">
            <div class="form-group"><label>Tu nombre *</label>
                <input type="text" name="admin_name" required></div>
            <div class="form-group"><label>Email *</label>
                <input type="email" name="admin_email" required></div>
            <div class="form-group" style="grid-column:span 2;"><label>Contraseña *</label>
                <input type="password" name="admin_password" required minlength="6"></div>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary btn-lg">Comenzar a usar DentalCore</button>
        </div>
    </form>
</div>
</body></html>
