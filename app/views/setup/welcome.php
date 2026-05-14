<!DOCTYPE html>
<html lang="es"><head>
    <meta charset="UTF-8"><title>Bienvenida · Instalación</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head><body class="login-body">
<div class="login-wrapper" style="max-width:560px;">
    <div class="login-card" style="text-align:center;padding:60px 40px;">
        <div style="font-size:5rem;margin-bottom:20px;">🌿</div>
        <h1 style="font-family:'Playfair Display',serif;font-size:2.4rem;color:#4a6356;margin-bottom:12px;">¡Bienvenido!</h1>
        <p style="color:#7a8a82;margin-bottom:30px;">Vamos a configurar tu sistema en 3 sencillos pasos.</p>
        <div style="display:flex;justify-content:space-around;margin:30px 0;color:#7a8a82;font-size:.9rem;">
            <div>📝<br>Tu negocio</div>
            <div>🇭🇳<br>Datos SAR</div>
            <div>👤<br>Admin</div>
        </div>
        <a href="<?= url('/setup/paso-1') ?>" class="btn-admin" style="display:inline-block;padding:14px 36px;font-size:1.05rem;">Comenzar →</a>
    </div>
</div></body></html>
