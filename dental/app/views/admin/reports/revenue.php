<?php /** @var array $data */ ?>
<div class="page-header">
    <h1>Ingresos por mes</h1>
    <a href="<?= url('/admin/reportes') ?>" class="btn">← Reportes</a>
</div>
<div class="card">
    <table>
        <thead><tr><th>Mes</th><th>Facturas</th><th>Facturado</th><th>Cobrado</th><th>Pendiente</th></tr></thead>
        <tbody>
            <?php $totalBilled = 0; $totalColl = 0;
                foreach ($data as $d) { $totalBilled += (float)$d['billed']; $totalColl += (float)$d['collected']; } ?>
            <?php foreach ($data as $d): ?>
                <tr>
                    <td><strong><?= e($d['month']) ?></strong></td>
                    <td><?= (int)$d['invoices'] ?></td>
                    <td><?= money((float)$d['billed']) ?></td>
                    <td><?= money((float)$d['collected']) ?></td>
                    <td><?= money((float)$d['billed'] - (float)$d['collected']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight:700;background:var(--gray-50);">
                <td colspan="2">Total</td>
                <td><?= money($totalBilled) ?></td>
                <td><?= money($totalColl) ?></td>
                <td><?= money($totalBilled - $totalColl) ?></td>
            </tr>
        </tfoot>
    </table>
</div>
