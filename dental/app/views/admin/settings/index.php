<?php /** @var array $settings */
$s = $settings;
?>
<div class="page-header">
    <div><h1>Configuración</h1>
        <p class="subtitle">Personaliza tu clínica</p></div>
    <a href="<?= url('/admin/configuracion/usuarios') ?>" class="btn">Usuarios</a>
</div>

<form method="post" action="<?= url('/admin/configuracion/guardar') ?>">
    <?= csrf_field() ?>

    <div class="card mb-3">
        <div class="card-header"><h2>Datos de la clínica</h2></div>
        <div class="card-body">
            <div class="grid grid-2">
                <div class="form-group"><label>Nombre</label>
                    <input type="text" name="clinic_name" value="<?= e($s['clinic_name'] ?? '') ?>"></div>
                <div class="form-group"><label>Slogan</label>
                    <input type="text" name="clinic_tagline" value="<?= e($s['clinic_tagline'] ?? '') ?>"></div>
                <div class="form-group" style="grid-column:span 2;"><label>Dirección</label>
                    <input type="text" name="clinic_address" value="<?= e($s['clinic_address'] ?? '') ?>"></div>
                <div class="form-group"><label>Teléfono</label>
                    <input type="tel" name="clinic_phone" value="<?= e($s['clinic_phone'] ?? '') ?>"></div>
                <div class="form-group"><label>Email</label>
                    <input type="email" name="clinic_email" value="<?= e($s['clinic_email'] ?? '') ?>"></div>
                <div class="form-group"><label>RTN / NIT</label>
                    <input type="text" name="clinic_rtn" value="<?= e($s['clinic_rtn'] ?? '') ?>"></div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h2>Branding</h2></div>
        <div class="card-body">
            <div class="grid grid-2">
                <div class="form-group"><label>Color primario</label>
                    <input type="color" name="clinic_color_primary" value="<?= e($s['clinic_color_primary'] ?? '#0ea5e9') ?>"></div>
                <div class="form-group"><label>Color de acento</label>
                    <input type="color" name="clinic_color_accent" value="<?= e($s['clinic_color_accent'] ?? '#06b6d4') ?>"></div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h2>Moneda e impuestos</h2></div>
        <div class="card-body">
            <div class="grid grid-3">
                <div class="form-group"><label>Símbolo de moneda</label>
                    <input type="text" name="currency_symbol" value="<?= e($s['currency_symbol'] ?? 'L') ?>"></div>
                <div class="form-group"><label>Código ISO</label>
                    <input type="text" name="currency_code" value="<?= e($s['currency_code'] ?? 'HNL') ?>"></div>
                <div class="form-group"><label>Impuesto por defecto (%)</label>
                    <input type="number" step="0.01" name="tax_rate" value="<?= e($s['tax_rate'] ?? 15) ?>"></div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h2>Horarios</h2></div>
        <div class="card-body">
            <div class="grid grid-3">
                <div class="form-group"><label>Apertura</label>
                    <input type="time" name="working_hours_start" value="<?= e($s['working_hours_start'] ?? '08:00') ?>"></div>
                <div class="form-group"><label>Cierre</label>
                    <input type="time" name="working_hours_end" value="<?= e($s['working_hours_end'] ?? '18:00') ?>"></div>
                <div class="form-group"><label>Paso de citas (min)</label>
                    <input type="number" name="appointment_step" value="<?= e($s['appointment_step'] ?? 30) ?>"></div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h2>Recordatorios y comunicación</h2></div>
        <div class="card-body">
            <div class="grid grid-3">
                <div class="form-group">
                    <label class="checkbox">
                        <input type="checkbox" name="reminder_enabled" value="1" <?= !empty($s['reminder_enabled']) ? 'checked' : '' ?>>
                        Recordatorios activos
                    </label>
                </div>
                <div class="form-group"><label>Horas antes</label>
                    <input type="number" name="reminder_hours_before" value="<?= e($s['reminder_hours_before'] ?? 24) ?>"></div>
                <div class="form-group"><label>Canal</label>
                    <select name="reminder_channel">
                        <?php foreach (['email','whatsapp','sms'] as $c): ?>
                            <option value="<?= $c ?>" <?= ($s['reminder_channel'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="form-group"><label>Recall: meses sin venir</label>
                    <input type="number" name="recall_months" value="<?= e($s['recall_months'] ?? 6) ?>"></div>
                <div class="form-group" style="grid-column:span 2;"><label>WhatsApp API URL (opcional)</label>
                    <input type="text" name="whatsapp_api_url" value="<?= e($s['whatsapp_api_url'] ?? '') ?>" placeholder="https://api.whatsapp.com/..."></div>
                <div class="form-group" style="grid-column:span 3;"><label>WhatsApp API token</label>
                    <input type="text" name="whatsapp_api_token" value="<?= e($s['whatsapp_api_token'] ?? '') ?>"></div>
            </div>
        </div>
    </div>

    <div class="flex justify-between">
        <span></span>
        <button class="btn btn-primary">Guardar configuración</button>
    </div>
</form>
