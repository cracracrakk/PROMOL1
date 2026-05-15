<?php /** @var array $treatments */ ?>
<div class="page-header">
    <div><h1>Catálogo de tratamientos</h1>
        <p class="subtitle"><?= count($treatments) ?> tratamientos registrados</p></div>
    <a href="<?= url('/admin/tratamientos/nuevo') ?>" class="btn btn-primary">+ Nuevo tratamiento</a>
</div>

<div class="card">
    <table>
        <thead><tr><th>Código</th><th>Nombre</th><th>Categoría</th><th>Duración</th><th>Precio</th><th>Estado</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($treatments as $t): ?>
            <tr>
                <td><code><?= e($t['code']) ?></code></td>
                <td><?= e($t['name']) ?></td>
                <td><?= e($t['category'] ?: '—') ?></td>
                <td><?= (int)$t['duration_min'] ?> min</td>
                <td><?= money((float)$t['default_price']) ?></td>
                <td><?= $t['is_active'] ? '<span class="badge badge-success">activo</span>' : '<span class="badge badge-muted">inactivo</span>' ?></td>
                <td><a href="<?= url('/admin/tratamientos/'.$t['id'].'/editar') ?>" class="btn btn-sm">Editar</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
