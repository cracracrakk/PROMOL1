<?php /** @var array $users */ ?>
<div class="page-header">
    <h1>Usuarios del sistema</h1>
    <a href="<?= url('/admin/configuracion') ?>" class="btn">← Volver</a>
</div>

<div class="grid grid-2">
    <div class="card">
        <table>
            <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Último acceso</th></tr></thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= e($u['name']) ?>
                            <?php if ($u['specialty']): ?><div class="text-muted" style="font-size:0.78rem;"><?= e($u['specialty']) ?></div><?php endif; ?>
                        </td>
                        <td><?= e($u['email']) ?></td>
                        <td><span class="badge badge-info"><?= e($u['role']) ?></span></td>
                        <td><?= $u['is_active'] ? '<span class="badge badge-success">activo</span>' : '<span class="badge badge-muted">inactivo</span>' ?></td>
                        <td class="text-muted"><?= e(format_date($u['last_login_at'], 'd/m H:i')) ?: '—' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="card-header"><h2>+ Nuevo usuario</h2></div>
        <div class="card-body">
            <form method="post" action="<?= url('/admin/configuracion/usuarios') ?>">
                <?= csrf_field() ?>
                <div class="form-group"><label>Nombre *</label><input type="text" name="name" required></div>
                <div class="form-group"><label>Email *</label><input type="email" name="email" required></div>
                <div class="form-group"><label>Rol</label>
                    <select name="role">
                        <option value="admin">Admin</option>
                        <option value="dentist">Odontólogo</option>
                        <option value="reception">Recepción</option>
                        <option value="assistant">Asistente</option>
                    </select></div>
                <div class="form-group"><label>Especialidad</label><input type="text" name="specialty"></div>
                <div class="form-group"><label>Colegiatura</label><input type="text" name="license_number"></div>
                <div class="form-group"><label>Contraseña *</label><input type="password" name="password" required minlength="6"></div>
                <label class="checkbox"><input type="checkbox" name="is_active" value="1" checked> Activo</label>
                <button class="btn btn-primary mt-2" style="width:100%;">Crear usuario</button>
            </form>
        </div>
    </div>
</div>
