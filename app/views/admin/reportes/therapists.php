<?php $pageTitle = 'Comparativa de terapeutas'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<form method="get" class="filters">
    <div class="form-group" style="margin:0;"><label style="display:block;font-size:.78rem;">Desde</label><input type="date" name="from" value="<?= e($from) ?>"></div>
    <div class="form-group" style="margin:0;"><label style="display:block;font-size:.78rem;">Hasta</label><input type="date" name="to" value="<?= e($to) ?>"></div>
    <button class="btn-admin">Filtrar</button>
</form>

<div class="card">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Terapeuta</th><th>Sesiones</th><th>Completadas</th><th>No-shows</th><th>Clientes únicos</th><th>Ingresos</th><th>NPS avg</th></tr></thead>
        <tbody>
        <?php foreach ($data as $t): ?>
            <tr>
                <td><strong><?= e($t['name']) ?></strong></td>
                <td><?= (int)$t['sessions'] ?></td>
                <td><?= (int)$t['completed'] ?></td>
                <td><?= (int)$t['no_shows'] ?></td>
                <td><?= (int)$t['unique_customers'] ?></td>
                <td><strong><?= money($t['revenue']) ?></strong></td>
                <td><?= $t['avg_nps'] ? round((float)$t['avg_nps'], 1) : '—' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
