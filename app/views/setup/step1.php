<!DOCTYPE html>
<html lang="es"><head>
    <meta charset="UTF-8"><title>Paso 1 · Tu negocio</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head><body class="login-body">
<div class="login-wrapper" style="max-width:640px;">
    <div class="login-card">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="color:#c9a96e;letter-spacing:3px;font-size:.8rem;">PASO 1 DE 3</div>
            <h1 style="font-family:'Playfair Display',serif;color:#4a6356;font-size:1.8rem;">Datos de tu negocio</h1>
        </div>
        <form method="post" action="<?= url('/setup/paso-1') ?>">
            <?= csrf_field() ?>
            <div class="form-group"><label>Nombre del negocio *</label><input type="text" name="spa_name" required placeholder="Spa Serenity"></div>
            <div class="form-group"><label>Eslogan</label><input type="text" name="spa_tagline" placeholder="Tu santuario de bienestar"></div>
            <div class="form-grid">
                <div class="form-group"><label>Teléfono</label><input type="text" name="spa_phone"></div>
                <div class="form-group"><label>Email</label><input type="email" name="spa_email"></div>
            </div>
            <div class="form-group"><label>Dirección</label><textarea name="spa_address" rows="2"></textarea></div>
            <div class="form-grid">
                <div class="form-group"><label>Color primario</label><input type="color" name="color_primary" value="#6b8a7a"></div>
                <div class="form-group"><label>Color acento</label><input type="color" name="color_accent" value="#c9a96e"></div>
            </div>
            <button type="submit" class="btn-admin btn-block">Continuar →</button>
        </form>
    </div>
</div></body></html>
