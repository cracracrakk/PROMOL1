<?php $pageTitle = 'Clientes en riesgo'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Clientes que no vienen hace más de 60 días</div>
</div>

<div class="card">
    <p style="color:var(--muted);margin-bottom:20px;">Ordenados por valor de vida (LTV). Los de arriba son los más valiosos a recuperar.</p>
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Cliente</th><th>Última visita</th><th>Visitas</th><th>LTV</th><th>Contacto</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($customers as $c): $days = $c['last_visit'] ? (int)((time() - strtotime($c['last_visit'])) / 86400) : 0; ?>
            <tr>
                <td>
                    <strong><?= e(trim($c['first_name'].' '.$c['last_name'])) ?></strong>
                    <?php if ($c['vip']): ?><span class="badge badge-warning">VIP</span><?php endif; ?>
                </td>
                <td><?= dt($c['last_visit'], 'd/m/Y') ?> <small style="color:#dc3545;">(<?= $days ?> días)</small></td>
                <td><?= (int)$c['visits'] ?></td>
                <td><strong><?= money($c['lifetime_value']) ?></strong></td>
                <td><small><?= e($c['phone']) ?><br><?= e($c['email']) ?></small></td>
                <td><a href="<?= url('/admin/clientes/'.$c['id']) ?>" class="btn-admin btn-sm btn-outline">Ver</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
