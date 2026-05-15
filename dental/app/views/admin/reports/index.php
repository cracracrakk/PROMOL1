<?php /** @var array $retention */ ?>
<div class="page-header">
    <h1>Reportes</h1>
    <p class="subtitle">Inteligencia de negocio para tu clínica</p>
</div>

<div class="grid grid-4 mb-3">
    <div class="stat"><div class="label">Pacientes activos</div><div class="value"><?= (int)$retention['total_patients'] ?></div></div>
    <div class="stat"><div class="label">Con citas</div><div class="value"><?= (int)$retention['with_appointments'] ?></div></div>
    <div class="stat"><div class="label">Recurrentes (2+ citas)</div><div class="value"><?= (int)$retention['returning'] ?></div></div>
    <div class="stat" style="border-color:var(--warning);">
        <div class="label">Inactivos 6 meses</div><div class="value"><?= (int)$retention['inactive_6mo'] ?></div>
        <div class="meta">Candidatos a recall</div>
    </div>
</div>

<div class="grid grid-3">
    <a href="<?= url('/admin/reportes/ingresos') ?>" class="card" style="text-decoration:none;color:inherit;">
        <div class="card-body"><div style="font-size:2rem;">💰</div>
            <h3>Ingresos por mes</h3>
            <p class="text-muted">Evolución de facturación y cobranza mensual</p></div>
    </a>
    <a href="<?= url('/admin/reportes/odontologos') ?>" class="card" style="text-decoration:none;color:inherit;">
        <div class="card-body"><div style="font-size:2rem;">👨‍⚕️</div>
            <h3>Por odontólogo</h3>
            <p class="text-muted">Producción por profesional</p></div>
    </a>
    <a href="<?= url('/admin/reportes/tratamientos') ?>" class="card" style="text-decoration:none;color:inherit;">
        <div class="card-body"><div style="font-size:2rem;">🦷</div>
            <h3>Top tratamientos</h3>
            <p class="text-muted">Tratamientos más rentables</p></div>
    </a>
    <a href="<?= url('/admin/reportes/cobranza') ?>" class="card" style="text-decoration:none;color:inherit;">
        <div class="card-body"><div style="font-size:2rem;">📊</div>
            <h3>Cuentas por cobrar</h3>
            <p class="text-muted">Aging report 0/30/60/90 días</p></div>
    </a>
    <a href="<?= url('/admin/reportes/retencion') ?>" class="card" style="text-decoration:none;color:inherit;">
        <div class="card-body"><div style="font-size:2rem;">🔁</div>
            <h3>Retención</h3>
            <p class="text-muted">Pacientes recurrentes vs nuevos</p></div>
    </a>
    <a href="<?= url('/admin/reportes/recall') ?>" class="card" style="text-decoration:none;color:inherit;">
        <div class="card-body"><div style="font-size:2rem;">📞</div>
            <h3>Recall</h3>
            <p class="text-muted">Pacientes para contactar (6+ meses sin venir)</p></div>
    </a>
</div>
