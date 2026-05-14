<?php require dirname(__DIR__) . '/layouts/public_header.php'; ?>
<section class="page-hero" style="min-height:50vh;display:flex;align-items:center;">
    <div class="container">
        <h1 style="font-size:5rem;">404</h1>
        <p>La página que buscas no existe o se ha movido.</p>
        <a href="<?= url('/') ?>" class="btn" style="margin-top:30px;">Volver al inicio</a>
    </div>
</section>
<?php require dirname(__DIR__) . '/layouts/public_footer.php'; ?>
