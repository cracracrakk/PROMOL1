<?php $pageTitle = 'Categorías de Servicios'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Agrupación de servicios</div>
    <button class="btn-admin" onclick="document.getElementById('cForm').classList.toggle('hidden')">+ Nueva</button>
</div>

<div id="cForm" class="card hidden">
    <form method="post" action="<?= url('/admin/catalogos/categorias-servicios/guardar') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group"><label>Nombre *</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Icono</label><input type="text" name="icon"></div>
            <div class="form-group"><label>Orden</label><input type="number" name="sort_order" value="0"></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Descripción</label><textarea name="description" rows="2"></textarea></div>
            <div class="form-group"><label><input type="checkbox" name="active" checked> Activa</label></div>
        </div>
        <button class="btn-admin">Guardar</button>
    </form>
</div>

<div class="card">
    <table class="table">
        <thead><tr><th>Nombre</th><th>Descripción</th><th>Orden</th><th>Estado</th></tr></thead>
        <tbody>
        <?php foreach ($categories as $c): ?>
            <tr>
                <td><strong><?= e($c['name']) ?></strong></td>
                <td><?= e($c['description'] ?? '—') ?></td>
                <td><?= (int)$c['sort_order'] ?></td>
                <td><?= $c['active'] ? '<span class="badge badge-success">Activa</span>' : '<span class="badge badge-secondary">Inactiva</span>' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>.hidden{display:none}</style>
<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
