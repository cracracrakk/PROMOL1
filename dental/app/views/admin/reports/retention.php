<?php /** @var array $data */
$retentionRate = $data['with_appointments'] > 0
    ? round(($data['returning'] / $data['with_appointments']) * 100, 1) : 0;
?>
<div class="page-header">
    <h1>Retención de pacientes</h1>
    <a href="<?= url('/admin/reportes') ?>" class="btn">← Reportes</a>
</div>
<div class="grid grid-2">
    <div class="card">
        <div class="card-body">
            <h2>Tasa de retención</h2>
            <div style="font-size:3rem;font-weight:800;color:var(--primary);"><?= $retentionRate ?>%</div>
            <p class="text-muted">% de pacientes con 2 o más citas</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h2>Resumen</h2>
            <table>
                <tr><td>Pacientes activos</td><td class="text-right"><strong><?= (int)$data['total_patients'] ?></strong></td></tr>
                <tr><td>Con al menos 1 cita</td><td class="text-right"><strong><?= (int)$data['with_appointments'] ?></strong></td></tr>
                <tr><td>Con 2+ citas (recurrentes)</td><td class="text-right"><strong><?= (int)$data['returning'] ?></strong></td></tr>
                <tr style="color:var(--warning);"><td>Inactivos hace 6+ meses</td><td class="text-right"><strong><?= (int)$data['inactive_6mo'] ?></strong></td></tr>
            </table>
        </div>
    </div>
</div>
