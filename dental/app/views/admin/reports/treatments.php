<?php /** @var array $data */ /** @var string $from */ /** @var string $to */ ?>
<div class="page-header">
    <h1>Tratamientos más rentables</h1>
    <a href="<?= url('/admin/reportes') ?>" class="btn">← Reportes</a>
</div>
<div class="card mb-3"><div class="card-body">
    <form method="get" class="flex gap-1 items-center">
        Desde <input type="date" name="from" value="<?= e($from) ?>">
        Hasta <input type="date" name="to" value="<?= e($to) ?>">
        <button class="btn btn-primary">Filtrar</button>
    </form>
</div></div>
<div class="card">
    <table>
        <thead><tr><th>Tratamiento</th><th>Veces realizado</th><th>Ingresos</th></tr></thead>
        <tbody>
            <?php if (!$data): ?><tr><td colspan="3" class="text-center text-muted" style="padding:1.5rem;">Sin datos</td></tr><?php endif; ?>
            <?php foreach ($data as $d): ?>
                <tr>
                    <td><?= e($d['name']) ?></td>
                    <td><?= (int)$d['n'] ?></td>
                    <td><strong><?= money((float)$d['revenue']) ?></strong></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
