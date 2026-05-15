<?php /** @var ?array $patient */
$p = $patient ?? [];
$mh = $patient['medical_history'] ?? [];
$isNew = empty($p['id']);
$action = $isNew ? url('/admin/pacientes/nuevo') : url('/admin/pacientes/'.$p['id'].'/editar');
?>
<div class="page-header">
    <h1><?= $isNew ? 'Nuevo paciente' : 'Editar paciente' ?></h1>
    <a href="<?= url('/admin/pacientes') ?>" class="btn">← Volver</a>
</div>

<form method="post" action="<?= $action ?>">
    <?= csrf_field() ?>

    <div class="card mb-3">
        <div class="card-header"><h2>Datos personales</h2></div>
        <div class="card-body">
            <div class="grid grid-3">
                <div class="form-group"><label>Nombre *</label>
                    <input type="text" name="first_name" required value="<?= e($p['first_name'] ?? '') ?>"></div>
                <div class="form-group"><label>Apellido *</label>
                    <input type="text" name="last_name" required value="<?= e($p['last_name'] ?? '') ?>"></div>
                <div class="form-group"><label>Tipo de documento</label>
                    <select name="document_type">
                        <?php foreach (['dni'=>'DNI','passport'=>'Pasaporte','other'=>'Otro'] as $k=>$v): ?>
                            <option value="<?= $k ?>" <?= (($p['document_type'] ?? '')===$k)?'selected':'' ?>><?= e($v) ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="form-group"><label>Número de documento</label>
                    <input type="text" name="document_number" value="<?= e($p['document_number'] ?? '') ?>"></div>
                <div class="form-group"><label>Fecha de nacimiento</label>
                    <input type="date" name="birth_date" value="<?= e($p['birth_date'] ?? '') ?>"></div>
                <div class="form-group"><label>Género</label>
                    <select name="gender">
                        <option value="">—</option>
                        <?php foreach (['male'=>'Masculino','female'=>'Femenino','other'=>'Otro'] as $k=>$v): ?>
                            <option value="<?= $k ?>" <?= (($p['gender'] ?? '')===$k)?'selected':'' ?>><?= e($v) ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="form-group"><label>Grupo sanguíneo</label>
                    <input type="text" name="blood_type" placeholder="O+, A-, ..." value="<?= e($p['blood_type'] ?? '') ?>"></div>
                <div class="form-group"><label>Ocupación</label>
                    <input type="text" name="occupation" value="<?= e($p['occupation'] ?? '') ?>"></div>
                <div class="form-group"><label>Referido por</label>
                    <input type="text" name="referred_by" value="<?= e($p['referred_by'] ?? '') ?>"></div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h2>Contacto</h2></div>
        <div class="card-body">
            <div class="grid grid-3">
                <div class="form-group"><label>Email</label>
                    <input type="email" name="email" value="<?= e($p['email'] ?? '') ?>"></div>
                <div class="form-group"><label>Teléfono fijo</label>
                    <input type="tel" name="phone" value="<?= e($p['phone'] ?? '') ?>"></div>
                <div class="form-group"><label>Móvil</label>
                    <input type="tel" name="mobile" value="<?= e($p['mobile'] ?? '') ?>"></div>
                <div class="form-group" style="grid-column:span 2;"><label>Dirección</label>
                    <input type="text" name="address" value="<?= e($p['address'] ?? '') ?>"></div>
                <div class="form-group"><label>Ciudad</label>
                    <input type="text" name="city" value="<?= e($p['city'] ?? '') ?>"></div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h2>Contacto de emergencia</h2></div>
        <div class="card-body">
            <div class="grid grid-3">
                <div class="form-group"><label>Nombre</label>
                    <input type="text" name="emergency_name" value="<?= e($p['emergency_name'] ?? '') ?>"></div>
                <div class="form-group"><label>Teléfono</label>
                    <input type="tel" name="emergency_phone" value="<?= e($p['emergency_phone'] ?? '') ?>"></div>
                <div class="form-group"><label>Parentesco</label>
                    <input type="text" name="emergency_rel" value="<?= e($p['emergency_rel'] ?? '') ?>"></div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h2>Historia médica</h2></div>
        <div class="card-body">
            <div class="grid grid-2">
                <div class="form-group"><label>Alergias</label>
                    <textarea name="allergies" rows="2"><?= e($mh['allergies'] ?? '') ?></textarea></div>
                <div class="form-group"><label>Condiciones crónicas</label>
                    <textarea name="chronic_conditions" rows="2"><?= e($mh['chronic_conditions'] ?? '') ?></textarea></div>
                <div class="form-group"><label>Medicamentos actuales</label>
                    <textarea name="current_medications" rows="2"><?= e($mh['current_medications'] ?? '') ?></textarea></div>
                <div class="form-group"><label>Cirugías previas</label>
                    <textarea name="past_surgeries" rows="2"><?= e($mh['past_surgeries'] ?? '') ?></textarea></div>
            </div>
            <div class="grid grid-4 mt-2">
                <?php foreach ([
                    'smokes'=>'Fuma', 'drinks_alcohol'=>'Consume alcohol',
                    'pregnant'=>'Embarazo', 'diabetes'=>'Diabetes',
                    'hypertension'=>'Hipertensión', 'heart_disease'=>'Cardiopatías',
                    'bleeding_disorders'=>'Trastornos de coagulación'
                ] as $k => $label): ?>
                    <label class="checkbox">
                        <input type="checkbox" name="<?= $k ?>" value="1" <?= !empty($mh[$k]) ? 'checked' : '' ?>>
                        <?= e($label) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="form-group mt-2">
                <label>Notas adicionales (historia médica)</label>
                <textarea name="medical_notes" rows="2"><?= e($mh['notes'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h2>Notas generales</h2></div>
        <div class="card-body">
            <textarea name="notes" rows="3"><?= e($p['notes'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="flex gap-1 justify-between">
        <a href="<?= url('/admin/pacientes' . ($isNew ? '' : '/'.$p['id'])) ?>" class="btn">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar paciente</button>
    </div>
</form>
