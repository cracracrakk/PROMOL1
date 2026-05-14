<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso · <?= e(brand_name()) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <style>
        :root { --brand: <?= e(setting('color_primary', '#6b8a7a')) ?>; --accent: <?= e(setting('color_accent', '#c9a96e')) ?>; }
    </style>
</head>
<body class="login-body">
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-brand">
                <?php if (brand_logo()): ?>
                    <img src="<?= brand_logo() ?>" alt="<?= e(brand_name()) ?>" style="height:60px;">
                <?php else: ?>
                    <h1 class="login-logo"><?= e(brand_name()) ?></h1>
                <?php endif; ?>
                <p>Panel de administración</p>
            </div>

            <?php if ($msg = flash('error')): ?>
                <div class="alert alert-error"><?= e($msg) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= url('/login') ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required autofocus value="<?= old('email') ?>">
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-admin btn-block">Iniciar sesión</button>
            </form>

            <p class="login-footer">
                <a href="<?= url('/') ?>">&larr; Volver al sitio web</a>
            </p>
        </div>
    </div>
</body>
</html>
