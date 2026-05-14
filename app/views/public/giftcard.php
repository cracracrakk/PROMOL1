<?php $pageTitle = 'Tarjeta Regalo'; require dirname(__DIR__) . '/layouts/public_header.php'; ?>

<section class="page-hero">
    <div class="container">
        <h1>Regala bienestar</h1>
        <p>Una experiencia inolvidable para alguien especial.</p>
    </div>
</section>

<section>
    <div class="container">
        <form method="post" action="<?= url('/regalo') ?>" class="form-card form-card-wide">
            <?= csrf_field() ?>
            <h3>Comprar Tarjeta Regalo</h3>

            <div class="form-group">
                <label>Importe</label>
                <div class="amount-options">
                    <?php foreach ([500, 1000, 2000, 3000, 5000] as $amount): ?>
                        <label class="amount-option">
                            <input type="radio" name="amount" value="<?= $amount ?>" <?= ($amount == 1000 ? 'checked' : '') ?>>
                            <span><?= money($amount) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tu nombre *</label>
                    <input type="text" name="buyer_name" required value="<?= old('buyer_name') ?>">
                </div>
                <div class="form-group">
                    <label>Tu email *</label>
                    <input type="email" name="buyer_email" required value="<?= old('buyer_email') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nombre del destinatario</label>
                    <input type="text" name="recipient_name" value="<?= old('recipient_name') ?>">
                </div>
                <div class="form-group">
                    <label>Email del destinatario</label>
                    <input type="email" name="recipient_email" value="<?= old('recipient_email') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Mensaje personal</label>
                <textarea name="message" rows="3" placeholder="¡Felicidades! Disfruta de esta experiencia..."><?= old('message') ?></textarea>
            </div>

            <button type="submit" class="btn btn-accent">Solicitar Tarjeta Regalo</button>
            <p style="margin-top:15px;font-size:.9rem;color:var(--muted);">
                Te contactaremos para coordinar el pago y la entrega.
            </p>
        </form>
    </div>
</section>

<?php require dirname(__DIR__) . '/layouts/public_footer.php'; ?>
