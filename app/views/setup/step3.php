<!DOCTYPE html>
<html lang="es"><head>
    <meta charset="UTF-8"><title>Paso 3 · Administrador</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head><body class="login-body">
<div class="login-wrapper" style="max-width:640px;">
    <div class="login-card">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="color:#c9a96e;letter-spacing:3px;font-size:.8rem;">PASO 3 DE 3</div>
            <h1 style="font-family:'Playfair Display',serif;color:#4a6356;font-size:1.8rem;">Tu cuenta de administrador</h1>
            <p style="color:#7a8a82;font-size:.9rem;">Con estos datos accederás al panel.</p>
        </div>
        <?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>
        <form method="post" action="<?= url('/setup/paso-3') ?>">
            <?= csrf_field() ?>
            <div class="form-group"><label>Nombre completo *</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Email *</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Contraseña * (mínimo 6 caracteres)</label><input type="password" name="password" required minlength="6"></div>
            <button type="submit" class="btn-admin btn-block">Finalizar instalación ✓</button>
        </form>
    </div>
</div></body></html>
