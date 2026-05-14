<?php
$isEdit = isset($user);
$pageTitle = $isEdit ? 'Editar usuario' : 'Nuevo usuario';
require dirname(__DIR__, 2) . '/layouts/admin_header.php';
$u = $user ?? [];
$g = fn($k, $d = '') => e($u[$k] ?? $d);
?>

<div class="page-header"><div class="breadcrumb"><a href="<?= url('/admin/sistema/usuarios') ?>">← Volver</a></div></div>

<form method="post" action="<?= url('/admin/sistema/usuarios/guardar') ?>" class="card">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int)$user['id'] ?>"><?php endif; ?>

    <div class="form-section">
        <h3>Datos del usuario</h3>
        <div class="form-grid">
            <div class="form-group"><label>Nombre completo *</label><input type="text" name="name" required value="<?= $g('name') ?>"></div>
            <div class="form-group"><label>Email *</label><input type="email" name="email" required value="<?= $g('email') ?>"></div>
            <div class="form-group"><label>Teléfono</label><input type="text" name="phone" value="<?= $g('phone') ?>"></div>
            <div class="form-group">
                <label>Rol *</label>
                <select name="role" required>
                    <?php foreach (['admin'=>'Administrador','gerente'=>'Gerente','recepcion'=>'Recepción','terapeuta'=>'Terapeuta','contable'=>'Contable'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= ($u['role'] ?? '') === $v ? 'selected' : '' ?>><?= e($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Contraseña <?= $isEdit ? '(dejar vacío para no cambiar)' : '*' ?></label>
                <input type="password" name="password" <?= $isEdit ? '' : 'required' ?>>
            </div>
            <div class="form-group"><label>Color identificativo</label><input type="color" name="color" value="<?= $g('color', '#6b8a7a') ?>"></div>
            <div class="form-group" style="grid-column:1/-1;"><label>Biografía</label><textarea name="bio" rows="3"><?= $g('bio') ?></textarea></div>
            <div class="form-group"><label><input type="checkbox" name="active" <?= ($u['active'] ?? 1) ? 'checked' : '' ?>> Activo</label></div>
        </div>
    </div>

    <div class="form-actions">
        <?php if ($isEdit && (int)$user['id'] !== Auth::id()): ?>
            <form method="post" action="<?= url('/admin/sistema/usuarios/'.$user['id'].'/eliminar') ?>" data-confirm="¿Desactivar este usuario?" style="display:inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-admin btn-danger">Desactivar</button>
            </form>
        <?php endif; ?>
        <a href="<?= url('/admin/sistema/usuarios') ?>" class="btn-admin btn-outline">Cancelar</a>
        <button type="submit" class="btn-admin"><?= $isEdit ? 'Guardar' : 'Crear usuario' ?></button>
    </div>
</form>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
