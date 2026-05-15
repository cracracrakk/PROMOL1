<?php /** @var array $filters */ /** @var array $result */ /** @var array $summary */ ?>
<div class="page-header">
    <div><h1>Facturas</h1>
        <p class="subtitle">Mes en curso: <?= money((float)$summary['billed']) ?> facturado · <?= money((float)$summary['pending']) ?> pendiente</p>
    </div>
    <a href="<?= url('/admin/facturas/nueva') ?>" class="btn btn-primary">+ Nueva factura</a>
</div>

<div class="grid grid-4 mb-3">
    <div class="stat"><div class="label">Facturas (mes)</div><div class="value"><?= (int)$summary['n'] ?></div></div>
    <div class="stat"><div class="label">Facturado</div><div class="value"><?= money((float)$summary['billed']) ?></div></div>
    <div class="stat"><div class="label">Cobrado</div><div class="value"><?= money((float)$summary['collected']) ?></div></div>
    <div class="stat"><div class="label">Pendiente</div><div class="value"><?= money((float)$summary['pending']) ?></div></div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="get" class="grid grid-4 items-center">
            <div class="form-group"><label>Estado</label>
                <select name="status">
                    <option value="">Todos</option>
                    <?php foreach (['draft','issued','partial','paid','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($filters['status'] ?? '')===$s?'selected':'' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="form-group"><label>Desde</label>
                <input type="date" name="from" value="<?= e($filters['from'] ?? '') ?>"></div>
            <div class="form-group"><label>Hasta</label>
                <input type="date" name="to" value="<?= e($filters['to'] ?? '') ?>"></div>
            <div class="form-group" style="display:flex;align-items:flex-end;">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <table>
        <thead><tr><th>Código</th><th>Fecha</th><th>Paciente</th><th>Total</th><th>Pagado</th><th>Saldo</th><th>Estado</th></tr></thead>
        <tbody>
            <?php if (!$result['data']): ?><tr><td colspan="7" class="text-center text-muted" style="padding:2rem;">Sin resultados</td></tr><?php endif; ?>
            <?php foreach ($result['data'] as $inv): ?>
                <tr>
                    <td><a href="<?= url('/admin/facturas/'.$inv['id']) ?>"><?= e($inv['code']) ?></a></td>
                    <td><?= e(format_date($inv['issue_date'])) ?></td>
                    <td><?= e($inv['patient_name']) ?> <span class="text-muted">(<?= e($inv['patient_code']) ?>)</span></td>
                    <td><?= money((float)$inv['total']) ?></td>
                    <td><?= money((float)$inv['paid']) ?></td>
                    <td><?= money((float)$inv['balance']) ?></td>
                    <td><?php
                        $cls = ['paid'=>'badge-success','partial'=>'badge-warning','cancelled'=>'badge-danger',
                                'issued'=>'badge-info','draft'=>'badge-muted'][$inv['status']] ?? 'badge-muted'; ?>
                        <span class="badge <?= $cls ?>"><?= e($inv['status']) ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
