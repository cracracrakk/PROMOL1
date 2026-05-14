<?php $pageTitle = 'Categorías de Productos'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>
<div class="page-header">
    <div class="breadcrumb">Agrupación de productos del inventario</div>
    <button class="btn-admin" onclick="document.getElementById('cForm').classList.toggle('hidden')">+ Nueva</button>
</div>
<div id="cForm" class="card hidden">
    <form method="post" action="<?= url('/admin/catalogos/categorias-productos/guardar') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group"><label>Nombre *</label><input type="text" name="name" required></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Descripción</label><input type="text" name="description"></div>
        </div>
        <button class="btn-admin">Guardar</button>
    </form>
</div>
<div class="card">
    <table class="table">
        <thead><tr><th>Nombre</th><th>Descripción</th></tr></thead>
        <tbody>
        <?php foreach ($categories as $c): ?>
            <tr><td><strong><?= e($c['name']) ?></strong></td><td><?= e($c['description'] ?? '—') ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<style>.hidden{display:none}</style>
<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
