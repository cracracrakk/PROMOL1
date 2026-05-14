<?php $pageTitle = 'Cabinas y Salas'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Espacios físicos del spa</div>
    <button class="btn-admin" onclick="document.getElementById('roomForm').classList.toggle('hidden')">+ Nueva cabina</button>
</div>

<div id="roomForm" class="card hidden">
    <form method="post" action="<?= url('/admin/catalogos/cabinas/guardar') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group"><label>Nombre *</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Tipo</label><input type="text" name="room_type" placeholder="masaje, facial, húmeda..."></div>
            <div class="form-group"><label>Capacidad</label><input type="number" name="capacity" value="1" min="1"></div>
            <div class="form-group"><label>Descripción</label><input type="text" name="description"></div>
            <div class="form-group"><label><input type="checkbox" name="active" checked> Activa</label></div>
        </div>
        <button type="submit" class="btn-admin">Guardar</button>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Nombre</th><th>Tipo</th><th>Capacidad</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rooms as $r): ?>
            <tr>
                <td><strong><?= e($r['name']) ?></strong><br><small style="color:var(--muted);"><?= e($r['description']) ?></small></td>
                <td><?= e($r['room_type'] ?? '—') ?></td>
                <td><?= (int)$r['capacity'] ?></td>
                <td><?= $r['active'] ? '<span class="badge badge-success">Activa</span>' : '<span class="badge badge-secondary">Inactiva</span>' ?></td>
                <td>
                    <form method="post" action="<?= url('/admin/catalogos/cabinas/'.$r['id'].'/eliminar') ?>" data-confirm="¿Eliminar cabina?" style="display:inline;">
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
