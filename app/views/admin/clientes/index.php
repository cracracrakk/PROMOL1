<?php $pageTitle = 'Clientes'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div><div class="breadcrumb">Base de clientes</div></div>
    <a href="<?= url('/admin/clientes/nuevo') ?>" class="btn-admin">+ Nuevo cliente</a>
</div>

<form method="get" class="filters">
    <input type="text" name="q" class="search-bar" placeholder="Buscar nombre, email, teléfono, RTN..." value="<?= e($q ?? '') ?>" data-search>
    <button type="submit" class="btn-admin btn-sm">Buscar</button>
</form>

<div class="card">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Cliente</th><th>Contacto</th><th>RTN</th><th>Marca</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($customers as $c): ?>
            <tr>
                <td>
                    <strong><?= e(trim($c['first_name'].' '.$c['last_name'])) ?></strong>
                    <?php if ($c['vip']): ?> <span class="badge badge-warning">VIP</span><?php endif; ?>
                    <?php if (!empty($c['allergies'])): ?> <span class="badge badge-danger">Alergias</span><?php endif; ?>
                    <?php if ($c['pregnant']): ?> <span class="badge badge-info">Embarazo</span><?php endif; ?>
                </td>
                <td><?= e($c['phone']) ?><br><small style="color:var(--muted);"><?= e($c['email']) ?></small></td>
                <td><?= e($c['tax_id'] ?? '—') ?></td>
                <td><small><?= (int)$c['loyalty_points'] ?> pts</small></td>
                <td>
                    <a href="<?= url('/admin/clientes/' . $c['id']) ?>" class="btn-admin btn-sm btn-outline">Ver</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($customers)): ?>
            <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--muted);">Sin clientes registrados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
