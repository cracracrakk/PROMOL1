<?php $pageTitle = 'Auditoría'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Registro de actividad del sistema</div>
</div>

<div class="card">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Entidad</th><th>Descripción</th><th>IP</th></tr></thead>
        <tbody>
        <?php foreach ($logs as $l): ?>
            <tr>
                <td><?= dt($l['created_at']) ?></td>
                <td><?= e($l['user_name'] ?? '—') ?></td>
                <td><span class="badge badge-secondary"><?= e($l['action']) ?></span></td>
                <td><?= e($l['entity'] ?? '') ?><?= $l['entity_id'] ? ' #'.(int)$l['entity_id'] : '' ?></td>
                <td><?= e($l['description'] ?? '') ?></td>
                <td><small><?= e($l['ip']) ?></small></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
