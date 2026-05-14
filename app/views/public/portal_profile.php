<?php $pageTitle = 'Mi perfil'; require dirname(__DIR__) . '/layouts/public_header.php'; ?>
<section class="page-hero" style="padding:50px 24px;">
    <div class="container">
        <h1>Mi perfil</h1>
        <p><a href="<?= url('/mi-cuenta/inicio') ?>" style="color:white;">← Volver</a></p>
    </div>
</section>

<section>
    <div class="container">
        <form method="post" action="<?= url('/mi-cuenta/perfil') ?>" class="form-card form-card-wide">
            <?= csrf_field() ?>
            <h3>Mis datos</h3>
            <div class="form-row">
                <div class="form-group"><label>Nombre *</label><input type="text" name="first_name" required value="<?= e($customer['first_name']) ?>"></div>
                <div class="form-group"><label>Apellidos</label><input type="text" name="last_name" value="<?= e($customer['last_name']) ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Teléfono</label><input type="text" name="phone" value="<?= e($customer['phone']) ?>"></div>
                <div class="form-group"><label>Fecha de nacimiento</label><input type="date" name="birthdate" value="<?= e($customer['birthdate']) ?>"></div>
            </div>
            <div class="form-group">
                <label>Dirección</label>
                <input type="text" name="address" value="<?= e($customer['address']) ?>">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="accepts_marketing" <?= $customer['accepts_marketing'] ? 'checked' : '' ?>> Quiero recibir ofertas y novedades</label>
            </div>
            <button type="submit" class="btn">Guardar cambios</button>
        </form>
    </div>
</section>
<?php require dirname(__DIR__) . '/layouts/public_footer.php'; ?>
