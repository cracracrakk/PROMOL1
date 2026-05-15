<?php /** @var array $patient */ /** @var array $upcoming */ /** @var array $history */
/** @var array $notes */ /** @var array $plans */ /** @var array $invoices */
$mh = $patient['medical_history'] ?? [];
?>
<div class="page-header">
    <div>
        <h1><?= e($patient['first_name'].' '.$patient['last_name']) ?></h1>
        <p class="subtitle">Ficha <?= e($patient['code']) ?> · <?= e(age_from($patient['birth_date']) ?: '—') ?> años</p>
    </div>
    <div class="flex gap-1 flex-wrap">
        <a href="<?= url('/admin/pacientes/'.$patient['id'].'/odontograma') ?>" class="btn btn-primary">🦷 Odontograma</a>
        <a href="<?= url('/admin/pacientes/'.$patient['id'].'/periodontograma') ?>" class="btn">Periodontograma</a>
        <a href="<?= url('/admin/pacientes/'.$patient['id'].'/historia') ?>" class="btn">Historia clínica</a>
        <a href="<?= url('/admin/pacientes/'.$patient['id'].'/documentos') ?>" class="btn">📁 Documentos</a>
        <a href="<?= url('/admin/pacientes/'.$patient['id'].'/recetas') ?>" class="btn">℞ Recetas</a>
        <a href="<?= url('/admin/citas/nueva?patient_id='.$patient['id']) ?>" class="btn">+ Cita</a>
        <a href="<?= url('/admin/facturas/nueva?patient_id='.$patient['id']) ?>" class="btn">+ Factura</a>
        <a href="<?= url('/admin/pacientes/'.$patient['id'].'/editar') ?>" class="btn">Editar</a>
    </div>
</div>

<?php $hasAlerts = !empty($mh['allergies']) || !empty($mh['diabetes']) || !empty($mh['hypertension']) || !empty($mh['heart_disease']); ?>
<?php if ($hasAlerts): ?>
<div class="alert alert-error" style="border-left:3px solid var(--danger);">
    <strong>⚠ Alertas médicas:</strong>
    <?php if (!empty($mh['allergies'])): ?> <strong>Alergias:</strong> <?= e($mh['allergies']) ?>.<?php endif; ?>
    <?php if (!empty($mh['diabetes'])): ?> Diabetes.<?php endif; ?>
    <?php if (!empty($mh['hypertension'])): ?> Hipertensión.<?php endif; ?>
    <?php if (!empty($mh['heart_disease'])): ?> Cardiopatía.<?php endif; ?>
</div>
<?php endif; ?>

<div class="grid grid-3 mb-3">
    <div class="card">
        <div class="card-header"><h2>Datos</h2></div>
        <div class="card-body" style="font-size:0.88rem;line-height:1.7;">
            <div><strong>Documento:</strong> <?= e($patient['document_number'] ?: '—') ?></div>
            <div><strong>Nacimiento:</strong> <?= e(format_date($patient['birth_date']) ?: '—') ?></div>
            <div><strong>Género:</strong> <?= e($patient['gender'] ?: '—') ?></div>
            <div><strong>Grupo sanguíneo:</strong> <?= e($patient['blood_type'] ?: '—') ?></div>
            <div><strong>Ocupación:</strong> <?= e($patient['occupation'] ?: '—') ?></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h2>Contacto</h2></div>
        <div class="card-body" style="font-size:0.88rem;line-height:1.7;">
            <div><strong>Email:</strong> <?= e($patient['email'] ?: '—') ?></div>
            <div><strong>Móvil:</strong> <?= e($patient['mobile'] ?: '—') ?></div>
            <div><strong>Teléfono:</strong> <?= e($patient['phone'] ?: '—') ?></div>
            <div><strong>Dirección:</strong> <?= e($patient['address'] ?: '—') ?></div>
            <div><strong>Ciudad:</strong> <?= e($patient['city'] ?: '—') ?></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h2>Emergencia</h2></div>
        <div class="card-body" style="font-size:0.88rem;line-height:1.7;">
            <div><strong>Contacto:</strong> <?= e($patient['emergency_name'] ?: '—') ?></div>
            <div><strong>Teléfono:</strong> <?= e($patient['emergency_phone'] ?: '—') ?></div>
            <div><strong>Parentesco:</strong> <?= e($patient['emergency_rel'] ?: '—') ?></div>
            <div><strong>Referido por:</strong> <?= e($patient['referred_by'] ?: '—') ?></div>
        </div>
    </div>
</div>

<div class="grid grid-2">
    <div class="card">
        <div class="card-header"><h2>Próximas citas</h2></div>
        <table>
            <thead><tr><th>Fecha</th><th>Tratamiento</th><th>Odontólogo</th><th>Estado</th></tr></thead>
            <tbody>
                <?php if (!$upcoming): ?><tr><td colspan="4" class="text-center text-muted" style="padding:1rem;">Sin citas próximas</td></tr><?php endif; ?>
                <?php foreach ($upcoming as $a): ?>
                    <tr>
                        <td><?= e(format_date($a['starts_at'], 'd/m H:i')) ?></td>
                        <td><?= e($a['treatment_name'] ?: $a['reason'] ?: '—') ?></td>
                        <td><?= e($a['dentist_name']) ?></td>
                        <td><span class="badge badge-info"><?= e($a['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="card-header"><h2>Historial de visitas</h2></div>
        <table>
            <thead><tr><th>Fecha</th><th>Tratamiento</th><th>Estado</th></tr></thead>
            <tbody>
                <?php if (!$history): ?><tr><td colspan="3" class="text-center text-muted" style="padding:1rem;">Sin historial</td></tr><?php endif; ?>
                <?php foreach (array_slice($history, 0, 10) as $a): ?>
                    <tr>
                        <td><?= e(format_date($a['starts_at'], 'd/m/Y')) ?></td>
                        <td><?= e($a['treatment_name'] ?: $a['reason'] ?: '—') ?></td>
                        <td><span class="badge badge-muted"><?= e($a['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header"><h2>Facturas</h2>
        <a href="<?= url('/admin/facturas/nueva?patient_id='.$patient['id']) ?>" class="btn btn-sm btn-primary">+ Nueva factura</a>
    </div>
    <table>
        <thead><tr><th>Código</th><th>Fecha</th><th>Total</th><th>Pagado</th><th>Saldo</th><th>Estado</th></tr></thead>
        <tbody>
            <?php if (!$invoices): ?><tr><td colspan="6" class="text-center text-muted" style="padding:1rem;">Sin facturas</td></tr><?php endif; ?>
            <?php foreach ($invoices as $inv): ?>
                <tr>
                    <td><a href="<?= url('/admin/facturas/'.$inv['id']) ?>"><?= e($inv['code']) ?></a></td>
                    <td><?= e(format_date($inv['issue_date'])) ?></td>
                    <td><?= money((float)$inv['total']) ?></td>
                    <td><?= money((float)$inv['paid']) ?></td>
                    <td><?= money((float)$inv['balance']) ?></td>
                    <td><span class="badge badge-info"><?= e($inv['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
