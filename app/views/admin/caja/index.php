<?php $pageTitle = 'Caja diaria'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div>
        <div class="breadcrumb">Cierre de caja del día</div>
    </div>
    <form method="get" style="display:flex;gap:8px;">
        <input type="date" name="date" value="<?= e($date) ?>" onchange="this.form.submit()">
    </form>
</div>

<div class="kpi-grid">
    <div class="kpi"><div class="kpi-label">Efectivo esperado</div><div class="kpi-value"><?= money($expectedCash) ?></div></div>
    <div class="kpi"><div class="kpi-label">Tarjeta</div><div class="kpi-value"><?= money($expectedCard) ?></div></div>
    <div class="kpi"><div class="kpi-label">Otros</div><div class="kpi-value"><?= money($expectedOther) ?></div></div>
    <div class="kpi"><div class="kpi-label">Total facturado</div><div class="kpi-value"><?= money($expectedCash + $expectedCard + $expectedOther) ?></div></div>
</div>

<div class="card-grid">
    <div>
        <div class="card">
            <h3>Facturas del día</h3>
            <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Número</th><th>Cliente</th><th>Método</th><th>Total</th></tr></thead>
                <tbody>
                <?php foreach ($invoices as $i): ?>
                    <tr>
                        <td><?= e($i['number']) ?></td>
                        <td><?= e($i['customer_name']) ?></td>
                        <td><?= e($i['payment_method']) ?></td>
                        <td><?= money($i['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($invoices)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--muted);">Sin facturas</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div>
        <form method="post" action="<?= url('/admin/caja/cierre') ?>" class="card">
            <?= csrf_field() ?>
            <h3>Cierre de caja</h3>
            <input type="hidden" name="date" value="<?= e($date) ?>">
            <input type="hidden" name="expected_cash" value="<?= e($expectedCash) ?>">
            <input type="hidden" name="expected_card" value="<?= e($expectedCard) ?>">
            <input type="hidden" name="expected_other" value="<?= e($expectedOther) ?>">

            <div class="form-group">
                <label>Esperado en efectivo (sistema)</label>
                <input type="text" readonly value="<?= money($expectedCash) ?>" style="background:var(--bg);">
            </div>
            <div class="form-group">
                <label>Efectivo contado (físico)</label>
                <input type="number" step="0.01" name="counted_cash" required value="<?= e($closing['counted_cash'] ?? $expectedCash) ?>">
            </div>
            <div class="form-group">
                <label>Notas / observaciones</label>
                <textarea name="notes" rows="3"><?= e($closing['notes'] ?? '') ?></textarea>
            </div>
            <?php if ($closing): ?>
                <div class="alert alert-info">Diferencia última: <strong><?= money($closing['difference']) ?></strong></div>
            <?php endif; ?>
            <button type="submit" class="btn-admin btn-block">Guardar cierre</button>
        </form>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
