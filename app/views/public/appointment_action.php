<?php $pageTitle = 'Cita'; require dirname(__DIR__) . '/layouts/public_header.php'; ?>
<section class="page-hero">
    <div class="container">
        <?php if ($success): ?>
            <div style="font-size:5rem;">✅</div>
            <h1>¡Listo!</h1>
            <p><?= e($message) ?></p>
        <?php else: ?>
            <div style="font-size:5rem;">⚠️</div>
            <h1>Hubo un problema</h1>
            <p><?= e($message) ?></p>
        <?php endif; ?>
        <a href="<?= url('/') ?>" class="btn" style="margin-top:30px;">Volver al inicio</a>
    </div>
</section>
<?php require dirname(__DIR__) . '/layouts/public_footer.php'; ?>
