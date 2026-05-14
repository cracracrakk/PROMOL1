<?php $pageTitle = 'Sucursales'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Locales del negocio</div>
    <button class="btn-admin" onclick="document.getElementById('bForm').classList.toggle('hidden')">+ Nueva sucursal</button>
</div>

<div id="bForm" class="card hidden">
    <form method="post" action="<?= url('/admin/sistema/sucursales/guardar') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group"><label>Nombre *</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Teléfono</label><input type="text" name="phone"></div>
            <div class="form-group"><label>Email</label><input type="email" name="email"></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Dirección</label><input type="text" name="address"></div>
            <div class="form-group"><label>Prefijo CAI (sucursal)</label><input type="text" name="sar_cai_prefix" placeholder="002, 003..."></div>
            <div class="form-group"><label>Zona horaria</label><input type="text" name="timezone" value="America/Tegucigalpa"></div>
            <div class="form-group"><label><input type="checkbox" name="active" checked> Activa</label></div>
        </div>
        <button class="btn-admin">Guardar</button>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Sucursal</th><th>Teléfono</th><th>Dirección</th><th>Prefijo CAI</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($branches as $b): ?>
            <tr>
                <td><strong><?= e($b['name']) ?></strong></td>
                <td><?= e($b['phone']) ?></td>
                <td><?= e($b['address']) ?></td>
                <td><?= e($b['sar_cai_prefix'] ?? '—') ?></td>
                <td><?= $b['active'] ? '<span class="badge badge-success">Activa</span>' : '<span class="badge badge-secondary">Inactiva</span>' ?></td>
                <td>
                    <form method="post" action="<?= url('/admin/sistema/sucursales/'.$b['id'].'/eliminar') ?>" data-confirm="¿Eliminar sucursal?" style="display:inline;">
                        <?= csrf_field() ?>
                        <button class="btn-icon danger">✕</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($branches)): ?>
            <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--muted);">Sin sucursales. Si solo tienes un local, no necesitas configurar esto.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<div class="card">
    <h3>ℹ️ Cómo funciona multi-sucursal</h3>
    <p style="color:var(--muted);line-height:1.7;">
        Si tienes <strong>una sola sucursal</strong>, ignora esta sección — todo funciona normalmente.<br><br>
        Para <strong>cadenas con varios locales</strong>: registra cada sucursal aquí.
        Cada sucursal puede tener su propio prefijo CAI (parte del establecimiento) si tributa con SAR independiente.
        La asignación de usuarios y citas a sucursal específica se hace en futuras versiones (campo branch_id pendiente).
    </p>
</div>

<style>.hidden{display:none}</style>
<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
