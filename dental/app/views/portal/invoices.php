<?php /** @var array $invoices */ ?>
<h1>Mis facturas</h1>
<div class="card">
    <table>
        <thead><tr><th>Código</th><th>Fecha</th><th>Total</th><th>Pagado</th><th>Saldo</th><th>Estado</th></tr></thead>
        <tbody>
            <?php if (!$invoices): ?><tr><td colspan="6" class="text-center text-muted" style="padding:1.5rem;">Sin facturas</td></tr><?php endif; ?>
            <?php foreach ($invoices as $inv): ?>
                <tr>
                    <td><?= e($inv['code']) ?></td>
                    <td><?= e(format_date($inv['issue_date'])) ?></td>
                    <td><?= money((float)$inv['total']) ?></td>
                    <td><?= money((float)$inv['paid']) ?></td>
                    <td><strong><?= money((float)$inv['balance']) ?></strong></td>
                    <td><span class="badge badge-info"><?= e($inv['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
