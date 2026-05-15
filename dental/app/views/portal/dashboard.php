<?php /** @var array $patient */ /** @var array $upcoming */ ?>
<h1>Hola, <?= e($patient['first_name']) ?> 👋</h1>
<p class="text-muted">Ficha <?= e($patient['code']) ?></p>

<div class="grid grid-3 mt-3">
    <div class="stat">
        <div class="label">Tu próxima cita</div>
        <div class="value" style="font-size:1.1rem;">
            <?php if ($upcoming): ?>
                <?= e(format_date($upcoming[0]['starts_at'], 'd/m H:i')) ?>
            <?php else: ?>
                <span class="text-muted">—</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="stat">
        <div class="label">Tus citas próximas</div>
        <div class="value"><?= count($upcoming) ?></div>
    </div>
    <div class="stat">
        <div class="label">Estado</div>
        <div class="value" style="font-size:1.1rem;">Activo</div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header"><h2>Próximas citas</h2>
        <a href="<?= url('/mi-cuenta/citas') ?>" class="btn btn-sm">Ver todas</a>
    </div>
    <table>
        <thead><tr><th>Fecha</th><th>Odontólogo</th><th>Estado</th></tr></thead>
        <tbody>
            <?php if (!$upcoming): ?>
                <tr><td colspan="3" class="text-center text-muted" style="padding:1.5rem;">Sin citas programadas</td></tr>
            <?php endif; foreach ($upcoming as $a): ?>
                <tr>
                    <td><?= e(format_date($a['starts_at'], 'd/m/Y H:i')) ?></td>
                    <td><?= e($a['dentist_name']) ?></td>
                    <td><span class="badge badge-info"><?= e($a['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
