<?php $pageTitle = 'Reportes'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Informes y libro de ventas SAR</div>
    <a href="<?= url('/admin/reportes/avanzados') ?>" class="btn-admin">📊 Reportes avanzados</a>
</div>

<form method="get" class="filters">
    <div class="form-group" style="margin:0;"><label style="display:block;font-size:.78rem;margin-bottom:4px;">Desde</label><input type="date" name="from" value="<?= e($from) ?>"></div>
    <div class="form-group" style="margin:0;"><label style="display:block;font-size:.78rem;margin-bottom:4px;">Hasta</label><input type="date" name="to" value="<?= e($to) ?>"></div>
    <div style="display:flex;gap:8px;align-items:flex-end;">
        <button type="submit" class="btn-admin">Filtrar</button>
        <a href="<?= url('/admin/reportes/sar?from='.$from.'&to='.$to) ?>" class="btn-admin btn-accent">📥 Exportar SAR (CSV)</a>
    </div>
</form>

<div class="kpi-grid">
    <div class="kpi"><div class="kpi-label">Documentos</div><div class="kpi-value"><?= (int)$summary['num'] ?></div></div>
    <div class="kpi"><div class="kpi-label">Total facturado</div><div class="kpi-value"><?= money($summary['total']) ?></div></div>
    <div class="kpi"><div class="kpi-label">ISV recaudado</div><div class="kpi-value"><?= money(($summary['isv15'] ?? 0) + ($summary['isv18'] ?? 0)) ?></div></div>
    <div class="kpi"><div class="kpi-label">Descuentos</div><div class="kpi-value"><?= money($summary['disc'] ?? 0) ?></div></div>
</div>

<div class="card-grid">
    <div class="card">
        <h3>Servicios más vendidos</h3>
        <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Servicio</th><th>Cant.</th><th>Total</th></tr></thead>
            <tbody>
            <?php foreach ($byService as $s): ?>
                <tr><td><?= e($s['name']) ?></td><td><?= (int)$s['cant'] ?></td><td><strong><?= money($s['total']) ?></strong></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>

    <div class="card">
        <h3>Por terapeuta</h3>
        <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Terapeuta</th><th>Sesiones</th><th>Total</th></tr></thead>
            <tbody>
            <?php foreach ($byTherapist as $t): ?>
                <tr><td><?= e($t['name']) ?></td><td><?= (int)$t['cant'] ?></td><td><strong><?= money($t['total']) ?></strong></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<div class="card">
    <h3>📋 Libro de ventas SAR</h3>
    <p class="form-help">Detalle de todos los documentos emitidos en el periodo. Exportable a CSV/Excel para presentar a SAR.</p>
    <div class="table-wrap">
    <table class="table" style="font-size:.85rem;">
        <thead><tr>
            <th>Fecha</th><th>Número</th><th>RTN</th><th>Cliente</th><th>Exento</th><th>Grav. 15%</th><th>Grav. 18%</th><th>ISV 15%</th><th>ISV 18%</th><th>Total</th><th>Estado</th>
        </tr></thead>
        <tbody>
        <?php foreach ($invoices as $i): ?>
            <tr style="<?= $i['status'] === 'anulada' ? 'background:rgba(220,53,69,.05);' : '' ?>">
                <td><?= dt($i['issue_date'], 'd/m/Y') ?></td>
                <td><?= e($i['number']) ?></td>
                <td><small><?= e($i['customer_rtn'] ?? '—') ?></small></td>
                <td><?= e($i['customer_name']) ?></td>
                <td><?= money($i['importe_exento']) ?></td>
                <td><?= money($i['importe_gravado_15']) ?></td>
                <td><?= money($i['importe_gravado_18']) ?></td>
                <td><?= money($i['isv_15']) ?></td>
                <td><?= money($i['isv_18']) ?></td>
                <td><strong><?= money($i['total']) ?></strong></td>
                <td><span class="badge badge-<?= $i['status'] === 'pagada' ? 'success' : ($i['status'] === 'anulada' ? 'danger' : 'secondary') ?>"><?= e($i['status']) ?></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
