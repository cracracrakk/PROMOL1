<?php $pageTitle = 'Usuarios y Roles'; require dirname(__DIR__, 2) . '/layouts/admin_header.php'; ?>

<div class="page-header">
    <div class="breadcrumb">Personal del sistema</div>
    <a href="<?= url('/admin/sistema/usuarios/nuevo') ?>" class="btn-admin">+ Nuevo usuario</a>
</div>

<div class="card">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Teléfono</th><th>Último acceso</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><span style="display:inline-block;width:8px;height:8px;background:<?= e($u['color']) ?>;border-radius:50%;margin-right:8px;"></span><strong><?= e($u['name']) ?></strong></td>
                <td><?= e($u['email']) ?></td>
                <td><span class="badge badge-secondary"><?= e($u['role']) ?></span></td>
                <td><?= e($u['phone'] ?? '—') ?></td>
                <td><?= $u['last_login_at'] ? dt($u['last_login_at']) : 'Nunca' ?></td>
                <td><?= $u['active'] ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-secondary">Inactivo</span>' ?></td>
                <td><a href="<?= url('/admin/sistema/usuarios/'.$u['id'].'/editar') ?>" class="btn-admin btn-sm btn-outline">Editar</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
