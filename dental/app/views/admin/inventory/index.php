<?php /** @var array $products */ /** @var array $low_stock */ ?>
<div class="page-header">
    <div><h1>Inventario</h1>
        <p class="subtitle"><?= count($products) ?> productos · <?= count($low_stock) ?> con stock bajo</p></div>
    <a href="<?= url('/admin/inventario/nuevo') ?>" class="btn btn-primary">+ Nuevo producto</a>
</div>

<?php if ($low_stock): ?>
<div class="alert alert-error">
    <strong>⚠ Stock bajo en:</strong>
    <?php foreach (array_slice($low_stock, 0, 5) as $p): ?>
        <?= e($p['name']) ?> (<?= e($p['stock']) ?>/<?= e($p['min_stock']) ?>);
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card">
    <table>
        <thead><tr><th>SKU</th><th>Producto</th><th>Categoría</th><th>Stock</th><th>Mínimo</th><th>Costo</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr <?= $p['stock'] <= $p['min_stock'] ? 'style="background:#fef2f2;"' : '' ?>>
                    <td><code><?= e($p['sku']) ?></code></td>
                    <td><strong><?= e($p['name']) ?></strong>
                        <?php if (!$p['is_active']): ?> <span class="badge badge-muted">inactivo</span><?php endif; ?></td>
                    <td><?= e($p['category'] ?: '—') ?></td>
                    <td><?= number_format((float)$p['stock'], 2) ?> <?= e($p['unit']) ?></td>
                    <td><?= number_format((float)$p['min_stock'], 2) ?></td>
                    <td><?= money((float)$p['cost']) ?></td>
                    <td>
                        <form method="post" action="<?= url('/admin/inventario/'.$p['id'].'/movimiento') ?>" style="display:inline-flex;gap:4px;align-items:center;">
                            <?= csrf_field() ?>
                            <select name="type" style="padding:3px;font-size:0.78rem;">
                                <option value="in">+ Entrada</option>
                                <option value="out">- Salida</option>
                                <option value="adjust">= Ajuste</option>
                            </select>
                            <input type="number" name="quantity" step="0.01" placeholder="cant" style="width:70px;padding:3px;font-size:0.78rem;" required>
                            <button class="btn btn-sm btn-primary">OK</button>
                        </form>
                        <a href="<?= url('/admin/inventario/'.$p['id'].'/editar') ?>" class="btn btn-sm">Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
