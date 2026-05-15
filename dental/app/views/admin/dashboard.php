<?php
/** @var array $patients */ /** @var array $today */ /** @var array $invoices */
/** @var array $upcoming */ /** @var array $recent_patients */ /** @var float $payments_today */
?>
<div class="page-header">
    <div>
        <h1>Bienvenido, <?= e(explode(' ', $_user['name'])[0]) ?></h1>
        <p class="subtitle"><?= e(date('l, d \\d\\e F Y')) ?></p>
    </div>
    <div class="flex gap-1">
        <a href="<?= url('/admin/citas/nueva') ?>" class="btn btn-primary">+ Nueva cita</a>
        <a href="<?= url('/admin/pacientes/nuevo') ?>" class="btn">+ Paciente</a>
    </div>
</div>

<div class="grid grid-4">
    <div class="stat">
        <div class="label">Citas hoy</div>
        <div class="value"><?= (int)$today['total'] ?></div>
        <div class="meta"><?= (int)$today['pending'] ?> pendientes · <?= (int)$today['completed'] ?> completadas</div>
    </div>
    <div class="stat">
        <div class="label">Pacientes</div>
        <div class="value"><?= (int)$patients['total'] ?></div>
        <div class="meta">+<?= (int)$patients['new_month'] ?> este mes</div>
    </div>
    <div class="stat">
        <div class="label">Facturado (mes)</div>
        <div class="value"><?= money((float)$invoices['billed']) ?></div>
        <div class="meta">Pendiente: <?= money((float)$invoices['pending']) ?></div>
    </div>
    <div class="stat">
        <div class="label">Cobrado hoy</div>
        <div class="value"><?= money((float)$payments_today) ?></div>
        <div class="meta"><?= (int)$invoices['n'] ?> facturas del mes</div>
    </div>
</div>

<div class="grid grid-2 mt-3">
    <div class="card">
        <div class="card-header"><h2>Próximas citas</h2>
            <a href="<?= url('/admin/citas') ?>" class="btn btn-sm">Ver agenda</a>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead><tr><th>Fecha</th><th>Paciente</th><th>Odontólogo</th><th>Estado</th></tr></thead>
                <tbody>
                <?php if (!$upcoming): ?>
                    <tr><td colspan="4" class="text-center text-muted" style="padding:1.5rem;">No hay citas próximas</td></tr>
                <?php endif; foreach ($upcoming as $a): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600;"><?= e(format_date($a['starts_at'], 'd/m')) ?></div>
                            <div class="text-muted" style="font-size:0.8rem;"><?= e(date('H:i', strtotime($a['starts_at']))) ?></div>
                        </td>
                        <td><?= e($a['patient_name']) ?></td>
                        <td><?= e($a['dentist_name']) ?></td>
                        <td>
                            <?php $st = $a['status'];
                                $cls = ['confirmed'=>'badge-success','scheduled'=>'badge-info','completed'=>'badge-muted',
                                        'cancelled'=>'badge-danger','no_show'=>'badge-warning','in_progress'=>'badge-warning'][$st] ?? 'badge-muted'; ?>
                            <span class="badge <?= $cls ?>"><?= e($st) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>Pacientes recientes</h2>
            <a href="<?= url('/admin/pacientes') ?>" class="btn btn-sm">Ver todos</a>
        </div>
        <table>
            <thead><tr><th>Código</th><th>Nombre</th><th>Registro</th></tr></thead>
            <tbody>
            <?php if (!$recent_patients): ?>
                <tr><td colspan="3" class="text-center text-muted" style="padding:1.5rem;">Sin pacientes aún</td></tr>
            <?php endif; foreach ($recent_patients as $p): ?>
                <tr>
                    <td><a href="<?= url('/admin/pacientes/'.$p['id']) ?>"><?= e($p['code']) ?></a></td>
                    <td><?= e($p['first_name'] . ' ' . $p['last_name']) ?></td>
                    <td class="text-muted"><?= e(format_date($p['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
