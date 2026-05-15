<section style="max-width:560px;margin:5rem auto;padding:0 1.5rem;">
    <h1 style="margin:0 0 0.5rem;font-size:2rem;">Solicita tu demo</h1>
    <p class="text-muted mb-3">Déjanos tus datos y te contactaremos en menos de 24 horas para mostrarte DentalCore en vivo.</p>

    <?php if ($msg = flash('success')): ?>
        <div class="alert alert-success"><?= e($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-error"><?= e($msg) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="post" action="<?= url('/contacto') ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Nombre completo *</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Clínica / consultorio</label>
                    <input type="text" name="clinic">
                </div>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="tel" name="phone">
                    </div>
                </div>
                <div class="form-group">
                    <label>Cuéntanos un poco *</label>
                    <textarea name="message" rows="4" required placeholder="¿Cuántos odontólogos son? ¿Qué problemas quieres resolver?"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">Enviar solicitud</button>
            </form>
        </div>
    </div>
</section>
