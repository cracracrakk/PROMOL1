<?php /** @var array $data */ /** @var string $from */ /** @var string $to */ ?>
<div class="page-header">
    <h1>Ingresos por odontólogo</h1>
    <a href="<?= url('/admin/reportes') ?>" class="btn">← Reportes</a>
</div>
<div class="card mb-3">
    <div class="card-body">
        <form method="get" class="flex gap-1 items-center">
            Desde <input type="date" name="from" value="<?= e($from) ?>">
            Hasta <input type="date" name="to" value="<?= e($to) ?>">
            <button class="btn btn-primary">Filtrar</button>
        </form>
    </div>
</div>
<div class="card">
    <table>
        <thead><tr><th>Odontólogo</th><th>Facturas</th><th>Facturado</th><th>Cobrado</th></tr></thead>
        <tbody>
            <?php foreach ($data as $d): ?>
                <tr>
                    <td><strong><?= e($d['name']) ?></strong></td>
                    <td><?= (int)$d['invoices'] ?></td>
                    <td><?= money((float)$d['billed']) ?></td>
                    <td><?= money((float)$d['collected']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
