<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($_title) ?> — DentalCore</title>
<link rel="stylesheet" href="<?= url('/assets/css/app.css') ?>">
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.5rem;">
            <span class="brand-mark" style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.2rem;">D</span>
            <strong style="font-size:1.2rem;">DentalCore</strong>
        </div>
        <h1>Bienvenido</h1>
        <p class="muted">Ingresa con tus credenciales para continuar.</p>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error"><?= e($msg) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= url('/login') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required autofocus>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Iniciar sesión</button>
        </form>

        <p class="text-muted text-center mt-3" style="font-size:0.8rem;">
            <a href="<?= url('/') ?>">← Volver al inicio</a>
        </p>
    </div>
</div>
</body>
</html>
