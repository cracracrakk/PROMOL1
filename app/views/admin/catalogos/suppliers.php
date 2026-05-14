<?php $pageTitle = 'Proveedores'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Proveedores de productos</div>
    <button class="btn-admin" onclick="document.getElementById('sForm').classList.toggle('hidden')">+ Nuevo proveedor</button>
</div>

<div id="sForm" class="card hidden">
    <form method="post" action="<?= url('/admin/catalogos/proveedores/guardar') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group"><label>Nombre *</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Contacto</label><input type="text" name="contact"></div>
            <div class="form-group"><label>Teléfono</label><input type="text" name="phone"></div>
            <div class="form-group"><label>Email</label><input type="email" name="email"></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Notas</label><textarea name="notes" rows="2"></textarea></div>
        </div>
        <button type="submit" class="btn-admin">Guardar</button>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Proveedor</th><th>Contacto</th><th>Teléfono</th><th>Email</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($suppliers as $s): ?>
            <tr>
                <td><strong><?= e($s['name']) ?></strong></td>
                <td><?= e($s['contact'] ?? '—') ?></td>
                <td><?= e($s['phone'] ?? '—') ?></td>
                <td><?= e($s['email'] ?? '—') ?></td>
                <td>
                    <form method="post" action="<?= url('/admin/catalogos/proveedores/'.$s['id'].'/eliminar') ?>" data-confirm="¿Eliminar?" style="display:inline;">
                        <?= csrf_field() ?>
                        <button class="btn-icon danger">✕</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<style>.hidden{display:none}</style>
<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
