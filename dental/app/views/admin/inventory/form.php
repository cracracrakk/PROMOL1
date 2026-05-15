<?php /** @var ?array $product */ /** @var array $movements */
$p = $product ?? [];
$isNew = empty($p['id']);
$action = $isNew ? url('/admin/inventario/nuevo') : url('/admin/inventario/'.$p['id'].'/editar');
?>
<div class="page-header">
    <h1><?= $isNew ? 'Nuevo producto' : 'Editar producto' ?></h1>
    <a href="<?= url('/admin/inventario') ?>" class="btn">← Volver</a>
</div>

<form method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="grid grid-3">
                <div class="form-group"><label>SKU</label>
                    <input type="text" name="sku" value="<?= e($p['sku'] ?? '') ?>"></div>
                <div class="form-group" style="grid-column:span 2;"><label>Nombre *</label>
                    <input type="text" name="name" required value="<?= e($p['name'] ?? '') ?>"></div>
                <div class="form-group"><label>Categoría</label>
                    <input type="text" name="category" value="<?= e($p['category'] ?? '') ?>" list="cat-list">
                    <datalist id="cat-list">
                        <option>Bioseguridad</option><option>Anestesia</option>
                        <option>Restauración</option><option>Cirugía</option><option>Endodoncia</option>
                    </datalist></div>
                <div class="form-group"><label>Unidad</label>
                    <input type="text" name="unit" value="<?= e($p['unit'] ?? 'unidad') ?>"></div>
                <?php if ($isNew): ?>
                <div class="form-group"><label>Stock inicial</label>
                    <input type="number" step="0.01" name="stock" value="0"></div>
                <?php endif; ?>
                <div class="form-group"><label>Stock mínimo</label>
                    <input type="number" step="0.01" name="min_stock" value="<?= e($p['min_stock'] ?? 0) ?>"></div>
                <div class="form-group"><label>Costo unitario</label>
                    <input type="number" step="0.01" name="cost" value="<?= e($p['cost'] ?? 0) ?>"></div>
                <div class="form-group"><label>Proveedor</label>
                    <input type="text" name="supplier" value="<?= e($p['supplier'] ?? '') ?>"></div>
            </div>
            <div class="form-group">
                <label>Notas</label>
                <textarea name="notes" rows="2"><?= e($p['notes'] ?? '') ?></textarea>
            </div>
            <label class="checkbox">
                <input type="checkbox" name="is_active" value="1" <?= !isset($p['is_active']) || $p['is_active'] ? 'checked' : '' ?>>
                Activo
            </label>
        </div>
    </div>
    <button class="btn btn-primary">Guardar</button>
</form>

<?php if ($movements): ?>
<div class="card mt-3">
    <div class="card-header"><h2>Movimientos recientes</h2></div>
    <table>
        <thead><tr><th>Fecha</th><th>Tipo</th><th>Cantidad</th><th>Motivo</th><th>Usuario</th></tr></thead>
        <tbody>
            <?php foreach ($movements as $m): ?>
                <tr>
                    <td><?= e(format_date($m['movement_date'], 'd/m/Y H:i')) ?></td>
                    <td><span class="badge badge-info"><?= e($m['type']) ?></span></td>
                    <td><?= number_format((float)$m['quantity'], 2) ?></td>
                    <td><?= e($m['reason'] ?: '—') ?></td>
                    <td><?= e($m['user_name'] ?: '—') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
