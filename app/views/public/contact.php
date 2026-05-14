<?php $pageTitle = 'Contacto'; require dirname(__DIR__) . '/layouts/public_header.php'; ?>

<section class="page-hero">
    <div class="container">
        <h1>Contacto</h1>
        <p>¿Tienes alguna pregunta? Escríbenos, te responderemos lo antes posible.</p>
    </div>
</section>

<section>
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <h3>Visítanos</h3>
                <p>📍 <?= nl2br(e(setting('spa_address', ''))) ?></p>
                <p>📞 <?= e(setting('spa_phone', '')) ?></p>
                <p>📧 <?= e(setting('spa_email', '')) ?></p>
                <h3 style="margin-top:30px;">Horario</h3>
                <p><?= nl2br(e(setting('spa_hours', ''))) ?></p>
            </div>
            <form method="post" action="<?= url('/contacto') ?>" class="form-card">
                <?= csrf_field() ?>
                <h3>Envíanos un mensaje</h3>
                <div class="form-group">
                    <label>Nombre completo *</label>
                    <input type="text" name="name" required value="<?= old('name') ?>">
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required value="<?= old('email') ?>">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="phone" value="<?= old('phone') ?>">
                </div>
                <div class="form-group">
                    <label>Mensaje *</label>
                    <textarea name="message" rows="5" required><?= old('message') ?></textarea>
                </div>
                <button type="submit" class="btn">Enviar mensaje</button>
            </form>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/layouts/public_footer.php'; ?>
