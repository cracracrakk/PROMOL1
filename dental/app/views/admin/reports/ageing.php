<?php /** @var array $rows */ /** @var array $buckets */ ?>
<div class="page-header">
    <h1>Cuentas por cobrar</h1>
    <a href="<?= url('/admin/reportes') ?>" class="btn">← Reportes</a>
</div>

<div class="grid grid-4 mb-3">
    <div class="stat"><div class="label">0-30 días</div><div class="value"><?= money($buckets['0-30']) ?></div></div>
    <div class="stat" style="border-color:#fde047;"><div class="label">31-60 días</div><div class="value"><?= money($buckets['31-60']) ?></div></div>
    <div class="stat" style="border-color:#fb923c;"><div class="label">61-90 días</div><div class="value"><?= money($buckets['61-90']) ?></div></div>
    <div class="stat" style="border-color:#ef4444;"><div class="label">+90 días</div><div class="value"><?= money($buckets['90+']) ?></div></div>
</div>

<div class="card">
    <table>
        <thead><tr><th>Factura</th><th>Fecha</th><th>Paciente</th><th>Días</th><th>Saldo</th><th>Antigüedad</th></tr></thead>
        <tbody>
            <?php if (!$rows): ?><tr><td colspan="6" class="text-center text-muted" style="padding:1.5rem;">✓ No hay cuentas pendientes</td></tr><?php endif; ?>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><a href="<?= url('/admin/facturas/'.$r['id']) ?>"><?= e($r['code']) ?></a></td>
                    <td><?= e(format_date($r['issue_date'])) ?></td>
                    <td><?= e($r['patient_name']) ?></td>
                    <td><?= (int)$r['days_old'] ?></td>
                    <td><strong><?= money((float)$r['balance']) ?></strong></td>
                    <td>
                        <?php $cls = ['0-30'=>'badge-info','31-60'=>'badge-warning','61-90'=>'badge-warning','90+'=>'badge-danger'][$r['bucket']]; ?>
                        <span class="badge <?= $cls ?>"><?= e($r['bucket']) ?> días</span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
