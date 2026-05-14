<?php $pageTitle = 'Inventario'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Productos y stock</div>
    <a href="<?= url('/admin/inventario/nuevo') ?>" class="btn-admin">+ Nuevo producto</a>
</div>

<form method="get" class="filters">
    <input type="text" name="q" class="search-bar" placeholder="Buscar producto, SKU o código de barras..." value="<?= e($q ?? '') ?>" data-search>
    <button type="submit" class="btn-admin btn-sm">Buscar</button>
</form>

<div class="card">
    <div class="table-wrap">
    <table class="table">
        <thead><tr>
            <th>Producto</th><th>SKU</th><th>Categoría</th><th>Coste</th><th>Venta</th><th>Stock</th><th>Estado</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr <?= $p['stock'] <= $p['stock_min'] ? 'style="background:rgba(220,53,69,.05)"' : '' ?>>
                <td>
                    <strong><?= e($p['name']) ?></strong>
                    <?php if ($p['is_consumable']): ?><span class="badge badge-info">Consumible</span><?php endif; ?>
                </td>
                <td><code><?= e($p['sku']) ?></code></td>
                <td><?= e($p['category_name'] ?? '—') ?></td>
                <td><?= money($p['cost_price']) ?></td>
                <td><strong><?= money($p['sale_price']) ?></strong></td>
                <td>
                    <?php if ($p['stock'] <= $p['stock_min']): ?>
                        <span class="badge badge-danger"><?= (int)$p['stock'] ?> <?= e($p['unit']) ?></span>
                    <?php else: ?>
                        <span class="badge badge-success"><?= (int)$p['stock'] ?> <?= e($p['unit']) ?></span>
                    <?php endif; ?>
                </td>
                <td><?= $p['active'] ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-secondary">Inactivo</span>' ?></td>
                <td><a href="<?= url('/admin/inventario/'.$p['id'].'/editar') ?>" class="btn-admin btn-sm btn-outline">Gestionar</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
