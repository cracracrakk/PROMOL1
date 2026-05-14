<?php
$isEdit = isset($customer);
$pageTitle = $isEdit ? 'Editar cliente' : 'Nuevo cliente';
require dirname(__DIR__, 2) . '/layouts/admin_header.php';
$c = $customer ?? [];
$g = fn($k, $d = '') => e($c[$k] ?? $d);
?>

<div class="page-header">
    <div class="breadcrumb"><a href="<?= url('/admin/clientes') ?>">← Volver</a></div>
</div>

<form method="post" action="<?= $isEdit ? url('/admin/clientes/'.$customer['id'].'/editar') : url('/admin/clientes/nuevo') ?>" class="card">
    <?= csrf_field() ?>

    <div class="form-section">
        <h3>Datos personales</h3>
        <div class="form-grid">
            <div class="form-group"><label>Nombre *</label><input type="text" name="first_name" required value="<?= $g('first_name') ?>"></div>
            <div class="form-group"><label>Apellidos</label><input type="text" name="last_name" value="<?= $g('last_name') ?>"></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= $g('email') ?>"></div>
            <div class="form-group"><label>Teléfono</label><input type="text" name="phone" value="<?= $g('phone') ?>"></div>
            <div class="form-group"><label>Fecha nacimiento</label><input type="date" name="birthdate" value="<?= $g('birthdate') ?>"></div>
            <div class="form-group">
                <label>Género</label>
                <select name="gender">
                    <option value="">—</option>
                    <option value="F" <?= ($c['gender'] ?? '') === 'F' ? 'selected' : '' ?>>Femenino</option>
                    <option value="M" <?= ($c['gender'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                    <option value="otro" <?= ($c['gender'] ?? '') === 'otro' ? 'selected' : '' ?>>Otro</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Datos fiscales (para facturación)</h3>
        <div class="form-grid">
            <div class="form-group"><label>RTN / Documento</label><input type="text" name="tax_id" value="<?= $g('tax_id') ?>" placeholder="08019999999999"></div>
            <div class="form-group"><label><input type="checkbox" name="is_company" <?= !empty($c['is_company']) ? 'checked' : '' ?>> Es empresa</label></div>
            <div class="form-group"><label>Dirección</label><input type="text" name="address" value="<?= $g('address') ?>"></div>
            <div class="form-group"><label>Ciudad</label><input type="text" name="city" value="<?= $g('city') ?>"></div>
            <div class="form-group"><label>Código postal</label><input type="text" name="postal_code" value="<?= $g('postal_code') ?>"></div>
        </div>
    </div>

    <div class="form-section">
        <h3>Ficha clínica</h3>
        <div class="form-grid">
            <div class="form-group" style="grid-column:1/-1;"><label>Alergias</label><textarea name="allergies" rows="2"><?= $g('allergies') ?></textarea></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Condiciones médicas</label><textarea name="medical_conditions" rows="2"><?= $g('medical_conditions') ?></textarea></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Medicación actual</label><textarea name="medications" rows="2"><?= $g('medications') ?></textarea></div>
            <div class="form-group"><label><input type="checkbox" name="pregnant" <?= !empty($c['pregnant']) ? 'checked' : '' ?>> Embarazo</label></div>
            <div class="form-group"><label>Semanas</label><input type="number" name="pregnancy_weeks" value="<?= $g('pregnancy_weeks') ?>" min="0" max="42"></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Preferencias y observaciones</label><textarea name="preferences" rows="2"><?= $g('preferences') ?></textarea></div>
        </div>
    </div>

    <div class="form-section">
        <h3>Marketing y notas</h3>
        <div class="form-grid">
            <div class="form-group"><label><input type="checkbox" name="vip" <?= !empty($c['vip']) ? 'checked' : '' ?>> Cliente VIP</label></div>
            <div class="form-group"><label><input type="checkbox" name="accepts_marketing" <?= ($c['accepts_marketing'] ?? 1) ? 'checked' : '' ?>> Acepta marketing</label></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Notas internas</label><textarea name="notes" rows="3"><?= $g('notes') ?></textarea></div>
        </div>
    </div>

    <div class="form-actions">
        <a href="<?= url('/admin/clientes') ?>" class="btn-admin btn-outline">Cancelar</a>
        <button type="submit" class="btn-admin"><?= $isEdit ? 'Guardar cambios' : 'Crear cliente' ?></button>
    </div>
</form>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
