<?php /** @var ?array $treatment */
$t = $treatment ?? [];
$isNew = empty($t['id']);
$action = $isNew ? url('/admin/tratamientos/nuevo') : url('/admin/tratamientos/'.$t['id'].'/editar');
?>
<div class="page-header">
    <h1><?= $isNew ? 'Nuevo tratamiento' : 'Editar tratamiento' ?></h1>
    <a href="<?= url('/admin/tratamientos') ?>" class="btn">← Volver</a>
</div>

<form method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="grid grid-2">
                <div class="form-group"><label>Código *</label>
                    <input type="text" name="code" required value="<?= e($t['code'] ?? '') ?>"></div>
                <div class="form-group"><label>Nombre *</label>
                    <input type="text" name="name" required value="<?= e($t['name'] ?? '') ?>"></div>
                <div class="form-group"><label>Categoría</label>
                    <input type="text" name="category" value="<?= e($t['category'] ?? '') ?>"
                           list="categories">
                    <datalist id="categories">
                        <option>Preventiva</option><option>Restauradora</option>
                        <option>Endodoncia</option><option>Cirugía</option>
                        <option>Prótesis</option><option>Ortodoncia</option>
                        <option>Estética</option><option>Diagnóstico</option>
                    </datalist></div>
                <div class="form-group"><label>Duración (min)</label>
                    <input type="number" name="duration_min" value="<?= e($t['duration_min'] ?? 30) ?>"></div>
                <div class="form-group"><label>Precio</label>
                    <input type="number" step="0.01" name="default_price" value="<?= e($t['default_price'] ?? 0) ?>"></div>
                <div class="form-group">
                    <label class="checkbox" style="margin-top:1.6rem;">
                        <input type="checkbox" name="requires_tooth" value="1" <?= !empty($t['requires_tooth'])?'checked':'' ?>>
                        Requiere especificar pieza dental
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="description" rows="3"><?= e($t['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="checkbox">
                    <input type="checkbox" name="is_active" value="1" <?= !isset($t['is_active']) || $t['is_active'] ? 'checked' : '' ?>>
                    Activo
                </label>
            </div>
        </div>
    </div>
    <div class="flex justify-between mt-3">
        <a href="<?= url('/admin/tratamientos') ?>" class="btn">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</form>
