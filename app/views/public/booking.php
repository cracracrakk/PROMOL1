<?php $pageTitle = 'Reservar cita'; require dirname(__DIR__) . '/layouts/public_header.php'; ?>

<section class="page-hero">
    <div class="container">
        <h1>Reserva tu cita</h1>
        <p>Elige tu tratamiento, fecha y hora. Te confirmaremos por email.</p>
    </div>
</section>

<section>
    <div class="container">
        <form method="post" action="<?= url('/reservar') ?>" class="form-card form-card-wide">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="first_name" required value="<?= old('first_name') ?>">
                </div>
                <div class="form-group">
                    <label>Apellidos</label>
                    <input type="text" name="last_name" value="<?= old('last_name') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required value="<?= old('email') ?>">
                </div>
                <div class="form-group">
                    <label>Teléfono *</label>
                    <input type="text" name="phone" required value="<?= old('phone') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Servicio *</label>
                <select name="service_id" required id="serviceSelect">
                    <option value="">— Selecciona un servicio —</option>
                    <?php foreach ($services as $s): ?>
                        <option value="<?= (int)$s['id'] ?>"
                                data-duration="<?= (int)$s['duration_minutes'] ?>"
                                <?= (input('service') == $s['id']) ? 'selected' : '' ?>>
                            <?= e($s['name']) ?> · <?= (int)$s['duration_minutes'] ?> min · <?= money($s['price']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Fecha *</label>
                    <input type="date" name="date" required min="<?= date('Y-m-d') ?>" value="<?= old('date') ?>">
                </div>
                <div class="form-group">
                    <label>Hora *</label>
                    <input type="time" name="time" required value="<?= old('time') ?>" step="900">
                </div>
            </div>

            <div class="form-group">
                <label>Comentarios (alergias, preferencias...)</label>
                <textarea name="notes" rows="3"><?= old('notes') ?></textarea>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:10px;font-weight:normal;">
                    <input type="checkbox" name="gdpr" required>
                    Acepto la política de privacidad y el tratamiento de mis datos.
                </label>
            </div>

            <button type="submit" class="btn">Solicitar cita</button>
            <p style="margin-top:15px;font-size:.9rem;color:var(--muted);">
                * Tu solicitud quedará pendiente de confirmación. Te avisaremos por email/WhatsApp.
            </p>
        </form>
    </div>
</section>

<?php require dirname(__DIR__) . '/layouts/public_footer.php'; ?>
