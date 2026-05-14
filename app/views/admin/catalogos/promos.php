<?php $pageTitle = 'Códigos promocionales'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Descuentos y cupones</div>
    <button class="btn-admin" onclick="document.getElementById('pForm').classList.toggle('hidden')">+ Nuevo código</button>
</div>

<div id="pForm" class="card hidden">
    <form method="post" action="<?= url('/admin/catalogos/promos/guardar') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group"><label>Código *</label><input type="text" name="code" required style="text-transform:uppercase;"></div>
            <div class="form-group"><label>Descripción</label><input type="text" name="description"></div>
            <div class="form-group">
                <label>Tipo</label>
                <select name="discount_type"><option value="percent">Porcentaje (%)</option><option value="amount">Importe fijo (L.)</option></select>
            </div>
            <div class="form-group"><label>Valor *</label><input type="number" step="0.01" name="discount_value" required></div>
            <div class="form-group"><label>Válido desde</label><input type="date" name="valid_from"></div>
            <div class="form-group"><label>Válido hasta</label><input type="date" name="valid_until"></div>
            <div class="form-group"><label>Usos máximos (vacío = ilimitado)</label><input type="number" name="max_uses"></div>
            <div class="form-group"><label><input type="checkbox" name="active" checked> Activo</label></div>
        </div>
        <button class="btn-admin">Guardar</button>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Código</th><th>Descripción</th><th>Descuento</th><th>Validez</th><th>Usos</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($promos as $p): ?>
            <tr>
                <td><code><?= e($p['code']) ?></code></td>
                <td><?= e($p['description'] ?? '—') ?></td>
                <td><?= $p['discount_type'] === 'percent' ? (float)$p['discount_value'].'%' : money($p['discount_value']) ?></td>
                <td><small><?= e($p['valid_from'] ?? '—') ?> → <?= e($p['valid_until'] ?? 'sin límite') ?></small></td>
                <td><?= (int)$p['used_count'] ?> / <?= $p['max_uses'] ?: '∞' ?></td>
                <td><?= $p['active'] ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-secondary">Inactivo</span>' ?></td>
                <td>
                    <form method="post" action="<?= url('/admin/catalogos/promos/'.$p['id'].'/eliminar') ?>" data-confirm="¿Eliminar?" style="display:inline;">
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
