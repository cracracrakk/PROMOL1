<?php
$isEdit = isset($product);
$pageTitle = $isEdit ? 'Editar producto' : 'Nuevo producto';
require dirname(__DIR__, 2) . '/layouts/admin_header.php';
$p = $product ?? [];
$g = fn($k, $d = '') => e($p[$k] ?? $d);
?>

<div class="page-header"><div class="breadcrumb"><a href="<?= url('/admin/inventario') ?>">← Volver</a></div></div>

<div class="card-grid">
    <form method="post" action="<?= $isEdit ? url('/admin/inventario/'.$product['id'].'/editar') : url('/admin/inventario/nuevo') ?>" class="card">
        <?= csrf_field() ?>

        <div class="form-section">
            <h3>Información</h3>
            <div class="form-grid">
                <div class="form-group"><label>SKU</label><input type="text" name="sku" value="<?= $g('sku') ?>" placeholder="Auto si vacío"></div>
                <div class="form-group"><label>Código de barras</label><input type="text" name="barcode" value="<?= $g('barcode') ?>"></div>
                <div class="form-group" style="grid-column:1/-1;"><label>Nombre *</label><input type="text" name="name" required value="<?= $g('name') ?>"></div>
                <div class="form-group" style="grid-column:1/-1;"><label>Descripción</label><textarea name="description" rows="2"><?= $g('description') ?></textarea></div>
                <div class="form-group">
                    <label>Categoría</label>
                    <select name="category_id">
                        <option value="">—</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int)$c['id'] ?>" <?= ($p['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Proveedor</label>
                    <select name="supplier_id">
                        <option value="">—</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= (int)$s['id'] ?>" <?= ($p['supplier_id'] ?? '') == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Precios e impuestos</h3>
            <div class="form-grid">
                <div class="form-group"><label>Precio coste *</label><input type="number" step="0.01" name="cost_price" required value="<?= $g('cost_price') ?>"></div>
                <div class="form-group"><label>Precio venta *</label><input type="number" step="0.01" name="sale_price" required value="<?= $g('sale_price') ?>"></div>
                <div class="form-group">
                    <label>ISV %</label>
                    <select name="tax_rate">
                        <option value="15" <?= ($p['tax_rate'] ?? 15) == 15 ? 'selected' : '' ?>>15%</option>
                        <option value="18" <?= ($p['tax_rate'] ?? 15) == 18 ? 'selected' : '' ?>>18%</option>
                        <option value="0"  <?= ($p['tax_rate'] ?? 15) == 0  ? 'selected' : '' ?>>Exento</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Stock</h3>
            <div class="form-grid">
                <?php if (!$isEdit): ?>
                <div class="form-group"><label>Stock inicial</label><input type="number" name="stock" value="0"></div>
                <?php endif; ?>
                <div class="form-group"><label>Stock mínimo *</label><input type="number" name="stock_min" required value="<?= $g('stock_min', 5) ?>"></div>
                <div class="form-group"><label>Unidad</label><input type="text" name="unit" value="<?= $g('unit', 'ud') ?>"></div>
            </div>
        </div>

        <div class="form-section">
            <h3>Configuración</h3>
            <div class="form-grid">
                <div class="form-group"><label><input type="checkbox" name="active" <?= ($p['active'] ?? 1) ? 'checked' : '' ?>> Activo</label></div>
                <div class="form-group"><label><input type="checkbox" name="sellable" <?= ($p['sellable'] ?? 1) ? 'checked' : '' ?>> Vendible</label></div>
                <div class="form-group"><label><input type="checkbox" name="is_consumable" <?= !empty($p['is_consumable']) ? 'checked' : '' ?>> Consumible interno</label></div>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= url('/admin/inventario') ?>" class="btn-admin btn-outline">Cancelar</a>
            <button type="submit" class="btn-admin"><?= $isEdit ? 'Guardar' : 'Crear producto' ?></button>
        </div>
    </form>

    <?php if ($isEdit): ?>
    <div>
        <div class="card">
            <h3>Movimiento de stock</h3>
            <p style="color:var(--muted);font-size:.9rem;">Stock actual: <strong><?= (int)$product['stock'] ?> <?= e($product['unit']) ?></strong></p>
            <form method="post" action="<?= url('/admin/inventario/'.$product['id'].'/movimiento') ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Tipo</label>
                    <select name="type">
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                        <option value="consumo">Consumo</option>
                        <option value="ajuste">Ajuste (stock final)</option>
                    </select>
                </div>
                <div class="form-group"><label>Cantidad</label><input type="number" name="quantity" required min="1"></div>
                <div class="form-group"><label>Motivo</label><input type="text" name="reason"></div>
                <button type="submit" class="btn-admin btn-block">Registrar</button>
            </form>
        </div>

        <div class="card">
            <h3>Historial</h3>
            <?php foreach ($movements as $m): ?>
                <div style="padding:8px 0;border-bottom:1px solid var(--border);font-size:.88rem;">
                    <strong><?= e(ucfirst($m['type'])) ?></strong> · <?= (int)$m['quantity'] ?>
                    <small style="float:right;color:var(--muted);"><?= dt($m['created_at'], 'd/m H:i') ?></small>
                    <?php if ($m['reason']): ?><div style="color:var(--muted);"><?= e($m['reason']) ?></div><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
