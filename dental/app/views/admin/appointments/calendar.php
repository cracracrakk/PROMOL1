<?php /** @var array $dentists */ /** @var array $rooms */ ?>
<div class="page-header">
    <div>
        <h1>Calendario</h1>
        <p class="subtitle">Vista semanal de citas</p>
    </div>
    <a href="<?= url('/admin/citas/nueva') ?>" class="btn btn-primary">+ Nueva cita</a>
</div>

<div class="card">
    <div class="card-body">
        <p class="text-muted text-center" style="padding:2rem;">
            Vista de calendario semanal. Implementa esta vista cargando citas vía
            <code>GET /api/appointments?from=...&to=...</code>.
        </p>
    </div>
</div>
