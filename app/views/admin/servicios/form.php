<?php
$isEdit = isset($service);
$pageTitle = $isEdit ? 'Editar servicio' : 'Nuevo servicio';
require dirname(__DIR__, 2) . '/layouts/admin_header.php';
$s = $service ?? [];
$g = fn($k, $d = '') => e($s[$k] ?? $d);
?>

<div class="page-header"><div class="breadcrumb"><a href="<?= url('/admin/servicios') ?>">← Volver</a></div></div>

<form method="post" action="<?= $isEdit ? url('/admin/servicios/'.$service['id'].'/editar') : url('/admin/servicios/nuevo') ?>" class="card">
    <?= csrf_field() ?>

    <div class="form-section">
        <h3>Información básica</h3>
        <div class="form-grid">
            <div class="form-group" style="grid-column:1/-1;"><label>Nombre *</label><input type="text" name="name" required value="<?= $g('name') ?>"></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Descripción</label><textarea name="description" rows="3"><?= $g('description') ?></textarea></div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="category_id">
                    <option value="">—</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= ($s['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Tipo de cabina requerida</label><input type="text" name="requires_room_type" value="<?= $g('requires_room_type') ?>" placeholder="masaje, facial, húmeda..."></div>
        </div>
    </div>

    <div class="form-section">
        <h3>Tiempos</h3>
        <div class="form-grid">
            <div class="form-group"><label>Duración (min) *</label><input type="number" name="duration_minutes" required value="<?= $g('duration_minutes', 60) ?>"></div>
            <div class="form-group"><label>Margen limpieza (min)</label><input type="number" name="buffer_minutes" value="<?= $g('buffer_minutes', 15) ?>"></div>
        </div>
    </div>

    <div class="form-section">
        <h3>Precio e impuestos</h3>
        <div class="form-grid">
            <div class="form-group"><label>Precio venta *</label><input type="number" step="0.01" name="price" required value="<?= $g('price') ?>"></div>
            <div class="form-group"><label>Coste interno</label><input type="number" step="0.01" name="cost" value="<?= $g('cost') ?>"></div>
            <div class="form-group">
                <label>ISV %</label>
                <select name="tax_rate">
                    <option value="15" <?= ($s['tax_rate'] ?? 15) == 15 ? 'selected' : '' ?>>15% (estándar)</option>
                    <option value="18" <?= ($s['tax_rate'] ?? 15) == 18 ? 'selected' : '' ?>>18% (turismo/alcohol)</option>
                    <option value="0"  <?= ($s['tax_rate'] ?? 15) == 0  ? 'selected' : '' ?>>Exento</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Disponibilidad</h3>
        <div class="form-grid">
            <div class="form-group"><label><input type="checkbox" name="active" <?= ($s['active'] ?? 1) ? 'checked' : '' ?>> Servicio activo</label></div>
            <div class="form-group"><label><input type="checkbox" name="bookable_online" <?= ($s['bookable_online'] ?? 1) ? 'checked' : '' ?>> Reservable desde web</label></div>
            <div class="form-group"><label>Orden de aparición</label><input type="number" name="sort_order" value="<?= $g('sort_order', 0) ?>"></div>
        </div>
    </div>

    <div class="form-actions">
        <a href="<?= url('/admin/servicios') ?>" class="btn-admin btn-outline">Cancelar</a>
        <button type="submit" class="btn-admin"><?= $isEdit ? 'Guardar' : 'Crear servicio' ?></button>
    </div>
</form>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
