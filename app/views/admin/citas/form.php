<?php
$pageTitle = isset($appointment) ? 'Editar cita' : 'Nueva cita';
require dirname(__DIR__, 2) . '/layouts/admin_header.php';
$isEdit = isset($appointment);
$prefDate = $isEdit ? date('Y-m-d', strtotime($appointment['starts_at'])) : ($_GET['date'] ?? date('Y-m-d'));
$prefTime = $isEdit ? date('H:i', strtotime($appointment['starts_at'])) : ($_GET['time'] ?? '10:00');
$formAction = $isEdit ? url('/admin/citas/' . $appointment['id'] . '/editar') : url('/admin/citas/nueva');
?>

<div class="page-header">
    <div><div class="breadcrumb"><a href="<?= url('/admin/citas/agenda') ?>">← Volver a la agenda</a></div></div>
</div>

<form method="post" action="<?= $formAction ?>" class="card">
    <?= csrf_field() ?>

    <div class="form-section">
        <h3>Cliente y servicio</h3>
        <div class="form-grid">
            <div class="form-group">
                <label>Cliente *</label>
                <select name="customer_id" required>
                    <option value="">— Selecciona —</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= ($isEdit && $appointment['customer_id'] == $c['id']) ? 'selected' : '' ?>>
                            <?= e(trim($c['first_name'] . ' ' . $c['last_name'])) ?> – <?= e($c['phone']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="form-help"><a href="<?= url('/admin/clientes/nuevo') ?>" target="_blank">+ Crear nuevo cliente</a></p>
            </div>
            <div class="form-group">
                <label>Servicio *</label>
                <select name="service_id" required>
                    <option value="">— Selecciona —</option>
                    <?php foreach ($services as $s): ?>
                        <option value="<?= (int)$s['id'] ?>" <?= ($isEdit && $appointment['service_id'] == $s['id']) ? 'selected' : '' ?>>
                            <?= e($s['name']) ?> · <?= (int)$s['duration_minutes'] ?> min · <?= money($s['price']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Fecha y asignación</h3>
        <div class="form-grid">
            <div class="form-group">
                <label>Fecha *</label>
                <input type="date" name="date" required value="<?= e($prefDate) ?>">
            </div>
            <div class="form-group">
                <label>Hora *</label>
                <input type="time" name="time" required value="<?= e($prefTime) ?>" step="900">
            </div>
            <div class="form-group">
                <label>Terapeuta</label>
                <select name="therapist_id">
                    <option value="">— Sin asignar —</option>
                    <?php foreach ($therapists as $t): ?>
                        <option value="<?= (int)$t['id'] ?>" <?= ($isEdit && $appointment['therapist_id'] == $t['id']) ? 'selected' : '' ?>><?= e($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Cabina</label>
                <select name="room_id">
                    <option value="">— Sin asignar —</option>
                    <?php foreach ($rooms as $r): ?>
                        <option value="<?= (int)$r['id'] ?>" <?= ($isEdit && $appointment['room_id'] == $r['id']) ? 'selected' : '' ?>><?= e($r['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="status">
                    <?php foreach (['pendiente','confirmada','en_curso','completada','cancelada','no_show'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($isEdit && $appointment['status'] === $st) ? 'selected' : ($st === 'confirmada' && !$isEdit ? 'selected' : '') ?>>
                            <?= ucfirst(str_replace('_',' ',$st)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Notas</h3>
        <div class="form-group">
            <textarea name="notes" rows="3" placeholder="Observaciones, alergias, preferencias..."><?= $isEdit ? e($appointment['notes']) : '' ?></textarea>
        </div>
    </div>

    <div class="form-actions">
        <?php if ($isEdit): ?>
            <button type="submit" form="deleteForm" class="btn-admin btn-danger">Eliminar cita</button>
            <?php
            // Botón WhatsApp si el cliente tiene teléfono
            $cust = Database::fetch('SELECT * FROM customers WHERE id = ?', [(int)$appointment['customer_id']]);
            $svc  = Database::fetch('SELECT name FROM services WHERE id = ?', [(int)$appointment['service_id']]);
            if ($cust && !empty($cust['phone'])):
                $waUrl = whatsapp_appointment_link($appointment, $cust, $svc['name'] ?? '');
            ?>
                <a href="<?= e($waUrl) ?>" target="_blank" class="btn-admin" style="background:#25D366;">
                    📱 Confirmar por WhatsApp
                </a>
            <?php endif; ?>
        <?php endif; ?>
        <a href="<?= url('/admin/citas/agenda') ?>" class="btn-admin btn-outline">Cancelar</a>
        <button type="submit" class="btn-admin"><?= $isEdit ? 'Guardar cambios' : 'Crear cita' ?></button>
    </div>
</form>

<?php if ($isEdit): ?>
<form id="deleteForm" method="post" action="<?= url('/admin/citas/' . $appointment['id'] . '/eliminar') ?>" data-confirm="¿Eliminar esta cita?" style="display:none;">
    <?= csrf_field() ?>
</form>
<?php endif; ?>

<?php require dirname(__DIR__, 2) . '/layouts/admin_footer.php'; ?>
