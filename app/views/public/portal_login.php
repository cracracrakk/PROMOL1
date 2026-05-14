<?php $pageTitle = 'Mi cuenta'; require dirname(__DIR__) . '/layouts/public_header.php'; ?>
<section class="page-hero">
    <div class="container">
        <h1>Mi cuenta</h1>
        <p>Accede a tus citas, bonos y datos personales.</p>
    </div>
</section>
<section>
    <div class="container">
        <form method="post" action="<?= url('/mi-cuenta/acceso') ?>" class="form-card">
            <?= csrf_field() ?>
            <h3>Recibir enlace de acceso</h3>
            <p style="color:var(--muted);margin-bottom:20px;">Te enviaremos un enlace seguro a tu email. Sin contraseñas que recordar.</p>
            <div class="form-group">
                <label>Tu email *</label>
                <input type="email" name="email" required autofocus>
            </div>
            <button type="submit" class="btn btn-block">📧 Enviarme el enlace</button>
        </form>
    </div>
</section>
<?php require dirname(__DIR__) . '/layouts/public_footer.php'; ?>
