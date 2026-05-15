<?php /** @var array $upcoming */ /** @var array $history */ ?>
<h1>Mis citas</h1>

<div class="card mb-3">
    <div class="card-header"><h2>Próximas</h2></div>
    <table>
        <thead><tr><th>Fecha</th><th>Odontólogo</th><th>Estado</th></tr></thead>
        <tbody>
            <?php if (!$upcoming): ?><tr><td colspan="3" class="text-center text-muted" style="padding:1.5rem;">Sin citas próximas</td></tr><?php endif; ?>
            <?php foreach ($upcoming as $a): ?>
                <tr>
                    <td><?= e(format_date($a['starts_at'], 'd/m/Y H:i')) ?></td>
                    <td><?= e($a['dentist_name']) ?></td>
                    <td><span class="badge badge-info"><?= e($a['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <div class="card-header"><h2>Historial</h2></div>
    <table>
        <thead><tr><th>Fecha</th><th>Odontólogo</th><th>Tratamiento</th><th>Estado</th></tr></thead>
        <tbody>
            <?php if (!$history): ?><tr><td colspan="4" class="text-center text-muted" style="padding:1.5rem;">Sin historial</td></tr><?php endif; ?>
            <?php foreach ($history as $a): ?>
                <tr>
                    <td><?= e(format_date($a['starts_at'], 'd/m/Y')) ?></td>
                    <td><?= e($a['dentist_name']) ?></td>
                    <td><?= e($a['treatment_name'] ?: '—') ?></td>
                    <td><span class="badge badge-muted"><?= e($a['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
