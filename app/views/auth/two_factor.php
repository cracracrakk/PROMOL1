<!DOCTYPE html>
<html lang="es"><head>
    <meta charset="UTF-8"><title>2FA · <?= e(brand_name()) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head><body class="login-body">
<div class="login-wrapper"><div class="login-card">
    <div class="login-brand">
        <h1 class="login-logo">🔒 Verificación</h1>
        <p>Introduce el código de tu app autenticadora</p>
    </div>
    <?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>
    <form method="post" action="<?= url('/login/2fa') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <input type="text" name="code" required autofocus inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
                   placeholder="000000" style="font-size:1.8rem;text-align:center;letter-spacing:8px;font-family:monospace;">
        </div>
        <button type="submit" class="btn-admin btn-block">Verificar</button>
    </form>
    <p class="login-footer"><a href="<?= url('/logout') ?>">← Cancelar</a></p>
</div></div></body></html>
