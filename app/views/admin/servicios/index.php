<?php $pageTitle = 'Servicios'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Catálogo de servicios</div>
    <a href="<?= url('/admin/servicios/nuevo') ?>" class="btn-admin">+ Nuevo servicio</a>
</div>

<div class="card">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Servicio</th><th>Categoría</th><th>Duración</th><th>Precio</th><th>ISV</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($services as $s): ?>
            <tr>
                <td><strong><?= e($s['name']) ?></strong><br><small style="color:var(--muted);"><?= e(mb_substr($s['description'], 0, 80)) ?></small></td>
                <td><?= e($s['category_name'] ?? '—') ?></td>
                <td><?= (int)$s['duration_minutes'] ?> min</td>
                <td><strong><?= money($s['price']) ?></strong></td>
                <td><?= (float)$s['tax_rate'] ?>%</td>
                <td>
                    <?php if ($s['active']): ?>
                        <span class="badge badge-success">Activo</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Inactivo</span>
                    <?php endif; ?>
                    <?php if (!$s['bookable_online']): ?><span class="badge badge-secondary">Solo manual</span><?php endif; ?>
                </td>
                <td><a href="<?= url('/admin/servicios/'.$s['id'].'/editar') ?>" class="btn-admin btn-sm btn-outline">Editar</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
