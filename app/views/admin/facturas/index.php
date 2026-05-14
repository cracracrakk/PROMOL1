<?php $pageTitle = 'Facturación'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Documentos fiscales</div>
    <a href="<?= url('/admin/facturas/nueva') ?>" class="btn-admin">+ Nueva factura</a>
</div>

<div class="filters">
    <select onchange="window.location='?type=' + this.value">
        <option value="">Todos los documentos</option>
        <option value="factura" <?= $type === 'factura' ? 'selected' : '' ?>>Facturas</option>
        <option value="nota_credito" <?= $type === 'nota_credito' ? 'selected' : '' ?>>Notas de Crédito</option>
        <option value="nota_debito" <?= $type === 'nota_debito' ? 'selected' : '' ?>>Notas de Débito</option>
        <option value="recibo" <?= $type === 'recibo' ? 'selected' : '' ?>>Recibos</option>
    </select>
</div>

<div class="card">
    <div class="table-wrap">
    <table class="table">
        <thead><tr>
            <th>Número</th><th>Tipo</th><th>Fecha</th><th>Cliente</th><th>Total</th><th>Estado</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($invoices as $i): ?>
            <tr>
                <td><strong><?= e($i['number']) ?></strong><br><small style="color:var(--muted);">CAI: <?= e(substr($i['cai'] ?? '', 0, 8)) ?>...</small></td>
                <td><span class="badge badge-secondary"><?= e(str_replace('_',' ', $i['document_type'])) ?></span></td>
                <td><?= dt($i['issue_date'], 'd/m/Y') ?></td>
                <td><?= e($i['customer_name'] ?? trim(($i['first_name'] ?? '').' '.($i['last_name'] ?? '')) ?: 'Consumidor final') ?>
                    <?php if ($i['customer_rtn']): ?><br><small><?= e($i['customer_rtn']) ?></small><?php endif; ?>
                </td>
                <td><strong><?= money($i['total']) ?></strong></td>
                <td><span class="badge badge-<?= $i['status'] === 'pagada' ? 'success' : ($i['status'] === 'anulada' ? 'danger' : 'secondary') ?>"><?= e($i['status']) ?></span></td>
                <td><a href="<?= url('/admin/facturas/'.$i['id']) ?>" class="btn-admin btn-sm btn-outline">Ver</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($invoices)): ?>
            <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted);">Sin documentos. <a href="<?= url('/admin/facturas/nueva') ?>">Crear primera factura</a></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
